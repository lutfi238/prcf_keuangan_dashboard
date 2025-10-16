# 🔧 Quick Setup untuk Demo dengan Ngrok

## Langkah Cepat (5 Menit)

### 1️⃣ Download Ngrok
```
https://ngrok.com/download
```
- Download untuk Windows
- Extract ke folder `C:\ngrok`

### 2️⃣ Setup Authtoken (Daftar dulu di ngrok.com)
```bash
cd C:\ngrok
ngrok config add-authtoken YOUR_TOKEN_HERE
```

### 3️⃣ Start XAMPP
- Start Apache ✅
- Start MySQL ✅

### 4️⃣ Jalankan Script Auto
**Double-click:**
```
start_ngrok.bat
```

Script ini akan otomatis:
- ✅ Cek XAMPP running
- ✅ Deteksi port Apache
- ✅ Start ngrok
- ✅ Tampilkan URL dan kredensial

### 5️⃣ Share URL ke Teman
```
https://xxxx.ngrok-free.app/prcf_keuangan_dashboard/
```

---

## 🎯 Untuk Demo Tanpa OTP

Karena email OTP tidak bekerja dari localhost/ngrok, ada 2 opsi:

### Opsi 1: Disable OTP (Recommended untuk Demo)

Jalankan ini di terminal:
```bash
cd C:\xampp\htdocs\prcf_keuangan_dashboard
copy login.php login_backup.php
```

Lalu edit `login.php` baris 20-32, ganti dengan:

```php
if (password_verify($password, $user['password_hash'])) {
    // Set session langsung tanpa OTP (UNTUK DEMO)
    $_SESSION['user_id'] = $user['id_user'];
    $_SESSION['user_name'] = $user['nama'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['logged_in'] = true;
    
    // Redirect ke dashboard
    switch ($user['role']) {
        case 'Project Manager':
            header('Location: dashboard_pm.php');
            break;
        case 'Staff Accountant':
            header('Location: dashboard_sa.php');
            break;
        case 'Finance Manager':
            header('Location: dashboard_fm.php');
            break;
        case 'Direktur':
            header('Location: dashboard_dir.php');
            break;
    }
    exit();
}
```

**Restore setelah demo:**
```bash
copy login_backup.php login.php
```

### Opsi 2: Tampilkan OTP di Halaman (Alternatif)

Edit `login.php`, tambahkan setelah baris yang generate OTP:

```php
// UNTUK DEMO - Tampilkan OTP
$_SESSION['demo_otp'] = $otp;
```

Lalu di `verify_otp.php`, tampilkan OTP:

```php
<?php if(isset($_SESSION['demo_otp'])): ?>
    <div class="bg-yellow-100 border border-yellow-400 p-4 rounded mb-4">
        <strong>DEMO MODE:</strong> OTP Anda: <?php echo $_SESSION['demo_otp']; ?>
    </div>
<?php endif; ?>
```

---

## 🔐 Kredensial Login

**Project Manager:**
- Email: `yadi@company.com`
- Password: `password`

**Finance Manager:**
- Email: `aam.wijaya@company.com`
- Password: `password`

**Staff Accountant:**
- Email: `ade.kurnia@company.com`
- Password: `password`

**Direktur:**
- Email: `imanul.huda@company.com`
- Password: `password`

---

## 📋 Skenario Demo

### Demo 1: Project Manager Flow
1. Login sebagai PM (`yadi@company.com`)
2. Klik "Buat Proposal"
3. Isi form proposal dan submit
4. Lihat status proposal di dashboard

### Demo 2: Full Workflow
1. **PM:** Buat proposal → status "submitted"
2. Logout, login sebagai **FM** (`aam.wijaya@company.com`)
3. **FM:** Review & approve proposal
4. Logout, login sebagai **PM**
5. **PM:** Buat laporan keuangan → "submitted"
6. Logout, login sebagai **SA** (`ade.kurnia@company.com`)
7. **SA:** Validate laporan → "verified"
8. Logout, login sebagai **FM**
9. **FM:** Approve laporan → "approved"
10. Logout, login sebagai **Dir** (`imanul.huda@company.com`)
11. **Dir:** Final approve

### Demo 3: Finance Manager Features
1. Login sebagai FM
2. Buka "Buku Bank" → Input transaksi baru
3. Buka "Buku Piutang" → Lihat piutang
4. Review proposals pending
5. Approve financial reports

---

## ⚠️ Catatan Penting

### Sebelum Demo:
- ✅ Backup database
- ✅ Test semua fitur
- ✅ Disable OTP (opsional)
- ✅ Siapkan skenario demo

### Saat Demo:
- 📱 Keep ngrok terminal terbuka
- ⏰ Ngrok free timeout 2 jam
- 🔄 URL berubah setiap restart

### Setelah Demo:
- ❌ Stop ngrok (Ctrl+C)
- ✅ Restore login.php (jika di-edit)
- ✅ Delete data testing (opsional)

---

## 🐛 Problem Solving

**Teman tidak bisa akses?**
- Cek XAMPP masih running
- Cek ngrok masih aktif
- Share full URL termasuk `/prcf_keuangan_dashboard/`

**Ngrok timeout?**
- Restart ngrok
- URL akan berubah, share URL baru

**Error saat login?**
- Pastikan database sudah diimport
- Cek kredensial: password semua user adalah `password`

---

## 🚀 Start Demo!

**3 Langkah Mudah:**

1. **Double-click** `start_ngrok.bat`
2. **Copy** URL yang muncul
3. **Share** ke teman + kredensial login

Selamat demo! 🎉

