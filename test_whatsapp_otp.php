<?php
require_once 'config.php';

$result = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = $_POST['phone'] ?? '';
    $otp = rand(100000, 999999);
    
    if (!empty($phone)) {
        $success = send_otp_whatsapp($phone, $otp);
        $result = [
            'success' => $success,
            'phone' => $phone,
            'otp' => $otp,
            'formatted_phone' => format_phone_number($phone)
        ];
    } else {
        $error = 'Nomor WhatsApp tidak boleh kosong';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test WhatsApp OTP - PRCFI</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen py-8">
    <div class="max-w-2xl mx-auto px-4">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">🧪 Test WhatsApp OTP</h1>
            <p class="text-gray-600 mb-6">Test kirim OTP via Fonnte WhatsApp API</p>
            
            <!-- Config Status -->
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
                <h2 class="font-semibold text-blue-800 mb-2">📋 Configuration Status:</h2>
                <div class="text-sm space-y-1">
                    <p>
                        <strong>API URL:</strong> 
                        <code class="bg-blue-100 px-2 py-1 rounded"><?php echo FONNTE_API_URL; ?></code>
                    </p>
                    <p>
                        <strong>Token:</strong> 
                        <?php if (FONNTE_TOKEN === 'YOUR_FONNTE_TOKEN_HERE'): ?>
                            <span class="text-red-600 font-semibold">❌ NOT CONFIGURED</span>
                            <br><span class="text-xs text-red-500">Update FONNTE_TOKEN di config.php!</span>
                        <?php else: ?>
                            <span class="text-green-600 font-semibold">✅ CONFIGURED</span>
                            <code class="bg-green-100 px-2 py-1 rounded text-xs"><?php echo substr(FONNTE_TOKEN, 0, 20) . '...'; ?></code>
                        <?php endif; ?>
                    </p>
                    <p>
                        <strong>WA OTP Enabled:</strong> 
                        <?php if (WA_OTP_ENABLED): ?>
                            <span class="text-green-600 font-semibold">✅ YES</span>
                        <?php else: ?>
                            <span class="text-orange-600 font-semibold">⚠️ DISABLED</span>
                        <?php endif; ?>
                    </p>
                </div>
            </div>

            <?php if ($result): ?>
                <?php if ($result['success']): ?>
                    <!-- Success -->
                    <div class="bg-green-100 border border-green-400 rounded-lg p-6 mb-6">
                        <div class="flex items-start">
                            <svg class="h-8 w-8 text-green-500 mr-3" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                            </svg>
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-green-800 mb-2">✅ WhatsApp OTP Berhasil Terkirim!</h3>
                                <div class="space-y-2 text-sm">
                                    <p><strong>Nomor Tujuan:</strong> <code class="bg-green-200 px-2 py-1 rounded"><?php echo $result['formatted_phone']; ?></code></p>
                                    <p><strong>OTP Code:</strong> <span class="text-2xl font-bold text-green-600"><?php echo $result['otp']; ?></span></p>
                                </div>
                                <div class="mt-4 p-3 bg-white rounded border border-green-200">
                                    <p class="text-xs text-gray-600 font-semibold mb-2">📱 Cek WhatsApp Anda!</p>
                                    <p class="text-xs text-gray-600">Pesan seharusnya sudah masuk dalam beberapa detik. Jika belum, cek:</p>
                                    <ul class="text-xs text-gray-600 list-disc list-inside mt-1">
                                        <li>Device connected di dashboard Fonnte</li>
                                        <li>Nomor WhatsApp aktif & valid</li>
                                        <li>Quota Fonnte masih tersedia</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Failed -->
                    <div class="bg-red-100 border border-red-400 rounded-lg p-6 mb-6">
                        <h3 class="text-lg font-bold text-red-800 mb-2">❌ Gagal Mengirim WhatsApp OTP</h3>
                        <div class="space-y-2 text-sm text-red-700">
                            <p><strong>Nomor Tujuan:</strong> <code class="bg-red-200 px-2 py-1 rounded"><?php echo $result['formatted_phone']; ?></code></p>
                            <p><strong>OTP Code:</strong> <?php echo $result['otp']; ?> (tidak terkirim)</p>
                        </div>
                        <div class="mt-4 p-3 bg-white rounded border border-red-200">
                            <p class="text-xs font-semibold mb-2">🔧 Troubleshooting:</p>
                            <ol class="text-xs text-gray-700 list-decimal list-inside space-y-1">
                                <li>Cek <code>FONNTE_TOKEN</code> di config.php sudah benar</li>
                                <li>Device WhatsApp connected di dashboard Fonnte</li>
                                <li>Nomor WhatsApp format valid (08xx... atau 628xx...)</li>
                                <li>Quota Fonnte masih tersedia (100 free/month)</li>
                                <li>Cek error log: <code>C:\xampp\apache\logs\error.log</code></li>
                            </ol>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <!-- Form -->
            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-gray-700 font-medium mb-2">
                        Nomor WhatsApp Tujuan
                        <span class="text-sm text-gray-500 font-normal">(untuk test)</span>
                    </label>
                    <input 
                        type="tel" 
                        name="phone" 
                        required 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400"
                        placeholder="08xxxxxxxxxx"
                        pattern="[0-9]{10,13}"
                        value="<?php echo $_POST['phone'] ?? ''; ?>"
                    >
                    <p class="text-xs text-gray-500 mt-1">
                        Format: 08xxxxxxxxxx atau 628xxxxxxxxxx (Indonesia)
                    </p>
                </div>

                <button 
                    type="submit" 
                    class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-3 rounded-lg transition duration-200 flex items-center justify-center"
                >
                    <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                    </svg>
                    📤 Kirim Test WhatsApp OTP
                </button>
            </form>

            <!-- Recent Error Log -->
            <div class="mt-8 pt-6 border-t">
                <h3 class="font-semibold text-gray-700 mb-3">📋 Recent PHP Error Log (last 15 lines):</h3>
                <div class="bg-gray-900 text-green-400 p-4 rounded-lg overflow-auto max-h-64 text-xs font-mono">
                    <?php
                    $log_file = 'C:/xampp/apache/logs/error.log';
                    if (file_exists($log_file)) {
                        $log_lines = file($log_file);
                        $last_lines = array_slice($log_lines, -15);
                        foreach ($last_lines as $line) {
                            // Highlight WhatsApp related logs
                            if (strpos($line, 'WhatsApp') !== false || strpos($line, '📱') !== false) {
                                echo '<span class="text-yellow-300 font-bold">' . htmlspecialchars($line) . '</span>';
                            } else {
                                echo htmlspecialchars($line);
                            }
                        }
                    } else {
                        echo "Error log file not found.";
                    }
                    ?>
                </div>
            </div>

            <!-- Setup Guide Link -->
            <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                <p class="text-sm text-blue-800">
                    <strong>📚 Need help?</strong> Baca panduan lengkap di: 
                    <a href="SETUP_FONNTE.md" class="text-blue-600 underline hover:text-blue-800">SETUP_FONNTE.md</a>
                </p>
            </div>

            <!-- Back Link -->
            <div class="mt-6 text-center">
                <a href="login.php" class="text-blue-500 hover:text-blue-700 text-sm font-medium">
                    ← Kembali ke Login
                </a>
            </div>
        </div>
    </div>
</body>
</html>

