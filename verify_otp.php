<?php
session_start();
require_once 'config.php';
require_once 'maintenance_config.php';

// Check maintenance mode
check_maintenance();

if (!isset($_SESSION['pending_login'])) {
    header('Location: login.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['verify_otp'])) {
        $entered_otp = $_POST['otp'];
        $current_time = time();
        
        // Check if OTP expired (60 seconds)
        if ($current_time - $_SESSION['otp_time'] > 60) {
            $error = 'Kode OTP telah kadaluarsa';
        } elseif ($entered_otp == $_SESSION['otp']) {
            // OTP correct
            $user_id = $_SESSION['user_id'];
            
            // Get user data
            $stmt = $conn->prepare("SELECT * FROM user WHERE id_user = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
            
            // Set session
            $_SESSION['user_id'] = $user['id_user'];
            $_SESSION['user_name'] = $user['nama'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['logged_in'] = true;
            
            // Set device cookie for 30 days
            setcookie('device_verified', md5($user_id . $_SERVER['HTTP_USER_AGENT']), time() + (86400 * 30), '/');
            
            unset($_SESSION['otp']);
            unset($_SESSION['otp_time']);
            unset($_SESSION['pending_login']);
            unset($_SESSION['demo_otp_display']);
            
            // Redirect based on role
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
        } else {
            $error = 'Kode OTP salah, ulangi lagi';
        }
    } elseif (isset($_POST['resend_otp'])) {
        // Generate new OTP
        $otp = rand(100000, 999999);
        $_SESSION['otp'] = $otp;
        $_SESSION['otp_time'] = time();
        
        // Get user info
        $user_id = $_SESSION['user_id'];
        $stmt = $conn->prepare("SELECT no_HP FROM user WHERE id_user = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        
        // Check if user has phone number (no_HP)
        if (!empty($user['no_HP'])) {
            // Resend OTP via WhatsApp using no_HP
            $wa_sent = send_otp_whatsapp($user['no_HP'], $otp);
            
            if ($wa_sent) {
                $_SESSION['otp_sent_via'] = 'whatsapp';
                $_SESSION['otp_phone_masked'] = substr($user['no_HP'], 0, 4) . 'xxx' . substr($user['no_HP'], -3);
                $success = 'Kode OTP baru telah dikirim via WhatsApp';
            } else {
                // Fallback to manual display
                $_SESSION['demo_otp_display'] = $otp;
                $_SESSION['otp_sent_via'] = 'manual';
                $success = 'Kode OTP baru ditampilkan di halaman (WhatsApp gagal)';
            }
        } else {
            // No phone number, use manual display
            $_SESSION['demo_otp_display'] = $otp;
            $_SESSION['otp_sent_via'] = 'manual';
            $success = 'Kode OTP baru ditampilkan di halaman';
        }
    }
}

$time_left = 60 - (time() - $_SESSION['otp_time']);
if ($time_left < 0) $time_left = 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi OTP - PRCFI Financial Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Verifikasi OTP</h1>
            <p class="text-gray-600">Masukkan kode OTP yang telah dikirim ke email Anda</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['otp_sent_via'])): ?>
            <?php if ($_SESSION['otp_sent_via'] === 'whatsapp'): ?>
                <!-- WhatsApp OTP Sent -->
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 p-4 mb-4 rounded-r-lg shadow-md">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-green-500" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm text-green-800 font-semibold mb-2">
                                💬 Kode OTP telah dikirim via WhatsApp
                            </p>
                            <p class="text-xs text-green-700 mb-2">
                                Cek WhatsApp Anda di nomor: <strong><?php echo $_SESSION['otp_phone_masked']; ?></strong>
                            </p>
                            <p class="text-xs text-green-600">
                                <i class="fas fa-clock mr-1"></i> Kode berlaku selama 60 detik
                            </p>
                            <p class="text-xs text-green-600 mt-2">
                                💡 Jika tidak menerima, klik "Kirim Ulang Kode" di bawah
                            </p>
                        </div>
                    </div>
                </div>
            <?php elseif ($_SESSION['otp_sent_via'] === 'manual' && isset($_SESSION['demo_otp_display'])): ?>
                <!-- Manual OTP Display (Fallback) -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-500 p-4 mb-4 rounded-r-lg shadow-md">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm text-blue-800 font-semibold mb-3">
                                🔐 Kode OTP Anda
                            </p>
                            <div class="bg-white border-2 border-blue-400 rounded-lg p-5 shadow-sm">
                                <p class="text-xs text-gray-600 mb-2 text-center">Masukkan kode berikut:</p>
                                <p class="text-5xl font-bold text-blue-600 tracking-widest text-center font-mono">
                                    <?php echo $_SESSION['demo_otp_display']; ?>
                                </p>
                            </div>
                            <p class="mt-3 text-xs text-blue-700 text-center">
                                <i class="fas fa-clock mr-1"></i> 
                                Kode berlaku selama 60 detik
                            </p>
                            <p class="mt-2 text-xs text-orange-600 text-center">
                                ⚠️ Mode fallback - WhatsApp tidak tersedia
                            </p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-gray-700 text-sm font-medium mb-2">Kode OTP</label>
                <input type="text" name="otp" required maxlength="6" pattern="[0-9]{6}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 text-center text-2xl tracking-widest"
                    placeholder="000000" autofocus>
            </div>

            <div class="text-center text-sm text-gray-600">
                Kode berlaku: <span id="timer" class="font-bold text-blue-600"><?php echo $time_left; ?></span> detik
            </div>

            <button type="submit" name="verify_otp" 
                class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600 transition duration-200 font-medium">
                <i class="fas fa-check-circle mr-2"></i> Verifikasi
            </button>
        </form>

        <!-- Form terpisah untuk kirim ulang OTP (tidak perlu validasi) -->
        <form method="POST" class="mt-4">
            <button type="submit" name="resend_otp" id="resendBtn"
                class="w-full bg-gray-200 text-gray-700 py-2 rounded-lg hover:bg-gray-300 transition duration-200 font-medium">
                <i class="fas fa-redo mr-2"></i> Kirim Ulang Kode
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="login.php" class="text-blue-500 hover:text-blue-700 text-sm font-medium">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Login
            </a>
        </div>
    </div>

    <script>
        let timeLeft = <?php echo $time_left; ?>;
        const timerElement = document.getElementById('timer');
        const resendBtn = document.getElementById('resendBtn');

        const countdown = setInterval(() => {
            if (timeLeft <= 0) {
                clearInterval(countdown);
                timerElement.textContent = '0';
                timerElement.parentElement.innerHTML = '<span class="text-red-600 font-bold">Kode telah kadaluarsa</span>';
            } else {
                timeLeft--;
                timerElement.textContent = timeLeft;
            }
        }, 1000);
    </script>
</body>
</html>