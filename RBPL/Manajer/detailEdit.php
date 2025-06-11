<?php
session_start();
include 'koneksi.php';

// Cek apakah user sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Cek apakah ada ID yang dikirim
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: halamanmenu.php");
    exit();
}

$id = (int)$_GET['id'];
$username = $_SESSION['username'];

// Ambil data menu berdasarkan ID
$query_menu = "SELECT * FROM menu WHERE id = $id";
$result_menu = mysqli_query($koneksi, $query_menu);

if (mysqli_num_rows($result_menu) == 0) {
    header("Location: halamanmenu.php");
    exit();
}

$menu_data = mysqli_fetch_assoc($result_menu);

// Proses update menu
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $category = mysqli_real_escape_string($koneksi, $_POST['category']);
    $harga = (int)$_POST['harga'];
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    
    // Cek apakah ada gambar baru yang diupload
    if ($_FILES['gambar']['size'] > 0) {
        $gambar = $_FILES['gambar']['tmp_name'];
        $gambar_data = addslashes(file_get_contents($gambar));
        
        // Validasi file gambar
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $file_type = $_FILES['gambar']['type'];
        
        if (!in_array($file_type, $allowed_types)) {
            $error = "Format gambar tidak didukung! Gunakan JPG, PNG, atau GIF.";
        } elseif ($_FILES['gambar']['size'] > 2097152) { // 2MB
            $error = "Ukuran gambar terlalu besar! Maksimal 2MB.";
        } else {
            // Update dengan gambar baru
            $update_query = "UPDATE menu SET 
                            nama = '$nama', 
                            category = '$category', 
                            harga = $harga, 
                            deskripsi = '$deskripsi', 
                            gambar = '$gambar_data' 
                            WHERE id = $id";
        }
    } else {
        // Update tanpa mengganti gambar
        $update_query = "UPDATE menu SET 
                        nama = '$nama', 
                        category = '$category', 
                        harga = $harga, 
                        deskripsi = '$deskripsi' 
                        WHERE id = $id";
    }
    
    if (!isset($error)) {
        if (mysqli_query($koneksi, $update_query)) {
            $success = "Menu berhasil diperbarui!";
            // Refresh data menu
            $result_menu = mysqli_query($koneksi, $query_menu);
            $menu_data = mysqli_fetch_assoc($result_menu);
        } else {
            $error = "Gagal memperbarui menu: " . mysqli_error($koneksi);
        }
    }
}

// Ambil daftar kategori untuk dropdown
$query_kategori = "SELECT DISTINCT category FROM menu ORDER BY category";
$result_kategori = mysqli_query($koneksi, $query_kategori);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Menu - Cafe Cruz</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('backgroundhome.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .navbar-brand {
            font-weight: 700;
            color: #2c3e50 !important;
            font-size: 24px;
        }

        .logo-nav {
            max-height: 40px;
            margin-right: 10px;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
        }

        .btn-logout {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            border: none;
            border-radius: 25px;
            padding: 8px 20px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 2px 10px rgba(231, 76, 60, 0.3);
        }

        .btn-logout:hover {
            background: linear-gradient(135deg, #c0392b, #a93226);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(231, 76, 60, 0.4);
            color: white;
        }

        .btn-back {
            background: linear-gradient(135deg, #6c757d, #5a6268);
            border: none;
            border-radius: 25px;
            padding: 8px 20px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 2px 10px rgba(108, 117, 125, 0.3);
            text-decoration: none;
            margin-right: 10px;
        }

        .btn-back:hover {
            background: linear-gradient(135deg, #5a6268, #495057);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(108, 117, 125, 0.4);
            color: white;
            text-decoration: none;
        }

        .main-container {
            padding: 100px 0 40px 0;
            min-height: 100vh;
        }

        .page-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .page-title {
            color: white;
            font-size: 42px;
            font-weight: 700;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .page-subtitle {
            color: rgba(255, 255, 255, 0.9);
            font-size: 18px;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
        }

        .content-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.2);
            margin-bottom: 30px;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }

        .form-label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
        }

        .form-control, .form-select {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 12px 15px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }

        .form-control:focus, .form-select:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
            background: white;
        }

        .form-control.is-invalid {
            border-color: #e74c3c;
        }

        .invalid-feedback {
            color: #e74c3c;
            font-weight: 500;
        }

        .btn-primary {
            background: linear-gradient(135deg, #3498db, #2980b9);
            border: none;
            border-radius: 12px;
            padding: 12px 30px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
            width: 100%;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #2980b9, #21618c);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(52, 152, 219, 0.4);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #95a5a6, #7f8c8d);
            border: none;
            border-radius: 12px;
            padding: 12px 30px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(149, 165, 166, 0.3);
            color: white;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            width: 100%;
            margin-top: 10px;
        }

        .btn-secondary:hover {
            background: linear-gradient(135deg, #7f8c8d, #6c7b7d);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(149, 165, 166, 0.4);
            color: white;
            text-decoration: none;
        }

        .alert {
            border-radius: 12px;
            border: none;
            padding: 15px 20px;
            margin-bottom: 25px;
            font-weight: 500;
        }

        .alert-success {
            background: linear-gradient(135deg, #27ae60, #229954);
            color: white;
        }

        .alert-danger {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
        }

        .user-info {
            color: #2c3e50;
            font-weight: 600;
            margin-right: 15px;
        }

        .current-image {
            max-width: 200px;
            max-height: 200px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
            margin-bottom: 15px;
        }

        .image-preview {
            border: 2px dashed #bdc3c7;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            background: rgba(189, 195, 199, 0.1);
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }

        .image-preview:hover {
            border-color: #3498db;
            background: rgba(52, 152, 219, 0.05);
        }

        .image-preview.has-image {
            border-color: #27ae60;
            background: rgba(39, 174, 96, 0.05);
        }

        .preview-image {
            max-width: 100%;
            max-height: 200px;
            border-radius: 8px;
            margin-top: 10px;
        }

        .form-floating {
            margin-bottom: 20px;
        }

        .form-floating > .form-control {
            height: calc(3.5rem + 2px);
            padding: 1rem 0.75rem;
        }

        .form-floating > textarea.form-control {
            height: 120px;
        }

        .form-floating > label {
            color: #6c757d;
        }

        @media (max-width: 768px) {
            .page-title {
                font-size: 32px;
            }

            .content-card {
                padding: 25px;
                margin: 0 15px 20px 15px;
            }

            .current-image {
                max-width: 150px;
                max-height: 150px;
            }
        }

        .fade-in {
            animation: fadeIn 0.8s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-row > div {
            flex: 1;
        }

        @media (max-width: 576px) {
            .form-row {
                flex-direction: column;
                gap: 0;
            }
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="halamanoption.php">
                <img src="logocruz.png" alt="Logo Cafe Cruz" class="logo-nav">
                Cafe Cruz Manager
            </a>
            <div class="d-flex align-items-center">
                <span class="user-info d-none d-sm-inline">
                    <i class="fas fa-user-circle me-2"></i>
                    <?php echo htmlspecialchars($username); ?>
                </span>
                <a href="halamanmenu.php" class="btn btn-back">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
                <a href="?logout=true" class="btn btn-logout" onclick="return confirm('Yakin ingin logout?')">
                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-container">
        <div class="container">
            <!-- Page Header -->
            <div class="page-header fade-in">
                <h1 class="page-title">Edit Menu</h1>
                <p class="page-subtitle">Perbarui informasi menu <?php echo htmlspecialchars($menu_data['nama']); ?></p>
            </div>

            <!-- Content Card -->
            <div class="content-card fade-in">
                <?php if (isset($success)): ?>
                    <div class="alert alert-success" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        <?php echo $success; ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data" id="editMenuForm">
                    <!-- Nama Menu dan Kategori -->
                    <div class="form-row">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="nama" name="nama" 
                                   value="<?php echo htmlspecialchars($menu_data['nama']); ?>" 
                                   placeholder="Nama Menu" required>
                            <label for="nama">
                                <i class="fas fa-utensils me-2"></i>Nama Menu
                            </label>
                        </div>

                        <div class="form-floating">
                            <select class="form-select" id="category" name="category" required>
                                <option value="">Pilih Kategori</option>
                                <option value="Makanan" <?php echo ($menu_data['category'] == 'Makanan') ? 'selected' : ''; ?>>Makanan</option>
                                <option value="Minuman" <?php echo ($menu_data['category'] == 'Minuman') ? 'selected' : ''; ?>>Minuman</option>
                                <option value="Snack" <?php echo ($menu_data['category'] == 'Snack') ? 'selected' : ''; ?>>Snack</option>
                                <option value="Dessert" <?php echo ($menu_data['category'] == 'Dessert') ? 'selected' : ''; ?>>Dessert</option>
                                <?php
                                // Tambahkan kategori yang sudah ada di database
                                mysqli_data_seek($result_kategori, 0);
                                while ($kategori = mysqli_fetch_assoc($result_kategori)):
                                    if (!in_array($kategori['category'], ['Makanan', 'Minuman', 'Snack', 'Dessert'])):
                                ?>
                                    <option value="<?php echo htmlspecialchars($kategori['category']); ?>" 
                                            <?php echo ($menu_data['category'] == $kategori['category']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($kategori['category']); ?>
                                    </option>
                                <?php 
                                    endif;
                                endwhile; 
                                ?>
                            </select>
                            <label for="category">
                                <i class="fas fa-tags me-2"></i>Kategori
                            </label>
                        </div>
                    </div>

                    <!-- Harga -->
                    <div class="form-floating">
                        <input type="number" class="form-control" id="harga" name="harga" 
                               value="<?php echo $menu_data['harga']; ?>" 
                               placeholder="Harga" min="0" step="500" required>
                        <label for="harga">
                            <i class="fas fa-money-bill-wave me-2"></i>Harga (Rp)
                        </label>
                    </div>

                    <!-- Deskripsi -->
                    <div class="form-floating">
                        <textarea class="form-control" id="deskripsi" name="deskripsi" 
                                  placeholder="Deskripsi menu" style="height: 120px;" required><?php echo htmlspecialchars($menu_data['deskripsi']); ?></textarea>
                        <label for="deskripsi">
                            <i class="fas fa-align-left me-2"></i>Deskripsi Menu
                        </label>
                    </div>

                    <!-- Gambar Saat Ini -->
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-image me-2"></i>Gambar Saat Ini
                        </label>
                        <div class="text-center">
                            <?php if (!empty($menu_data['gambar'])): ?>
                                <img src="data:image/jpeg;base64,<?php echo base64_encode($menu_data['gambar']); ?>"
                                     alt="<?php echo htmlspecialchars($menu_data['nama']); ?>"
                                     class="current-image">
                            <?php else: ?>
                                <div class="current-image d-flex align-items-center justify-content-center" 
                                     style="background: #f8f9fa; width: 200px; height: 200px; margin: 0 auto;">
                                    <i class="fas fa-image text-muted fa-3x"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Upload Gambar Baru -->
                    <div class="mb-3">
                        <label for="gambar" class="form-label">
                            <i class="fas fa-upload me-2"></i>Ganti Gambar (Opsional)
                        </label>
                        <div class="image-preview" id="imagePreview">
                            <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-2">Klik untuk memilih gambar baru atau drag & drop</p>
                            <small class="text-muted">Format: JPG, PNG, GIF (Maks. 2MB)</small>
                            <input type="file" class="form-control mt-2" id="gambar" name="gambar" 
                                   accept="image/*" style="display: none;">
                        </div>
                    </div>

                    <!-- Tombol Submit -->
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-save me-2"></i>Simpan Perubahan
                    </button>
                    
                    <a href="halamanmenu.php" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i>Batal
                    </a>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-20px)';
                    setTimeout(() => {
                        alert.remove();
                    }, 300);
                }, 5000);
            });
        });

        // Image preview functionality
        const imagePreview = document.getElementById('imagePreview');
        const imageInput = document.getElementById('gambar');

        imagePreview.addEventListener('click', () => {
            imageInput.click();
        });

        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validasi ukuran file
                if (file.size > 2097152) { // 2MB
                    alert('Ukuran file terlalu besar! Maksimal 2MB.');
                    this.value = '';
                    return;
                }

                // Validasi tipe file
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Format file tidak didukung! Gunakan JPG, PNG, atau GIF.');
                    this.value = '';
                    return;
                }

                // Preview gambar
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.innerHTML = `
                        <img src="${e.target.result}" class="preview-image" alt="Preview">
                        <p class="text-success mt-2 mb-0">
                            <i class="fas fa-check-circle me-2"></i>Gambar siap diupload
                        </p>
                    `;
                    imagePreview.classList.add('has-image');
                };
                reader.readAsDataURL(file);
            }
        });

        // Drag and drop functionality
        imagePreview.addEventListener('dragover', (e) => {
            e.preventDefault();
            imagePreview.style.borderColor = '#3498db';
            imagePreview.style.background = 'rgba(52, 152, 219, 0.1)';
        });

        imagePreview.addEventListener('dragleave', (e) => {
            e.preventDefault();
            imagePreview.style.borderColor = '#bdc3c7';
            imagePreview.style.background = 'rgba(189, 195, 199, 0.1)';
        });

        imagePreview.addEventListener('drop', (e) => {
            e.preventDefault();
            imagePreview.style.borderColor = '#bdc3c7';
            imagePreview.style.background = 'rgba(189, 195, 199, 0.1)';
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                imageInput.files = files;
                imageInput.dispatchEvent(new Event('change'));
            }
        });

        // Form validation
        document.getElementById('editMenuForm').addEventListener('submit', function(e) {
            const nama = document.getElementById('nama').value.trim();
            const category = document.getElementById('category').value;
            const harga = document.getElementById('harga').value;
            const deskripsi = document.getElementById('deskripsi').value.trim();

            if (!nama || !category || !harga || !deskripsi) {
                e.preventDefault();
                alert('Semua field harus diisi!');
                return;
            }

            if (parseInt(harga) < 0) {
                e.preventDefault();
                alert('Harga tidak boleh negatif!');
                return;
            }

            // Loading state
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';
            submitBtn.disabled = true;
        });

        // Format harga input
        document.getElementById('harga').addEventListener('input', function(e) {
            let value = parseInt(e.target.value);
            if (isNaN(value)) value = 0;
            
            // Round to nearest 500
            value = Math.round(value / 500) * 500;
            e.target.value = value;
        });

        // Auto-resize textarea
        document.getElementById('deskripsi').addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    </script>
</body>

</html>