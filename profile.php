<?php
session_start();
require_once 'config.php';
require_once 'maintenance_config.php';

// Check maintenance mode
check_maintenance();

if (!isset($_SESSION['logged_in'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Check username availability
if (isset($_POST['check_username'])) {
    $username = $_POST['username'];
    $stmt = $conn->prepare("SELECT id_user FROM user WHERE nama = ? AND id_user != ?");
    $stmt->bind_param("si", $username, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    echo json_encode(['available' => $result->num_rows === 0]);
    exit();
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $new_username = $_POST['username'];
    
    // Check if username is taken
    $stmt = $conn->prepare("SELECT id_user FROM user WHERE nama = ? AND id_user != ?");
    $stmt->bind_param("si", $new_username, $user_id);
    $stmt->execute();
    
    if ($stmt->get_result()->num_rows > 0) {
        $error = 'Username sudah digunakan';
    } else {
        $stmt = $conn->prepare("UPDATE user SET nama = ? WHERE id_user = ?");
        $stmt->bind_param("si", $new_username, $user_id);
        
        if ($stmt->execute()) {
            $_SESSION['user_name'] = $new_username;
            $success = 'Profil berhasil diperbarui!';
        } else {
            $error = 'Gagal memperbarui profil';
        }
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Get current password
    $stmt = $conn->prepare("SELECT password_hash, email FROM user WHERE id_user = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    
    if (!password_verify($old_password, $user['password_hash'])) {
        $error = 'Password lama salah';
    } elseif (strlen($new_password) < 8) {
        $error = 'Password baru minimal 8 karakter';
    } elseif ($new_password !== $confirm_password) {
        $error = 'Konfirmasi password tidak cocok';
    } else {
        // Generate OTP
        $otp = rand(100000, 999999);
        $_SESSION['password_change_otp'] = $otp;
        $_SESSION['password_change_time'] = time();
        $_SESSION['new_password_hash'] = password_hash($new_password, PASSWORD_DEFAULT);
        
        // Send OTP
        send_otp_email($user['email'], $otp);
        
        $show_otp = true;
    }
}

// Verify OTP for password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_password_otp'])) {
    $entered_otp = $_POST['otp'];
    
    if (time() - $_SESSION['password_change_time'] > 60) {
        $error = 'Kode OTP telah kadaluarsa';
    } elseif ($entered_otp == $_SESSION['password_change_otp']) {
        // Update password
        $new_hash = $_SESSION['new_password_hash'];
        $stmt = $conn->prepare("UPDATE user SET password_hash = ? WHERE id_user = ?");
        $stmt->bind_param("si", $new_hash, $user_id);
        
        if ($stmt->execute()) {
            unset($_SESSION['password_change_otp']);
            unset($_SESSION['password_change_time']);
            unset($_SESSION['new_password_hash']);
            $success = 'Password berhasil diubah!';
        } else {
            $error = 'Gagal mengubah password';
        }
    } else {
        $error = 'Kode OTP salah';
        $show_otp = true;
    }
}

// Resend OTP
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['resend_password_otp'])) {
    $stmt = $conn->prepare("SELECT email FROM user WHERE id_user = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    
    $otp = rand(100000, 999999);
    $_SESSION['password_change_otp'] = $otp;
    $_SESSION['password_change_time'] = time();
    
    send_otp_email($user['email'], $otp);
    $show_otp = true;
    $success = 'Kode OTP baru telah dikirim';
}

// Get user data
$stmt = $conn->prepare("SELECT * FROM user WHERE id_user = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil - PRCFI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 min-h-screen">
    <header class="bg-white border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-4">
                    <a href="javascript:history.back()" class="text-gray-600 hover:text-gray-800">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h1 class="text-xl font-bold text-gray-800">Edit Profil</h1>
                </div>
                <span class="text-gray-700 font-medium"><?php echo $user['nama']; ?></span>
            </div>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Profile Picture -->
            <div class="md:col-span-1">
                <div class="bg-white rounded-lg shadow-lg p-6 text-center border border-gray-200">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user['nama']); ?>&background=3B82F6&color=fff&size=200" 
                        class="w-32 h-32 rounded-full mx-auto mb-4 border-4 border-blue-400">
                    <h3 class="font-bold text-gray-800 text-lg"><?php echo $user['nama']; ?></h3>
                    <p class="text-sm text-gray-600 mt-1"><?php echo $user['role']; ?></p>
                    <p class="text-sm text-gray-500 mt-2"><?php echo $user['email']; ?></p>
                </div>
            </div>

            <!-- Edit Forms -->
            <div class="md:col-span-2 space-y-6">
                <!-- Update Username -->
                <div class="bg-white rounded-lg shadow-lg p-6 border border-gray-200">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Informasi Profil</h3>
                    
                    <form method="POST" class="space-y-4">
                        <div>
                            <label class="block text-gray-700 text-sm font-medium mb-2">Username</label>
                            <input type="text" name="username" id="username" value="<?php echo $user['nama']; ?>" required 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
                            <p id="usernameStatus" class="text-sm mt-1"></p>
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-medium mb-2">Email</label>
                            <input type="email" value="<?php echo $user['email']; ?>" readonly
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100">
                            <p class="text-xs text-gray-500 mt-1">Email tidak dapat diubah</p>
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-medium mb-2">Role</label>
                            <input type="text" value="<?php echo $user['role']; ?>" readonly
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100">
                            <p class="text-xs text-gray-500 mt-1">Role tidak dapat diubah</p>
                        </div>

                        <button type="submit" name="update_profile"
                            class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600 transition duration-200 font-medium">
                            <i class="fas fa-save mr-2"></i> Simpan Perubahan
                        </button>
                    </form>
                </div>

                <!-- Change Password -->
                <div class="bg-white rounded-lg shadow-lg p-6 border border-gray-200">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Ubah Password</h3>
                    
                    <div id="passwordForm">
                        <button onclick="togglePasswordForm()" id="showPasswordBtn"
                            class="text-blue-500 hover:text-blue-700 text-sm font-medium">
                            <i class="fas fa-key mr-1"></i> Change Password
                        </button>

                        <form method="POST" id="changePasswordForm" class="hidden space-y-4 mt-4">
                            <div>
                                <label class="block text-gray-700 text-sm font-medium mb-2">Password Lama *</label>
                                <input type="password" name="old_password" required 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
                            </div>

                            <div>
                                <label class="block text-gray-700 text-sm font-medium mb-2">Password Baru *</label>
                                <input type="password" name="new_password" id="new_password" required minlength="8"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
                                <p class="text-xs text-gray-500 mt-1">Minimal 8 karakter</p>
                            </div>

                            <div>
                                <label class="block text-gray-700 text-sm font-medium mb-2">Konfirmasi Password Baru *</label>
                                <input type="password" name="confirm_password" id="confirm_password" required 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
                            </div>

                            <div class="flex space-x-3">
                                <button type="button" onclick="togglePasswordForm()"
                                    class="flex-1 bg-gray-200 text-gray-700 py-2 rounded-lg hover:bg-gray-300 transition duration-200 font-medium">
                                    Batal
                                </button>
                                <button type="submit" name="change_password"
                                    class="flex-1 bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600 transition duration-200 font-medium">
                                    <i class="fas fa-key mr-2"></i> Ubah Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <?php if (isset($show_otp) && $show_otp): ?>
                <!-- OTP Verification for Password Change -->
                <div class="bg-white rounded-lg shadow-lg p-6 border border-blue-200">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Verifikasi OTP</h3>
                    <p class="text-sm text-gray-600 mb-4">Kode OTP telah dikirim ke email Anda. Masukkan kode untuk mengkonfirmasi perubahan password.</p>
                    
                    <form method="POST" class="space-y-4">
                        <div>
                            <label class="block text-gray-700 text-sm font-medium mb-2">Kode OTP</label>
                            <input type="text" name="otp" required maxlength="6" pattern="[0-9]{6}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 text-center text-2xl tracking-widest"
                                placeholder="000000">
                        </div>

                        <div class="text-center text-sm text-gray-600">
                            Kode berlaku: <span id="timer" class="font-bold text-blue-600">60</span> detik
                        </div>

                        <button type="submit" name="verify_password_otp" 
                            class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600 transition duration-200 font-medium">
                            Verifikasi
                        </button>

                        <button type="submit" name="resend_password_otp"
                            class="w-full bg-gray-200 text-gray-700 py-2 rounded-lg hover:bg-gray-300 transition duration-200 font-medium">
                            Kirim Ulang Kode
                        </button>
                    </form>
                </div>

                <script>
                    let timeLeft = 60;
                    const timerElement = document.getElementById('timer');

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
                <?php endif; ?>
            </div>
        </div>
    </main>

    <script>
        // Check username availability
        let typingTimer;
        const originalUsername = '<?php echo $user['nama']; ?>';
        
        document.getElementById('username').addEventListener('input', function() {
            clearTimeout(typingTimer);
            const username = this.value;
            
            if (username && username !== originalUsername) {
                typingTimer = setTimeout(() => {
                    fetch('profile.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                        body: 'check_username=1&username=' + encodeURIComponent(username)
                    })
                    .then(r => r.json())
                    .then(data => {
                        const status = document.getElementById('usernameStatus');
                        if (data.available) {
                            status.textContent = 'Username tersedia';
                            status.className = 'text-green-600 text-sm mt-1';
                        } else {
                            status.textContent = 'Username sudah digunakan';
                            status.className = 'text-red-600 text-sm mt-1';
                        }
                    });
                }, 500);
            } else if (username === originalUsername) {
                document.getElementById('usernameStatus').textContent = '';
            }
        });

        function togglePasswordForm() {
            const form = document.getElementById('changePasswordForm');
            const btn = document.getElementById('showPasswordBtn');
            
            if (form.classList.contains('hidden')) {
                form.classList.remove('hidden');
                btn.classList.add('hidden');
            } else {
                form.classList.add('hidden');
                btn.classList.remove('hidden');
            }
        }

        // Password confirmation validation
        document.getElementById('confirm_password')?.addEventListener('input', function() {
            const newPass = document.getElementById('new_password').value;
            if (this.value && this.value !== newPass) {
                this.setCustomValidity('Password tidak cocok');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>
</body>
</html>