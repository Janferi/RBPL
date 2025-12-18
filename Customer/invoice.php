<?php
require_once '../security_headers.php';
// Koneksi ke database
include 'koneksi.php';
// Inisialisasi session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Check Order ID
$order_id = isset($_SESSION['id_pesanan']) ? $_SESSION['id_pesanan'] : (isset($_GET['order_id']) ? $_GET['order_id'] : null);
if (!$order_id) {
    header("Location: home.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Invoice #<?= $order_id ?> - CRUZ Coffee</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Manrope:wght@200;300;400;500;600&display=swap"
        rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'cruz-black': '#121212',
                        'cruz-charcoal': '#1a1a1a',
                        'cruz-gold': '#C5A065',
                        'cruz-cream': '#F5F5F0',
                    },
                    fontFamily: {
                        'serif': ['"Playfair Display"', 'serif'],
                        'sans': ['"Manrope"', 'sans-serif'],
                    },
                }
            }
        }
    </script>
</head>

<body class="bg-gray-100 font-sans antialiased min-h-screen py-10 px-4">

    <div class="max-w-xl mx-auto bg-white p-10 md:p-16 shadow-2xl relative print:shadow-none print:max-w-none">
        <!-- Watermark -->
        <h1
            class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-9xl font-serif text-gray-50 opacity-20 pointer-events-none italic">
            CRUZ.</h1>

        <!-- Header -->
        <div class="flex justify-between items-start mb-16 relative z-10">
            <div>
                <h1 class="text-2xl font-serif font-bold text-cruz-black tracking-widest uppercase mb-4">CRUZ.</h1>
                <p class="text-[10px] uppercase tracking-widest text-gray-400">Premium Coffee House</p>
                <p class="text-[10px] uppercase tracking-widest text-gray-400">Jakarta, Indonesia</p>
            </div>
            <div class="text-right">
                <h2 class="text-4xl font-serif text-cruz-black mb-2">Invoice</h2>
                <p class="font-mono text-sm text-gray-500">#<?= str_pad($order_id, 6, '0', STR_PAD_LEFT) ?></p>
                <p class="text-xs text-gray-400 mt-1"><?= date('F d, Y') ?></p>
            </div>
        </div>

        <!-- Content -->
        <div class="mb-16 relative z-10">
            <p class="text-xs uppercase tracking-widest text-gray-400 mb-6 border-b border-gray-100 pb-2">Bill To</p>
            <p class="text-lg font-serif italic text-cruz-black">Valued Customer</p>
        </div>

        <table class="w-full text-left mb-16 relative z-10">
            <thead>
                <tr class="text-[10px] uppercase tracking-widest text-gray-400 border-b border-cruz-black">
                    <th class="pb-4 font-normal">Item Description</th>
                    <th class="pb-4 font-normal text-right">Amount</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <!-- Ideally Loop items here -->
                <tr>
                    <td class="py-4 text-sm text-gray-600">Coffee Selection & Pastries (Bundle)</td>
                    <td class="py-4 text-right text-sm font-light text-cruz-black">
                        <?php
                        if (isset($_SESSION['total_harga']))
                            echo 'IDR ' . number_format($_SESSION['total_harga'], 0, ',', '.');
                        else
                            echo 'IDR 0';
                        ?>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Total -->
        <div class="flex justify-end mb-20 relative z-10">
            <div class="w-1/2">
                <div class="flex justify-between mb-4 text-xs">
                    <span class="text-gray-400 uppercase tracking-widest">Subtotal</span>
                    <span class="font-light"><?php if (isset($_SESSION['total_harga']))
                        echo number_format($_SESSION['total_harga'], 0, ',', '.'); ?></span>
                </div>
                <div class="flex justify-between text-xl font-serif text-cruz-black border-t border-gray-200 pt-4">
                    <span>Total</span>
                    <span><?php if (isset($_SESSION['total_harga']))
                        echo 'IDR ' . number_format($_SESSION['total_harga'], 0, ',', '.'); ?></span>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center relative z-10 print:hidden">
            <button onclick="window.print()"
                class="px-8 py-3 bg-cruz-black text-white text-xs uppercase tracking-widest hover:bg-cruz-gold transition mr-4">
                Print Invoice
            </button>
            <a href="home.php"
                class="px-8 py-3 border border-gray-200 text-cruz-charcoal text-xs uppercase tracking-widest hover:border-cruz-black transition">
                Back Home
            </a>
        </div>
    </div>

</body>

</html>