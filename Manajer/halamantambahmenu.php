<?php
session_start();

// Cek apakah user sudah login sebagai manajer
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Koneksi ke database
include 'koneksi.php';

// Handle notifikasi dari URL parameter
$success_message = isset($_GET['success']) ? $_GET['success'] : null;
$error_message = isset($_GET['error']) ? $_GET['error'] : null;

// Proses tambah menu
if (isset($_POST['tambah_menu'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $category = mysqli_real_escape_string($koneksi, $_POST['category']);
    $harga = mysqli_real_escape_string($koneksi, $_POST['harga']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);

    // Handle upload gambar
    $gambar = null;
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png'];
        $file_type = $_FILES['gambar']['type'];

        if (in_array($file_type, $allowed_types)) {
            $gambar = file_get_contents($_FILES['gambar']['tmp_name']);
        }
    }

    // Insert ke database
    if ($gambar) {
        $stmt = mysqli_prepare($koneksi, "INSERT INTO menu (nama, category, harga, deskripsi, gambar) VALUES (?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssiss", $nama, $category, $harga, $deskripsi, $gambar);
    } else {
        $stmt = mysqli_prepare($koneksi, "INSERT INTO menu (nama, category, harga, deskripsi) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssis", $nama, $category, $harga, $deskripsi);
    }

    if (mysqli_stmt_execute($stmt)) {
        $success_message = "Menu berhasil ditambahkan!";
    } else {
        $error_message = "Gagal menambahkan menu. Silakan coba lagi.";
    }
}

// Ambil kategori yang dipilih (default: all)
$category_filter = isset($_GET['category']) ? $_GET['category'] : 'all';

// Query untuk mengambil data menu
if ($category_filter == 'all') {
    $query = "SELECT * FROM menu ORDER BY id DESC";
    $result = mysqli_query($koneksi, $query);
} else {
    $stmt = mysqli_prepare($koneksi, "SELECT * FROM menu WHERE category = ? ORDER BY id DESC");
    mysqli_stmt_bind_param($stmt, "s", $category_filter);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
}

// Ambil kategori unik untuk filter
$cat_query = "SELECT DISTINCT category FROM menu";
$cat_result = mysqli_query($koneksi, $cat_query);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>CRUZ Coffee & Work Space - Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <style>
        body {
            background-color: #f4e8c8;
        }

        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .modal {
            transition: opacity 0.25s ease;
        }

        .modal-open {
            overflow: hidden;
        }
    </style>
</head>

<body>
    <div class="container mx-auto p-5">
        <!-- Header -->
        <header class="flex justify-between items-center mb-6">
            <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 border-2 border-black rounded-full flex items-center justify-center">
                        <div class="w-6 h-6 border-2 border-black rounded-full"></div>
                    </div>
                    <h1 class="text-2xl font-bold">CRUZ</h1>
                </div>
                <span class="text-gray-600 ml-4">Coffee & Work Space - Manager</span>
            </div>
            <div class="flex items-center space-x-4">
                <span class="text-gray-700">Selamat datang,
                    <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <button onclick="window.location.href='halamanoption.php'"
                    class="bg-gray-500 text-white px-4 py-2 rounded-full hover:bg-gray-600 transition duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </button>
                <button onclick="window.location.href='logout.php'"
                    class="bg-red-500 text-white px-4 py-2 rounded-full hover:bg-red-600 transition duration-200">
                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                </button>
            </div>
        </header>

        <!-- Tombol Tambah Menu -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold">Manajemen Menu</h2>
            <button onclick="openModal()"
                class="bg-green-500 text-white px-6 py-3 rounded-full hover:bg-green-600 transition duration-200 flex items-center">
                <i class="fas fa-plus mr-2"></i>Tambah Menu
            </button>
        </div>

        <!-- Filter Kategori -->
        <nav class="flex justify-center mb-6 flex-wrap">
            <a href="?category=all">
                <button
                    class="<?= ($category_filter == 'all') ? 'bg-yellow-300' : 'bg-transparent border border-gray-400' ?> px-4 py-2 rounded-full mx-1 transition duration-300 ease-in-out hover:bg-yellow-200">
                    All
                </button>
            </a>
            <?php
            if ($cat_result) {
                while ($cat_row = mysqli_fetch_assoc($cat_result)) {
                    $cat_name = $cat_row['category'];
                    $active_class = ($category_filter == $cat_name) ? 'bg-yellow-300' : 'bg-transparent border border-gray-400';
                    echo '<a href="?category=' . urlencode($cat_name) . '" class="m-1">';
                    echo '<button class="' . $active_class . ' px-4 py-2 rounded-full mx-1 transition duration-300 ease-in-out hover:bg-yellow-200">';
                    echo htmlspecialchars($cat_name);
                    echo '</button>';
                    echo '</a>';
                }
            }
            ?>
        </nav>

        <!-- Notifikasi -->
        <?php if (isset($success_message)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <i class="fas fa-check-circle mr-2"></i><?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <i class="fas fa-exclamation-circle mr-2"></i><?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <!-- Daftar Menu -->
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $name = htmlspecialchars($row['nama']);
                    $price = number_format($row['harga'], 0, ',', '.');
                    $category = htmlspecialchars($row['category']);
                    $deskripsi = htmlspecialchars($row['deskripsi']);
                    ?>
                    <div
                        class="bg-white border border-gray-300 p-4 rounded-lg shadow-md card-hover transition-all duration-300">
                        <?php if ($row['gambar']): ?>
                            <img src="data:image/jpeg;base64,<?= base64_encode($row['gambar']) ?>" alt="<?= $name ?>"
                                class="w-full h-40 object-cover mx-auto rounded-md mb-3" />
                        <?php else: ?>
                            <div class="w-full h-40 bg-gray-200 flex items-center justify-center rounded-md mb-3">
                                <i class="fas fa-image text-gray-400 text-3xl"></i>
                            </div>
                        <?php endif; ?>

                        <div class="text-center">
                            <h3 class="text-lg font-semibold mb-1"><?= $name ?></h3>
                            <p class="text-sm text-gray-600 mb-2"><?= $category ?></p>
                            <p class="text-lg font-bold text-green-600 mb-2">Rp<?= $price ?></p>
                            <p class="text-sm text-gray-500 mb-3"><?= $deskripsi ?></p>

                            <div class="flex justify-center space-x-2">
                                <button onclick="editMenu(<?= $row['id'] ?>)"
                                    class="bg-blue-500 text-white px-3 py-1 rounded-full text-sm hover:bg-blue-600 transition duration-200">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button onclick="deleteMenu(<?= $row['id'] ?>, '<?= $name ?>')"
                                    class="bg-red-500 text-white px-3 py-1 rounded-full text-sm hover:bg-red-600 transition duration-200">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo '<div class="col-span-full text-center py-8 text-gray-500">Tidak ada menu tersedia.</div>';
            }
            ?>
        </section>
    </div>

    <!-- Modal Tambah Menu -->
    <div id="menuModal" class="modal fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold">Tambah Menu Baru</h3>
                <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form method="POST" enctype="multipart/form-data">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Nama Menu</label>
                    <input type="text" name="nama" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Kategori</label>
                    <select name="category" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                        <option value="">Pilih Kategori</option>
                        <option value="Main Food">Main Food</option>
                        <option value="Snack">Snack</option>
                        <option value="Espresso Based">Espresso Based</option>
                        <option value="Milk Based">Milk Based</option>
                        <option value="Refresher">Refresher</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Harga</label>
                    <input type="number" name="harga" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Deskripsi</label>
                    <textarea name="deskripsi" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"></textarea>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Gambar</label>
                    <input type="file" name="gambar" accept="image/*"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                </div>

                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeModal()"
                        class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-200">
                        Batal
                    </button>
                    <button type="submit" name="tambah_menu"
                        class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition duration-200">
                        Tambah Menu
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('menuModal').classList.remove('hidden');
            document.getElementById('menuModal').classList.add('flex');
            document.body.classList.add('modal-open');
        }

        function closeModal() {
            document.getElementById('menuModal').classList.add('hidden');
            document.getElementById('menuModal').classList.remove('flex');
            document.body.classList.remove('modal-open');
        }

        function editMenu(id) {
            // Redirect ke halaman edit menu dengan ID
            window.location.href = 'editmenu.php?id=' + id;
        }

        function deleteMenu(id, name) {
            if (confirm('Apakah Anda yakin ingin menghapus menu "' + name + '"?')) {
                window.location.href = 'deletemenu.php?id=' + id;
            }
        }

        // Close modal when clicking outside
        document.getElementById('menuModal').addEventListener('click', function (e) {
            if (e.target === this) {
                closeModal();
            }
        });
    </script>
</body>

</html>