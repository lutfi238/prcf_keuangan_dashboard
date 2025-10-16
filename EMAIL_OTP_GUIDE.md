# 📧 Email OTP Guide - Why It's Not Working & Solutions

**Last Updated:** October 16, 2025

---

## 🔴 **PROBLEM: Gmail Freemail + DKIM/DMARC Issue**

### **Root Cause:**

Email OTP **"berhasil dikirim"** oleh Brevo SMTP, tapi **tidak sampai ke inbox** karena:

1. **Sender menggunakan Gmail (`@gmail.com`)** - freemail domain
2. **DKIM signature: "Default"** - tidak optimal
3. **DMARC: "Freemail domain is not recommended"** ⚠️
4. **Gmail's new policy (2024)** - reject email dari freemail sender via third-party SMTP

### **Gmail's Logic:**
```
Email from: lutfifirdaus238@gmail.com
Sent via: Brevo SMTP (smtp-relay.brevo.com)

Gmail: "Kok email dari Gmail tapi kirimnya lewat Brevo? 
        Ini kemungkinan phishing/spam! BLOCK!"
```

### **Evidence from Brevo Dashboard:**
```
✅ Sender Verified: lutfifirdaus238@gmail.com
⚠️ Warning: "One or several of your senders are not compliant 
            with Google, Yahoo, and Microsoft's new requirements"
⚠️ DKIM: Default (not configured)
⚠️ DMARC: Freemail domain is not recommended
```

---

## ✅ **CURRENT SOLUTION: Manual OTP Display (Demo Mode)**

**Status:** ✅ **IMPLEMENTED**

### **How It Works:**

1. User login dengan username & password
2. OTP generated (6 digit)
3. **OTP ditampilkan langsung di halaman** `verify_otp.php`
4. User copy & paste OTP ke form
5. Berhasil login

### **Pros:**
- ✅ Langsung berfungsi (no email issues)
- ✅ Cocok untuk demo/testing
- ✅ Tidak perlu konfigurasi email
- ✅ Fast & reliable

### **Cons:**
- ❌ Tidak secure untuk production (OTP visible di screen)
- ❌ Tidak ada email notification
- ❌ User bisa screenshot OTP

### **Files Modified:**
- `login.php` - disable `send_otp_email()`, enable `$_SESSION['demo_otp_display']`
- `verify_otp.php` - display OTP box di halaman

---

## 🎯 **FUTURE SOLUTIONS FOR PRODUCTION**

### **OPTION 1: Custom Domain + DKIM/DMARC (RECOMMENDED)** ⭐

**Requirement:**
- Beli domain sendiri (contoh: `prcfi.com`)
- Setup DNS records (DKIM, DMARC, SPF)
- Verify domain di Brevo

**Setup Steps:**

1. **Beli Domain** (Rp 150,000/tahun)
   - Niagahoster: https://niagahoster.co.id
   - Rumahweb: https://rumahweb.com
   - GoDaddy: https://godaddy.com
   - Contoh: `prcfi.com` atau `prcfi.id`

2. **Setup Email di Brevo dengan Domain**
   ```php
   define('FROM_EMAIL', 'noreply@prcfi.com');
   define('FROM_NAME', 'PRCFI Financial');
   ```

3. **Verify Domain di Brevo**
   - Brevo Dashboard → Settings → Senders → Domains
   - Add domain: `prcfi.com`
   - Follow DNS setup instructions

4. **Add DNS Records** (di control panel domain)
   ```
   Type: TXT
   Name: _dmarc.prcfi.com
   Value: v=DMARC1; p=none; rua=mailto:dmarc@prcfi.com

   Type: TXT
   Name: brevo-code
   Value: (dari Brevo dashboard)
   
   Type: CNAME
   Name: brevo._domainkey.prcfi.com
   Value: (dari Brevo dashboard)
   ```

5. **Test Email**
   - Wait 24-48 hours untuk DNS propagation
   - Test kirim email
   - ✅ Email akan masuk inbox (tidak SPAM!)

**Result:**
```
✅ DKIM: Configured
✅ DMARC: Configured
✅ SPF: Configured
✅ Email masuk inbox Gmail/Yahoo/Outlook
✅ Professional sender (noreply@prcfi.com)
```

**Cost:**
- Domain: Rp 150,000/tahun (~$10/year)
- Brevo SMTP: FREE (300 emails/day)

**Effort:**
- Setup time: 1-2 hours
- DNS propagation: 24-48 hours

---

### **OPTION 2: WhatsApp OTP (Alternative)** 💬

**Requirement:**
- WhatsApp Business API (FREE via Fonnte/Wablas)
- User harus punya WhatsApp

**Setup Steps:**

1. **Daftar WhatsApp Business API**
   - Fonnte: https://fonnte.com (FREE 100 messages/month)
   - Wablas: https://wablas.com (starts at Rp 50,000/month)

2. **Get API Key**
   ```php
   define('WA_API_URL', 'https://api.fonnte.com/send');
   define('WA_API_KEY', 'your_api_key_here');
   ```

3. **Update OTP Function**
   ```php
   function send_otp_whatsapp($phone, $otp) {
       $message = "🔐 Kode OTP Login PRCFI Financial\n\n";
       $message .= "Kode: *{$otp}*\n\n";
       $message .= "Berlaku 60 detik.\n";
       $message .= "Jangan share ke siapapun!";
       
       $data = [
           'target' => $phone,
           'message' => $message,
           'delay' => 0
       ];
       
       $curl = curl_init();
       curl_setopt_array($curl, [
           CURLOPT_URL => WA_API_URL,
           CURLOPT_RETURNTRANSFER => true,
           CURLOPT_POST => true,
           CURLOPT_POSTFIELDS => http_build_query($data),
           CURLOPT_HTTPHEADER => [
               'Authorization: ' . WA_API_KEY
           ]
       ]);
       
       $response = curl_exec($curl);
       curl_close($curl);
       
       return json_decode($response, true)['status'] ?? false;
   }
   ```

4. **Update Database**
   ```sql
   ALTER TABLE user ADD COLUMN whatsapp VARCHAR(20) AFTER email;
   ```

**Pros:**
- ✅ No DKIM/DMARC issues
- ✅ Instant delivery
- ✅ High open rate (99%)
- ✅ Works di Indonesia

**Cons:**
- ❌ Cost (after free tier)
- ❌ Butuh WhatsApp number dari user
- ❌ API bisa down

**Cost:**
- Fonnte: FREE 100 messages/month
- Wablas: Rp 50,000/month (~$3/month)

---

### **OPTION 3: SMS OTP (Legacy)** 📱

**Requirement:**
- SMS Gateway (Twilio, Nexmo, Zenziva)
- Credit untuk SMS

**Setup Steps:**

Similar to WhatsApp, tapi pakai SMS gateway.

**Pros:**
- ✅ Universal (no app needed)
- ✅ Reliable

**Cons:**
- ❌ Expensive (Rp 300-500/SMS)
- ❌ Slow delivery
- ❌ Carrier issues

**Cost:**
- Rp 300-500 per SMS (~$0.02-0.04/SMS)

---

### **OPTION 4: Disable OTP for Demo** ❌

**Not recommended for production!**

**For demo purposes only:**
```php
// In login.php
if (DEMO_MODE) {
    // Skip OTP, direct login
    $_SESSION['user_id'] = $user['id_user'];
    $_SESSION['logged_in'] = true;
    header('Location: dashboard_pm.php');
    exit();
}
```

---

## 📊 **COMPARISON TABLE**

| Method | Cost | Reliability | Setup Time | Production Ready |
|--------|------|-------------|------------|------------------|
| **Manual Display** | FREE | ⭐⭐⭐⭐⭐ | 0 hours | ❌ Demo only |
| **Email (Custom Domain)** | $10/year | ⭐⭐⭐⭐ | 2 hours | ✅ Yes |
| **WhatsApp OTP** | $3/month | ⭐⭐⭐⭐⭐ | 1 hour | ✅ Yes |
| **SMS OTP** | $0.03/SMS | ⭐⭐⭐ | 1 hour | ✅ Yes |
| **No OTP** | FREE | ⭐⭐⭐⭐⭐ | 0 hours | ❌ Not secure |

---

## 🚀 **RECOMMENDED APPROACH**

### **For Demo/Testing (Now):**
✅ **Use Manual OTP Display** (already implemented)

### **For Production (Future):**
✅ **Option 1: Custom Domain + Email OTP** (paling professional)
- atau -
✅ **Option 2: WhatsApp OTP** (paling populer di Indonesia)

---

## 📝 **CURRENT CONFIG**

### **Brevo SMTP Settings (Configured but Not Used):**
```php
define('SMTP_HOST', 'smtp-relay.brevo.com');
define('SMTP_PORT', 587);
define('SMTP_USER', '9964f3002@smtp-brevo.com');
define('SMTP_PASS', 'xsmtpsib-...');
define('FROM_EMAIL', 'lutfifirdaus238@gmail.com'); // ← Freemail issue!
define('FROM_NAME', 'PRCFI Financial');
```

### **Current Status:**
- ✅ SMTP connection: **Working**
- ✅ Email "sent" by Brevo: **Yes**
- ❌ Email delivered to inbox: **No** (Gmail blocks)
- ✅ Manual OTP display: **Active**

---

## 🔧 **HOW TO SWITCH TO PRODUCTION EMAIL**

When you have a custom domain:

1. **Update config.php:**
   ```php
   define('FROM_EMAIL', 'noreply@prcfi.com'); // ← Custom domain!
   ```

2. **Verify domain in Brevo:**
   - Add DNS records
   - Wait 24-48 hours
   - Verify in Brevo dashboard

3. **Enable email in login.php:**
   ```php
   // Uncomment this line:
   send_otp_email($user['email'], $otp);
   
   // Comment this line:
   // $_SESSION['demo_otp_display'] = $otp;
   ```

4. **Update verify_otp.php:**
   - Remove manual OTP display block
   - Uncomment email notification message

5. **Test:**
   ```
   http://localhost/prcf_keuangan_dashboard/test_email.php
   ```

---

## 📚 **REFERENCES**

- Brevo Docs: https://developers.brevo.com/docs
- Gmail Sender Guidelines: https://support.google.com/mail/answer/81126
- DKIM Setup: https://help.brevo.com/hc/en-us/articles/209553769
- DMARC Setup: https://help.brevo.com/hc/en-us/articles/360000991299

---

## 💡 **TIPS**

1. **For Demo:** Manual OTP display is fine
2. **For Production:** MUST use custom domain or WhatsApp
3. **Don't use Gmail/Yahoo** as sender for business emails
4. **Always test** before going live
5. **Monitor Brevo logs** for delivery status

---

**Need Help?**
- Check `SETUP_BREVO.md` for Brevo setup
- Check `FIX_BREVO_GUIDE.md` for troubleshooting
- Check `test_email.php` for email testing

---

**Status:** ✅ **Manual OTP Display Active (Demo Mode)**

**Next Step:** Setup custom domain untuk production email OTP

