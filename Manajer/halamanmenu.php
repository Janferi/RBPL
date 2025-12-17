<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

$success = isset($_GET['success']) ? htmlspecialchars($_GET['success']) : '';
$error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';

$category = isset($_GET['category']) ? $_GET['category'] : 'all';
$query_menu = "SELECT * FROM menu";
if ($category !== 'all') {
    $query_menu .= " WHERE category = '" . mysqli_real_escape_string($koneksi, $category) . "'";
}
$query_menu .= " ORDER BY id DESC";
$result_menu = mysqli_query($koneksi, $query_menu);

$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Menu - CRUZ Manager</title>
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

<body class="bg-cruz-cream text-cruz-charcoal font-sans antialiased min-h-screen">

    <!-- Navbar -->
    <nav class="bg-white border-b border-gray-100 py-5 sticky top-0 z-50">
        <div class="container mx-auto px-6 flex justify-between items-center">
            <a href="halamanoption.php"
                class="text-xl font-serif font-bold text-cruz-charcoal tracking-widest uppercase">
                CRUZ.
            </a>
            <div class="flex items-center space-x-6">
                <span
                    class="text-xs text-gray-400 uppercase tracking-widest hidden md:inline"><?= htmlspecialchars($username) ?></span>
                <a href="?logout=true" onclick="return confirm('Keluar?')"
                    class="text-xs uppercase tracking-widest text-gray-500 hover:text-red-500 transition">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-6 py-12">
        <!-- Header & Actions -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-4">
            <div>
                <h1 class="text-3xl font-serif text-cruz-black mb-1">Menu Collection</h1>
                <p class="text-gray-400 text-xs uppercase tracking-widest">Kelola Daftar Menu</p>
            </div>
            <a href="detailTambahMenu.php"
                class="px-6 py-3 bg-cruz-charcoal text-white text-xs uppercase tracking-widest hover:bg-cruz-gold transition">
                <i class="fas fa-plus mr-2"></i> Tambah Menu
            </a>
        </div>

        <!-- Alerts -->
        <?php if ($success): ?>
            <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 text-sm">
                <i class="fas fa-check-circle mr-2"></i><?= $success ?>
            </div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 text-sm">
                <i class="fas fa-exclamation-triangle mr-2"></i><?= $error ?>
            </div>
        <?php endif; ?>

        <!-- Filters -->
        <div class="flex flex-wrap gap-3 mb-10">
            <a href="?category=all"
                class="<?= ($category === 'all') ? 'bg-cruz-charcoal text-white' : 'bg-white text-gray-500 border border-gray-200 hover:border-cruz-gold' ?> px-4 py-2 text-xs uppercase tracking-widest transition">All</a>
            <?php
            $cat_query = "SELECT DISTINCT category FROM menu ORDER BY category";
            $cat_result = mysqli_query($koneksi, $cat_query);
            if ($cat_result) {
                while ($cat_row = mysqli_fetch_assoc($cat_result)) {
                    $cat_name = $cat_row['category'];
                    $is_active = ($category == $cat_name);
                    ?>
                    <a href="?category=<?= urlencode($cat_name) ?>"
                        class="<?= $is_active ? 'bg-cruz-charcoal text-white' : 'bg-white text-gray-500 border border-gray-200 hover:border-cruz-gold' ?> px-4 py-2 text-xs uppercase tracking-widest transition"><?= htmlspecialchars($cat_name) ?></a>
                <?php }
            } ?>
        </div>

        <!-- Menu Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
            <?php if ($result_menu && mysqli_num_rows($result_menu) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result_menu)): ?>
                    <div class="bg-white border border-gray-100 group hover:shadow-xl transition duration-500 relative">
                        <!-- Golden Accent -->
                        <div class="absolute top-0 left-0 w-1 h-full bg-cruz-gold opacity-0 group-hover:opacity-100 transition">
                        </div>

                        <!-- Image -->
                        <div class="w-full aspect-square bg-[#F9F9F7] flex items-center justify-center p-6 overflow-hidden">
                            <?php if (!empty($row['gambar'])): ?>
                                <img src="data:image/jpeg;base64,<?= base64_encode($row['gambar']) ?>"
                                    alt="<?= htmlspecialchars($row['nama']) ?>"
                                    class="max-w-full max-h-full object-contain transition duration-700 group-hover:scale-110">
                            <?php else: ?>
                                <i class="fas fa-image text-4xl text-gray-300"></i>
                            <?php endif; ?>
                        </div>

                        <!-- Info -->
                        <div class="p-6 text-center">
                            <h3 class="text-lg font-serif text-cruz-black mb-1"><?= htmlspecialchars($row['nama']) ?></h3>
                            <p class="text-cruz-gold font-light text-sm mb-4">IDR
                                <?= number_format($row['harga'], 0, ',', '.') ?></p>

                            <div class="flex justify-center gap-3">
                                <a href="detailEdit.php?id=<?= $row['id'] ?>"
                                    class="px-4 py-2 bg-gray-100 text-gray-600 text-xs uppercase tracking-widest hover:bg-cruz-gold hover:text-white transition">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="deletemenu.php?id=<?= $row['id'] ?>"
                                    onclick="return confirm('Hapus menu <?= htmlspecialchars($row['nama']) ?>?')"
                                    class="px-4 py-2 bg-red-50 text-red-500 text-xs uppercase tracking-widest hover:bg-red-500 hover:text-white transition">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-span-full text-center py-20 text-gray-400">
                    <i class="fas fa-utensils text-4xl mb-4"></i>
                    <p class="font-light">Belum ada menu di kategori ini.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer class="text-center py-8 text-[10px] text-gray-400 uppercase tracking-widest">
        &copy; 2024 CRUZ Coffee
    </footer>

</body>

</html>