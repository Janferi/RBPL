<?php
// Koneksi ke database
include 'koneksi.php';

// Inisialisasi session jika belum dimulai
session_start();

// Ambil order ID dari URL atau session
$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : (isset($_SESSION['order_id']) ? $_SESSION['order_id'] : '');
$order_db_id = isset($_SESSION['order_db_id']) ? $_SESSION['order_db_id'] : null;

// Jika tidak ada order ID, redirect ke home
if (empty($order_id)) {
    header("Location: home.php");
    exit();
}

// Set default values
$payment_method = isset($_SESSION['payment_method']) ? $_SESSION['payment_method'] : 'N/A';
$total_harga = isset($_SESSION['total_harga']) ? $_SESSION['total_harga'] : 0;
$status_pesanan = "Pending"; // Default status
$order_date = date('d F Y');
$order_time = date('H:i');

// Ambil item pesanan dari database jika order_db_id tersedia
$items = [];
if ($order_db_id) {
    $query = "SELECT detail_pesanan, status, tanggal_pesanan, waktu_pesanan FROM pesanan WHERE id_pesanan = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $order_db_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            $items = json_decode($row['detail_pesanan'], true) ?: [];
            $status_pesanan = $row['status'] ?: 'Pending';
            $order_date = date('d F Y', strtotime($row['tanggal_pesanan']));
            $order_time = date('H:i', strtotime($row['waktu_pesanan']));
            // Debugging: Check if items are retrieved
            if (empty($items)) {
                error_log("No items found in database for order_db_id: $order_db_id");
            }
        } else {
            error_log("No record found in pesanan table for order_db_id: $order_db_id");
        }
        mysqli_stmt_close($stmt);
    } else {
        error_log("Failed to prepare statement: " . mysqli_error($koneksi));
    }
}

// Fallback: Ambil dari session purchased_items atau keranjang
if (empty($items)) {
    if (isset($_SESSION['purchased_items']) && !empty($_SESSION['purchased_items'])) {
        $items = $_SESSION['purchased_items'];
        $total_harga = array_sum(array_column($items, 'subtotal'));
    } elseif (isset($_SESSION['keranjang']) && !empty($_SESSION['keranjang'])) {
        foreach ($_SESSION['keranjang'] as $id_menu => $jumlah) {
            $query = "SELECT * FROM menu WHERE id = ?";
            $stmt = mysqli_prepare($koneksi, $query);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "i", $id_menu);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                
                if ($row = mysqli_fetch_assoc($result)) {
                    $nama = $row['nama'];
                    $harga = $row['harga'];
                    $gambar = $row['gambar'];
                    $subtotal = $harga * $jumlah;
                    
                    $items[] = [
                        'id' => $id_menu,
                        'jumlah' => $jumlah,
                        'nama' => $nama,
                        'harga' => $harga,
                        'gambar' => $gambar,
                        'subtotal' => $subtotal
                    ];
                    $total_harga += $subtotal;
                }
                mysqli_stmt_close($stmt);
            }
        }
    } else {
        error_log("No items found in session data for order_id: $order_id");
    }
}

// Status order mapping untuk tampilan
$status_colors = [
    'Pending' => 'bg-yellow-500',
    'Processing' => 'bg-blue-500',
    'Ready' => 'bg-green-500',
    'Completed' => 'bg-green-700',
    'Cancelled' => 'bg-red-500'
];

$status_color = isset($status_colors[$status_pesanan]) ? $status_colors[$status_pesanan] : 'bg-gray-500';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - CRUZ Coffee & Work Space</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
</head>
<body class="bg-yellow-100 text-gray-800">
    <div class="container mx-auto p-5 max-w-md">
        <!-- Header -->
        <header class="flex justify-between items-center mb-8">
            <a href="home.php">
                <img alt="CRUZ logo" src="../image/logocruz.png" width="150" />
            </a>
            <div class="flex items-center space-x-4">
                <a href="keranjang.php">
                    <div class="cart-icon">
                        <i class="fas fa-shopping-cart fa-2x text-dark"></i>
                    </div>
                </a>
                <i class="fas fa-bars text-3xl cursor-pointer" id="menu-toggle"></i>
            </div>
        </header>

        <!-- Side Navigation Menu -->
        <div id="side-menu" class="fixed top-0 right-0 h-full w-64 bg-yellow-200 z-50 transform translate-x-full transition-transform duration-300 ease-in-out shadow-lg">
            <div class="p-5">
                <div class="flex justify-end mb-8">
                    <button id="close-menu" class="text-2xl">×</button>
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

        <!-- Order Status -->
        <div class="bg-white rounded-lg p-6 mb-6 shadow-md text-center">
            <div class="mb-4">
                <span class="<?= $status_color ?> text-white px-4 py-1 rounded-full text-sm font-semibold">
                    <?= htmlspecialchars($status_pesanan) ?>
                </span>
            </div>
            <h1 class="text-2xl font-bold mb-2">Your Order</h1>
            <p class="text-gray-500 mb-1">Order Number: <span class="font-semibold"><?= htmlspecialchars($order_id) ?></span></p>
            <p class="text-gray-500 mb-2"><?= htmlspecialchars($order_date) ?>, <?= htmlspecialchars($order_time) ?></p>
        </div>
        
        <!-- Order Items -->
        <div class="bg-white rounded-lg p-6 mb-6 shadow-md">
            <h2 class="font-semibold mb-4 border-b pb-2">Order Items</h2>
            
            <?php if (empty($items)): ?>
                <p class="text-center text-gray-500 py-4">No items found</p>
                <?php if ($order_db_id): ?>
                    <p class="text-xs text-gray-400 text-center">Order ID: <?= htmlspecialchars($order_db_id) ?></p>
                <?php endif; ?>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($items as $item): ?>
                    <div class="flex justify-between items-center">
                        <div class="flex items-center space-x-3">
                            <?php if (isset($item['gambar']) && !empty($item['gambar'])): ?>
                            <img src="data:image/jpeg;base64,<?= base64_encode($item['gambar']) ?>" 
                                 alt="<?= htmlspecialchars($item['nama']) ?>" 
                                 class="w-16 h-16 object-cover rounded">
                            <?php else: ?>
                            <div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center">
                                <i class="fas fa-coffee text-gray-400"></i>
                            </div>
                            <?php endif; ?>
                            <div>
                                <p class="font-medium"><?= htmlspecialchars($item['nama']) ?></p>
                                <p class="text-sm text-gray-500">
                                    Rp <?= number_format($item['harga'], 0, ',', '.') ?> x <?= htmlspecialchars($item['jumlah']) ?>
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-medium">Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="border-t border-gray-200 mt-4 pt-4">
                    <div class="flex justify-between py-2">
                        <p class="font-medium">Subtotal</p>
                        <p class="font-medium">Rp <?= number_format($total_harga, 0, ',', '.') ?></p>
                    </div>
                    
                    <div class="flex justify-between py-2">
                        <p class="font-medium">Service Fee</p>
                        <p class="font-medium">Rp 0</p>
                    </div>
                    
                    <div class="flex justify-between py-2 border-t border-gray-200 mt-2">
                        <p class="font-bold">Total</p>
                        <p class="font-bold">Rp <?= number_format($total_harga, 0, ',', '.') ?></p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Payment Information -->
        <div class="bg-white rounded-lg p-6 mb-6 shadow-md">
            <h2 class="font-semibold mb-2">Payment Information</h2>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-700">Method</p>
                    <p class="font-medium"><?= htmlspecialchars(strtoupper($payment_method)) ?></p>
                </div>
                <?php if (strtolower($payment_method) != 'n/a'): ?>
                <img src="../image/logo<?= strtolower($payment_method) ?>.png" 
                     alt="<?= htmlspecialchars($payment_method) ?>" 
                     class="h-8">
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Shop Information -->
        <div class="bg-white rounded-lg p-6 mb-6 shadow-md">
            <h3 class="font-semibold mb-2">Shop Information</h3>
            <p class="text-gray-700 mb-1">CRUZ Coffee & Work Space</p>
            <p class="text-gray-500 text-sm mb-1">Jl. Kembang Raya No. 12</p>
            <p class="text-gray-500 text-sm mb-1">Jakarta Selatan</p>
            <p class="text-gray-500 text-sm mb-1">Phone: (021) 1234 5678</p>
        </div>
        
        <!-- Buttons -->
        <div class="flex flex-col space-y-3 mt-6">
            <?php if ($status_pesanan != 'Completed' && $status_pesanan != 'Cancelled'): ?>
            <button onclick="window.location.reload()" class="bg-gray-200 text-gray-800 px-6 py-3 rounded-md font-semibold hover:bg-gray-300 transition duration-300 flex items-center justify-center">
                <i class="fas fa-sync-alt mr-2"></i> Refresh Status
            </button>
            <?php endif; ?>
            
            <a href="invoice.php<?= !empty($order_id) ? '?order_id=' . urlencode($order_id) : '' ?>" class="bg-gray-200 text-gray-800 px-6 py-3 rounded-md font-semibold hover:bg-gray-300 transition duration-300 text-center">
                <i class="fas fa-receipt mr-2"></i> View Invoice
            </a>
            
            <a href="home.php" class="bg-gray-900 text-white px-6 py-3 rounded-md font-semibold hover:bg-gray-800 transition duration-300 text-center">
                Back to Home
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
            if (menuToggle && sideMenu && overlay) {
                menuToggle.addEventListener('click', function() {
                    sideMenu.classList.remove('translate-x-full');
                    overlay.classList.remove('hidden');
                });
            }

            // Close menu
            if (closeMenu && sideMenu && overlay) {
                closeMenu.addEventListener('click', function() {
                    sideMenu.classList.add('translate-x-full');
                    overlay.classList.add('hidden');
                });

                // Close menu when clicking on overlay
                overlay.addEventListener('click', function() {
                    sideMenu.classList.add('translate-x-full');
                    overlay.classList.add('hidden');
                });
            }
        });
    </script>
</body>
</html>