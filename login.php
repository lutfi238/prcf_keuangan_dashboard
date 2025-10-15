<?php
session_start();
require_once 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['login'])) {
        $identifier = $_POST['identifier'];
        $password = $_POST['password'];
        
        $stmt = $conn->prepare("SELECT * FROM user WHERE email = ? OR no_HP = ?");
        $stmt->bind_param("ss", $identifier, $identifier);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password_hash'])) {
                // Generate OTP
                $otp = rand(100000, 999999);
                $_SESSION['otp'] = $otp;
                $_SESSION['otp_time'] = time();
                $_SESSION['user_id'] = $user['id_user'];
                $_SESSION['pending_login'] = true;
                
                // Send OTP via email (implement email function)
                // send_otp_email($user['email'], $otp);
                
                header('Location: verify_otp.php');
                exit();
            } else {
                $error = 'Password salah';
            }
        } else {
            $error = 'Email atau nomor HP tidak ditemukan';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PRCFI Financial Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">PRCFI Financial</h1>
            <p class="text-gray-600">Sistem Tata Kelola Keuangan</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-gray-700 text-sm font-medium mb-2">Email atau Nomor HP</label>
                <input type="text" name="identifier" required 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"
                    placeholder="Masukkan email atau nomor HP">
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-medium mb-2">Password</label>
                <input type="password" name="password" required 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"
                    placeholder="Masukkan password">
            </div>

            <button type="submit" name="login" 
                class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600 transition duration-200 font-medium">
                Login
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="register.php" class="text-blue-500 hover:text-blue-700 text-sm font-medium">
                Buat Akun Baru
            </a>
        </div>
    </div>
</body>
</html>