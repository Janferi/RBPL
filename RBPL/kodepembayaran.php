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

// PERBAIKAN: Simpan data keranjang ke purchased_items sebelum melakukan pembayaran
if (!isset($_SESSION['purchased_items'])) {
    $purchased_items = [];
    foreach ($_SESSION['keranjang'] as $id_menu => $jumlah) {
        // Ambil detail menu dari database
        $query = "SELECT * FROM menu WHERE id = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "i", $id_menu);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($row = mysqli_fetch_assoc($result)) {
            $nama = $row['nama'];
            $harga = $row['harga'];
            $subtotal = $harga * $jumlah;
            
            $purchased_items[] = [
                'nama' => $nama,
                'harga' => $harga,
                'jumlah' => $jumlah,
                'subtotal' => $subtotal
            ];
        }
    }
    // Simpan ke session
    $_SESSION['purchased_items'] = $purchased_items;
}

// Generate kode pembayaran (untuk Bank) atau QR Code (untuk e-wallet)
$kode_pembayaran = '';
$qr_image = '';
$payment_type = '';
$payment_name = '';

// Sesuaikan dengan metode pembayaran yang dipilih
switch ($payment_method) {
    case 'qris':
        $payment_type = 'qr';
        $payment_name = 'QRIS';
        $qr_image = 'image/qr_sample.jpeg';  // Ganti dengan QR code yang sesuai
        break;
    case 'gopay':
        $payment_type = 'qr';
        $payment_name = 'GoPay';
        $qr_image = 'image/qr_sample.jpeg';  // Ganti dengan QR code yang sesuai
        break;
    case 'shopeepay':
        $payment_type = 'qr';
        $payment_name = 'ShopeePay';
        $qr_image = 'image/qr_sample.jpeg';  // Ganti dengan QR code yang sesuai
        break;
    case 'ovo':
        $payment_type = 'qr';
        $payment_name = 'OVO';
        $qr_image = 'image/qr_sample.jpeg';  // Ganti dengan QR code yang sesuai
        break;
    case 'bni':
        $payment_type = 'va';
        $payment_name = 'BNI';
        $kode_pembayaran = '0217 082 1192 8543 6';  // Generate kode pembayaran yang sesuai
        break;
    case 'bca':
        $payment_type = 'va';
        $payment_name = 'BCA';
        $kode_pembayaran = '1234 5678 9012 3456';  // Generate kode pembayaran yang sesuai
        break;
    default:
        // Jika metode pembayaran tidak valid, redirect ke halaman pemilihan pembayaran
        header("Location: paymentmethod.php");
        exit();
}

// Generate tanggal dan waktu kedaluwarsa (15 menit kemudian)
$tanggal_sekarang = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
$tanggal_kedaluwarsa = $tanggal_sekarang->add(new DateInterval('PT15M')); // 15 menit
$tanggal_format = $tanggal_kedaluwarsa->format('d F Y, H.i');

// Simulasi pembayaran selesai dan redirect ke halaman terima kasih
if (isset($_POST['confirm_payment'])) {
    // Di sini biasanya akan ada proses verifikasi pembayaran
    // Untuk simulasi, kita langsung menganggap pembayaran berhasil
    
    // PERBAIKAN: Jangan hapus keranjang di sini, biarkan invoice.php yang menanganinya
    // $_SESSION['keranjang'] = array();
    
    // Redirect ke halaman terima kasih
    header("Location: invoice.php");
    exit();
}
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
    <div class="p-5">
        <!-- Header -->
        <header class="flex justify-between items-center mb-8">
            <a href="home.php">
                <img alt="CRUZ logo" src="image/logocruz.png" width="200" />
            </a>
            <div class="flex items-center space-x-4">
                <i class="fas fa-bars text-3xl cursor-pointer"></i>
            </div>
        </header>
        
        <div class="container mx-auto max-w-2xl">

        <!-- Payment Information -->
        <div class="bg-yellow-50 rounded-lg p-6 mb-6 shadow-md">
            <div class="text-center mb-4">
                <p class="text-gray-600">Payment Total:</p>
                <p class="text-3xl font-bold">Rp<?= number_format($total_harga, 0, ',', '.') ?></p>
            </div>
            
            <div class="mb-4 text-center">
                <?php if ($payment_type == 'qr'): ?>
                <!-- QR Code untuk QRIS/GoPay/ShopeePay/OVO -->
                <div class="mt-4">
                    <img src="<?= $payment_name == 'QRIS' ? 'image/logoqris.png' : 'image/logo' . strtolower($payment_name) . '.png' ?>" 
                         alt="<?= $payment_name ?>" 
                         class="h-12 mx-auto mb-2">
                    <div class="bg-white p-4 rounded-lg inline-block">
                        <img src="<?= $qr_image ?>" alt="QR Code" class="w-48 h-48 mx-auto">
                    </div>
                </div>
                <?php else: ?>
                <!-- Virtual Account untuk BNI/BCA -->
                <div class="mt-4">
                    <img src="image/logo<?= strtolower($payment_method) ?>.png" 
                         alt="<?= $payment_name ?>" 
                         class="h-12 mx-auto mb-2">
                    <p class="text-sm text-gray-600 mb-1">Virtual Account Number</p>
                    <p class="text-2xl font-mono font-bold mb-1"><?= $kode_pembayaran ?></p>
                    <p class="text-xs text-gray-500">Only accept from Bank <?= $payment_name ?></p>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="text-center text-sm text-gray-600 mt-6">
                <p>Valid until : <?= $tanggal_format ?></p>
            </div>
        </div>

        <!-- Tombol untuk simulasi pembayaran selesai -->
        <form method="post" action="">
            <div class="flex justify-center">
                <button type="submit" name="confirm_payment" 
                        class="bg-gray-900 text-white px-8 py-3 rounded-md w-full font-semibold hover:bg-gray-800 transition duration-300 text-center">
                    Pay Now
                </button>
            </div>
        </form>
        
        <!-- Petunjuk Pembayaran -->
        <div class="mt-8 bg-white p-4 rounded-lg shadow-sm">
            <h2 class="font-bold text-lg mb-2">Payment Instructions</h2>
            
            <?php if ($payment_type == 'qr'): ?>
            <!-- Petunjuk untuk QR Code -->
            <ol class="list-decimal pl-5 space-y-2 text-sm">
                <li>Open your <?= $payment_name ?> app on your smartphone</li>
                <li>Scan the QR code displayed above</li>
                <li>Check the payment details and confirm the payment</li>
                <li>After payment is completed, return to this page</li>
            </ol>
            <?php else: ?>
            <!-- Petunjuk untuk Virtual Account -->
            <ol class="list-decimal pl-5 space-y-2 text-sm">
                <li>Login to your <?= $payment_name ?> Mobile Banking app or Internet Banking</li>
                <li>Select "Transfer" or "Payment" menu</li>
                <li>Choose "Virtual Account" option</li>
                <li>Enter the virtual account number: <?= $kode_pembayaran ?></li>
                <li>Confirm the payment details and complete the transaction</li>
                <li>Save your payment receipt</li>
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
</body>
</html>