# 🔧 Maintenance Mode Guide

**Last Updated:** October 16, 2025

---

## 📋 **OVERVIEW**

Maintenance Mode memungkinkan Anda untuk:
- 🔒 **Menonaktifkan website** sementara untuk maintenance
- 🎨 **Menampilkan halaman professional** dengan animation
- 👨‍💼 **Whitelist IP admin** untuk tetap bisa akses
- ⏰ **Schedule maintenance** otomatis

---

## 🚀 **QUICK START**

### **1. Enable Maintenance Mode:**

Edit file `maintenance_config.php`:
```php
define('MAINTENANCE_MODE', true); // ← Ubah jadi true
```

**Restart Apache** → Website langsung maintenance!

### **2. Disable (Kembali Normal):**

```php
define('MAINTENANCE_MODE', false); // ← Ubah jadi false
```

**Restart Apache** → Website kembali normal!

---

## 📝 **CARA PENGGUNAAN LENGKAP**

### **Step 1: Edit maintenance_config.php**

**File location:** `c:\xampp\htdocs\prcf_keuangan_dashboard\maintenance_config.php`

```php
// ============================================================================
// MAINTENANCE MODE SETTING
// ============================================================================

// Set TRUE untuk enable maintenance mode, FALSE untuk disable
define('MAINTENANCE_MODE', true); // ← UBAH INI

// ============================================================================
// IP WHITELIST (Optional)
// ============================================================================
// Admin dengan IP ini tetap bisa akses website

$MAINTENANCE_WHITELIST_IPS = [
    '127.0.0.1',        // Localhost (always included)
    '::1',              // Localhost IPv6
    '192.168.1.100',    // ← Tambahkan IP admin di sini
];
```

### **Step 2: Update Pages yang Perlu Maintenance Check**

**Tambahkan di TOP setiap file PHP utama:**

```php
<?php
require_once 'maintenance_config.php';
check_maintenance(); // Redirect to maintenance if active

// Rest of your code...
?>
```

**Files yang perlu diupdate:**
- `login.php`
- `register.php`
- `dashboard_pm.php`
- `dashboard_sa.php`
- `dashboard_fm.php`
- `dashboard_dir.php`
- dll.

### **Step 3: Restart Apache**

1. Buka **XAMPP Control Panel**
2. Click **Stop** on Apache
3. Click **Start** on Apache

---

## 🎨 **CUSTOMIZE MAINTENANCE PAGE**

### **Edit maintenance.php:**

**File location:** `c:\xampp\htdocs\prcf_keuangan_dashboard\maintenance.php`

#### **Change Title:**
```php
<h2 class="text-3xl font-bold text-gray-800 mb-4">
    🔧 Sedang Dalam Perbaikan  <!-- ← Edit ini -->
</h2>
```

#### **Change Message:**
```php
<p class="text-gray-600 text-lg mb-6 leading-relaxed">
    Kami sedang melakukan maintenance... <!-- ← Edit ini -->
</p>
```

#### **Change Estimate:**
```php
<span class="px-4 py-2 bg-blue-100 text-blue-800 rounded-full text-sm font-semibold">
    ⏱️ Estimasi: 1-2 Jam  <!-- ← Edit ini -->
</span>
```

#### **Change Animation:**
```javascript
<script>
    const animation = lottie.loadAnimation({
        container: document.getElementById('lottie-animation'),
        renderer: 'svg',
        loop: true,
        autoplay: true,
        path: 'assets/fixing/Maintenance web.json'  // ← Ganti file animation
    });
</script>
```

---

## 🎯 **USE CASES**

### **Scenario 1: Scheduled Maintenance**

**Friday night 00:00 - 02:00:**

```php
define('MAINTENANCE_MODE', true);
define('MAINTENANCE_START', '2025-10-17 00:00:00');
define('MAINTENANCE_END', '2025-10-17 02:00:00');
```

Website otomatis maintenance di jam tersebut!

---

### **Scenario 2: Emergency Maintenance**

**Ada bug critical, perlu fix ASAP:**

1. Set `MAINTENANCE_MODE = true`
2. Restart Apache
3. Fix bug dengan tenang
4. Test di localhost (IP whitelisted)
5. Set `MAINTENANCE_MODE = false`
6. Restart Apache

---

### **Scenario 3: Update Database**

**Perlu update database schema:**

1. Enable maintenance
2. Backup database
3. Run migration SQL
4. Test di localhost
5. Disable maintenance

---

## 👨‍💼 **IP WHITELIST (Admin Access)**

### **Cara Menambahkan IP Admin:**

#### **Step 1: Cek IP Address Kamu**

**Method 1 - PHP:**
Create file `check_ip.php`:
```php
<?php
echo "Your IP: " . $_SERVER['REMOTE_ADDR'];
?>
```

**Method 2 - Online:**
- Buka: https://whatismyipaddress.com
- Copy IP address yang muncul

#### **Step 2: Add to Whitelist**

Edit `maintenance_config.php`:
```php
$MAINTENANCE_WHITELIST_IPS = [
    '127.0.0.1',        // Localhost
    '::1',              // Localhost IPv6
    '203.0.113.45',     // ← Tambahkan IP admin 1
    '198.51.100.12',    // ← Tambahkan IP admin 2
];
```

#### **Step 3: Test**

1. Enable maintenance
2. Access website dari IP admin
3. Should bypass maintenance page!

---

## 🎨 **ANIMATIONS**

### **Current Animations:**

1. **Maintenance web.json**
   - Location: `assets/fixing/Maintenance web.json`
   - Use for: Website maintenance
   - Style: Professional, clean

2. **Under Construction 1.json**
   - Location: `assets/fixing/Under Construction 1.json`
   - Use for: Features under development
   - Style: Modern, colorful

### **Add New Animation:**

1. **Download Lottie animation:**
   - https://lottiefiles.com
   - Format: JSON

2. **Save to folder:**
   ```
   assets/fixing/new-animation.json
   ```

3. **Update maintenance.php:**
   ```javascript
   path: 'assets/fixing/new-animation.json'
   ```

---

## 🔍 **TROUBLESHOOTING**

### **Problem 1: Maintenance page tidak muncul**

**Cek:**
1. ✅ `MAINTENANCE_MODE = true`?
2. ✅ `check_maintenance()` dipanggil di file PHP?
3. ✅ Apache sudah di-restart?

**Solution:**
```php
// Di top file PHP:
require_once 'maintenance_config.php';
check_maintenance();
```

---

### **Problem 2: Admin juga kena maintenance**

**Cek:**
1. ✅ IP sudah masuk whitelist?
2. ✅ IP benar? (cek dengan `check_ip.php`)

**Solution:**
```php
// Tambahkan IP ke whitelist:
$MAINTENANCE_WHITELIST_IPS = [
    '127.0.0.1',
    'YOUR_IP_HERE',  // ← Pastikan benar
];
```

---

### **Problem 3: Animation tidak muncul**

**Cek:**
1. ✅ File JSON ada di folder `assets/fixing/`?
2. ✅ Path benar di `maintenance.php`?
3. ✅ Browser console ada error?

**Solution:**
```javascript
// Cek path:
path: 'assets/fixing/Maintenance web.json'  // Pastikan benar
```

---

### **Problem 4: Infinite redirect**

**Cek:**
```php
// Pastikan ada ini di maintenance.php:
if (basename($_SERVER['PHP_SELF']) === 'maintenance.php') {
    return; // Skip maintenance check
}
```

---

## 📚 **FILES STRUCTURE**

```
prcf_keuangan_dashboard/
├── maintenance_config.php       ← Configuration
├── maintenance.php              ← Maintenance page (beautiful)
├── under_construction.php       ← Under construction page
├── assets/
│   └── fixing/
│       ├── Maintenance web.json        ← Animation 1
│       └── Under Construction 1.json   ← Animation 2
├── login.php                    ← Add check_maintenance()
├── dashboard_pm.php             ← Add check_maintenance()
└── ...
```

---

## 🎯 **BEST PRACTICES**

### **Before Maintenance:**
1. ✅ **Notify users** - Email/WhatsApp 24h sebelumnya
2. ✅ **Schedule off-peak** - Malam atau weekend
3. ✅ **Backup database** - Before any changes
4. ✅ **Test first** - Test di localhost dulu
5. ✅ **Prepare rollback** - Backup lengkap

### **During Maintenance:**
1. ✅ **Monitor** - Check error logs
2. ✅ **Test thoroughly** - Test semua fitur
3. ✅ **Document** - Catat apa yang diubah
4. ✅ **Keep backup** - Jangan hapus backup

### **After Maintenance:**
1. ✅ **Disable maintenance mode** - ASAP
2. ✅ **Monitor** - 15-30 menit setelah
3. ✅ **Notify users** - "We're back!"
4. ✅ **Document** - Update changelog

---

## 🔄 **ALTERNATIVE: Toggle via Admin Panel**

### **Future Feature:** Admin dapat enable/disable maintenance dari dashboard

**Implementation:**
```php
// admin_settings.php
if (isset($_POST['toggle_maintenance'])) {
    $new_status = $_POST['maintenance'] === 'true' ? 'true' : 'false';
    
    // Update file
    $config = file_get_contents('maintenance_config.php');
    $config = preg_replace(
        "/define\('MAINTENANCE_MODE', (true|false)\);/",
        "define('MAINTENANCE_MODE', $new_status);",
        $config
    );
    file_put_contents('maintenance_config.php', $config);
    
    // Success message
    echo "Maintenance mode updated!";
}
```

**UI:**
```html
<form method="POST">
    <label>
        <input type="checkbox" name="maintenance" value="true">
        Enable Maintenance Mode
    </label>
    <button type="submit" name="toggle_maintenance">Save</button>
</form>
```

---

## 📊 **SUMMARY**

### **Quick Commands:**

**Enable Maintenance:**
```
1. Edit maintenance_config.php
2. Set MAINTENANCE_MODE = true
3. Restart Apache
```

**Disable Maintenance:**
```
1. Edit maintenance_config.php
2. Set MAINTENANCE_MODE = false
3. Restart Apache
```

**Whitelist IP:**
```
Add to $MAINTENANCE_WHITELIST_IPS array in maintenance_config.php
```

---

## 🎉 **DONE!**

Website sekarang punya professional maintenance mode dengan:
- ✅ Beautiful animation
- ✅ Clear messaging
- ✅ Admin whitelist
- ✅ Easy toggle

**Maintenance Made Easy!** 💪

---

**Need Help?**
- Check `maintenance_config.php` for settings
- Check `maintenance.php` for customization
- Check browser console for errors

**Last Updated:** October 16, 2025

