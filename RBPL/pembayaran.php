<?php
// Koneksi ke database
include 'koneksi.php';

// Inisialisasi session jika belum dimulai
session_start();

// Redirect ke halaman menu jika keranjang kosong
if (!isset($_SESSION['keranjang']) || empty($_SESSION['keranjang'])) {
    header("Location: halamanmenu.php");
    exit();
}

// Hitung total harga
$total_harga = 0;
$items = array();

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
        $total_harga += $subtotal;
        
        $items[] = array(
            'nama' => $nama,
            'jumlah' => $jumlah,
            'harga' => $harga,
            'subtotal' => $subtotal
        );
    }
}

// Simpan total harga ke session untuk digunakan di halaman pembayaran
$_SESSION['total_harga'] = $total_harga;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - CRUZ Coffee & Work Space</title>
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
        
        <div class="container mx-auto max-w-4xl">
            <!-- Payment Title -->
            <h1 class="text-5xl font-normal mb-8 italic bold">Payment</h1>

            <!-- Payment Details Card -->
            <div class="bg-gray-800 text-white rounded-2xl p-8 mb-8 border-4  shadow-lg">
                <!-- Items -->
                <?php foreach ($items as $item): ?>
                <div class="flex justify-between items-center mb-6">
                    <div class="flex items-center">
                        <span class="font-medium text-lg"><?= $item['nama'] ?></span>
                        <span class="text-gray-300 ml-4 text-lg">X <?= $item['jumlah'] ?></span>
                    </div>
                    <div class="text-right">
                        <span class="text-lg font-medium">Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <!-- Spacer for visual balance -->
                <div class="h-32"></div>
                
                <!-- Total Section -->
                <div class="border-t border-gray-600 pt-6">
                    <div class="flex justify-between items-center">
                        <span class="font-medium text-xl">Total</span>
                        <span class="font-bold text-xl">Rp <?= number_format($total_harga, 0, ',', '.') ?></span>
                    </div>
                </div>
            </div>

            <!-- Payment Button -->
            <div class="flex justify-center mb-8">
                <a href="paymentmethod.php" class="bg-gray-800 text-white px-16 py-4 rounded-full font-medium hover:bg-gray-700 transition duration-300 text-lg italic">
                    Pay Now
                </a>
            </div>
            
            <!-- Back to Cart -->
            <div class="text-center">
                <a href="keranjang.php" class="text-blue-600 hover:underline text-lg">
                    <i class="fas fa-arrow-left mr-1"></i> Back to Cart
                </a>
            </div>
        </div>
    </div>
</body>
</html>