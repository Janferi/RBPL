<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: halamanmenu.php");
    exit();
}

$id = (int) $_GET['id'];
$username = $_SESSION['username'];

$query_menu = "SELECT * FROM menu WHERE id = ?";
$stmt_menu = mysqli_prepare($koneksi, $query_menu);
mysqli_stmt_bind_param($stmt_menu, "i", $id);
mysqli_stmt_execute($stmt_menu);
$result_menu = mysqli_stmt_get_result($stmt_menu);

if (mysqli_num_rows($result_menu) == 0) {
    header("Location: halamanmenu.php");
    exit();
}

$menu_data = mysqli_fetch_assoc($result_menu);
mysqli_stmt_close($stmt_menu);

$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty($_POST['nama']) || empty($_POST['harga'])) {
        $error = "Nama dan harga wajib diisi!";
    } else {
        $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
        $harga = (int) $_POST['harga'];
        $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi'] ?: ($menu_data['deskripsi'] ?? ''));

        $gambar = null;
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
            $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            $file_type = $_FILES['gambar']['type'];
            $file_size = $_FILES['gambar']['size'];

            if (!in_array($file_type, $allowed_types)) {
                $error = "Format gambar tidak didukung!";
            } elseif ($file_size > 5000000) {
                $error = "Ukuran gambar terlalu besar! Maksimal 5MB.";
            } else {
                $gambar = file_get_contents($_FILES['gambar']['tmp_name']);
            }
        }

        if (empty($error)) {
            if ($gambar !== null) {
                $update_query = "UPDATE menu SET nama = ?, harga = ?, deskripsi = ?, gambar = ? WHERE id = ?";
                $stmt = mysqli_prepare($koneksi, $update_query);
                mysqli_stmt_bind_param($stmt, "sissi", $nama, $harga, $deskripsi, $gambar, $id);
            } else {
                $update_query = "UPDATE menu SET nama = ?, harga = ?, deskripsi = ? WHERE id = ?";
                $stmt = mysqli_prepare($koneksi, $update_query);
                mysqli_stmt_bind_param($stmt, "sisi", $nama, $harga, $deskripsi, $id);
            }

            if (mysqli_stmt_execute($stmt)) {
                $success = "Menu berhasil diperbarui!";
                $stmt_menu = mysqli_prepare($koneksi, $query_menu);
                mysqli_stmt_bind_param($stmt_menu, "i", $id);
                mysqli_stmt_execute($stmt_menu);
                $result_menu = mysqli_stmt_get_result($stmt_menu);
                $menu_data = mysqli_fetch_assoc($result_menu);
                mysqli_stmt_close($stmt_menu);
            } else {
                $error = "Gagal memperbarui menu. Silakan coba lagi.";
            }
            mysqli_stmt_close($stmt);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Menu - CRUZ Manager</title>
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
            <a href="halamanmenu.php"
                class="text-xs uppercase tracking-widest text-gray-500 hover:text-cruz-gold transition">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </nav>

    <main class="container mx-auto px-6 py-12 flex-grow flex items-center justify-center">
        <div class="bg-white w-full max-w-2xl shadow-xl flex flex-col md:flex-row overflow-hidden">

            <!-- Image Preview -->
            <div class="md:w-1/2 bg-[#F9F9F7] flex items-center justify-center p-10">
                <?php if (!empty($menu_data['gambar'])): ?>
                    <img src="data:image/jpeg;base64,<?= base64_encode($menu_data['gambar']) ?>"
                        alt="<?= htmlspecialchars($menu_data['nama']) ?>" class="max-w-full max-h-64 object-contain">
                <?php else: ?>
                    <i class="fas fa-image text-6xl text-gray-300"></i>
                <?php endif; ?>
            </div>

            <!-- Form -->
            <div class="md:w-1/2 p-10 relative">
                <div class="absolute top-0 left-0 w-full h-1 bg-cruz-gold"></div>

                <h1 class="text-xl font-serif text-cruz-black italic mb-6">Edit Menu</h1>

                <?php if ($success): ?>
                    <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-3 mb-4 text-xs"><i
                            class="fas fa-check-circle mr-1"></i><?= $success ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-3 mb-4 text-xs"><i
                            class="fas fa-exclamation-triangle mr-1"></i><?= $error ?></div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data" class="space-y-5">
                    <div>
                        <label class="block text-[10px] uppercase tracking-widest text-gray-400 mb-1">Nama</label>
                        <input type="text" name="nama" required value="<?= htmlspecialchars($menu_data['nama']) ?>"
                            class="w-full border-b border-gray-200 bg-transparent py-2 text-sm focus:outline-none focus:border-cruz-gold transition">
                    </div>
                    <div>
                        <label class="block text-[10px] uppercase tracking-widest text-gray-400 mb-1">Harga
                            (IDR)</label>
                        <input type="number" name="harga" required min="1000" value="<?= $menu_data['harga'] ?>"
                            class="w-full border-b border-gray-200 bg-transparent py-2 text-sm focus:outline-none focus:border-cruz-gold transition">
                    </div>
                    <div>
                        <label class="block text-[10px] uppercase tracking-widest text-gray-400 mb-1">Deskripsi</label>
                        <textarea name="deskripsi" rows="2"
                            class="w-full border-b border-gray-200 bg-transparent py-2 text-sm focus:outline-none focus:border-cruz-gold transition resize-none"><?= htmlspecialchars($menu_data['deskripsi'] ?? '') ?></textarea>
                    </div>
                    <div>
                        <label class="block text-[10px] uppercase tracking-widest text-gray-400 mb-1">Ganti
                            Gambar</label>
                        <input type="file" name="gambar" accept="image/jpeg,image/jpg,image/png,image/gif"
                            class="w-full text-xs file:mr-3 file:py-1 file:px-3 file:bg-cruz-charcoal file:text-white file:border-0 file:text-[10px] file:uppercase file:tracking-widest">
                    </div>

                    <div class="flex gap-3 pt-4">
                        <a href="halamanmenu.php"
                            class="flex-1 text-center border border-gray-200 text-gray-500 py-3 text-xs uppercase tracking-widest hover:border-cruz-charcoal transition">Batal</a>
                        <button type="submit"
                            class="flex-1 bg-cruz-charcoal text-white py-3 text-xs uppercase tracking-widest hover:bg-cruz-gold transition">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </main>

</body>

</html>