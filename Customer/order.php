<?php
// Koneksi ke database
include 'koneksi.php';
// Inisialisasi session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Simples way to get order
$order_id_display = isset($_SESSION['id_pesanan']) ? $_SESSION['id_pesanan'] : '#';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Orders - CRUZ Coffee</title>
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
                        'cruz-gray': '#888888',
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

<body class="bg-cruz-cream text-cruz-charcoal font-sans antialiased min-h-screen">

    <!-- Navbar (Consistent) -->
    <nav class="w-full bg-white/80 backdrop-blur-md border-b border-gray-200 py-6 sticky top-0 z-50">
        <div class="container mx-auto px-6 flex justify-between items-center">
            <a href="home.php"
                class="text-xl font-serif font-bold text-cruz-charcoal tracking-widest uppercase hover:text-cruz-gold transition">
                CRUZ.
            </a>
            <div class="flex items-center space-x-8">
                <a href="home.php"
                    class="text-xs uppercase tracking-widest text-gray-500 hover:text-cruz-gold transition">Home</a>
                <a href="halamanmenu.php"
                    class="text-xs uppercase tracking-widest text-gray-500 hover:text-cruz-gold transition">Collection</a>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-6 py-16 max-w-4xl">
        <h1 class="text-4xl font-serif text-cruz-black mb-2 text-center italic">Order History</h1>
        <p class="text-center text-gray-400 text-xs uppercase tracking-widest mb-16">Track your experience</p>

        <!-- Order Card Example (Ideally looped from DB) -->
        <div
            class="bg-white p-8 md:p-12 shadow-sm border border-gray-100 mb-8 relative group hover:shadow-xl transition duration-500">
            <div
                class="absolute top-0 left-0 w-1 h-full bg-cruz-gold opacity-0 group-hover:opacity-100 transition duration-500">
            </div>

            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
                <div>
                    <span
                        class="bg-cruz-charcoal text-white text-[10px] uppercase tracking-widest px-3 py-1 mb-2 inline-block">Processing</span>
                    <h2 class="text-2xl font-serif text-cruz-black mt-2">Order #<?= $order_id_display ?></h2>
                    <p class="text-gray-400 text-sm font-light mt-1"><?= date('F d, Y') ?></p>
                </div>
                <div class="mt-4 md:mt-0 text-right">
                    <p class="text-xs uppercase tracking-widest text-gray-400 mb-1">Total</p>
                    <p class="text-xl font-serif text-cruz-black">
                        <?php
                        if (isset($_SESSION['total_harga']))
                            echo 'IDR ' . number_format($_SESSION['total_harga'], 0, ',', '.');
                        else
                            echo 'IDR 0';
                        ?>
                    </p>
                </div>
            </div>

            <div class="border-t border-gray-100 pt-8 flex flex-col md:flex-row justify-between items-center">
                <p class="text-sm font-light text-gray-500 italic mb-4 md:mb-0">
                    "Thank you for choosing CRUZ. Your order is being crafted with care."
                </p>
                <button
                    class="px-8 py-3 border border-gray-200 text-cruz-charcoal text-xs uppercase tracking-widest hover:border-cruz-black transition">
                    View Receipt
                </button>
            </div>
        </div>

        <!-- Empty State Fallback (Visual) -->
        <div class="text-center mt-12">
            <a href="halamanmenu.php"
                class="text-cruz-gold text-xs uppercase tracking-widest border-b border-cruz-gold pb-1 hover:text-cruz-black hover:border-cruz-black transition">
                Place New Order
            </a>
        </div>

    </div>

</body>

</html>