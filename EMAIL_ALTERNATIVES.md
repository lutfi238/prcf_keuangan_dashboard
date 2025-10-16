# 📧 Alternatif Email Gratis untuk Ngrok/Hosting

## 🎯 Masalah:
- Gmail SMTP sering diblokir saat hosting eksternal (ngrok)
- Port 587/465 kadang di-block firewall
- Gmail membutuhkan 2FA dan App Password yang ribet

---

## ✅ SOLUSI TERBAIK (Free & Reliable):

### **1. BREVO (Sendinblue) - RECOMMENDED! ⭐⭐⭐⭐⭐**

**Kenapa Brevo?**
- ✅ **300 email/hari GRATIS**
- ✅ **Tidak ada expired** (free forever)
- ✅ **Mudah setup** (5 menit)
- ✅ **Reliable** untuk production
- ✅ **No credit card needed**
- ✅ **Work 100% dengan ngrok**

**Free Tier:**
```
✅ 300 emails per day (cukup banget untuk demo!)
✅ Unlimited contacts
✅ SMTP & API
✅ Real-time statistics
✅ Email templates
```

**Website:** https://www.brevo.com/

---

### **2. Resend - Modern & Simple ⭐⭐⭐⭐⭐**

**Kenapa Resend?**
- ✅ **100 email/hari GRATIS**
- ✅ **API super simple** (3 baris code)
- ✅ **Modern UI**
- ✅ **No credit card**
- ✅ **Perfect untuk dev**

**Free Tier:**
```
✅ 100 emails per day
✅ 1 domain
✅ Beautiful email tracking
✅ Analytics
```

**Website:** https://resend.com/

---

### **3. Mailgun**

**Free Tier:**
```
✅ 5,000 emails/month (3 bulan pertama)
✅ Setelah itu: 1,000 emails/month
✅ SMTP & API
```

**Website:** https://www.mailgun.com/

---

### **4. SendGrid**

**Free Tier:**
```
✅ 100 emails per day
✅ Forever free
✅ Industry standard
```

**Cons:** Setup agak ribet, perlu verifikasi

**Website:** https://sendgrid.com/

---

### **5. SMTP2GO**

**Free Tier:**
```
✅ 1,000 emails per month
✅ No credit card
✅ Easy setup
```

**Website:** https://www.smtp2go.com/

---

## 🚀 IMPLEMENTASI BREVO (Paling Mudah!)

### **Step 1: Daftar Brevo (2 menit)**

1. Buka: **https://app.brevo.com/account/register**
2. Isi:
   - Email Anda
   - Password
   - Nama
3. Klik "Create my account"
4. Verify email (cek inbox)
5. Login

### **Step 2: Get SMTP Credentials (1 menit)**

1. Dashboard → **SMTP & API**
2. Klik **SMTP** tab
3. Copy credentials:
   ```
   SMTP Server: smtp-relay.brevo.com
   Port: 587
   Login: (email Anda)
   SMTP Password: (klik "Create a new SMTP key")
   ```

### **Step 3: Update config.php (1 menit)**

Buka `config.php`, ganti bagian email:

```php
// Ganti Gmail SMTP dengan Brevo SMTP
define('SMTP_HOST', 'smtp-relay.brevo.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your-brevo-email@gmail.com'); // Email yg dipake daftar Brevo
define('SMTP_PASS', 'your-smtp-key-here'); // SMTP key dari step 2
define('FROM_EMAIL', 'your-brevo-email@gmail.com');
define('FROM_NAME', 'PRCFI Financial');
```

### **Step 4: Test! (1 menit)**

```
1. Login ke aplikasi
2. Email OTP akan terkirim via Brevo
3. Cek inbox!
4. ✅ BERHASIL!
```

---

## 💡 ALTERNATIF: Pakai Resend (Lebih Modern)

### **Setup Resend:**

1. Daftar: https://resend.com/
2. Get API Key
3. Install via cURL (no library!)

### **Code untuk Resend:**

Tambahkan di `config.php`:

```php
// Fungsi kirim email via Resend API
function send_otp_via_resend($to_email, $otp, $html_content) {
    $api_key = 'YOUR_RESEND_API_KEY'; // Get dari dashboard Resend
    
    $data = [
        'from' => 'PRCFI Financial <onboarding@resend.dev>',
        'to' => [$to_email],
        'subject' => 'Kode OTP Login - PRCFI Financial',
        'html' => $html_content
    ];
    
    $ch = curl_init('https://api.resend.com/emails');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $api_key,
        'Content-Type: application/json'
    ]);
    
    $result = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return $http_code == 200;
}
```

---

## 🎯 PERBANDINGAN:

| Service | Free Limit | Setup | Work di Ngrok? | Recommended |
|---------|-----------|-------|----------------|-------------|
| **Brevo** | 300/day | ⭐⭐⭐⭐⭐ | ✅ YES | ✅ **BEST** |
| **Resend** | 100/day | ⭐⭐⭐⭐⭐ | ✅ YES | ✅ Great |
| **Mailgun** | 1000/month | ⭐⭐⭐⭐ | ✅ YES | ⚠️ OK |
| **SendGrid** | 100/day | ⭐⭐⭐ | ✅ YES | ⚠️ OK |
| **Gmail SMTP** | Unlimited | ⭐⭐ | ❌ **BLOCKED** | ❌ NO |

---

## 📱 BONUS: WhatsApp OTP (Alternatif Non-Email)

### **Fonnte.com - WhatsApp Gateway**

**Free Tier:**
```
✅ 100 messages/month gratis
✅ Setelah itu: Rp 200/message
✅ WhatsApp Business API
✅ Sangat reliable
```

**Setup:**

1. Daftar: https://fonnte.com/
2. Connect WhatsApp Business
3. Get API Token

**Code:**

```php
function send_otp_via_whatsapp($phone, $otp) {
    $token = 'YOUR_FONNTE_TOKEN';
    
    $data = [
        'target' => $phone, // Format: 628123456789
        'message' => "🔐 *PRCFI Financial*\n\nKode OTP Anda: *{$otp}*\n\nBerlaku 60 detik.\nJangan bagikan kode ini!",
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.fonnte.com/send');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: ' . $token
    ]);
    
    $result = curl_exec($ch);
    curl_close($ch);
    
    return $result;
}
```

Ganti di `login.php`: tambah field no_HP dan kirim via WhatsApp!

---

## ✅ REKOMENDASI SAYA:

### **Untuk Demo via Ngrok:**
**Pakai BREVO** - Alasan:
- ✅ Setup paling mudah (5 menit)
- ✅ 300 email/day (cukup banget)
- ✅ 100% work di ngrok
- ✅ Free forever
- ✅ No credit card
- ✅ Reliable

### **Untuk Production:**
**Brevo atau Resend**
- Brevo: Limit lebih besar (300/day)
- Resend: Lebih modern, API simple

### **Untuk Indonesia:**
**WhatsApp via Fonnte**
- User lebih familiar dengan WhatsApp
- Delivery rate 99%
- Murah (Rp 200/message)

---

## 🎬 QUICK START - BREVO (Copy-Paste Ready!)

**1. Daftar Brevo:**
```
https://app.brevo.com/account/register
```

**2. Get SMTP Key:**
```
Dashboard → SMTP & API → Create SMTP key
```

**3. Update config.php:**
```php
define('SMTP_HOST', 'smtp-relay.brevo.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your-email@gmail.com'); // Email Brevo Anda
define('SMTP_PASS', 'your-smtp-key'); // SMTP key dari dashboard
```

**4. Test:**
```
Login → Email OTP terkirim!
```

---

## 💬 Mau yang mana?

1. **Brevo** - Mudah, reliable ✅ (Rekomendasi!)
2. **Resend** - Modern, API simple ✅
3. **WhatsApp** - Via Fonnte 📱

Kasih tahu mana yang mau dipakai, nanti saya setup full di code! 🚀
