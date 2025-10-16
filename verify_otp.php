<?php
session_start();
require_once 'config.php';

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
        
        // Save OTP untuk ditampilkan
        $_SESSION['demo_otp_display'] = $otp;
        
        // Email OTP akan diimplementasikan nanti
        // $user_id = $_SESSION['user_id'];
        // $stmt = $conn->prepare("SELECT email FROM user WHERE id_user = ?");
        // $stmt->bind_param("i", $user_id);
        // $stmt->execute();
        // $user = $stmt->get_result()->fetch_assoc();
        // @send_otp_email($user['email'], $otp);
        
        $success = 'Kode OTP baru telah dikirim';
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

        <?php if (isset($_SESSION['demo_otp_display'])): ?>
            <!-- Tampilkan OTP di halaman -->
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
                    </div>
                </div>
            </div>
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