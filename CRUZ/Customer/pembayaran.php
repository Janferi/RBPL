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
    <div class="container mx-auto p-5">
        <!-- Header -->
        <header class="flex justify-between items-center mb-8">
            <a href="home.php">
                <img alt="CRUZ logo" src="../image/logocruz.png" width="200" />
            </a>
            <div class="flex items-center space-x-4">
                <i class="fas fa-bars text-3xl cursor-pointer" id="menu-toggle"></i>
            </div>
        </header>

        <!-- Side Navigation Menu -->
        <div id="side-menu" class="fixed top-0 right-0 h-full w-64 bg-yellow-200 z-50 transform translate-x-full transition-transform duration-300 ease-in-out shadow-lg">
            <div class="p-5">
                <div class="flex justify-end mb-8">
                    <button id="close-menu" class="text-2xl">&times;</button>
                </div>
                <div class="flex flex-col space-y-4">
                    <a href="halamanmenu.php" class="flex items-center space-x-2 p-3 rounded-lg hover:bg-yellow-300 transition duration-200">
                        <i class="fas fa-utensils"></i>
                        <span>All Menu</span>
                        <i class="fas fa-star ml-auto text-yellow-500"></i>
                    </a>
                    <a href="order.php" class="flex items-center space-x-2 p-3 rounded-lg hover:bg-yellow-300 transition duration-200">
                        <i class="fas fa-receipt"></i>
                        <span>Your Order</span>
                        <i class="fas fa-envelope ml-auto"></i>
                    </a>
                    <a href="keranjang.php" class="flex items-center space-x-2 p-3 rounded-lg hover:bg-yellow-300 transition duration-200">
                        <i class="fas fa-shopping-basket"></i>
                        <span>Your Basket</span>
                        <i class="fas fa-shopping-cart ml-auto"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Payment Title -->
        <h1 class="text-5xl font-bold mb-8">Payment</h1>

        <!-- Payment Details -->
        <div class="bg-gray-900 text-white rounded-lg p-6 mb-6">
            <!-- Items -->
            <?php foreach ($items as $item): ?>
            <div class="flex justify-between items-center mb-4">
                <div>
                    <span class="font-medium"><?= $item['nama'] ?></span>
                    <span class="text-gray-400 ml-2">x <?= $item['jumlah'] ?></span>
                </div>
                <div class="text-right">
                    <span>Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></span>
                </div>
            </div>
            <?php endforeach; ?>
            
            <!-- Divider -->
            <div class="border-t border-gray-700 my-4"></div>
            
            <!-- Total -->
            <div class="flex justify-between items-center mt-4">
                <span class="font-medium">Total</span>
                <span class="font-bold">Rp <?= number_format($total_harga, 0, ',', '.') ?></span>
            </div>
        </div>

        <!-- Payment Button -->
        <div class="flex justify-center">
            <a href="paymentmethod.php" class="bg-gray-900 text-white px-8 py-3 rounded-md font-semibold hover:bg-gray-800 transition duration-300 text-center">
                Pay Now
            </a>
        </div>
        
        <!-- Back to Cart -->
        <div class="mt-8 text-center">
            <a href="keranjang.php" class="text-blue-600 hover:underline">
                <i class="fas fa-arrow-left mr-1"></i> Back to Cart
            </a>
        </div>
    </div>

    <!-- Overlay for when menu is open -->
    <div id="overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.getElementById('menu-toggle');
            const closeMenu = document.getElementById('close-menu');
            const sideMenu = document.getElementById('side-menu');
            const overlay = document.getElementById('overlay');

            // Open menu
            menuToggle.addEventListener('click', function() {
                sideMenu.classList.remove('translate-x-full');
                overlay.classList.remove('hidden');
            });

            // Close menu
            closeMenu.addEventListener('click', function() {
                sideMenu.classList.add('translate-x-full');
                overlay.classList.add('hidden');
            });

            // Close menu when clicking on overlay
            overlay.addEventListener('click', function() {
                sideMenu.classList.add('translate-x-full');
                overlay.classList.add('hidden');
            });
        });
    </script>
</body>
</html>