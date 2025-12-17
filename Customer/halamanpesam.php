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
        $deskripsi = isset($row['deskripsi']) ? $row['deskripsi'] : 'Experience the rich flavors and delicate notes carefully crafted for your pleasure.';
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
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $nama_menu; ?> - CRUZ Coffee</title>
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

<body class="bg-cruz-cream text-cruz-charcoal font-sans antialiased h-screen overflow-hidden flex flex-col md:flex-row">

    <!-- Left Section: Image (Full Height) -->
    <div class="h-1/2 md:h-full md:w-1/2 relative bg-gray-200">
        <img src="data:image/jpeg;base64,<?php echo base64_encode($gambar); ?>" alt="<?php echo $nama_menu; ?>"
            class="w-full h-full object-cover">

        <!-- Back Button Overlay -->
        <a href="halamanmenu.php"
            class="absolute top-8 left-8 text-white hover:text-cruz-gold transition duration-300 z-10 flex items-center bg-black/20 p-2 rounded-full backdrop-blur-sm">
            <i class="fas fa-arrow-left mr-2"></i> <span class="text-xs uppercase tracking-widest">Back</span>
        </a>
    </div>

    <!-- Right Section: Details (Scrollable) -->
    <div class="h-1/2 md:h-full md:w-1/2 bg-white flex flex-col overflow-y-auto">

        <!-- Navbar Placeholder (Desktop) -->
        <nav class="hidden md:flex justify-end p-8">
            <a href="keranjang.php" class="relative text-cruz-charcoal hover:text-cruz-gold transition duration-300">
                <i class="fas fa-shopping-bag text-lg"></i>
                <?php
                if (session_status() == PHP_SESSION_NONE) {
                    session_start();
                }
                if (isset($_SESSION['keranjang']) && count($_SESSION['keranjang']) > 0): ?>
                    <span class="absolute -top-1 -right-1 bg-cruz-gold w-2 h-2 rounded-full"></span>
                <?php endif; ?>
            </a>
        </nav>

        <div class="flex-grow flex flex-col justify-center px-8 md:px-20 py-12">
            <span class="text-cruz-gold uppercase tracking-[0.2em] text-xs font-bold mb-4 block">Premium
                Selection</span>

            <h1 class="text-4xl md:text-5xl font-serif text-cruz-black mb-6 leading-tight italic">
                <?php echo $nama_menu; ?>
            </h1>

            <p class="text-3xl font-light text-cruz-charcoal mb-8">
                IDR <?php echo number_format($harga, 0, ',', '.'); ?>
            </p>

            <div class="w-16 h-[1px] bg-gray-200 mb-8"></div>

            <p class="text-gray-500 font-light leading-relaxed mb-12 text-lg">
                <?php echo $deskripsi; ?>
            </p>

            <form action="keranjang.php" method="POST" class="space-y-8">
                <input type="hidden" name="id_menu" value="<?php echo $id_menu; ?>">

                <!-- Quantity -->
                <div class="flex items-center space-x-4">
                    <span class="text-xs uppercase tracking-widest text-gray-400">Quantity</span>
                    <div class="flex items-center border-b border-gray-300 pb-1">
                        <button type="button" class="text-gray-400 hover:text-cruz-black px-2 transition"
                            onclick="decrementQuantity()">-</button>
                        <input type="text" name="jumlah" id="jumlah" value="1"
                            class="w-8 text-center bg-transparent border-none font-serif text-xl outline-none" readonly>
                        <button type="button" class="text-gray-400 hover:text-cruz-black px-2 transition"
                            onclick="incrementQuantity()">+</button>
                    </div>
                </div>

                <!-- Add Button -->
                <button type="submit"
                    class="w-full md:w-auto px-12 py-5 bg-cruz-charcoal text-white text-xs uppercase tracking-[0.2em] hover:bg-cruz-gold transition-all duration-300 shadow-xl">
                    Add to Order
                </button>
            </form>
        </div>
    </div>

    <script>
        function decrementQuantity() {
            const input = document.getElementById('jumlah');
            let value = parseInt(input.value);
            if (value > 1) {
                value--;
                input.value = value;
            }
        }

        function incrementQuantity() {
            const input = document.getElementById('jumlah');
            let value = parseInt(input.value);
            value++;
            input.value = value;
        }
    </script>
</body>

</html>