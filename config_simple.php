<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'prcf_keuangan');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset
$conn->set_charset("utf8mb4");

// Helper function to send OTP email - SIMPLE VERSION (menggunakan mail() PHP)
function send_otp_email($email, $otp) {
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
    
    // Plain text alternative
    $plain_message = "PRCFI Financial - Kode OTP Login\n\n";
    $plain_message .= "Kode OTP Anda: $otp\n\n";
    $plain_message .= "Kode ini berlaku selama 1 menit (60 detik).\n";
    $plain_message .= "JANGAN bagikan kode ini kepada siapapun!\n\n";
    $plain_message .= "Jika Anda tidak melakukan permintaan ini, abaikan email ini.\n\n";
    $plain_message .= "---\n";
    $plain_message .= "PRCFI Financial Management System";
    
    // Headers untuk HTML email
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "From: PRCFI Financial <noreply@prcfi.com>\r\n";
    $headers .= "Reply-To: no-reply@prcfi.com\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    
    // Kirim email
    $sent = mail($email, $subject, $message, $headers);
    
    // Log untuk debugging
    if ($sent) {
        error_log("OTP email sent successfully to: $email - OTP: $otp");
    } else {
        error_log("Failed to send OTP email to: $email");
    }
    
    return $sent;
}

// Helper function to send notification email
function send_notification_email($email, $subject, $message) {
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
    
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "From: PRCFI Financial <noreply@prcfi.com>\r\n";
    $headers .= "Reply-To: no-reply@prcfi.com\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    
    $sent = mail($email, $subject, $html_message, $headers);
    
    if ($sent) {
        error_log("Notification email sent to: $email - Subject: $subject");
    } else {
        error_log("Failed to send notification email to: $email");
    }
    
    return $sent;
}
?>