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

// Proses pembayaran jika tombol Pay Now ditekan
if (isset($_POST['pay_now']) && isset($_POST['payment_method'])) {
    $payment_method = $_POST['payment_method'];
    
    // Simpan metode pembayaran ke session (opsional)
    $_SESSION['payment_method'] = $payment_method;
    
    // Redirect ke halaman konfirmasi atau proses pembayaran
    header("Location: kodepembayaran.php");
    exit();
}

// Daftar metode pembayaran yang tersedia
$payment_methods = [
    ['id' => 'qris', 'name' => 'QRIS', 'image' => 'image/logoqris.png'],
    ['id' => 'bni', 'name' => 'BNI', 'image' => 'image/logobni.png'],
    ['id' => 'bca', 'name' => 'BCA', 'image' => 'image/logobca.png'],
    ['id' => 'ovo', 'name' => 'OVO', 'image' => 'image/logoovo.png'],
    ['id' => 'gopay', 'name' => 'GoPay', 'image' => 'image/logogopay.png'],
    ['id' => 'shopeepay', 'name' => 'ShopeePay', 'image' => 'image/logospay.png']
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Methods - CRUZ Coffee & Work Space</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <style>
        .payment-option {
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .payment-option.selected {
            border: 2px solid #000;
            background-color: rgba(0, 0, 0, 0.05);
        }
        .payment-option:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="bg-yellow-100 text-gray-800">
    <div class="container mx-auto p-5">
        <!-- Header -->
        <header class="flex justify-between items-center mb-8">
            <a href="home.php">
                <img alt="CRUZ logo" src="image/logocruz.png" width="200" />
            </a>
            <div class="flex items-center space-x-4">
                <i class="fas fa-bars text-3xl cursor-pointer"></i>
            </div>
        </header>

        <!-- Payment Methods Title -->
        <h1 class="text-5xl font-bold mb-8">Payment Methods</h1>

        <form method="post" action="" id="payment-form">
            <input type="hidden" id="selected_method" name="payment_method" value="">
            
            <!-- Payment Methods Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-8">
                <?php foreach ($payment_methods as $method): ?>
                <div class="payment-option bg-white rounded-lg p-4 flex items-center justify-center h-32" 
                     data-method="<?= $method['id'] ?>"
                     onclick="selectPaymentMethod('<?= $method['id'] ?>')">
                    <img src="<?= $method['image'] ?>" alt="<?= $method['name'] ?>" class="max-h-20 max-w-full">
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Payment Button -->
            <div class="flex justify-center">
                <button type="submit" name="pay_now" id="pay-button" 
                        class="bg-gray-900 text-white px-8 py-3 rounded-md font-semibold hover:bg-gray-800 transition duration-300 opacity-50 cursor-not-allowed" disabled>
                    Pay Now
                </button>
            </div>
        </form>
        
        <!-- Back Button -->
        <div class="mt-8 text-center">
            <a href="pembayaran.php" class="text-blue-600 hover:underline">
                <i class="fas fa-arrow-left mr-1"></i> Back to Cart Summary
            </a>
        </div>
    </div>

    <script>
        function selectPaymentMethod(methodId) {
            // Remove selected class from all options
            document.querySelectorAll('.payment-option').forEach(option => {
                option.classList.remove('selected');
            });
            
            // Add selected class to clicked option
            document.querySelector(`.payment-option[data-method="${methodId}"]`).classList.add('selected');
            
            // Set hidden input value
            document.getElementById('selected_method').value = methodId;
            
            // Enable the pay button
            const payButton = document.getElementById('pay-button');
            payButton.classList.remove('opacity-50', 'cursor-not-allowed');
            payButton.disabled = false;
        }
    </script>
</body>
</html>