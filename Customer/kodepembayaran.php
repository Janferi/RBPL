<?php
require_once '../security_headers.php';
include 'koneksi.php';
session_start();
if (!isset($_SESSION['keranjang']) || empty($_SESSION['keranjang']) || !isset($_SESSION['payment_method'])) {
    header("Location: halamanmenu.php");
    exit();
}
// ... (Logic remains same, just UI update)
$payment_method = $_SESSION['payment_method'];
$total_harga = $_SESSION['total_harga'];
// Default Setup
$kode_pembayaran = '0000 0000 0000';
$qr_image = '';
$payment_type = '';
$payment_name = strtoupper($payment_method);
$payment_mapping = [
    'qris' => ['QRIS', 'qr', '../image/qr_sample.jpeg', '../image/logoqris.png'],
    'gopay' => ['GoPay', 'qr', '../image/qr_sample.jpeg', '../image/logogopay.png'],
    'shopeepay' => ['ShopeePay', 'qr', '../image/qr_sample.jpeg', '../image/logoshopeepay.png'],
    'ovo' => ['OVO', 'qr', '../image/qr_sample.jpeg', '../image/logoovo.png'],
    'dana' => ['DANA', 'qr', '../image/qr_sample.jpeg', '../image/logodana.png'],
    'bni' => ['BNI', 'va', '0217 082 1192 8543 6', '../image/logobni.png'],
    'bca' => ['BCA', 'va', '1234 5678 9012 3456', '../image/logobca.png'],
    'bri' => ['BRI', 'va', '9876 5432 1098 7654', '../image/logobri.png'],
    'mandiri' => ['Mandiri', 'va', '1357 9024 6813 5792', '../image/logomandiri.png'],
];

// Handle edge cases like 'shopee'
if (strpos($payment_method, 'shopee') !== false)
    $payment_method = 'shopeepay';

if (isset($payment_mapping[$payment_method])) {
    $data = $payment_mapping[$payment_method];
    $payment_name = $data[0];
    $payment_type = $data[1];
    if ($payment_type == 'qr')
        $qr_image = $data[2];
    else
        $kode_pembayaran = $data[2];
    $logo_image = $data[3];
}

// Logic simpan (copied from previous)
function simpanPesanan($koneksi, $total_harga, $payment_method, $keranjang)
{
    try {
        mysqli_begin_transaction($koneksi);
        $detail_pesanan = [];
        foreach ($keranjang as $id_menu => $jumlah) {
            $query = "SELECT nama, harga FROM menu WHERE id = ?";
            $stmt = mysqli_prepare($koneksi, $query);
            mysqli_stmt_bind_param($stmt, "i", $id_menu);
            mysqli_stmt_execute($stmt);
            $r = mysqli_stmt_get_result($stmt);
            if ($row = mysqli_fetch_assoc($r)) {
                $detail_pesanan[] = ['nama' => $row['nama'], 'harga' => $row['harga'], 'jumlah' => $jumlah, 'subtotal' => $row['harga'] * $jumlah];
            }
        }
        $detail_json = json_encode($detail_pesanan);
        $d = date('Y-m-d');
        $t = date('H:i:s');
        $q = "INSERT INTO pesanan (tanggal_pesanan, waktu_pesanan, total_bayar, payment_method, detail_pesanan, status) VALUES (?, ?, ?, ?, ?, 'pending')";
        $stmt = mysqli_prepare($koneksi, $q);
        mysqli_stmt_bind_param($stmt, "ssdss", $d, $t, $total_harga, $payment_method, $detail_json);
        if (mysqli_stmt_execute($stmt)) {
            $id = mysqli_insert_id($koneksi);
            mysqli_commit($koneksi);
            return $id;
        }
        mysqli_rollback($koneksi);
        return false;
    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        return false;
    }
}

if (isset($_POST['confirm_payment'])) {
    // Validasi CSRF token
    if (csrf_validate_token()) {
        $id = simpanPesanan($koneksi, $total_harga, $payment_name, $_SESSION['keranjang']);
        if ($id) {
            $_SESSION['id_pesanan'] = $id;
            $_SESSION['payment_method_name'] = $payment_name;
            $_SESSION['payment_date'] = date('d F Y');
            $_SESSION['purchased_items'] = [];
            $_SESSION['keranjang'] = [];
            unset($_SESSION['payment_method']);
            header("Location: order.php");
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Payment Code - CRUZ Coffee</title>
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

<body class="bg-cruz-cream text-cruz-charcoal font-sans antialiased min-h-screen flex flex-col">

    <!-- Navbar -->
    <nav class="bg-white border-b border-gray-100 py-5">
        <div class="container mx-auto px-6 flex justify-between items-center">
            <a href="home.php"
                class="text-xl font-serif font-bold text-cruz-charcoal tracking-widest uppercase">CRUZ.</a>
            <a href="paymentmethod.php"
                class="text-xs uppercase tracking-widest text-gray-500 hover:text-cruz-gold transition">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </nav>

    <main class="flex-grow flex items-center justify-center p-6">
        <div class="w-full max-w-md bg-white text-cruz-charcoal p-10 shadow-xl relative">
            <!-- Golden Accent -->
            <div class="absolute top-0 left-1/2 transform -translate-x-1/2 w-16 h-1 bg-cruz-gold"></div>

            <div class="text-center mb-10 mt-4">
                <h1 class="text-2xl font-serif mb-2">Complete Payment</h1>
                <p class="text-gray-400 text-xs uppercase tracking-widest">Expires in 30:00</p>
            </div>

            <div class="mb-10 text-center">
                <p class="text-gray-400 text-xs uppercase mb-2">Total Amount</p>
                <p class="text-3xl font-serif text-cruz-black">IDR <?= number_format($total_harga, 0, ',', '.') ?></p>
            </div>

            <div class="border border-gray-200 p-8 mb-10 text-center relative overflow-hidden">
                <div
                    class="absolute top-0 left-0 bg-gray-50 text-[10px] items-center flex px-2 py-1 uppercase tracking-widest text-gray-400">
                    <?= $payment_name ?>
                </div>

                <?php if ($payment_type == 'qr'): ?>
                    <img src="<?= $qr_image ?>" class="w-48 h-48 object-contain mx-auto mb-4 mix-blend-multiply">
                    <p class="text-[10px] text-gray-400 uppercase tracking-widest">Scan to Pay</p>
                <?php else: ?>
                    <p class="text-gray-400 text-xs mb-4">Virtual Account</p>
                    <p class="text-2xl font-mono tracking-widest mb-4"><?= $kode_pembayaran ?></p>
                    <button onclick="navigator.clipboard.writeText('<?= $kode_pembayaran ?>')"
                        class="text-cruz-gold text-xs uppercase tracking-widest hover:text-black transition">
                        Copy Number
                    </button>
                <?php endif; ?>
            </div>

            <form method="post">
                <?php echo csrf_token_field(); ?>
                <button type="submit" name="confirm_payment"
                    class="w-full bg-cruz-charcoal text-white py-4 text-xs uppercase tracking-[0.2em] hover:bg-cruz-gold transition duration-500 shadow-xl">
                    I Have Paid
                </button>
            </form>

            <div class="text-center mt-6">
                <a href="paymentmethod.php"
                    class="text-[10px] uppercase tracking-widest text-gray-400 hover:text-cruz-black">Cancel
                    Transaction</a>
            </div>
        </div>
    </main>

</body>

</html>