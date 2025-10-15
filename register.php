<?php
session_start();
require_once 'config.php';

$error = '';
$success = '';

// Check username availability
if (isset($_POST['check_username'])) {
    $username = $_POST['username'];
    $stmt = $conn->prepare("SELECT id_user FROM user WHERE nama = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    echo json_encode(['available' => $result->num_rows === 0]);
    exit();
}

// Check phone availability
if (isset($_POST['check_phone'])) {
    $phone = $_POST['phone'];
    $stmt = $conn->prepare("SELECT id_user FROM user WHERE no_HP = ?");
    $stmt->bind_param("s", $phone);
    $stmt->execute();
    $result = $stmt->get_result();
    echo json_encode(['available' => $result->num_rows === 0]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $phone = $_POST['phone'];
    $role = $_POST['role'];
    
    // Validation
    if (strlen($password) < 8) {
        $error = 'Password minimal 8 karakter';
    } elseif ($password !== $confirm_password) {
        $error = 'Password tidak cocok';
    } else {
        // Check if email exists
        $stmt = $conn->prepare("SELECT id_user FROM user WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $error = 'Email sudah terdaftar';
        } else {
            // Insert user
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO user (nama, email, password_hash, no_HP, role) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $username, $email, $password_hash, $phone, $role);
            
            if ($stmt->execute()) {
                $success = 'Akun berhasil dibuat!';
            } else {
                $error = 'Gagal membuat akun';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Akun - PRCFI Financial Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center py-8">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Buat Akun Baru</h1>
            <p class="text-gray-600">PRCFI Financial Management</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?php echo $success; ?>
                <div class="mt-4">
                    <a href="login.php" class="text-blue-500 hover:text-blue-700 font-medium">
                        Kembali ke Beranda
                    </a>
                </div>
            </div>
        <?php else: ?>
            <form method="POST" id="registerForm" class="space-y-4">
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2">Username</label>
                    <input type="text" name="username" id="username" required 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"
                        placeholder="Masukkan username">
                    <p id="usernameStatus" class="text-sm mt-1"></p>
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2">Email</label>
                    <input type="email" name="email" required 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"
                        placeholder="Masukkan email">
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2">Password</label>
                    <input type="password" name="password" id="password" required minlength="8"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"
                        placeholder="Minimal 8 karakter">
                    <p id="passwordError" class="text-red-500 text-sm mt-1 hidden">Password minimal 8 karakter</p>
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2">Konfirmasi Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" required 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"
                        placeholder="Masukkan ulang password">
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2">Nomor Handphone</label>
                    <input type="tel" name="phone" id="phone" required 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"
                        placeholder="08xxxxxxxxxx">
                    <p id="phoneStatus" class="text-sm mt-1"></p>
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2">Role</label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="radio" name="role" value="Finance Manager" required class="mr-2">
                            <span class="text-gray-700">Finance Manager (FM)</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="role" value="Staff Accountant" required class="mr-2">
                            <span class="text-gray-700">Staff Accounting (SA)</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="role" value="Project Manager" required class="mr-2">
                            <span class="text-gray-700">Project Manager (PM)</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="role" value="Direktur" required class="mr-2">
                            <span class="text-gray-700">Direktur (Dir)</span>
                        </label>
                    </div>
                </div>

                <button type="submit" name="register" 
                    class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600 transition duration-200 font-medium">
                    Buat Akun
                </button>
            </form>

            <div class="mt-6 text-center">
                <a href="login.php" class="text-blue-500 hover:text-blue-700 text-sm font-medium">
                    Sudah punya akun? Login
                </a>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Check username availability
        document.getElementById('username').addEventListener('blur', function() {
            const username = this.value;
            if (username) {
                fetch('register.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'check_username=1&username=' + encodeURIComponent(username)
                })
                .then(r => r.json())
                .then(data => {
                    const status = document.getElementById('usernameStatus');
                    if (data.available) {
                        status.textContent = 'Username available to use';
                        status.className = 'text-green-600 text-sm mt-1';
                    } else {
                        status.textContent = 'Username already use';
                        status.className = 'text-red-600 text-sm mt-1';
                    }
                });
            }
        });

        // Check phone availability
        document.getElementById('phone').addEventListener('blur', function() {
            const phone = this.value;
            if (phone) {
                fetch('register.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'check_phone=1&phone=' + encodeURIComponent(phone)
                })
                .then(r => r.json())
                .then(data => {
                    const status = document.getElementById('phoneStatus');
                    if (data.available) {
                        status.textContent = 'Number available to use';
                        status.className = 'text-green-600 text-sm mt-1';
                    } else {
                        status.textContent = 'Number already use';
                        status.className = 'text-red-600 text-sm mt-1';
                    }
                });
            }
        });

        // Password validation
        document.getElementById('password').addEventListener('input', function() {
            const error = document.getElementById('passwordError');
            if (this.value.length < 8 && this.value.length > 0) {
                error.classList.remove('hidden');
            } else {
                error.classList.add('hidden');
            }
        });
    </script>
</body>
</html>