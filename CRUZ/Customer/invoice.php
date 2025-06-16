<?php
// Koneksi ke database
include 'koneksi.php';

// Inisialisasi session jika belum dimulai
session_start();

// Generate order ID jika belum ada
if (!isset($_SESSION['order_id'])) {
    $_SESSION['order_id'] = 'CRUZ' . date('YmdHis') . rand(100, 999);
}

// Ambil data order dari session
$order_id = $_SESSION['order_id'];
// Gunakan 'payment_method_name' yang sudah disiapkan dari kodepembayaran.php
$payment_method = isset($_SESSION['payment_method_name']) ? $_SESSION['payment_method_name'] : 'N/A';
$total_harga = isset($_SESSION['total_harga']) ? $_SESSION['total_harga'] : 0;
$order_db_id = isset($_SESSION['order_db_id']) ? $_SESSION['order_db_id'] : null;

// Ambil item yang dibeli
$purchased_items = [];

// Cek apakah ada data purchased_items yang sudah disimpan sebelumnya
if (isset($_SESSION['purchased_items']) && !empty($_SESSION['purchased_items'])) {
    $purchased_items = $_SESSION['purchased_items'];
} else {
    // Jika belum ada dan ada ID pesanan dari database, ambil dari database
    if ($order_db_id) {
        $query = "SELECT detail_pesanan FROM pesanan WHERE id_pesanan = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "i", $order_db_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            $purchased_items = json_decode($row['detail_pesanan'], true);
            $_SESSION['purchased_items'] = $purchased_items;
        }
    } else {
        // Fallback: ambil dari keranjang yang masih ada (jika ada)
        if (isset($_SESSION['keranjang']) && !empty($_SESSION['keranjang'])) {
            foreach ($_SESSION['keranjang'] as $id_menu => $jumlah) {
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

            $_SESSION['purchased_items'] = $purchased_items;
            $_SESSION['keranjang'] = array();
        }
    }
}

// Ambil informasi pesanan dari database jika ada
$order_info = null;
if ($order_db_id) {
    $query = "SELECT * FROM pesanan WHERE id_pesanan = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "i", $order_db_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $order_info = mysqli_fetch_assoc($result);
}

// Format tanggal dan waktu
if ($order_info) {
    $order_date = date('d F Y', strtotime($order_info['tanggal_pesanan']));
    $order_time = date('H:i', strtotime($order_info['waktu_pesanan']));
} else {
    $order_date = date('d F Y');
    date_default_timezone_set('Asia/Jakarta');
    $order_time = date('H:i');
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You - CRUZ Coffee & Work Space</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <style>
        @media print {
            .no-print {
                display: none !important;
            }

            body {
                background: white !important;
            }

            .print-only {
                display: block !important;
            }
        }

        .print-only {
            display: none;
        }
    </style>
</head>

<body class="bg-yellow-100 text-gray-800">
    <div class="p-5">
        <!-- Header -->
        <header class="flex justify-between items-center mb-8 no-print">
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

        <div class="container mx-auto max-w-2xl">

            <!-- Success Message -->
            <?php if (isset($_SESSION['payment_success'])): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 no-print">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        <span><?= $_SESSION['payment_message'] ?></span>
                    </div>
                </div>
            <?php
                unset($_SESSION['payment_success']);
                unset($_SESSION['payment_message']);
            endif; ?>

            <!-- Thank You Message -->
            <div class="bg-white rounded-lg p-8 mb-6 shadow-md text-center">
                <div class="text-5xl text-green-500 mb-4">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h1 class="text-3xl font-bold mb-2">Thank You!</h1>
                <p class="text-gray-600 mb-4">Your order has been placed successfully.</p>
                <p class="text-gray-500 mb-2">Order Number: <span class="font-semibold"><?= $order_id ?></span></p>
                <?php if ($order_db_id): ?>
                    <p class="text-gray-500 mb-4">Database ID: <span class="font-semibold">#<?= $order_db_id ?></span></p>
                <?php endif; ?>

                <!-- Order Status -->
                <?php if ($order_info): ?>
                    <div class="mt-4 p-3 bg-gray-100 rounded-lg">
                        <p class="text-sm text-gray-600">Status:</p>
                        <span class="inline-block px-3 py-1 rounded-full text-sm font-medium <?= $order_info['status'] == 'selesai' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' ?>">
                            <?= $order_info['status'] == 'selesai' ? 'Completed' : 'In Progress' ?>
                        </span>
                    </div>
                <?php endif; ?>
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
                                    <p class="font-medium"><?= htmlspecialchars($item['nama']) ?></p>
                                    <p class="text-sm text-gray-500">Rp <?= number_format($item['harga'], 0, ',', '.') ?> x <?= $item['jumlah'] ?></p>
                                </div>
                                <div class="text-right">
                                    <p class="font-medium">Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-gray-500 text-center py-4">No items found</p>
                        <?php if ($order_db_id): ?>
                            <p class="text-xs text-gray-400 text-center">Order saved to database with ID: <?= $order_db_id ?></p>
                        <?php endif; ?>
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

                <!-- Print Only Information -->
                <div class="print-only mt-6 pt-4 border-t">
                    <p class="text-xs text-gray-500">Printed on: <?= date('d F Y H:i') ?></p>
                    <p class="text-xs text-gray-500">Thank you for choosing CRUZ Coffee & Work Space!</p>
                </div>
            </div>

            <!-- Shop Information -->
            <div class="bg-white rounded-lg p-6 mb-6 shadow-md">
                <h3 class="font-semibold mb-2">Shop Information</h3>
                <p class="text-gray-700 mb-1">CRUZ Coffee & Work Space</p>
                <p class="text-gray-500 text-sm mb-1">Jl. Kembang Raya No. 12</p>
                <p class="text-gray-500 text-sm mb-1">Jakarta Selatan</p>
                <p class="text-gray-500 text-sm mb-1">Phone: (021) 1234 5678</p>
                <p class="text-gray-500 text-sm">Email: info@cruzcoffee.com</p>
            </div>

            <!-- Buttons -->
            <div class="flex flex-col space-y-3 mt-6 no-print">
                <button onclick="window.print()" class="bg-gray-200 text-gray-800 px-6 py-3 rounded-md font-semibold hover:bg-gray-300 transition duration-300 flex items-center justify-center">
                    <i class="fas fa-print mr-2"></i> Print Receipt
                </button>

                <?php if ($order_db_id): ?>
                    <a href="order_status.php?id=<?= $order_db_id ?>" class="bg-blue-600 text-white px-6 py-3 rounded-md font-semibold hover:bg-blue-700 transition duration-300 text-center">
                        <i class="fas fa-eye mr-2"></i> Track Order Status
                    </a>
                <?php endif; ?>

                <a href="home.php" class="bg-gray-900 text-white px-6 py-3 rounded-md font-semibold hover:bg-gray-800 transition duration-300 text-center">
                    <i class="fas fa-home mr-2"></i> Back to Home
                </a>
            </div>
        </div>

        <!-- Overlay for when menu is open -->
        <div id="overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden"></div>

        <script>
            // Auto-clear session data after successful order (optional)
            <?php if (isset($order_db_id) && $order_db_id): ?>
                setTimeout(function() {
                    // Optional: Clear some session data to free up memory
                    fetch('clear_order_session.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            order_id: <?= $order_db_id ?>
                        })
                    });
                }, 30000); // Clear after 30 seconds
            <?php endif; ?>

            // Enhanced print functionality
            function printReceipt() {
                const printWindow = window.open('', '_blank');
                const printContent = document.querySelector('.container').innerHTML;

                printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Receipt - CRUZ Coffee</title>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 20px; }
                        .text-center { text-align: center; }
                        .font-bold { font-weight: bold; }
                        .mb-4 { margin-bottom: 1rem; }
                        .py-2 { padding: 0.5rem 0; }
                        .border-t { border-top: 1px solid #ccc; margin-top: 1rem; padding-top: 1rem; }
                        .flex { display: flex; }
                        .justify-between { justify-content: space-between; }
                        .no-print { display: none !important; }
                    </style>
                </head>
                <body>${printContent}</body>
                </html>
            `);

                printWindow.document.close();
                printWindow.focus();
                printWindow.print();
                printWindow.close();
            }
        </script>
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
    </div>
</body>

</html>