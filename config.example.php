<?php
/**
 * ============================================================================
 * PRCFI FINANCIAL DASHBOARD - CONFIGURATION TEMPLATE
 * ============================================================================
 * 
 * INSTRUKSI SETUP:
 * ----------------
 * 1. Copy file ini dan rename jadi: config.php
 * 2. Update semua placeholder dengan nilai sebenarnya
 * 3. Jangan commit config.php ke Git (sudah ada di .gitignore)
 * 
 * COMMAND:
 * cp config.example.php config.php
 * 
 * Lalu edit config.php dengan credentials Anda
 */

// ============================================================================
// DATABASE CONFIGURATION
// ============================================================================

define('DB_HOST', 'localhost');           // Database host (biasanya localhost)
define('DB_USER', 'root');                // Database username (default: root di XAMPP)
define('DB_PASS', '');                    // Database password (kosongkan jika localhost)
define('DB_NAME', 'prcf_keuangan');      // Nama database yang sudah dibuat

// ============================================================================
// OTP CONFIGURATION - CURRENT & FUTURE
// ============================================================================

// ----------------------------------------------------------------------------
// ACTIVE: WhatsApp OTP Configuration (Fonnte API) ✅
// ----------------------------------------------------------------------------
// Status: ✅ CURRENTLY USED
// Provider: Fonnte.com
// Free Tier: 100 messages/month (No credit card required)
// Setup Guide: Baca SETUP_FONNTE.md untuk panduan lengkap
// Format nomor: 628xxxxxxxxxx (Indonesia, tanpa + atau 0)
// 
// Cara Setup:
// 1. Daftar di https://fonnte.com
// 2. Connect WhatsApp device (scan QR code)
// 3. Dapatkan API token dari dashboard
// 4. Paste token di bawah (ganti YOUR_FONNTE_TOKEN_HERE)
// 5. Restart Apache
//
define('FONNTE_API_URL', 'https://api.fonnte.com/send');
define('FONNTE_TOKEN', 'YOUR_FONNTE_TOKEN_HERE'); // ← GANTI dengan token dari Fonnte dashboard
define('WA_OTP_ENABLED', true); // Set false untuk disable WhatsApp OTP

// ----------------------------------------------------------------------------
// DEPRECATED: Email OTP Configuration (Brevo SMTP) ❌
// ----------------------------------------------------------------------------
// Status: ❌ DISABLED - Not currently used
// 
// Alasan di-disable:
// ------------------
// Gmail freemail policy (2024) memblock emails dari freemail sender via third-party SMTP.
// Email "berhasil terkirim" tapi TIDAK SAMPAI INBOX karena:
//   1. Sender pakai Gmail (@gmail.com) = freemail domain
//   2. DKIM signature "Default" = tidak ter-configured
//   3. DMARC warning = "Freemail domain is not recommended"
//   4. Gmail policy baru = otomatis block email suspicious
// 
// Cara Mengaktifkan Kembali (Future):
// ------------------------------------
// Jika ingin menggunakan Email OTP di masa depan, Anda HARUS:
//   1. Beli custom domain (misal: prcfi.com) - ~Rp150k/tahun
//   2. Setup DNS records (DKIM, DMARC, SPF)
//   3. Verify domain di Brevo dashboard
//   4. Update FROM_EMAIL ke noreply@prcfi.com (BUKAN Gmail!)
//   5. Uncomment kode di bawah & restart Apache
//   6. Baca EMAIL_OTP_GUIDE.md untuk panduan detail
//
// Config yang perlu diaktifkan (uncomment jika sudah punya custom domain):
/*
define('SMTP_HOST', 'smtp-relay.brevo.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'YOUR_BREVO_SMTP_USER');      // ← Dari Brevo dashboard
define('SMTP_PASS', 'YOUR_BREVO_SMTP_PASSWORD');  // ← Dari Brevo dashboard
define('FROM_EMAIL', 'noreply@yourdomain.com');   // ← HARUS custom domain!
define('FROM_NAME', 'PRCFI Financial');
*/

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset
$conn->set_charset("utf8mb4");

// Helper function to send OTP email - GMAIL SMTP VERSION
function send_otp_email($email, $otp) {
    try {
    $subject = "Kode OTP Login - PRCFI Financial";
    
    // HTML Email
    $message = '
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; background: #f3f4f6; padding: 20px; }
            .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
            .header { background: linear-gradient(135deg, #3B82F6 0%, #1D4ED8 100%); color: white; padding: 30px; text-align: center; }
            .content { padding: 30px; }
            .otp-box { background: #EFF6FF; border: 2px solid #3B82F6; border-radius: 8px; padding: 20px; text-align: center; margin: 20px 0; }
            .otp-code { font-size: 36px; font-weight: bold; color: #3B82F6; letter-spacing: 10px; font-family: monospace; }
            .warning { background: #FEF3C7; border-left: 4px solid #F59E0B; padding: 15px; margin: 15px 0; border-radius: 4px; }
            .footer { text-align: center; color: #6B7280; font-size: 12px; padding: 20px; background: #F9FAFB; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1 style="margin: 0;">🔐 Kode OTP Anda</h1>
                <p style="margin: 10px 0 0 0;">PRCFI Financial Management System</p>
            </div>
            <div class="content">
                <h2 style="color: #1F2937;">Halo!</h2>
                <p>Anda menerima email ini karena ada permintaan login ke sistem PRCFI Financial.</p>
                
                <div class="otp-box">
                    <p style="margin: 0 0 10px 0; font-size: 14px; color: #6B7280;">Kode OTP Anda:</p>
                    <div class="otp-code">' . $otp . '</div>
                </div>
                
                <div class="warning">
                    <strong>⏱️ Penting:</strong> Kode ini hanya berlaku selama <strong>1 menit (60 detik)</strong>.<br>
                    🔒 Jangan bagikan kode ini kepada siapapun!
                </div>
                
                <p style="color: #6B7280; font-size: 14px;">Jika Anda tidak melakukan permintaan ini, abaikan email ini atau hubungi administrator.</p>
            </div>
            <div class="footer">
                <p>Email ini dikirim secara otomatis, mohon tidak membalas.</p>
                <p>&copy; ' . date('Y') . ' PRCFI. All rights reserved.</p>
            </div>
        </div>
    </body>
    </html>
    ';
    
        // Send via Gmail SMTP
        $sent = smtp_send_email(
            SMTP_HOST,
            SMTP_PORT,
            SMTP_USER,
            SMTP_PASS,
            FROM_EMAIL,
            FROM_NAME,
            $email,
            $subject,
            $message
        );
    
    // Log untuk debugging
    if ($sent) {
            error_log("✅ OTP email sent successfully to: $email - OTP: $otp");
    } else {
            error_log("❌ Failed to send OTP email to: $email");
    }
    
    return $sent;
        
    } catch (Exception $e) {
        error_log("❌ Email error: " . $e->getMessage());
        return false;
    }
}

// SMTP Send Function - Using cURL (More Reliable for Brevo)
function smtp_send_email($smtp_host, $smtp_port, $smtp_user, $smtp_pass, $from_email, $from_name, $to_email, $subject, $html_message) {
    try {
        // Prepare email content
        $email_content = "From: " . $from_name . " <" . $from_email . ">\r\n";
        $email_content .= "To: <" . $to_email . ">\r\n";
        $email_content .= "Subject: " . $subject . "\r\n";
        $email_content .= "MIME-Version: 1.0\r\n";
        $email_content .= "Content-Type: text/html; charset=UTF-8\r\n";
        $email_content .= "\r\n";
        $email_content .= $html_message;
        
        // Create temporary file for email content
        $temp_file = tmpfile();
        fwrite($temp_file, $email_content);
        $temp_path = stream_get_meta_data($temp_file)['uri'];
        
        // Use cURL for SMTP (more reliable than fsockopen)
        $ch = curl_init();
        
        // Enable verbose logging for debugging (writes to php_error.log)
        $verbose_log = fopen('php://temp', 'rw+');
        
        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, "smtp://" . $smtp_host . ":" . $smtp_port);
        curl_setopt($ch, CURLOPT_USE_SSL, CURLUSESSL_TRY);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_USERNAME, $smtp_user);
        curl_setopt($ch, CURLOPT_PASSWORD, $smtp_pass);
        curl_setopt($ch, CURLOPT_MAIL_FROM, $from_email);
        curl_setopt($ch, CURLOPT_MAIL_RCPT, array($to_email));
        curl_setopt($ch, CURLOPT_READDATA, $temp_file);
        curl_setopt($ch, CURLOPT_UPLOAD, true);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_STDERR, $verbose_log);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        // Execute
        $result = curl_exec($ch);
        $error = curl_error($ch);
        $curl_info = curl_getinfo($ch);
        
        // Get verbose output
        rewind($verbose_log);
        $verbose_output = stream_get_contents($verbose_log);
        
        curl_close($ch);
        fclose($temp_file);
        fclose($verbose_log);
        
        // Log detailed info
        error_log("📧 SMTP Debug Info:");
        error_log("  To: $to_email");
        error_log("  SMTP: $smtp_host:$smtp_port");
        error_log("  Response Code: " . $curl_info['http_code']);
        error_log("  Total Time: " . round($curl_info['total_time'], 2) . "s");
        
        if ($error) {
            error_log("❌ SMTP cURL Error: " . $error);
            error_log("Verbose Output: " . substr($verbose_output, 0, 500));
            return false;
        }
        
        // Check if email was accepted by SMTP server
        if (strpos($verbose_output, '250') !== false) {
            error_log("✅ Email ACCEPTED by SMTP server: $to_email");
            error_log("   Note: If email doesn't arrive, check SPAM folder!");
            return true;
        } else {
            error_log("⚠️ Email sent but unclear status: $to_email");
            error_log("Verbose Output: " . substr($verbose_output, 0, 500));
            return true; // Still return true as cURL didn't error
        }
        
    } catch (Exception $e) {
        error_log("❌ SMTP Error: " . $e->getMessage());
        return false;
    }
}

// Helper function to send notification email
function send_notification_email($email, $subject, $message) {
    try {
    $html_message = '
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; background: #f3f4f6; padding: 20px; }
            .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
            .header { background: linear-gradient(135deg, #3B82F6 0%, #1D4ED8 100%); color: white; padding: 20px; text-align: center; }
            .content { padding: 30px; }
            .footer { text-align: center; color: #6B7280; font-size: 12px; padding: 20px; background: #F9FAFB; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h2 style="margin: 0;">📧 Notifikasi PRCFI</h2>
            </div>
            <div class="content">
                ' . nl2br(htmlspecialchars($message)) . '
            </div>
            <div class="footer">
                <p>Email ini dikirim secara otomatis, mohon tidak membalas.</p>
                <p>&copy; ' . date('Y') . ' PRCFI. All rights reserved.</p>
            </div>
        </div>
    </body>
    </html>
    ';
    
        // Send via Gmail SMTP
        $sent = smtp_send_email(
            SMTP_HOST,
            SMTP_PORT,
            SMTP_USER,
            SMTP_PASS,
            FROM_EMAIL,
            FROM_NAME,
            $email,
            $subject,
            $html_message
        );
        
        if ($sent) {
            error_log("✅ Notification email sent to: $email - Subject: $subject");
        } else {
            error_log("❌ Failed to send notification email to: $email");
        }
        
        return $sent;
        
    } catch (Exception $e) {
        error_log("❌ Notification email error: " . $e->getMessage());
        return false;
    }
}

// =====================================================
// WHATSAPP OTP FUNCTIONS (Fonnte API)
// =====================================================

/**
 * Send OTP via WhatsApp using Fonnte API
 * 
 * @param string $phone WhatsApp number in format 628xxxxxxxxxx
 * @param string $otp 6-digit OTP code
 * @return bool True if sent successfully, false otherwise
 */
function send_otp_whatsapp($phone, $otp) {
    try {
        // Check if WhatsApp OTP is enabled
        if (!WA_OTP_ENABLED) {
            error_log("⚠️ WhatsApp OTP is disabled");
            return false;
        }
        
        // Check if token is configured
        if (FONNTE_TOKEN === 'YOUR_FONNTE_TOKEN_HERE' || empty(FONNTE_TOKEN)) {
            error_log("⚠️ Fonnte token not configured. Please update FONNTE_TOKEN in config.php");
            return false;
        }
        
        // Format phone number (remove +, spaces, dashes)
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Convert 08xx to 628xx if needed
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }
        
        // Add 62 prefix if not present
        if (substr($phone, 0, 2) !== '62') {
            $phone = '62' . $phone;
        }
        
        // Create message
        $message = "🔐 *Kode OTP Login - PRCFI Financial*\n\n";
        $message .= "Kode OTP Anda: *{$otp}*\n\n";
        $message .= "⏱️ Berlaku selama 60 detik.\n";
        $message .= "🔒 Jangan bagikan kode ini kepada siapapun!\n\n";
        $message .= "PRCFI Financial Management System";
        
        // Prepare data for Fonnte API
        $curl = curl_init();
        
        curl_setopt_array($curl, [
            CURLOPT_URL => FONNTE_API_URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => [
                'target' => $phone,
                'message' => $message,
                'countryCode' => '62' // Indonesia
            ],
            CURLOPT_HTTPHEADER => [
                'Authorization: ' . FONNTE_TOKEN
            ],
        ]);
        
        $response = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);
        
        curl_close($curl);
        
        if ($error) {
            error_log("❌ WhatsApp OTP cURL Error: " . $error);
            return false;
        }
        
        // Parse response
        $result = json_decode($response, true);
        
        // Log for debugging
        error_log("📱 WhatsApp OTP Debug:");
        error_log("  Phone: " . $phone);
        error_log("  HTTP Code: " . $http_code);
        error_log("  Response: " . $response);
        
        // Check if successful
        if ($http_code === 200 && isset($result['status']) && $result['status'] === true) {
            error_log("✅ WhatsApp OTP sent successfully to: " . $phone . " - OTP: " . $otp);
            return true;
        } else {
            $error_msg = isset($result['reason']) ? $result['reason'] : 'Unknown error';
            error_log("❌ Failed to send WhatsApp OTP to: " . $phone . " - Error: " . $error_msg);
            return false;
        }
        
    } catch (Exception $e) {
        error_log("❌ WhatsApp OTP Exception: " . $e->getMessage());
        return false;
    }
}

/**
 * Format phone number to WhatsApp format (628xxxxxxxxxx)
 * 
 * @param string $phone Phone number in various formats
 * @return string Formatted phone number (628xxxxxxxxxx)
 */
function format_phone_number($phone) {
    // Remove all non-numeric characters
    $phone = preg_replace('/[^0-9]/', '', $phone);
    
    // Convert 08xx to 628xx
    if (substr($phone, 0, 1) === '0') {
        $phone = '62' . substr($phone, 1);
    }
    
    // Add 62 prefix if not present
    if (substr($phone, 0, 2) !== '62') {
        $phone = '62' . $phone;
    }
    
    return $phone;
}

/**
 * Validate Indonesian WhatsApp number (strict validation)
 * 
 * @param string $phone Phone number to validate
 * @return array ['valid' => bool, 'error' => string|null]
 */
function validate_whatsapp_number($phone) {
    // Remove all non-numeric characters
    $phone_clean = preg_replace('/[^0-9]/', '', $phone);
    
    // Check if empty
    if (empty($phone_clean)) {
        return ['valid' => false, 'error' => 'Nomor WhatsApp tidak boleh kosong'];
    }
    
    // Check length (min 10, max 13)
    if (strlen($phone_clean) < 10 || strlen($phone_clean) > 13) {
        return ['valid' => false, 'error' => 'Nomor WhatsApp harus 10-13 digit'];
    }
    
    // Must start with 0 or 62
    if (!preg_match('/^(0|62)/', $phone_clean)) {
        return ['valid' => false, 'error' => 'Nomor harus dimulai dengan 0 atau 62'];
    }
    
    // If starts with 0, must be 08xx (Indonesian mobile)
    if (substr($phone_clean, 0, 1) === '0') {
        if (substr($phone_clean, 0, 2) !== '08') {
            return ['valid' => false, 'error' => 'Nomor HP harus dimulai dengan 08 (contoh: 081234567890)'];
        }
        // Check length for 08xx format (should be 11-13 digits)
        if (strlen($phone_clean) < 11 || strlen($phone_clean) > 13) {
            return ['valid' => false, 'error' => 'Nomor dengan awalan 08 harus 11-13 digit'];
        }
    }
    
    // If starts with 62, must be 628xx (Indonesian mobile international)
    if (substr($phone_clean, 0, 2) === '62') {
        if (substr($phone_clean, 0, 3) !== '628') {
            return ['valid' => false, 'error' => 'Nomor internasional harus dimulai dengan 628 (contoh: 6281234567890)'];
        }
        // Check length for 628xx format (should be 12-14 digits)
        if (strlen($phone_clean) < 12 || strlen($phone_clean) > 14) {
            return ['valid' => false, 'error' => 'Nomor dengan awalan 628 harus 12-14 digit'];
        }
    }
    
    // Validate Indonesian operator prefixes (after 08 or 628)
    $operator_prefix = substr($phone_clean, 0, 1) === '0' 
        ? substr($phone_clean, 2, 2)  // Get 2 digits after '08'
        : substr($phone_clean, 3, 2); // Get 2 digits after '628'
    
    // Valid Indonesian operator prefixes
    $valid_operators = ['11', '12', '13', '14', '15', '16', '17', '18', '19', // Telkomsel
                        '21', '22', '23',                                      // Indosat
                        '31', '32', '33', '38',                                // XL
                        '55', '56', '57', '58', '59',                          // Indosat (IM3)
                        '77', '78',                                            // XL
                        '81', '82', '83', '84', '85', '88',                    // Smartfren, Axis
                        '95', '96', '97', '98', '99'];                         // Three
    
    if (!in_array($operator_prefix, $valid_operators)) {
        return ['valid' => false, 'error' => 'Nomor operator tidak valid. Gunakan nomor dari provider Indonesia (Telkomsel, Indosat, XL, Three, Axis, Smartfren)'];
    }
    
    return ['valid' => true, 'error' => null];
}

/**
 * Validate Indonesian phone number (backward compatibility)
 * 
 * @param string $phone Phone number to validate
 * @return bool True if valid, false otherwise
 */
function is_valid_phone_number($phone) {
    $result = validate_whatsapp_number($phone);
    return $result['valid'];
}
?>

