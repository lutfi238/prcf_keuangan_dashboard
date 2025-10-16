<?php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in'])) {
    header('Location: login.php');
    exit();
}

$user_name = $_SESSION['user_name'];
$user_role = $_SESSION['user_role'];

// Get feature name from URL parameter
$feature = isset($_GET['feature']) ? htmlspecialchars($_GET['feature']) : 'Fitur';

// Determine return dashboard based on role
$return_dashboard = 'login.php';
switch ($user_role) {
    case 'Project Manager':
        $return_dashboard = 'dashboard_pm.php';
        break;
    case 'Staff Accountant':
        $return_dashboard = 'dashboard_sa.php';
        break;
    case 'Finance Manager':
        $return_dashboard = 'dashboard_fm.php';
        break;
    case 'Direktur':
        $return_dashboard = 'dashboard_dir.php';
        break;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Under Construction - PRCFI Financial</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.12.2/lottie.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Poppins', sans-serif;
        }
        
        #lottie-animation {
            max-width: 400px;
            width: 100%;
            margin: 0 auto;
        }
        
        .fade-in {
            animation: fadeIn 1s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .progress-bar {
            width: 0%;
            animation: progress 3s ease-out forwards;
        }
        
        @keyframes progress {
            to { width: 65%; }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center py-8">
    <div class="max-w-2xl mx-auto px-4 py-8">
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-8 text-center fade-in">
                <div class="inline-block p-4 bg-white rounded-full mb-4">
                    <i class="fas fa-tools text-5xl text-blue-500"></i>
                </div>
                <h1 class="text-3xl font-bold text-white mb-2">🚧 Under Construction</h1>
                <p class="text-blue-100">Fitur sedang dalam pengembangan</p>
            </div>

            <!-- Lottie Animation -->
            <div class="p-6">
                <div id="lottie-animation"></div>
            </div>

            <!-- Content -->
            <div class="px-8 pb-8 text-center">
                <div class="mb-6">
                    <div class="inline-block px-4 py-2 bg-blue-100 text-blue-700 rounded-full text-sm font-medium mb-4">
                        <i class="fas fa-user-tag mr-2"></i><?php echo $user_role; ?>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800 mb-3">
                        <?php echo $feature; ?>
                    </h2>
                    <p class="text-gray-600 mb-6">
                        Maaf, fitur <strong class="text-blue-600"><?php echo $feature; ?></strong> saat ini sedang dalam tahap pengembangan dan belum tersedia.
                    </p>
                </div>

                <!-- Progress Bar -->
                <div class="mb-6">
                    <div class="flex justify-between text-sm text-gray-600 mb-2">
                        <span>Progress Pengembangan</span>
                        <span class="font-semibold text-blue-600">65%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                        <div class="progress-bar h-full bg-gradient-to-r from-blue-400 to-indigo-500 rounded-full"></div>
                    </div>
                </div>

                <!-- Status Info -->
                <div class="bg-gray-50 rounded-lg p-6 mb-6">
                    <div class="grid grid-cols-3 gap-4 text-center">
                        <div>
                            <div class="text-3xl mb-2">✅</div>
                            <p class="text-sm text-gray-600">Planning<br>Selesai</p>
                        </div>
                        <div>
                            <div class="text-3xl mb-2">⚙️</div>
                            <p class="text-sm text-gray-600">Development<br>Berjalan</p>
                        </div>
                        <div>
                            <div class="text-3xl mb-2">🚀</div>
                            <p class="text-sm text-gray-600">Launch<br>Segera</p>
                        </div>
                    </div>
                </div>

                <!-- Features Coming Soon -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6 text-left">
                    <h3 class="font-bold text-gray-800 mb-3 flex items-center">
                        <i class="fas fa-rocket text-blue-500 mr-2"></i>
                        Fitur yang Akan Datang:
                    </h3>
                    <ul class="space-y-2 text-sm text-gray-700">
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                            <span>Dashboard interaktif dengan grafik real-time</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                            <span>Sistem notifikasi otomatis</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                            <span>Export laporan ke berbagai format (PDF, Excel, CSV)</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                            <span>Laporan untuk donor dengan template profesional</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                            <span>Analisis keuangan dan forecasting</span>
                        </li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div class="text-sm text-gray-500 mb-6">
                    <p>Butuh bantuan atau ada pertanyaan?</p>
                    <p class="font-medium text-blue-600">
                        <i class="fas fa-envelope mr-1"></i> pblprcf@gmail.com
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="<?php echo $return_dashboard; ?>" 
                        class="inline-flex items-center justify-center px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition duration-200 font-medium">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali ke Dashboard
                    </a>
                    <a href="profile.php" 
                        class="inline-flex items-center justify-center px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition duration-200 font-medium">
                        <i class="fas fa-user mr-2"></i>
                        Profil Saya
                    </a>
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 px-8 py-4 text-center text-sm text-gray-600 border-t">
                <p>© <?php echo date('Y'); ?> PRCFI Financial Management System</p>
                <p class="text-xs mt-1">Version 1.0 Beta</p>
            </div>
        </div>

        <!-- Additional Info -->
        <div class="text-center mt-6 text-sm text-gray-600">
            <p>Login sebagai: <span class="font-medium text-gray-800"><?php echo $user_name; ?></span></p>
        </div>
    </div>

    <script>
        // Load Lottie animation
        try {
            const animation = lottie.loadAnimation({
                container: document.getElementById('lottie-animation'),
                renderer: 'svg',
                loop: true,
                autoplay: true,
                path: 'assets/fixing/Under Construction 1.json'
            });
        } catch (error) {
            console.error('Error loading animation:', error);
            // Hide animation container if loading fails
            document.getElementById('lottie-animation').style.display = 'none';
        }
    </script>
</body>
</html>

