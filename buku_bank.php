<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['user_role'] !== 'Finance Manager') {
    header('Location: login.php');
    exit();
}

$user_name = $_SESSION['user_name'];
$user_id = $_SESSION['user_id'];

// Handle add entry
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_entry'])) {
    $kode_projek = $_POST['kode_projek'];
    $nama_rek = $_POST['nama_rek'];
    $no_rek = $_POST['no_rek'];
    $date = $_POST['date'];
    $reff = $_POST['reff'];
    $activity = $_POST['activity'];
    $cost_desc = $_POST['cost_desc'];
    $recipient = $_POST['recipient'];
    $p_code = $_POST['p_code'];
    $exp_code = $_POST['exp_code'];
    $nominal_code = $_POST['nominal_code'];
    $exrate = $_POST['exrate'];
    $cost_curr = $_POST['cost_curr'];
    $debit_idr = $_POST['debit_idr'] ?? 0;
    $debit_usd = $_POST['debit_usd'] ?? 0;
    $credit_idr = $_POST['credit_idr'] ?? 0;
    $credit_usd = $_POST['credit_usd'] ?? 0;
    
    // Get last balance
    $balance_stmt = $conn->prepare("SELECT balance_idr, balance_usd FROM buku_bank WHERE kode_projek = ? ORDER BY id_bank DESC LIMIT 1");
    $balance_stmt->bind_param("s", $kode_projek);
    $balance_stmt->execute();
    $balance_result = $balance_stmt->get_result();
    
    if ($balance_result->num_rows > 0) {
        $last_balance = $balance_result->fetch_assoc();
        $balance_idr = $last_balance['balance_idr'] + $debit_idr - $credit_idr;
        $balance_usd = $last_balance['balance_usd'] + $debit_usd - $credit_usd;
    } else {
        $balance_idr = $debit_idr - $credit_idr;
        $balance_usd = $debit_usd - $credit_usd;
    }
    
    $stmt = $conn->prepare("INSERT INTO buku_bank (kode_projek, nama_rek, no_rek, date, reff, activity, cost_desc, recipient, p_code, exp_code, nominal_code, exrate, cost_curr, debit_idr, debit_usd, credit_idr, credit_usd, balance_idr, balance_usd) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssssssddddddd", $kode_projek, $nama_rek, $no_rek, $date, $reff, $activity, $cost_desc, $recipient, $p_code, $exp_code, $nominal_code, $exrate, $cost_curr, $debit_idr, $debit_usd, $credit_idr, $credit_usd, $balance_idr, $balance_usd);
    
    if ($stmt->execute()) {
        $success = 'Entry berhasil ditambahkan!';
    } else {
        $error = 'Gagal menambahkan entry';
    }
}

// Get filter
$filter_project = $_GET['project'] ?? '';

// Get bank book entries
if ($filter_project) {
    $stmt = $conn->prepare("SELECT bb.*, p.nama_proyek FROM buku_bank bb LEFT JOIN proyek p ON bb.kode_projek = p.kode_proyek WHERE bb.kode_projek = ? ORDER BY bb.date DESC, bb.id_bank DESC");
    $stmt->bind_param("s", $filter_project);
} else {
    $stmt = $conn->query("SELECT bb.*, p.nama_proyek FROM buku_bank bb LEFT JOIN proyek p ON bb.kode_projek = p.kode_proyek ORDER BY bb.date DESC, bb.id_bank DESC LIMIT 100");
}

if (isset($stmt) && is_object($stmt)) {
    $stmt->execute();
    $entries = $stmt->get_result();
} else {
    $entries = $stmt;
}

// Get projects
$projects = $conn->query("SELECT kode_proyek, nama_proyek FROM proyek WHERE status_proyek != 'cancelled'");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Bank - PRCFI</title>
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
                    <h1 class="text-xl font-bold text-gray-800">Buku Bank</h1>
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
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center space-x-4">
                <div>
                    <label class="text-sm text-gray-600 mr-2">Filter Proyek:</label>
                    <select onchange="window.location.href='buku_bank.php?project=' + this.value"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="">Semua Proyek</option>
                        <?php 
                        $projects->data_seek(0);
                        while ($project = $projects->fetch_assoc()): 
                        ?>
                            <option value="<?php echo $project['kode_proyek']; ?>" <?php echo $filter_project === $project['kode_proyek'] ? 'selected' : ''; ?>>
                                <?php echo $project['kode_proyek'] . ' - ' . $project['nama_proyek']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>

            <button onclick="toggleAddForm()" 
                class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition duration-200 font-medium">
                <i class="fas fa-plus mr-2"></i> Tambah Entry
            </button>
        </div>

        <!-- Add Entry Form -->
        <div id="addForm" class="hidden mb-6 bg-white rounded-lg shadow-lg p-6 border border-gray-200">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Tambah Entry Buku Bank</h3>
            
            <form method="POST" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-2">Kode Proyek *</label>
                        <select name="kode_projek" required 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
                            <option value="">Pilih Proyek</option>
                            <?php 
                            $projects->data_seek(0);
                            while ($project = $projects->fetch_assoc()): 
                            ?>
                                <option value="<?php echo $project['kode_proyek']; ?>">
                                    <?php echo $project['kode_proyek'] . ' - ' . $project['nama_proyek']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-2">Nama Rekening *</label>
                        <input type="text" name="nama_rek" required 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-2">No. Rekening</label>
                        <input type="text" name="no_rek" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-2">Tanggal *</label>
                        <input type="date" name="date" required value="<?php echo date('Y-m-d'); ?>"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-2">Referensi</label>
                        <input type="text" name="reff" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-2">Aktivitas</label>
                        <input type="text" name="activity" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>

                    <div class="md:col-span-3">
                        <label class="block text-gray-700 text-sm font-medium mb-2">Deskripsi Biaya</label>
                        <textarea name="cost_desc" rows="2"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"></textarea>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-2">Penerima</label>
                        <input type="text" name="recipient" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-2">P Code</label>
                        <input type="text" name="p_code" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-2">Exp Code</label>
                        <input type="text" name="exp_code" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-2">Nominal Code</label>
                        <input type="text" name="nominal_code" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-2">Mata Uang *</label>
                        <select name="cost_curr" required 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
                            <option value="IDR">IDR</option>
                            <option value="USD">USD</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-2">Exchange Rate *</label>
                        <input type="number" name="exrate" step="0.0001" value="1.0000" required 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-2">Debit IDR</label>
                        <input type="number" name="debit_idr" step="0.01" value="0" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-2">Debit USD</label>
                        <input type="number" name="debit_usd" step="0.01" value="0" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-2">Credit IDR</label>
                        <input type="number" name="credit_idr" step="0.01" value="0" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-2">Credit USD</label>
                        <input type="number" name="credit_usd" step="0.01" value="0" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="toggleAddForm()"
                        class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition duration-200 font-medium">
                        Batal
                    </button>
                    <button type="submit" name="add_entry"
                        class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition duration-200 font-medium">
                        <i class="fas fa-save mr-2"></i> Simpan Entry
                    </button>
                </div>
            </form>
        </div>

        <!-- Bank Book Table -->
        <div class="bg-white rounded-lg shadow-lg border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left">Tanggal</th>
                            <th class="px-4 py-3 text-left">Proyek</th>
                            <th class="px-4 py-3 text-left">Rekening</th>
                            <th class="px-4 py-3 text-left">Deskripsi</th>
                            <th class="px-4 py-3 text-right">Debit IDR</th>
                            <th class="px-4 py-3 text-right">Credit IDR</th>
                            <th class="px-4 py-3 text-right">Balance IDR</th>
                            <th class="px-4 py-3 text-right">Debit USD</th>
                            <th class="px-4 py-3 text-right">Credit USD</th>
                            <th class="px-4 py-3 text-right">Balance USD</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php while ($entry = $entries->fetch_assoc()): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3"><?php echo date('d/m/Y', strtotime($entry['date'])); ?></td>
                            <td class="px-4 py-3"><?php echo $entry['kode_projek']; ?></td>
                            <td class="px-4 py-3"><?php echo $entry['nama_rek']; ?></td>
                            <td class="px-4 py-3"><?php echo $entry['cost_desc']; ?></td>
                            <td class="px-4 py-3 text-right"><?php echo number_format($entry['debit_idr'], 2); ?></td>
                            <td class="px-4 py-3 text-right"><?php echo number_format($entry['credit_idr'], 2); ?></td>
                            <td class="px-4 py-3 text-right font-bold"><?php echo number_format($entry['balance_idr'], 2); ?></td>
                            <td class="px-4 py-3 text-right"><?php echo number_format($entry['debit_usd'], 2); ?></td>
                            <td class="px-4 py-3 text-right"><?php echo number_format($entry['credit_usd'], 2); ?></td>
                            <td class="px-4 py-3 text-right font-bold"><?php echo number_format($entry['balance_usd'], 2); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script>
        function toggleAddForm() {
            const form = document.getElementById('addForm');
            form.classList.toggle('hidden');
        }
    </script>
</body>
</html>