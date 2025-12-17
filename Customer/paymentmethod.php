<?php
include 'koneksi.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['keranjang']) || empty($_SESSION['keranjang'])) {
    header("Location: halamanmenu.php");
    exit();
}
if (isset($_POST['pay_now']) && isset($_POST['payment_method'])) {
    $_SESSION['payment_method'] = $_POST['payment_method'];
    header("Location: kodepembayaran.php");
    exit();
}
$payment_methods = [
    ['id' => 'qris', 'name' => 'QRIS', 'image' => '../image/logoqris.png'],
    ['id' => 'bni', 'name' => 'BNI', 'image' => '../image/logobni.png'],
    ['id' => 'bca', 'name' => 'BCA', 'image' => '../image/logobca.png'],
    ['id' => 'ovo', 'name' => 'OVO', 'image' => '../image/logoovo.png'],
    ['id' => 'gopay', 'name' => 'GoPay', 'image' => '../image/logogopay.png'],
    ['id' => 'shopeepay', 'name' => 'ShopeePay', 'image' => '../image/logospay.png']
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Method - CRUZ Coffee</title>
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
    <style>
        .payment-card.selected {
            border-color: #C5A065;
            background-color: #FDFBF7;
        }

        .payment-card.selected img {
            filter: grayscale(0);
        }
    </style>
</head>

<body class="bg-cruz-cream text-cruz-charcoal font-sans antialiased min-h-screen flex items-center justify-center p-6">

    <div class="bg-white w-full max-w-4xl shadow-2xl p-10 md:p-14 relative">
        <div class="text-center mb-12">
            <h1 class="text-3xl font-serif text-cruz-black italic mb-2">Payment Method</h1>
            <p class="text-xs uppercase tracking-widest text-gray-400">Secure Transaction</p>
        </div>

        <form method="post" action="" id="payment-form">
            <input type="hidden" id="selected_method" name="payment_method" value="">

            <div class="grid grid-cols-2 md:grid-cols-3 gap-6 mb-12">
                <?php foreach ($payment_methods as $method): ?>
                    <div class="payment-card group border border-gray-200 p-8 flex items-center justify-center h-32 cursor-pointer hover:border-cruz-gold transition duration-300"
                        data-method="<?= $method['id'] ?>" onclick="selectPaymentMethod('<?= $method['id'] ?>')">
                        <img src="<?= $method['image'] ?>" alt="<?= $method['name'] ?>"
                            class="max-h-12 w-auto filter grayscale opacity-60 group-hover:grayscale-0 group-hover:opacity-100 transition duration-500">
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="text-center">
                <button type="submit" name="pay_now" id="pay-button"
                    class="px-16 py-4 bg-gray-200 text-gray-400 text-xs uppercase tracking-[0.2em] transition duration-300 cursor-not-allowed"
                    disabled>
                    Complete Payment
                </button>
                <div class="mt-6">
                    <a href="pembayaran.php"
                        class="text-xs uppercase tracking-widest text-gray-400 hover:text-cruz-charcoal transition">
                        Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>

    <script>
        function selectPaymentMethod(methodId) {
            document.querySelectorAll('.payment-card').forEach(option => {
                option.classList.remove('selected');
            });
            const selected = document.querySelector(`.payment-card[data-method="${methodId}"]`);
            selected.classList.add('selected');

            document.getElementById('selected_method').value = methodId;

            const payButton = document.getElementById('pay-button');
            payButton.classList.remove('bg-gray-200', 'text-gray-400', 'cursor-not-allowed');
            payButton.classList.add('bg-cruz-black', 'text-white', 'hover:bg-cruz-gold', 'shadow-xl');
            payButton.disabled = false;
        }
    </script>

</body>

</html>