<?php
include 'koneksi.php'; // Menghubungkan ke database

// Pastikan id_menu tersedia di URL
if (isset($_GET['id'])) {
    $id_menu = $_GET['id'];

    // Query untuk mengambil detail menu
    $query = "SELECT * FROM menu WHERE id = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "i", $id_menu);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        $nama_menu = $row['nama'];
        $deskripsi = isset($row['deskripsi']) ? $row['deskripsi'] : '';
        $harga = $row['harga'];
        $gambar = $row['gambar'];
    } else {
        echo "<script>alert('Menu tidak ditemukan'); window.location='halamanmenu.php';</script>";
        exit();
    }
} else {
    echo "<script>alert('ID Menu tidak valid'); window.location='halamanmenu.php';</script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $nama_menu; ?> - CRUZ Coffee & Work Space</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <style>
        body {
            font-family: 'poppins', sans-serif;
        }

        h1,
        h3 {
            font-family: 'poppins', sans-serif;
        }
    </style>
</head>

<body class="bg-yellow-50 text-gray-800">
    <div class="container mx-auto p-5">
        <!-- Header -->
        <header class="flex justify-between items-center mb-4">
            <a href="home.php">
                <img alt="CRUZ logo" src="image/logocruz.png" width="200" />
            </a>
            <div class="flex items-center space-x-4">
                <a href="keranjang.php">
                    <div class="cart-icon">
                        <i class="fas fa-shopping-cart fa-2x text-dark"></i>
                    </div>
                </a>
                <i class="fas fa-bars text-3xl cursor-pointer"></i>
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
        
        <!-- Content -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between bg-white shadow-lg rounded-lg p-6">
            <div class="md:w-1/2 space-y-4">
                <h1 class="text-8xl font-bold text-black"><?php echo $nama_menu; ?></h1>
                <br>
                <p class="text-2xl text-gray-700"><?php echo $deskripsi; ?></p>
                <br>
                <h3 class="text-3xl font-bold mt-4 text-black-500">Rp<?php echo number_format($harga, 0, ',', '.'); ?></h3>

                <div class="flex items-center space-x-4 mt-4">
                    <div class="flex items-center border border-gray-300 rounded">
                        <button class="px-4 py-2 text-xl text-yellow-600 hover:bg-yellow-200" onclick="decrementQuantity()">-</button>
                        <input type="text" id="quantity" value="1" class="w-12 text-center border-none" readonly>
                        <button class="px-4 py-2 text-xl text-yellow-600 hover:bg-yellow-200" onclick="incrementQuantity()">+</button>
                    </div>

                    <form action="keranjang.php" method="POST" class="flex">
                        <input type="hidden" name="id_menu" value="<?php echo $id_menu; ?>">
                        <input type="hidden" name="jumlah" id="jumlah_form" value="1">
                        <button type="submit" class="bg-yellow-300 px-4 py-2 rounded flex items-center justify-center ease-in-out hover:bg-yellow-400 transition duration-200">
                            <i class="fas fa-shopping-cart mr-2"></i> Add Food
                        </button>
                    </form>
                </div>

                <div class="mt-4">
                    <a href="halamanmenu.php" class="text-blue-600 hover:underline">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali ke Menu
                    </a>
                </div>
            </div>

            <div class="md:w-1/3 mt-4 md:mt-0">
                <img src="data:image/jpeg;base64,<?php echo base64_encode($gambar); ?>"
                    alt="<?php echo $nama_menu; ?>"
                    class="w-full rounded-lg object-cover shadow-md">
            </div>
        </div>
    </div>

    <script>
        function decrementQuantity() {
            const input = document.getElementById('quantity');
            const hiddenInput = document.getElementById('jumlah_form');
            let value = parseInt(input.value);
            if (value > 1) {
                value--;
                input.value = value;
                hiddenInput.value = value;
            }
        }

        function incrementQuantity() {
            const input = document.getElementById('quantity');
            const hiddenInput = document.getElementById('jumlah_form');
            let value = parseInt(input.value);
            value++;
            input.value = value;
            hiddenInput.value = value;
        }
    </script>
</body>

</html>