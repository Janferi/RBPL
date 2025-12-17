<?php
// Koneksi ke database
include 'koneksi.php';
// Inisialisasi session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Redirect
if (!isset($_SESSION['keranjang']) || empty($_SESSION['keranjang'])) {
    header("Location: halamanmenu.php");
    exit();
}

// Hitung total harga
$total_harga = 0;
$items = array();
foreach ($_SESSION['keranjang'] as $id_menu => $jumlah) {
    $query = "SELECT * FROM menu WHERE id = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "i", $id_menu);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_assoc($result)) {
        $subtotal = $row['harga'] * $jumlah;
        $total_harga += $subtotal;
        $items[] = array('nama' => $row['nama'], 'jumlah' => $jumlah, 'subtotal' => $subtotal);
    }
}
$_SESSION['total_harga'] = $total_harga;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - CRUZ Coffee</title>
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

<body class="bg-gray-50 text-cruz-charcoal font-sans antialiased min-h-screen flex items-center justify-center p-6">

    <div class="bg-white w-full max-w-lg shadow-2xl p-10 md:p-14 relative overflow-hidden">
        <!-- Decor -->
        <div class="absolute top-0 left-0 w-full h-2 bg-cruz-gold"></div>

        <div class="text-center mb-10">
            <h2 class="text-xs uppercase tracking-[0.3em] text-gray-400 mb-2">Final Step</h2>
            <h1 class="text-3xl font-serif text-cruz-black italic">Confirm Order.</h1>
        </div>

        <div class="space-y-4 mb-10 border-t border-b border-gray-100 py-6">
            <?php foreach ($items as $item): ?>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-600"><span class="font-bold text-cruz-black mr-2"><?= $item['jumlah'] ?>x</span>
                        <?= $item['nama'] ?></span>
                    <span class="font-light">IDR <?= number_format($item['subtotal'], 0, ',', '.') ?></span>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="flex justify-between items-end mb-10">
            <span class="text-xs uppercase tracking-widest text-gray-400">Total Amount</span>
            <span class="text-2xl font-serif text-cruz-black">IDR <?= number_format($total_harga, 0, ',', '.') ?></span>
        </div>

        <a href="paymentmethod.php"
            class="block w-full text-center bg-cruz-black text-white py-4 text-xs uppercase tracking-[0.2em] hover:bg-cruz-gold transition duration-500 shadow-xl mb-4">
            Proceed to Payment
        </a>

        <a href="keranjang.php"
            class="block w-full text-center text-xs uppercase tracking-widest text-gray-400 hover:text-cruz-charcoal transition">
            Modify Order
        </a>
    </div>

</body>

</html>