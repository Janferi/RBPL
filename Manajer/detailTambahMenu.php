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

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validasi CSRF token
    if (!csrf_validate_token()) {
        $error = "Security token tidak valid. Silakan refresh halaman.";
    } elseif (empty($_POST['nama']) || empty($_POST['category']) || empty($_POST['harga']) || empty($_POST['deskripsi'])) {
        $error = "Semua kolom wajib diisi!";
    } else {
        $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
        $category = mysqli_real_escape_string($koneksi, $_POST['category']);
        $harga = (int) $_POST['harga'];
        $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);

        $gambar = null;
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
            $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            $file_type = $_FILES['gambar']['type'];
            $file_size = $_FILES['gambar']['size'];

            if (in_array($file_type, $allowed_types) && $file_size <= 5000000) {
                $gambar = file_get_contents($_FILES['gambar']['tmp_name']);
            } else {
                $error = "Format gambar tidak didukung atau ukuran terlalu besar (max 5MB)!";
            }
        }

        if (empty($error)) {
            if ($gambar) {
                $query = "INSERT INTO menu (nama, category, harga, deskripsi, gambar) VALUES (?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($koneksi, $query);
                mysqli_stmt_bind_param($stmt, "ssiss", $nama, $category, $harga, $deskripsi, $gambar);
            } else {
                $query = "INSERT INTO menu (nama, category, harga, deskripsi) VALUES (?, ?, ?, ?)";
                $stmt = mysqli_prepare($koneksi, $query);
                mysqli_stmt_bind_param($stmt, "ssis", $nama, $category, $harga, $deskripsi);
            }

            if (mysqli_stmt_execute($stmt)) {
                $success = "Menu berhasil ditambahkan!";
                $_POST = array();
            } else {
                $error = "Gagal menambahkan menu. Silakan coba lagi.";
            }
            mysqli_stmt_close($stmt);
        }
    }
}

$query_kategori = "SELECT DISTINCT category FROM menu ORDER BY category";
$result_kategori = mysqli_query($koneksi, $query_kategori);
$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Menu - CRUZ Manager</title>
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
            <a href="halamanoption.php"
                class="text-xl font-serif font-bold text-cruz-charcoal tracking-widest uppercase">CRUZ.</a>
            <a href="?logout=true" onclick="return confirm('Keluar?')"
                class="text-xs uppercase tracking-widest text-gray-500 hover:text-red-500 transition">Logout</a>
        </div>
    </nav>

    <main class="container mx-auto px-6 py-12 flex-grow flex items-center justify-center">
        <div class="bg-white w-full max-w-lg shadow-xl p-10 relative">
            <div class="absolute top-0 left-0 w-full h-1 bg-cruz-gold"></div>

            <div class="mb-10 text-center">
                <h1 class="text-2xl font-serif text-cruz-black italic">Tambah Menu Baru</h1>
            </div>

            <?php if ($success): ?>
                <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 text-sm"><i
                        class="fas fa-check-circle mr-2"></i><?= $success ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 text-sm"><i
                        class="fas fa-exclamation-triangle mr-2"></i><?= $error ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="space-y-6">
                <?php echo csrf_token_field(); ?>
                <div>
                    <label class="block text-xs uppercase tracking-widest text-gray-400 mb-2">Nama</label>
                    <input type="text" name="nama" required
                        value="<?= isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : '' ?>"
                        class="w-full border border-gray-200 bg-cruz-cream px-4 py-3 text-sm focus:outline-none focus:border-cruz-gold transition">
                </div>
                <div>
                    <label class="block text-xs uppercase tracking-widest text-gray-400 mb-2">Kategori</label>
                    <select name="category" required
                        class="w-full border border-gray-200 bg-cruz-cream px-4 py-3 text-sm focus:outline-none focus:border-cruz-gold transition appearance-none">
                        <option value="">Pilih Kategori</option>
                        <?php if ($result_kategori && mysqli_num_rows($result_kategori) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($result_kategori)): ?>
                                <option value="<?= htmlspecialchars($row['category']) ?>">
                                    <?= htmlspecialchars($row['category']) ?>
                                </option>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <option value="Coffee">Coffee</option>
                            <option value="Non-Coffee">Non-Coffee</option>
                            <option value="Food">Food</option>
                            <option value="Snack">Snack</option>
                        <?php endif; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs uppercase tracking-widest text-gray-400 mb-2">Harga (IDR)</label>
                    <input type="number" name="harga" required min="1000"
                        value="<?= isset($_POST['harga']) ? $_POST['harga'] : '' ?>"
                        class="w-full border border-gray-200 bg-cruz-cream px-4 py-3 text-sm focus:outline-none focus:border-cruz-gold transition">
                </div>
                <div>
                    <label class="block text-xs uppercase tracking-widest text-gray-400 mb-2">Deskripsi</label>
                    <textarea name="deskripsi" required rows="3"
                        class="w-full border border-gray-200 bg-cruz-cream px-4 py-3 text-sm focus:outline-none focus:border-cruz-gold transition resize-none"><?= isset($_POST['deskripsi']) ? htmlspecialchars($_POST['deskripsi']) : '' ?></textarea>
                </div>
                <div>
                    <label class="block text-xs uppercase tracking-widest text-gray-400 mb-2">Gambar</label>
                    <input type="file" name="gambar" accept="image/jpeg,image/jpg,image/png,image/gif"
                        class="w-full border border-gray-200 bg-cruz-cream px-4 py-3 text-sm file:mr-4 file:py-1 file:px-4 file:bg-cruz-charcoal file:text-white file:border-0 file:text-xs file:uppercase file:tracking-widest">
                </div>

                <div class="flex gap-4 mt-8">
                    <a href="halamanmenu.php"
                        class="flex-1 text-center border border-gray-200 text-gray-500 py-3 text-xs uppercase tracking-widest hover:border-cruz-charcoal hover:text-cruz-charcoal transition">Batal</a>
                    <button type="submit"
                        class="flex-1 bg-cruz-charcoal text-white py-3 text-xs uppercase tracking-widest hover:bg-cruz-gold transition">Simpan</button>
                </div>
            </form>
        </div>
    </main>

</body>

</html>