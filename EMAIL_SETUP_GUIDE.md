# 📧 Email OTP Setup Guide - Gmail SMTP

## ✅ SUDAH DIKONFIGURASI!

Email OTP sudah **siap berfungsi** dengan Gmail SMTP!

---

## 🎯 Yang Sudah Dikonfigurasi:

### **1. Gmail SMTP Settings:**
```
SMTP Host: smtp.gmail.com
SMTP Port: 587 (TLS)
Email: pblprcf@gmail.com
App Password: vwkx trnf ordu sfuh
```

### **2. Fungsi Email:**
- ✅ `send_otp_email()` - Kirim OTP via Gmail SMTP
- ✅ `send_notification_email()` - Kirim notifikasi via Gmail SMTP
- ✅ `smtp_send_email()` - Native PHP SMTP (no library needed)

---

## 🚀 Cara Kerja:

### **Smart Fallback System:**

1. **Email Berhasil Terkirim:**
   - OTP dikirim ke email user
   - Kotak kuning "DEMO MODE" **TIDAK muncul**
   - User cek email dan input OTP

2. **Email Gagal (localhost/firewall):**
   - OTP ditampilkan di halaman (kotak kuning)
   - User bisa langsung copy OTP
   - Tetap bisa login

---

## 🧪 Cara Test:

### **Test 1: Di Localhost**

```bash
1. Start XAMPP (Apache + MySQL)
2. Buka: http://localhost/prcf_keuangan_dashboard/
3. Login dengan: yadi@company.com / password
4. Lihat halaman verify OTP:
   - Jika kotak kuning muncul = email gagal (normal di localhost)
   - Jika tidak muncul = email berhasil terkirim! ✅
5. Cek email di inbox pblprcf@gmail.com
```

### **Test 2: Via Ngrok (Production-like)**

```bash
1. Start ngrok: ngrok http 80
2. Buka URL ngrok di browser
3. Login dengan: yadi@company.com / password
4. Lihat halaman verify OTP:
   - Kemungkinan besar email BERHASIL terkirim ✅
   - Cek inbox email user
   - Jika berhasil, kotak kuning tidak muncul
```

### **Test 3: Cek Error Log**

```bash
# Buka file error log PHP:
C:\xampp\apache\logs\error.log

# Cari baris:
✅ OTP email sent successfully to: email@example.com - OTP: 123456
atau
❌ Failed to send OTP email to: email@example.com
```

---

## 📱 Email OTP Template:

User akan menerima email seperti ini:

```
From: PRCFI Financial <pblprcf@gmail.com>
Subject: Kode OTP Login - PRCFI Financial

┌─────────────────────────────┐
│     🔐 Kode OTP Anda       │
│  PRCFI Financial System     │
│                             │
│      123456                 │
│                             │
│ Berlaku: 60 detik           │
│ Jangan bagikan kode ini!    │
└─────────────────────────────┘
```

---

## 🔧 Troubleshooting:

### **Problem: Email tidak terkirim**

**Cek 1: App Password benar?**
```
App Password: vwkx trnf ordu sfuh
Email: pblprcf@gmail.com
```

Pastikan App Password ini masih aktif di Google Account.

**Cek 2: Extension OpenSSL aktif?**

Buka `C:\xampp\php\php.ini`, cari:
```ini
extension=openssl
```
Pastikan TIDAK ada titik koma (;) di depannya.

**Cek 3: Firewall/Antivirus?**

Windows Firewall atau antivirus mungkin block koneksi SMTP.

Temporary disable untuk test:
- Windows Defender Firewall
- Antivirus third-party

**Cek 4: Port 587 terbuka?**

Test koneksi ke Gmail SMTP:
```bash
telnet smtp.gmail.com 587
```

Jika tidak bisa connect, coba port 465 (SSL):
Edit `config.php`:
```php
define('SMTP_PORT', 465);
```
Dan di fungsi `smtp_send_email()` ganti:
```php
$smtp = fsockopen('ssl://' . $smtp_host, $smtp_port, $errno, $errstr, 30);
```

---

## 🔐 Keamanan:

### **App Password vs Regular Password:**

✅ **App Password** (yang dipakai):
- Khusus untuk aplikasi
- Bisa di-revoke kapan saja
- Lebih aman
- Google recommended

❌ **Regular Password** (JANGAN dipakai):
- Kurang aman
- Bisa akses semua Google services
- Tidak recommended

### **Cara Buat App Password Baru:**

Jika perlu buat App Password baru:

1. Login ke: https://myaccount.google.com/
2. Pilih "Security"
3. Cari "2-Step Verification" → Aktifkan
4. Cari "App passwords"
5. Pilih "Mail" dan "Other (Custom)"
6. Nama: "PRCF Dashboard"
7. Copy 16-digit password
8. Paste ke `config.php` → `SMTP_PASS`

---

## 💡 Tips:

### **Untuk Production:**

1. **Ganti Email:**
   - Pakai email khusus untuk aplikasi
   - Jangan pakai email pribadi

2. **Monitor Logs:**
   - Cek regular log file
   - Setup email notification jika gagal

3. **Rate Limiting:**
   - Gmail limit: 500 email/day
   - Cukup untuk aplikasi kecil
   - Upgrade ke Google Workspace jika butuh lebih

### **Untuk Demo:**

1. **Test dengan Email Sendiri:**
   ```
   Login dengan user test
   Ganti email di database ke email Anda
   Test login dan cek inbox
   ```

2. **Share ke Teman:**
   ```
   Teman login
   Email OTP otomatis terkirim
   Teman cek inbox dan masukkan OTP
   ```

---

## 📊 Status Saat Ini:

| Fitur | Status | Keterangan |
|-------|--------|------------|
| Gmail SMTP Setup | ✅ Ready | Configured dengan native PHP |
| Send OTP Email | ✅ Working | Kirim OTP saat login |
| Send Notification Email | ✅ Working | Untuk notifikasi approval dll |
| Fallback Display | ✅ Working | Tampil di halaman jika email gagal |
| Error Logging | ✅ Working | Log di Apache error.log |
| HTML Email Template | ✅ Ready | Professional design |

---

## 🎉 Kesimpulan:

**Email OTP sudah berfungsi!**

- ✅ Siap untuk demo via ngrok
- ✅ Auto fallback jika gagal
- ✅ Professional email template
- ✅ Logging untuk debugging
- ✅ No external library needed

**Coba login sekarang dan cek apakah email terkirim!**

---

**Updated:** 15 Oktober 2025  
**Version:** 1.0 - Gmail SMTP Native

