<?php
require_once '../security_headers.php';
// Koneksi ke database
include 'koneksi.php';

// Ambil kategori yang dipilih (default: all)
$category = isset($_GET['category']) ? $_GET['category'] : 'all';

// Persiapkan query
if ($category == 'all') {
    $query = "SELECT * FROM menu";
    $stmt = mysqli_prepare($koneksi, $query);
} else {
    $stmt = mysqli_prepare($koneksi, "SELECT * FROM menu WHERE category = ?");
    mysqli_stmt_bind_param($stmt, "s", $category);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Collection - CRUZ Coffee</title>
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

<body class="bg-cruz-cream text-cruz-charcoal font-sans antialiased">

    <!-- Navbar (Consistent with Home) -->
    <nav
        class="sticky top-0 w-full z-50 bg-cruz-cream/80 backdrop-blur-md border-b border-gray-200 transition-all duration-300">
        <div class="container mx-auto px-6 py-5 flex justify-between items-center">
            <!-- Brand -->
            <a href="home.php"
                class="text-xl font-serif font-bold text-cruz-charcoal tracking-widest uppercase hover:text-cruz-gold transition">
                CRUZ.
            </a>

            <!-- Desktop Links -->
            <div class="hidden md:flex items-center space-x-12">
                <a href="home.php"
                    class="text-xs uppercase tracking-[0.2em] text-cruz-charcoal hover:text-cruz-gold transition duration-300">Home</a>
                <a href="halamanmenu.php"
                    class="text-xs uppercase tracking-[0.2em] text-cruz-charcoal font-bold border-b border-cruz-gold pb-1 transition duration-300">Collection</a>
                <a href="order.php"
                    class="text-xs uppercase tracking-[0.2em] text-cruz-charcoal hover:text-cruz-gold transition duration-300">Orders</a>
            </div>

            <!-- Icons -->
            <div class="flex items-center space-x-6">
                <a href="keranjang.php" aria-label="Shopping Cart"
                    class="relative text-cruz-charcoal hover:text-cruz-gold transition duration-300">
                    <i class="fas fa-shopping-bag text-lg"></i>
                    <!-- Simple dot indicator for cart -->
                    <?php
                    if (session_status() === PHP_SESSION_NONE)
                        session_start();
                    if (isset($_SESSION['keranjang']) && count($_SESSION['keranjang']) > 0): ?>
                        <span class="absolute -top-1 -right-1 bg-cruz-gold w-2 h-2 rounded-full"></span>
                    <?php endif; ?>
                </a>
                <button id="mobile-menu-btn" aria-label="Toggle Menu"
                    class="md:hidden text-cruz-charcoal focus:outline-none">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>
        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden md:hidden bg-white border-t p-6">
            <a href="home.php"
                class="block py-3 text-xs uppercase tracking-widest text-gray-600 hover:text-cruz-gold">Home</a>
            <a href="halamanmenu.php"
                class="block py-3 text-xs uppercase tracking-widest text-cruz-charcoal font-bold">Collection</a>
            <a href="order.php"
                class="block py-3 text-xs uppercase tracking-widest text-gray-600 hover:text-cruz-gold">Orders</a>
        </div>
    </nav>

    <main class="min-h-screen container mx-auto px-6 py-12 md:py-20">

        <!-- Header -->
        <div class="text-center mb-16">
            <span class="text-gray-600 uppercase tracking-[0.2em] text-xs font-bold mb-4 block">Discover Taste</span>
            <h1 class="text-4xl md:text-5xl font-serif text-cruz-black mb-6">The Collection</h1>

            <!-- Minimalist Filters -->
            <div class="flex flex-wrap justify-center gap-4 mt-8">
                <a href="?category=all"
                    class="<?= ($category == 'all') ? 'text-cruz-black border-b border-cruz-black' : 'text-gray-600 border-b border-transparent hover:text-cruz-gold' ?> px-2 py-1 text-xs uppercase tracking-widest transition duration-300">
                    All
                </a>
                <?php
                $cat_query = "SELECT DISTINCT category FROM menu";
                $cat_result = mysqli_query($koneksi, $cat_query);
                if ($cat_result) {
                    while ($cat_row = mysqli_fetch_assoc($cat_result)) {
                        $cat_name = $cat_row['category'];
                        $is_active = ($category == $cat_name);
                        ?>
                        <a href="?category=<?= urlencode($cat_name) ?>"
                            class="<?= $is_active ? 'text-cruz-black border-b border-cruz-black' : 'text-gray-600 border-b border-transparent hover:text-cruz-gold' ?> px-2 py-1 text-xs uppercase tracking-widest transition duration-300">
                            <?= htmlspecialchars($cat_name) ?>
                        </a>
                        <?php
                    }
                }
                ?>
            </div>
        </div>

        <!-- Menu Grid -->
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-x-8 gap-y-16">
            <?php
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $name = htmlspecialchars($row['nama']);
                    $price = number_format($row['harga'], 0, ',', '.');
                    ?>
                    <!-- Premium Card -->
                    <div class="group relative">
                        <!-- Image Container with Aspect Ratio -->
                        <div
                            class="relative w-full aspect-[3/4] overflow-hidden bg-[#F9F9F7] mb-6 flex items-center justify-center p-8">
                            <a href="halamanpesam.php?id=<?= $row['id'] ?>" aria-label="View details for <?= $name ?>"
                                class="w-full h-full flex items-center justify-center">
                                <img src="data:image/jpeg;base64,<?= base64_encode($row['gambar']) ?>" alt="<?= $name ?>"
                                    class="max-w-full max-h-full object-contain transition duration-700 ease-out transform group-hover:scale-110 filter grayscale-0 group-hover:contrast-105" />
                            </a>

                            <!-- Quick Add Button (Visible on Hover) -->
                            <form action="keranjang.php" method="POST"
                                class="absolute bottom-4 right-4 opacity-0 group-hover:opacity-100 transition duration-300 transform translate-y-4 group-hover:translate-y-0">
                                <?php echo csrf_token_field(); ?>
                                <input type="hidden" name="id_menu" value="<?= $row['id'] ?>">
                                <input type="hidden" name="jumlah" value="1">
                                <button type="submit" aria-label="Add <?= $name ?> to cart"
                                    class="w-10 h-10 bg-white text-cruz-black hover:bg-cruz-gold hover:text-white flex items-center justify-center rounded-full shadow-lg transition">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </form>
                        </div>

                        <!-- Info -->
                        <div class="text-center">
                            <h2
                                class="text-lg font-serif text-cruz-black mb-1 group-hover:text-cruz-gold transition duration-300">
                                <a href="halamanpesam.php?id=<?= $row['id'] ?>"><?= $name ?></a>
                            </h2>
                            <p class="text-gray-600 font-light text-sm tracking-wide">IDR <?= $price ?></p>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo '<div class="col-span-full text-center py-20 text-gray-500 font-light text-xl italic">No items found in this collection.</div>';
            }
            ?>
        </section>
    </main>

    <!-- Minimal Footer -->
    <footer class="bg-white border-t border-gray-100 py-12 text-center">
        <p class="text-cruz-charcoal text-xs uppercase tracking-widest">&copy; 2024 CRUZ Coffee</p>
    </footer>

    <script>
        const btn = document.getElementById('mobile-menu-btn');
        const menu = document.getElementById('mobile-menu');
        if (btn && menu) {
            btn.addEventListener('click', () => {
                menu.classList.toggle('hidden');
            });
        }
    </script>
</body>

</html>