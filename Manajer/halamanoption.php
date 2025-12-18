<?php
require_once '../security_headers.php';
session_start();
include 'koneksi.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
if (isset($_GET['logout'])) {
    secure_session_destroy();
    header("Location: login.php");
    exit();
}
$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - CRUZ Manager</title>
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
    <nav class="bg-white border-b border-gray-100 py-5">
        <div class="container mx-auto px-6 flex justify-between items-center">
            <a href="halamanoption.php"
                class="text-xl font-serif font-bold text-cruz-charcoal tracking-widest uppercase">
                CRUZ.
            </a>
            <div class="flex items-center space-x-6">
                <span class="text-xs text-gray-400 uppercase tracking-widest hidden md:inline">
                    <i class="fas fa-user mr-2"></i> <?= htmlspecialchars($username) ?>
                </span>
                <a href="?logout=true" onclick="return confirm('Keluar dari sistem?')"
                    class="text-xs uppercase tracking-widest text-gray-500 hover:text-red-500 transition">
                    Logout
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container mx-auto px-6 py-16 md:py-24">
        <div class="text-center mb-16">
            <span class="text-cruz-gold uppercase tracking-[0.2em] text-xs font-bold mb-4 block">Control Panel</span>
            <h1 class="text-4xl md:text-5xl font-serif text-cruz-black italic">Welcome,
                <?= htmlspecialchars($username) ?>.
            </h1>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-4xl mx-auto">
            <!-- Menu Card -->
            <a href="halamanmenu.php"
                class="group bg-white p-10 text-center shadow-sm border border-gray-100 hover:shadow-xl hover:border-cruz-gold transition duration-500 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-1 h-full bg-cruz-gold opacity-0 group-hover:opacity-100 transition">
                </div>
                <i class="fas fa-utensils text-3xl text-cruz-gold mb-6"></i>
                <h3 class="text-lg font-serif text-cruz-black mb-2">Menu</h3>
                <p class="text-gray-400 text-xs uppercase tracking-widest">Kelola Daftar Menu</p>
            </a>

            <!-- Add Menu Card -->
            <a href="detailTambahMenu.php"
                class="group bg-white p-10 text-center shadow-sm border border-gray-100 hover:shadow-xl hover:border-cruz-gold transition duration-500 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-1 h-full bg-cruz-gold opacity-0 group-hover:opacity-100 transition">
                </div>
                <i class="fas fa-plus-circle text-3xl text-cruz-gold mb-6"></i>
                <h3 class="text-lg font-serif text-cruz-black mb-2">Tambah</h3>
                <p class="text-gray-400 text-xs uppercase tracking-widest">Tambah Menu Baru</p>
            </a>

            <!-- Reports Card -->
            <a href="laporan.php"
                class="group bg-white p-10 text-center shadow-sm border border-gray-100 hover:shadow-xl hover:border-cruz-gold transition duration-500 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-1 h-full bg-cruz-gold opacity-0 group-hover:opacity-100 transition">
                </div>
                <i class="fas fa-chart-line text-3xl text-cruz-gold mb-6"></i>
                <h3 class="text-lg font-serif text-cruz-black mb-2">Laporan</h3>
                <p class="text-gray-400 text-xs uppercase tracking-widest">Unduh Laporan</p>
            </a>
        </div>
    </main>

    <!-- Footer -->
    <footer class="text-center py-8 text-xs text-gray-400 uppercase tracking-widest">
        &copy; 2024 CRUZ Coffee - Manager Panel
    </footer>

</body>

</html>