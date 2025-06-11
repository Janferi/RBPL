<?php
// Koneksi ke database
include 'koneksi.php';

// Ambil kategori yang dipilih (default: all)
$category = isset($_GET['category']) ? $_GET['category'] : 'all';

// Persiapkan query dengan prepared statement untuk keamanan
if ($category == 'all') {
    $query = "SELECT * FROM menu";
    $result = mysqli_query($koneksi, $query);
} else {
    $stmt = mysqli_prepare($koneksi, "SELECT * FROM menu WHERE category = ?");
    mysqli_stmt_bind_param($stmt, "s", $category);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
}

// Periksa apakah query berhasil
if (!$result) {
    die("Query error: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>CRUZ Coffee & Work Space</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
</head>

<body class="bg-yellow-100 text-gray-800">
    <div class="container mx-auto p-5 relative">
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

        <!-- Navigation -->
        <nav class="flex justify-center mb-4 flex-wrap">
            <a href="?category=all">
                <button class="<?= ($category == 'all') ? 'bg-yellow-300' : 'bg-transparent border' ?> px-4 py-2 rounded-full mx-1 transition duration-300 ease-in-out hover:bg-yellow-200">
                    All
                </button>
            </a>
            <?php
            // Ambil kategori unik dari database
            $cat_query = "SELECT DISTINCT category FROM menu";
            $cat_result = mysqli_query($koneksi, $cat_query);

            if ($cat_result) {
                while ($cat_row = mysqli_fetch_assoc($cat_result)) {
                    $cat_name = $cat_row['category'];
                    $active_class = ($category == $cat_name) ? 'bg-yellow-300' : 'bg-transparent border';
                    echo '<a href="?category=' . urlencode($cat_name) . '" class="m-1">';
                    echo '<button class="' . $active_class . ' px-4 py-2 rounded-full mx-1 transition duration-300 ease-in-out hover:bg-yellow-200">';
                    echo htmlspecialchars($cat_name);
                    echo '</button>';
                    echo '</a>';
                }
            }
            ?>
        </nav>

        <!-- Daftar Menu -->
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <?php
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    // Ambil data sesuai nama kolom di database
                    $name = htmlspecialchars($row['nama']);
                    $price = number_format($row['harga'], 0, ',', '.');
            ?>
                    <div class="bg-transparent border border-black p-4 rounded-lg shadow-md transition-transform transform hover:scale-105">
                        <img src="data:image/jpeg;base64,<?= base64_encode($row['gambar']) ?>"
                            alt="<?= $name ?>"
                            class="w-40 h-40 object-contain mx-auto rounded-md" />
                        <h2 class="text-lg font-semibold"><?= $name ?></h2>
                        <p class="text-gray-600">Rp<?= $price ?></p>
                        <a href="halamanpesam.php?id=<?= $row['id'] ?>">
                            <button class="bg-yellow-300 p-3 w-10 h-10 rounded-full flex items-center justify-center ease-in-out hover:bg-yellow-400">
                                <i class="fas fa-plus"></i>
                            </button>
                        </a>
                    </div>
            <?php
                }
            } else {
                echo '<div class="col-span-full text-center py-8 text-gray-500">Menu tidak tersedia.</div>';
            }
            ?>
        </section>
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
</body>

</html>