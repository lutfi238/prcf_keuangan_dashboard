<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['user_role'] !== 'Project Manager') {
    header('Location: login.php');
    exit();
}

$user_name = $_SESSION['user_name'];
$user_id = $_SESSION['user_id'];

// Get proposals created by this PM
$proposals = $conn->query("SELECT p.*, pr.nama_proyek 
    FROM proposal p 
    LEFT JOIN proyek pr ON p.kode_proyek = pr.kode_proyek 
    WHERE p.pemohon = '{$user_name}' 
    ORDER BY p.created_at DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Project Manager - PRCFI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-white min-h-screen">
    <!-- Header -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <h1 class="text-xl font-bold text-gray-800">PRCFI Financial</h1>
                </div>
                
                <div class="flex items-center space-x-4">
                    <span class="text-gray-700 font-medium"><?php echo $user_name; ?></span>
                    
                    <!-- Notifications -->
                    <div class="relative" id="notificationDropdown">
                        <button onclick="toggleNotifications()" class="relative p-2 text-gray-600 hover:text-gray-800">
                            <i class="fas fa-bell text-xl"></i>
                            <span class="absolute top-0 right-0 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">3</span>
                        </button>
                        
                        <div id="notificationPanel" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl border border-gray-200 z-50">
                            <div class="p-4 border-b border-gray-200">
                                <h3 class="font-bold text-gray-800">Notifikasi</h3>
                            </div>
                            <div class="max-h-96 overflow-y-auto">
                                <div class="p-4 hover:bg-gray-50 border-b border-gray-100">
                                    <p class="text-sm text-gray-800 font-medium">Proposal Anda telah diterima</p>
                                    <p class="text-xs text-gray-500 mt-1">2 jam yang lalu</p>
                                </div>
                                <div class="p-4 hover:bg-gray-50 border-b border-gray-100">
                                    <p class="text-sm text-gray-800 font-medium">Laporan keuangan perlu revisi</p>
                                    <p class="text-xs text-gray-500 mt-1">5 jam yang lalu</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Profile -->
                    <div class="relative" id="profileDropdown">
                        <button onclick="toggleProfile()" class="flex items-center space-x-2">
                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user_name); ?>&background=3B82F6&color=fff" 
                                class="w-10 h-10 rounded-full border-2 border-blue-400">
                        </button>
                        
                        <div id="profilePanel" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border border-gray-200 z-50">
                            <a href="profile.php" class="block px-4 py-3 text-gray-700 hover:bg-gray-50">
                                <i class="fas fa-user mr-2"></i> Edit Profil
                            </a>
                            <a href="logout.php" class="block px-4 py-3 text-red-600 hover:bg-gray-50">
                                <i class="fas fa-sign-out-alt mr-2"></i> Logout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Selamat Datang, <?php echo $user_name; ?></h2>
            <p class="text-gray-600">Dashboard Project Manager</p>
        </div>

        <!-- Action Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-6 rounded-lg border border-blue-200 hover:shadow-lg transition duration-200">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Buat Proposal</h3>
                        <p class="text-sm text-gray-600 mt-1">Ajukan proposal baru ke Finance Manager</p>
                    </div>
                    <div class="bg-blue-500 p-3 rounded-full">
                        <i class="fas fa-file-alt text-white text-2xl"></i>
                    </div>
                </div>
                <a href="create_proposal.php" class="inline-block bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition duration-200 font-medium">
                    Buat Proposal
                </a>
            </div>

            <div class="bg-gradient-to-br from-green-50 to-green-100 p-6 rounded-lg border border-green-200 hover:shadow-lg transition duration-200">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Buat Laporan Keuangan</h3>
                        <p class="text-sm text-gray-600 mt-1">Kirim laporan ke Staff Accounting</p>
                    </div>
                    <div class="bg-green-500 p-3 rounded-full">
                        <i class="fas fa-chart-line text-white text-2xl"></i>
                    </div>
                </div>
                <a href="create_financial_report.php" class="inline-block bg-green-500 text-white px-6 py-2 rounded-lg hover:bg-green-600 transition duration-200 font-medium">
                    Buat Laporan
                </a>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Aktivitas Terbaru</h3>
            <div class="space-y-4">
                <div class="flex items-start space-x-4 p-4 bg-gray-50 rounded-lg">
                    <div class="bg-blue-500 p-2 rounded-full">
                        <i class="fas fa-file-alt text-white"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-medium text-gray-800">Proposal Proyek ABC</h4>
                        <p class="text-sm text-gray-600">Status: Menunggu persetujuan FM</p>
                        <p class="text-xs text-gray-500 mt-1">Dikirim 2 hari yang lalu</p>
                    </div>
                </div>

                <div class="flex items-start space-x-4 p-4 bg-gray-50 rounded-lg">
                    <div class="bg-green-500 p-2 rounded-full">
                        <i class="fas fa-check-circle text-white"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-medium text-gray-800">Laporan Keuangan Q1</h4>
                        <p class="text-sm text-gray-600">Status: Disetujui oleh SA</p>
                        <p class="text-xs text-gray-500 mt-1">5 hari yang lalu</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        function toggleNotifications() {
            const panel = document.getElementById('notificationPanel');
            const profilePanel = document.getElementById('profilePanel');
            profilePanel.classList.add('hidden');
            panel.classList.toggle('hidden');
        }

        function toggleProfile() {
            const panel = document.getElementById('profilePanel');
            const notifPanel = document.getElementById('notificationPanel');
            notifPanel.classList.add('hidden');
            panel.classList.toggle('hidden');
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            const notifDropdown = document.getElementById('notificationDropdown');
            const profileDropdown = document.getElementById('profileDropdown');
            
            if (!notifDropdown.contains(event.target)) {
                document.getElementById('notificationPanel').classList.add('hidden');
            }
            if (!profileDropdown.contains(event.target)) {
                document.getElementById('profilePanel').classList.add('hidden');
            }
        });
    </script>
</body>
</html>