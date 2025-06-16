<?php
// Koneksi ke database
include 'koneksi.php';

// Inisialisasi session jika belum dimulai
session_start();

// Redirect ke halaman menu jika keranjang kosong atau metode pembayaran tidak dipilih
if (!isset($_SESSION['keranjang']) || empty($_SESSION['keranjang']) || !isset($_SESSION['payment_method'])) {
    header("Location: halamanmenu.php");
    exit();
}

// Ambil data pembayaran dari session
$payment_method = $_SESSION['payment_method'];
$total_harga = $_SESSION['total_harga'];

// Generate kode pembayaran (untuk Bank) atau QR Code (untuk e-wallet)
$kode_pembayaran = '';
$qr_image = '';
$payment_type = '';
$payment_name = '';
$logo_image = '';

// Sesuaikan dengan metode pembayaran yang dipilih
switch ($payment_method) {
    case 'qris':
        $payment_type = 'qr';
        $payment_name = 'QRIS';
        $qr_image = '../image/qr_sample.jpeg';
        $logo_image = '../image/logoqris.png';
        break;
    case 'gopay':
        $payment_type = 'qr';
        $payment_name = 'GoPay';
        $qr_image = '../image/qr_sample.jpeg';
        $logo_image = '../image/logogopay.png';
        break;
    case 'shopeepay':
    case 'shopee':
    case 'shopipey': // Tambahkan case untuk menangani typo yang mungkin terjadi
        $payment_type = 'qr';
        $payment_name = 'ShopeePay';
        $qr_image = '../image/qr_sample.jpeg';
        $logo_image = '../image/logoshopeepay.png';
        break;
    case 'ovo':
        $payment_type = 'qr';
        $payment_name = 'OVO';
        $qr_image = '../image/qr_sample.jpeg';
        $logo_image = '../image/logoovo.png';
        break;
    case 'dana':
        $payment_type = 'qr';
        $payment_name = 'DANA';
        $qr_image = '../image/qr_sample.jpeg';
        $logo_image = '../image/logodana.png';
        break;
    case 'bni':
        $payment_type = 'va';
        $payment_name = 'BNI';
        $kode_pembayaran = '0217 082 1192 8543 6';
        $logo_image = '../image/logobni.png';
        break;
    case 'bca':
        $payment_type = 'va';
        $payment_name = 'BCA';
        $kode_pembayaran = '1234 5678 9012 3456';
        $logo_image = '../image/logobca.png';
        break;
    case 'bri':
        $payment_type = 'va';
        $payment_name = 'BRI';
        $kode_pembayaran = '9876 5432 1098 7654';
        $logo_image = '../image/logobri.png';
        break;
    case 'mandiri':
        $payment_type = 'va';
        $payment_name = 'Mandiri';
        $kode_pembayaran = '1357 9024 6813 5792';
        $logo_image = '../image/logomandiri.png';
        break;
    default:
        // Jika payment method tidak dikenali, tampilkan error dan redirect
        $_SESSION['error_message'] = "Metode pembayaran tidak valid: " . htmlspecialchars($payment_method);
        header("Location: paymentmethod.php");
        exit();
}

// Generate tanggal dan waktu kedaluwarsa (30 menit kemudian)
date_default_timezone_set('Asia/Jakarta');

$tanggal_sekarang = new DateTime();
$tanggal_kedaluwarsa = clone $tanggal_sekarang;
$tanggal_kedaluwarsa->add(new DateInterval('PT30M'));
$tanggal_format = $tanggal_kedaluwarsa->format('d F Y, H.i');

// Fungsi untuk menyimpan pesanan ke database
function simpanPesanan($koneksi, $total_harga, $payment_method, $keranjang)
{
    try {
        // Mulai transaksi
        mysqli_begin_transaction($koneksi);

        // Siapkan detail pesanan dalam format JSON
        $detail_pesanan = [];
        foreach ($keranjang as $id_menu => $jumlah) {
            // Ambil detail menu dari database
            $query = "SELECT nama, harga FROM menu WHERE id = ?";
            $stmt = mysqli_prepare($koneksi, $query);
            mysqli_stmt_bind_param($stmt, "i", $id_menu);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($row = mysqli_fetch_assoc($result)) {
                $detail_pesanan[] = [
                    'id_menu' => $id_menu,
                    'nama' => $row['nama'],
                    'harga' => $row['harga'],
                    'jumlah' => $jumlah,
                    'subtotal' => $row['harga'] * $jumlah
                ];
            }
        }

        // Konversi detail pesanan ke JSON
        $detail_json = json_encode($detail_pesanan, JSON_UNESCAPED_UNICODE);

        // Set timezone untuk Indonesia
        date_default_timezone_set('Asia/Jakarta');
        $tanggal_pesanan = date('Y-m-d');
        $waktu_pesanan = date('H:i:s');

        // Mapping payment method yang lebih lengkap untuk database
        $payment_method_mapping = [
            'qris' => 'QRIS',
            'gopay' => 'GoPay',
            'shopeepay' => 'ShopeePay',
            'shopee' => 'ShopeePay',
            'shopipey' => 'ShopeePay', // Handle typo
            'ovo' => 'OVO',
            'dana' => 'DANA',
            'bni' => 'BNI',
            'bca' => 'BCA',
            'bri' => 'BRI',
            'mandiri' => 'Mandiri'
        ];
        
        $payment_method_db = isset($payment_method_mapping[$payment_method]) 
            ? $payment_method_mapping[$payment_method] 
            : strtoupper($payment_method);

        // Insert ke tabel pesanan
        $query_insert = "INSERT INTO pesanan (tanggal_pesanan, waktu_pesanan, total_bayar, payment_method, detail_pesanan, status) VALUES (?, ?, ?, ?, ?, 'pending')";
        $stmt_insert = mysqli_prepare($koneksi, $query_insert);
        mysqli_stmt_bind_param($stmt_insert, "ssdss", $tanggal_pesanan, $waktu_pesanan, $total_harga, $payment_method_db, $detail_json);

        if (mysqli_stmt_execute($stmt_insert)) {
            // Ambil ID pesanan yang baru dibuat
            $id_pesanan = mysqli_insert_id($koneksi);

            // Commit transaksi
            mysqli_commit($koneksi);

            return $id_pesanan;
        } else {
            // Rollback jika ada error
            mysqli_rollback($koneksi);
            throw new Exception("Gagal menyimpan pesanan: " . mysqli_error($koneksi));
        }
    } catch (Exception $e) {
        // Rollback transaksi jika ada error
        mysqli_rollback($koneksi);
        error_log("Error simpan pesanan: " . $e->getMessage());
        return false;
    }
}

// Simulasi pembayaran selesai dan redirect ke halaman terima kasih
if (isset($_POST['confirm_payment'])) {
    // Simpan pesanan ke database
    $id_pesanan = simpanPesanan($koneksi, $total_harga, $payment_method, $_SESSION['keranjang']);

    if ($id_pesanan) {
        // Simpan data pesanan ke session untuk ditampilkan di invoice
        $_SESSION['purchased_items'] = [];
        foreach ($_SESSION['keranjang'] as $id_menu => $jumlah) {
            $query = "SELECT * FROM menu WHERE id = ?";
            $stmt = mysqli_prepare($koneksi, $query);
            mysqli_stmt_bind_param($stmt, "i", $id_menu);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($row = mysqli_fetch_assoc($result)) {
                $_SESSION['purchased_items'][] = [
                    'nama' => $row['nama'],
                    'harga' => $row['harga'],
                    'jumlah' => $jumlah,
                    'subtotal' => $row['harga'] * $jumlah
                ];
            }
        }

        // Simpan ID pesanan dan data pembayaran ke session
        $_SESSION['id_pesanan'] = $id_pesanan;
        $_SESSION['payment_method_name'] = $payment_name;
        $_SESSION['payment_date'] = date('d F Y, H:i');

        // Kosongkan keranjang
        $_SESSION['keranjang'] = array();
        
        // Hapus session payment method yang tidak diperlukan lagi
        unset($_SESSION['payment_method']);

        // Redirect ke halaman terima kasih
        header("Location: invoice.php");
        exit();
    } else {
        // Jika gagal menyimpan pesanan, tampilkan pesan error
        $_SESSION['error_message'] = "Maaf, terjadi kesalahan saat memproses pesanan. Silakan coba lagi.";
    }
}

// Debug: Tampilkan payment method yang dipilih (hapus setelah testing)
// echo "Payment Method: " . htmlspecialchars($payment_method) . "<br>";
// echo "Payment Type: " . htmlspecialchars($payment_type) . "<br>";
// echo "Payment Name: " . htmlspecialchars($payment_name) . "<br>";
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Code - CRUZ Coffee & Work Space</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
</head>

<body class="bg-yellow-100 text-gray-800">
    <div class="container mx-auto p-5 max-w-md">
        <!-- Header -->
        <header class="flex justify-between items-center mb-8">
            <a href="home.php">
                <img alt="CRUZ logo" src="../image/logocruz.png" width="150" class="mx-auto" />
            </a>
        </header>

        <!-- Debug Info (hapus setelah testing) -->
        <?php if (isset($_GET['debug'])): ?>
            <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-4">
                <p><strong>Debug Info:</strong></p>
                <p>Payment Method: <?= htmlspecialchars($payment_method) ?></p>
                <p>Payment Type: <?= htmlspecialchars($payment_type) ?></p>
                <p>Payment Name: <?= htmlspecialchars($payment_name) ?></p>
            </div>
        <?php endif; ?>

        <!-- Error Message -->
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <?= htmlspecialchars($_SESSION['error_message']) ?>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <!-- Payment Information -->
        <div class="bg-yellow-50 rounded-lg p-6 mb-6 shadow-md">
            <div class="text-center mb-4">
                <p class="text-gray-600">Payment Total:</p>
                <p class="text-3xl font-bold">Rp<?= number_format($total_harga, 0, ',', '.') ?></p>
            </div>

            <div class="mb-4 text-center">
                <?php if ($payment_type == 'qr'): ?>
                    <!-- QR Code untuk QRIS/GoPay/ShopeePay/OVO/DANA -->
                    <div class="mt-4">
                        <img src="<?= htmlspecialchars($logo_image) ?>"
                            alt="<?= htmlspecialchars($payment_name) ?>"
                            class="h-12 mx-auto mb-2"
                            onerror="this.src='../image/default_payment.png'">
                        <div class="bg-white p-4 rounded-lg inline-block">
                            <img src="<?= htmlspecialchars($qr_image) ?>" alt="QR Code" class="w-48 h-48 mx-auto"
                                 onerror="this.src='../image/qr_placeholder.png'">
                        </div>
                        <p class="text-sm text-gray-600 mt-2">Scan QR Code to pay with <?= htmlspecialchars($payment_name) ?></p>
                    </div>
                <?php else: ?>
                    <!-- Virtual Account untuk BNI/BCA/BRI/Mandiri -->
                    <div class="mt-4">
                        <img src="<?= htmlspecialchars($logo_image) ?>"
                            alt="<?= htmlspecialchars($payment_name) ?>"
                            class="h-12 mx-auto mb-2"
                            onerror="this.src='../image/default_bank.png'">
                        <p class="text-sm text-gray-600 mb-1">Virtual Account Number</p>
                        <p class="text-2xl font-mono font-bold mb-1 bg-gray-100 p-2 rounded border-2 border-dashed border-gray-300">
                            <?= htmlspecialchars($kode_pembayaran) ?>
                        </p>
                        <p class="text-xs text-gray-500">Only accept from Bank <?= htmlspecialchars($payment_name) ?></p>
                        <button onclick="copyToClipboard('<?= htmlspecialchars($kode_pembayaran) ?>')" 
                                class="mt-2 text-blue-600 hover:text-blue-800 text-sm">
                            <i class="fas fa-copy mr-1"></i>Copy Account Number
                        </button>
                    </div>
                <?php endif; ?>
            </div>

            <div class="text-center text-sm text-gray-600 mt-6">
                <i class="fas fa-clock mr-1"></i>
                <p>Valid until: <span class="timer-display"><?= $tanggal_format ?></span></p>
            </div>
        </div>

        <!-- Tombol untuk simulasi pembayaran selesai -->
        <form method="post" action="" onsubmit="return confirmPayment()">
            <div class="flex justify-center">
                <button type="submit" name="confirm_payment"
                    class="bg-gray-900 text-white px-8 py-3 rounded-md w-full font-semibold hover:bg-gray-800 transition duration-300 text-center">
                    <i class="fas fa-credit-card mr-2"></i>Confirm Payment
                </button>
            </div>
        </form>

        <!-- Petunjuk Pembayaran -->
        <div class="mt-8 bg-white p-4 rounded-lg shadow-sm">
            <h2 class="font-bold text-lg mb-2">
                <i class="fas fa-info-circle mr-2 text-blue-600"></i>Payment Instructions
            </h2>

            <?php if ($payment_type == 'qr'): ?>
                <!-- Petunjuk untuk QR Code -->
                <ol class="list-decimal pl-5 space-y-2 text-sm">
                    <li>Open your <?= htmlspecialchars($payment_name) ?> app on your smartphone</li>
                    <li>Scan the QR code displayed above</li>
                    <li>Check the payment amount: <strong>Rp<?= number_format($total_harga, 0, ',', '.') ?></strong></li>
                    <li>Confirm and complete the payment in your app</li>
                    <li>Click "Confirm Payment" button below after the payment is successful</li>
                </ol>
            <?php else: ?>
                <!-- Petunjuk untuk Virtual Account -->
                <ol class="list-decimal pl-5 space-y-2 text-sm">
                    <li>Login to your <?= htmlspecialchars($payment_name) ?> Mobile Banking app or Internet Banking</li>
                    <li>Select "Transfer" or "Payment" menu</li>
                    <li>Choose "Virtual Account" or "Transfer to Other Bank" option</li>
                    <li>Enter the virtual account number: <strong><?= htmlspecialchars($kode_pembayaran) ?></strong></li>
                    <li>Verify the payment amount: <strong>Rp<?= number_format($total_harga, 0, ',', '.') ?></strong></li>
                    <li>Complete the transaction with your PIN/password</li>
                    <li>Save the transaction receipt</li>
                    <li>Click "Confirm Payment" button below after the payment is successful</li>
                </ol>
            <?php endif; ?>
        </div>

        <!-- Back to Payment Method -->
        <div class="mt-8 text-center">
            <a href="paymentmethod.php" class="text-blue-600 hover:underline">
                <i class="fas fa-arrow-left mr-1"></i> Choose Another Payment Method
            </a>
        </div>
    </div>

    <script>
        // Function untuk copy virtual account number
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text.replace(/\s/g, '')).then(function() {
                // Show success message
                const button = event.target.closest('button');
                const originalText = button.innerHTML;
                button.innerHTML = '<i class="fas fa-check mr-1"></i>Copied!';
                button.className = button.className.replace('text-blue-600', 'text-green-600');
                
                setTimeout(() => {
                    button.innerHTML = originalText;
                    button.className = button.className.replace('text-green-600', 'text-blue-600');
                }, 2000);
            }).catch(function(err) {
                console.error('Could not copy text: ', err);
                alert('Failed to copy. Please copy manually: ' + text);
            });
        }

        // Konfirmasi pembayaran
        function confirmPayment() {
            return confirm('Are you sure you have completed the payment? This action cannot be undone.');
        }

        // Auto-refresh untuk simulasi real-time payment checking
        let paymentTimer;
        let timeLeft = 30 * 60; // 30 minutes in seconds

        function updateTimer() {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            
            // Update timer display if element exists
            const timerElement = document.querySelector('.timer-display');
            if (timerElement) {
                timerElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')} remaining`;
            }
            
            if (timeLeft <= 0) {
                alert('Payment time has expired. Please create a new payment.');
                window.location.href = 'paymentmethod.php';
                return;
            }
            
            timeLeft--;
            setTimeout(updateTimer, 1000);
        }

        function simulatePaymentCheck() {
            // Simulasi pengecekan status pembayaran setiap 30 detik
            paymentTimer = setTimeout(() => {
                // Dalam implementasi nyata, ini akan memanggil API untuk cek status pembayaran
                console.log('Checking payment status...');
                simulatePaymentCheck();
            }, 30000);
        }

        // Mulai timer dan simulasi pengecekan pembayaran
        updateTimer();
        simulatePaymentCheck();

        // Hentikan timer jika user meninggalkan halaman
        window.addEventListener('beforeunload', () => {
            if (paymentTimer) {
                clearTimeout(paymentTimer);
            }
        });

        // Prevent form resubmission on page refresh
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</body>

</html>