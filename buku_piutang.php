<?php
session_start();
require_once 'config.php';
require_once 'maintenance_config.php';

// Check maintenance mode
check_maintenance();

if (!isset($_SESSION['logged_in']) || $_SESSION['user_role'] !== 'Finance Manager') {
    header('Location: login.php');
    exit();
}

$user_name = $_SESSION['user_name'];
$user_id = $_SESSION['user_id'];

// Handle create new piutang header
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_header'])) {
    $kode_proyek = $_POST['kode_proyek'];
    $account_name = $_POST['account_name'];
    $periode_mulai = $_POST['periode_mulai'];
    $periode_selesai = $_POST['periode_selesai'];
    $beginning_balance_idr = $_POST['beginning_balance_idr'] ?? 0;
    $beginning_balance_usd = $_POST['beginning_balance_usd'] ?? 0;
    
    $stmt = $conn->prepare("INSERT INTO buku_piutang_header (kode_proyek, account_name, periode_mulai, periode_selesai, beginning_balance_idr, beginning_balance_usd, ending_balance_idr, ending_balance_usd, created_by, status, tgl_pembuatan) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'draft', CURDATE())");
    $stmt->bind_param("ssssddddi", $kode_proyek, $account_name, $periode_mulai, $periode_selesai, $beginning_balance_idr, $beginning_balance_usd, $beginning_balance_idr, $beginning_balance_usd, $user_id);
    
    if ($stmt->execute()) {
        $success = 'Buku piutang berhasil dibuat!';
    } else {
        $error = 'Gagal membuat buku piutang';
    }
}

// Get piutang headers
$headers = $conn->query("SELECT ph.*, u.nama as creator_name, p.nama_proyek 
    FROM buku_piutang_header ph 
    LEFT JOIN user u ON ph.created_by = u.id_user 
    LEFT JOIN proyek p ON ph.kode_proyek = p.kode_proyek 
    ORDER BY ph.created_at DESC");

// Get projects
$projects = $conn->query("SELECT kode_proyek, nama_proyek FROM proyek WHERE status_proyek != 'cancelled'");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Piutang - PRCFI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 min-h-screen">
    <header class="bg-white border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-4">
                    <a href="dashboard_fm.php" class="text-gray-600 hover:text-gray-800">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h1 class="text-xl font-bold text-gray-800">Buku Piutang</h1>
                </div>
                <span class="text-gray-700 font-medium"><?php echo $user_name; ?></span>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <?php if (isset($success)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <!-- Action Bar -->
        <div class="flex justify-end mb-6">
            <button onclick="toggleCreateForm()" 
                class="px-6 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition duration-200 font-medium">
                <i class="fas fa-plus mr-2"></i> Buat Buku Piutang Baru
            </button>
        </div>

        <!-- Create Form -->
        <div id="createForm" class="hidden mb-6 bg-white rounded-lg shadow-lg p-6 border border-gray-200">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Buat Buku Piutang Baru</h3>
            
            <form method="POST" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-2">Kode Proyek *</label>
                        <select name="kode_proyek" required 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400">
                            <option value="">Pilih Proyek</option>
                            <?php while ($project = $projects->fetch_assoc()): ?>
                                <option value="<?php echo $project['kode_proyek']; ?>">
                                    <?php echo $project['kode_proyek'] . ' - ' . $project['nama_proyek']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-2">Nama Akun *</label>
                        <input type="text" name="account_name" required 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400">
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-2">Periode Mulai *</label>
                        <input type="date" name="periode_mulai" required 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400">
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-2">Periode Selesai *</label>
                        <input type="date" name="periode_selesai" required 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400">
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-2">Saldo Awal IDR</label>
                        <input type="number" name="beginning_balance_idr" step="0.01" value="0" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400">
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-2">Saldo Awal USD</label>
                        <input type="number" name="beginning_balance_usd" step="0.01" value="0" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400">
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="toggleCreateForm()"
                        class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition duration-200 font-medium">
                        Batal
                    </button>
                    <button type="submit" name="create_header"
                        class="px-6 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition duration-200 font-medium">
                        <i class="fas fa-save mr-2"></i> Buat Buku Piutang
                    </button>
                </div>
            </form>
        </div>

        <!-- Piutang List -->
        <div class="grid grid-cols-1 gap-6">
            <?php while ($header = $headers->fetch_assoc()): ?>
            <div class="bg-white rounded-lg shadow-lg border border-gray-200 overflow-hidden">
                <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-green-50 to-white">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-gray-800 mb-2"><?php echo $header['account_name']; ?></h3>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                <div>
                                    <p class="text-gray-600">Proyek</p>
                                    <p class="font-medium text-gray-800"><?php echo $header['kode_proyek']; ?></p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Periode</p>
                                    <p class="font-medium text-gray-800">
                                        <?php echo date('d/m/Y', strtotime($header['periode_mulai'])); ?> - 
                                        <?php echo date('d/m/Y', strtotime($header['periode_selesai'])); ?>
                                    </p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Saldo Awal IDR</p>
                                    <p class="font-medium text-gray-800">Rp <?php echo number_format($header['beginning_balance_idr'], 2); ?></p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Saldo Akhir IDR</p>
                                    <p class="font-medium text-green-600">Rp <?php echo number_format($header['ending_balance_idr'], 2); ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="ml-4">
                            <span class="px-3 py-1 text-xs font-semibold rounded-full 
                                <?php 
                                echo match($header['status']) {
                                    'draft' => 'bg-gray-100 text-gray-800',
                                    'submitted' => 'bg-yellow-100 text-yellow-800',
                                    'approved' => 'bg-green-100 text-green-800',
                                    default => 'bg-blue-100 text-blue-800'
                                };
                                ?>">
                                <?php echo strtoupper($header['status']); ?>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="font-medium text-gray-800">Detail Transaksi</h4>
                        <a href="piutang_detail.php?id=<?php echo $header['id_piutang']; ?>" 
                            class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition duration-200 text-sm">
                            <i class="fas fa-eye mr-2"></i> Lihat Detail
                        </a>
                    </div>

                    <?php
                    // Get transaction count
                    $detail_stmt = $conn->prepare("SELECT COUNT(*) as count FROM buku_piutang_detail WHERE id_piutang = ?");
                    $detail_stmt->bind_param("i", $header['id_piutang']);
                    $detail_stmt->execute();
                    $detail_count = $detail_stmt->get_result()->fetch_assoc()['count'];
                    
                    // Get unliquidated count
                    $unliq_stmt = $conn->prepare("SELECT COUNT(*) as count FROM buku_piutang_unliquidated WHERE id_piutang = ? AND status = 'pending'");
                    $unliq_stmt->bind_param("i", $header['id_piutang']);
                    $unliq_stmt->execute();
                    $unliq_count = $unliq_stmt->get_result()->fetch_assoc()['count'];
                    ?>

                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div class="p-3 bg-blue-50 rounded-lg">
                            <p class="text-gray-600 mb-1">Total Transaksi</p>
                            <p class="text-2xl font-bold text-blue-600"><?php echo $detail_count; ?></p>
                        </div>
                        <div class="p-3 bg-yellow-50 rounded-lg">
                            <p class="text-gray-600 mb-1">Unliquidated</p>
                            <p class="text-2xl font-bold text-yellow-600"><?php echo $unliq_count; ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </main>

    <script>
        function toggleCreateForm() {
            const form = document.getElementById('createForm');
            form.classList.toggle('hidden');
        }
    </script>
</body>
</html>