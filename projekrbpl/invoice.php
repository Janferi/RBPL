<?php
// Koneksi ke database
include 'koneksi.php';

// Inisialisasi session jika belum dimulai
session_start();

// Generate order ID
if (!isset($_SESSION['order_id'])) {
    $_SESSION['order_id'] = 'CRUZ' . date('YmdHis') . rand(100, 999);
}

// Ambil data order dari session
$order_id = $_SESSION['order_id'];
$payment_method = isset($_SESSION['payment_method']) ? $_SESSION['payment_method'] : 'N/A';
$total_harga = isset($_SESSION['total_harga']) ? $_SESSION['total_harga'] : 0;

// Ambil item yang dibeli
$purchased_items = [];
if (isset($_SESSION['keranjang']) && !empty($_SESSION['keranjang'])) {
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
}

// Kosongkan keranjang setelah berhasil membuat invoice
// Uncomment baris di bawah ini setelah testing selesai
// $_SESSION['keranjang'] = array();

// Format tanggal dan waktu
$order_date = date('d F Y');
$order_time = date('H:i');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You - CRUZ Coffee & Work Space</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
</head>
<body class="bg-yellow-100 text-gray-800">
    <div class="container mx-auto p-5 max-w-md">
        <!-- Header -->
        <header class="flex justify-center mb-8">
            <a href="home.php">
                <img alt="CRUZ logo" src="image/logocruz.png" width="150" class="mx-auto" />
            </a>
        </header>

        <!-- Thank You Message -->
        <div class="bg-white rounded-lg p-8 mb-6 shadow-md text-center">
            <div class="text-5xl text-green-500 mb-4">
                <i class="fas fa-check-circle"></i>
            </div>
            <h1 class="text-3xl font-bold mb-2">Thank You!</h1>
            <p class="text-gray-600 mb-4">Your order has been placed successfully.</p>
            <p class="text-gray-500 mb-4">Order Number: <span class="font-semibold"><?= $order_id ?></span></p>
        </div>
        
        <!-- Invoice -->
        <div class="bg-white rounded-lg p-6 mb-6 shadow-md">
            <h2 class="text-xl font-bold mb-4 text-center">INVOICE</h2>
            
            <div class="flex justify-between mb-4">
                <div>
                    <p class="text-sm text-gray-500">Order Date</p>
                    <p class="font-medium"><?= $order_date ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Order Time</p>
                    <p class="font-medium"><?= $order_time ?></p>
                </div>
            </div>
            
            <div class="mb-4">
                <p class="text-sm text-gray-500">Payment Method</p>
                <p class="font-medium"><?= strtoupper($payment_method) ?></p>
            </div>
            
            <div class="border-t border-gray-200 pt-4 mb-4">
                <h3 class="font-semibold mb-2">Order Details</h3>
                
                <?php if (!empty($purchased_items)): ?>
                    <?php foreach ($purchased_items as $item): ?>
                    <div class="flex justify-between py-2">
                        <div>
                            <p class="font-medium"><?= $item['nama'] ?></p>
                            <p class="text-sm text-gray-500">Rp <?= number_format($item['harga'], 0, ',', '.') ?> x <?= $item['jumlah'] ?></p>
                        </div>
                        <div class="text-right">
                            <p class="font-medium">Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-gray-500 text-center">No items found</p>
                <?php endif; ?>
            </div>
            
            <div class="border-t border-gray-200 pt-4">
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
            <button onclick="window.print()" class="bg-gray-200 text-gray-800 px-6 py-3 rounded-md font-semibold hover:bg-gray-300 transition duration-300 flex items-center justify-center">
                <i class="fas fa-print mr-2"></i> Print Receipt
            </button>
            
            <a href="home.php" class="bg-gray-900 text-white px-6 py-3 rounded-md font-semibold hover:bg-gray-800 transition duration-300 text-center">
                Back to Home
            </a>
        </div>
    </div>
    
    <script>
        // Add any JavaScript functionality you need here
        // For example, to enhance the print functionality or add animations
    </script>
</body>
</html>