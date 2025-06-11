<?php
session_start();
include 'koneksi.php';

// Cek apakah user sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Proses logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

$success = "";
$error = "";

// Proses form submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $category = mysqli_real_escape_string($koneksi, $_POST['category']);
    $harga = (int)$_POST['harga'];
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    
    // Handle file upload
    $gambar = null;
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $file_type = $_FILES['gambar']['type'];
        $file_size = $_FILES['gambar']['size'];
        
        if (in_array($file_type, $allowed_types) && $file_size <= 5000000) { // 5MB max
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
            // Clear form
            $_POST = array();
        } else {
            $error = "Gagal menambahkan menu: " . mysqli_error($koneksi);
        }
        mysqli_stmt_close($stmt);
    }
}

// Ambil kategori yang sudah ada untuk dropdown
$query_kategori = "SELECT DISTINCT category FROM menu ORDER BY category";
$result_kategori = mysqli_query($koneksi, $query_kategori);

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Menu - Cafe Cruz</title>
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

        .form-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .form-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .form-section {
            margin-bottom: 30px;
        }

        .section-title {
            color: #2c3e50;
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #3498db;
            display: flex;
            align-items: center;
        }

        .section-icon {
            margin-right: 10px;
            color: #3498db;
        }

        .form-floating {
            margin-bottom: 20px;
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

        .form-floating > label {
            color: #6c757d;
            font-weight: 500;
        }

        .input-group-text {
            background: rgba(255, 255, 255, 0.9);
            border: 2px solid #e9ecef;
            border-right: none;
            color: #6c757d;
            font-weight: 600;
        }

        .file-upload-area {
            border: 2px dashed #bdc3c7;
            border-radius: 12px;
            padding: 40px 20px;
            text-align: center;
            background: rgba(248, 249, 250, 0.5);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .file-upload-area:hover {
            border-color: #3498db;
            background: rgba(52, 152, 219, 0.05);
        }

        .file-upload-area.dragover {
            border-color: #27ae60;
            background: rgba(39, 174, 96, 0.1);
        }

        .upload-icon {
            font-size: 48px;
            color: #bdc3c7;
            margin-bottom: 15px;
        }

        .upload-text {
            color: #7f8c8d;
            margin-bottom: 10px;
        }

        .file-info {
            background: rgba(52, 152, 219, 0.1);
            border: 1px solid #3498db;
            border-radius: 8px;
            padding: 10px;
            margin-top: 15px;
            display: none;
        }

        .preview-image {
            max-width: 200px;
            max-height: 200px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            margin-top: 15px;
        }

        .btn-submit {
            background: linear-gradient(135deg, #27ae60, #229954);
            border: none;
            border-radius: 15px;
            padding: 15px 40px;
            color: white;
            font-weight: 700;
            font-size: 18px;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(39, 174, 96, 0.3);
            width: 100%;
        }

        .btn-submit:hover {
            background: linear-gradient(135deg, #229954, #1e8449);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(39, 174, 96, 0.4);
        }

        .btn-reset {
            background: linear-gradient(135deg, #f39c12, #e67e22);
            border: none;
            border-radius: 15px;
            padding: 12px 30px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(243, 156, 18, 0.3);
            margin-right: 15px;
        }

        .btn-reset:hover {
            background: linear-gradient(135deg, #e67e22, #d68910);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(243, 156, 18, 0.4);
        }

        .alert {
            border-radius: 12px;
            border: none;
            padding: 15px 20px;
            margin-bottom: 30px;
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

        .char-counter {
            font-size: 12px;
            color: #7f8c8d;
            text-align: right;
            margin-top: 5px;
        }

        .char-counter.warning {
            color: #f39c12;
        }

        .char-counter.danger {
            color: #e74c3c;
        }

        .loading {
            display: none;
        }

        .btn-submit.loading .btn-text {
            display: none;
        }

        .btn-submit.loading .loading {
            display: inline-block;
        }

        @media (max-width: 768px) {
            .page-title {
                font-size: 32px;
            }
            
            .form-card {
                padding: 25px;
                margin: 0 10px;
            }
            
            .section-title {
                font-size: 18px;
            }
            
            .file-upload-area {
                padding: 30px 15px;
            }
            
            .upload-icon {
                font-size: 36px;
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

        .required {
            color: #e74c3c;
        }

        .form-text {
            color: #6c757d;
            font-size: 14px;
            margin-top: 5px;
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
                <h1 class="page-title">Tambah Menu Baru</h1>
                <p class="page-subtitle">Lengkapi informasi menu yang akan ditambahkan</p>
            </div>

            <!-- Form Container -->
            <div class="form-container fade-in">
                <div class="form-card">
                    <?php if ($success): ?>
                        <div class="alert alert-success" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <?php echo $success; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="" enctype="multipart/form-data" id="menuForm">
                        <!-- Informasi Dasar -->
                        <div class="form-section">
                            <h3 class="section-title">
                                <i class="fas fa-info-circle section-icon"></i>
                                Informasi Dasar
                            </h3>
                            
                            <div class="form-floating">
                                <input type="text" class="form-control" id="nama" name="nama" 
                                       placeholder="Nama Menu" required maxlength="100"
                                       value="<?php echo isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : ''; ?>">
                                <label for="nama">Nama Menu <span class="required">*</span></label>
                                <div class="char-counter" id="namaCounter">0/100</div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select" id="category" name="category" required>
                                            <option value="">Pilih Kategori</option>
                                            <option value="Main Food" <?php echo (isset($_POST['category']) && $_POST['category'] == 'Main Food') ? 'selected' : ''; ?>>Main Food</option>
                                            <option value="Snack" <?php echo (isset($_POST['category']) && $_POST['category'] == 'Snack') ? 'selected' : ''; ?>>Snack</option>
                                            <option value="Espresso Based" <?php echo (isset($_POST['category']) && $_POST['category'] == 'Espresso Based') ? 'selected' : ''; ?>>Espresso Based</option>
                                            <option value="Milk Based" <?php echo (isset($_POST['category']) && $_POST['category'] == 'Milk Based') ? 'selected' : ''; ?>>Milk Based</option>
                                            <option value="Dessert" <?php echo (isset($_POST['category']) && $_POST['category'] == 'Dessert') ? 'selected' : ''; ?>>Dessert</option>
                                            <option value="Beverage" <?php echo (isset($_POST['category']) && $_POST['category'] == 'Beverage') ? 'selected' : ''; ?>>Beverage</option>
                                            <?php while($kategori = mysqli_fetch_assoc($result_kategori)): ?>
                                                <?php if (!in_array($kategori['category'], ['Main Food', 'Snack', 'Espresso Based', 'Milk Based', 'Dessert', 'Beverage'])): ?>
                                                    <option value="<?php echo htmlspecialchars($kategori['category']); ?>" 
                                                            <?php echo (isset($_POST['category']) && $_POST['category'] == $kategori['category']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($kategori['category']); ?>
                                                    </option>
                                                <?php endif; ?>
                                            <?php endwhile; ?>
                                        </select>
                                        <label for="category">Kategori <span class="required">*</span></label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <div class="form-floating flex-grow-1">
                                            <input type="number" class="form-control" id="harga" name="harga" 
                                                   placeholder="Harga" required min="1000" max="1000000"
                                                   value="<?php echo isset($_POST['harga']) ? $_POST['harga'] : ''; ?>">
                                            <label for="harga">Harga <span class="required">*</span></label>
                                        </div>
                                    </div>
                                    <div class="form-text">Minimal Rp 1.000, Maksimal Rp 1.000.000</div>
                                </div>
                            </div>
                        </div>

                        <!-- Deskripsi -->
                        <div class="form-section">
                            <h3 class="section-title">
                                <i class="fas fa-align-left section-icon"></i>
                                Deskripsi Menu
                            </h3>
                            
                            <div class="form-floating">
                                <textarea class="form-control" id="deskripsi" name="deskripsi" 
                                          placeholder="Deskripsi Menu" required maxlength="500" 
                                          style="height: 120px"><?php echo isset($_POST['deskripsi']) ? htmlspecialchars($_POST['deskripsi']) : ''; ?></textarea>
                                <label for="deskripsi">Deskripsi Menu <span class="required">*</span></label>
                                <div class="char-counter" id="deskripsiCounter">0/500</div>
                            </div>
                            <div class="form-text">Jelaskan detail menu, bahan, dan keunikan yang dimiliki</div>
                        </div>

                        <!-- Upload Gambar -->
                        <div class="form-section">
                            <h3 class="section-title">
                                <i class="fas fa-camera section-icon"></i>
                                Gambar Menu
                            </h3>
                            
                            <div class="file-upload-area" onclick="document.getElementById('gambar').click()">
                                <div class="upload-icon">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                </div>
                                <div class="upload-text">
                                    <strong>Klik untuk upload gambar</strong> atau drag & drop file di sini
                                </div>
                                <div class="form-text">
                                    Format: JPG, JPEG, PNG, GIF | Maksimal: 5MB
                                </div>
                                <input type="file" class="form-control d-none" id="gambar" name="gambar" 
                                       accept="image/jpeg,image/jpg,image/png,image/gif">
                                <div class="file-info" id="fileInfo"></div>
                                <img id="previewImage" class="preview-image" style="display: none;">
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="form-section">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <button type="button" class="btn btn-reset" onclick="resetForm()">
                                        <i class="fas fa-undo me-2"></i>Reset Form
                                    </button>
                                </div>
                                <button type="submit" class="btn btn-submit" style="width: auto; min-width: 200px;">
                                    <span class="btn-text">
                                        <i class="fas fa-save me-2"></i>Simpan Menu
                                    </span>
                                    <span class="loading">
                                        <i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...
                                    </span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Character counters
        function updateCharCounter(inputId, counterId, maxLength) {
            const input = document.getElementById(inputId);
            const counter = document.getElementById(counterId);
            
            input.addEventListener('input', function() {
                const length = this.value.length;
                counter.textContent = `${length}/${maxLength}`;
                
                if (length > maxLength * 0.9) {
                    counter.classList.add('danger');
                    counter.classList.remove('warning');
                } else if (length > maxLength * 0.7) {
                    counter.classList.add('warning');
                    counter.classList.remove('danger');
                } else {
                    counter.classList.remove('warning', 'danger');
                }
            });
            
            // Trigger on page load
            input.dispatchEvent(new Event('input'));
        }

        updateCharCounter('nama', 'namaCounter', 100);
        updateCharCounter('deskripsi', 'deskripsiCounter', 500);

        // File upload handling
        const fileInput = document.getElementById('gambar');
        const fileInfo = document.getElementById('fileInfo');
        const previewImage = document.getElementById('previewImage');
        const uploadArea = document.querySelector('.file-upload-area');

        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validate file type
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Format file tidak didukung! Gunakan JPG, PNG, atau GIF.');
                    this.value = '';
                    return;
                }
                
                // Validate file size (5MB)
                if (file.size > 5000000) {
                    alert('Ukuran file terlalu besar! Maksimal 5MB.');
                    this.value = '';
                    return;
                }
                
                // Show file info
                fileInfo.innerHTML = `
                    <div class="d-flex align-items-center">
                        <i class="fas fa-file-image me-2 text-primary"></i>
                        <div>
                            <strong>${file.name}</strong><br>
                            <small class="text-muted">${(file.size/1024/1024).toFixed(2)} MB</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger ms-auto" onclick="clearFile()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
                fileInfo.style.display = 'block';
                
                // Show preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewImage.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });

        // Drag and drop
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', function(e) {
            this.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                fileInput.dispatchEvent(new Event('change'));
            }
        });

        function clearFile() {
            fileInput.value = '';
            fileInfo.style.display = 'none';
            previewImage.style.display = 'none';
        }

        // Form validation
        document.getElementById('menuForm').addEventListener('submit', function(e) {
            const submitBtn = document.querySelector('.btn-submit');
            
            // Basic validation
            const nama = document.getElementById('nama').value.trim();
            const category = document.getElementById('category').value;
            const harga = document.getElementById('harga').value;
            const deskripsi = document.getElementById('deskripsi').value.trim();
            
            if (!nama || !category || !harga || !deskripsi) {
                e.preventDefault();
                alert('Mohon lengkapi semua field yang wajib diisi!');
                return;
            }
            
            if (harga < 1000 || harga > 1000000) {
                e.preventDefault();
                alert('Harga harus antara Rp 1.000 - Rp 1.000.000!');
                return;
            }
            
            // Show loading state
            submitBtn.classList.add('loading');
            submitBtn.disabled = true;
        });

        // Reset form function
        function resetForm() {
            if (confirm('Yakin ingin mereset semua data yang sudah diisi?')) {
                document.getElementById('menuForm').reset();
                clearFile();
                document.getElementById('namaCounter').textContent = '0/100';
                document.getElementById('deskripsiCounter').textContent = '0/500';
                document.getElementById('namaCounter').classList.remove('warning', 'danger');
                document.getElementById('deskripsiCounter').classList.remove('warning', 'danger');
            }
        }

        // Auto-hide alerts
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

        // Format harga input
        document.getElementById('harga').addEventListener('input', function() {
            // Remove non-numeric characters except for the initial input
            let value = this.value.replace(/[^\d]/g, '');
            this.value = value;
        });

        // Auto focus
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('nama').focus();
        });
        
        // Enter key navigation
        document.getElementById('nama').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('category').focus();
            }
        });

        document.getElementById('category').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('harga').focus();
            }
        });

        document.getElementById('harga').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('deskripsi').focus();
            }
        });

        // Auto resize textarea
        document.getElementById('deskripsi').addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 200) + 'px';
        });

        // Smooth scroll to error alerts
        window.addEventListener('load', function() {
            const alerts = document.querySelectorAll('.alert');
            if (alerts.length > 0) {
                alerts[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });

        // Form change detection
        let formChanged = false;
        const formInputs = document.querySelectorAll('#menuForm input, #menuForm select, #menuForm textarea');
        
        formInputs.forEach(input => {
            input.addEventListener('change', function() {
                formChanged = true;
            });
        });

        // Warn before leaving page if form has changes
        window.addEventListener('beforeunload', function(e) {
            if (formChanged) {
                e.preventDefault();
                e.returnValue = '';
                return '';
            }
        });

        // Don't warn when form is submitted
        document.getElementById('menuForm').addEventListener('submit', function() {
            formChanged = false;
        });

        // Format number input with thousand separators for display
        document.getElementById('harga').addEventListener('blur', function() {
            if (this.value) {
                const number = parseInt(this.value);
                if (!isNaN(number)) {
                    // Store the raw number for form submission
                    this.dataset.rawValue = number;
                }
            }
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl+S to save
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault();
                document.getElementById('menuForm').dispatchEvent(new Event('submit'));
            }
            
            // Ctrl+R to reset
            if ((e.ctrlKey || e.metaKey) && e.key === 'r') {
                e.preventDefault();
                resetForm();
            }
            
            // Esc to clear file
            if (e.key === 'Escape' && document.getElementById('fileInfo').style.display === 'block') {
                clearFile();
            }
        });

        // Add loading animation to buttons
        document.querySelectorAll('.btn').forEach(btn => {
            btn.addEventListener('click', function() {
                if (this.type === 'submit') {
                    return; // Already handled in form submit
                }
                
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Loading...';
                this.disabled = true;
                
                setTimeout(() => {
                    this.innerHTML = originalText;
                    this.disabled = false;
                }, 1000);
            });
        });

        // Enhanced file validation
        function validateFile(file) {
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            const maxSize = 5 * 1024 * 1024; // 5MB
            const minSize = 1024; // 1KB
            
            if (!allowedTypes.includes(file.type)) {
                return 'Format file tidak didukung! Gunakan JPG, JPEG, PNG, atau GIF.';
            }
            
            if (file.size > maxSize) {
                return 'Ukuran file terlalu besar! Maksimal 5MB.';
            }
            
            if (file.size < minSize) {
                return 'Ukuran file terlalu kecil! Minimal 1KB.';
            }
            
            return null;
        }

        // Update file input validation
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const error = validateFile(file);
                if (error) {
                    alert(error);
                    this.value = '';
                    return;
                }
                
                // Continue with existing file handling code...
                // (The existing file handling code from above would continue here)
            }
        });

        // Add tooltips for better UX
        const tooltips = {
            'nama': 'Masukkan nama menu yang menarik dan mudah diingat',
            'category': 'Pilih kategori yang sesuai dengan jenis menu',
            'harga': 'Tentukan harga yang kompetitif sesuai porsi dan kualitas',
            'deskripsi': 'Jelaskan detail menu, bahan utama, dan keunikan yang dimiliki',
            'gambar': 'Upload foto menu yang menarik untuk menarik perhatian pelanggan'
        };

        Object.keys(tooltips).forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.title = tooltips[id];
                
                // Add focus hint
                element.addEventListener('focus', function() {
                    const hint = document.createElement('div');
                    hint.className = 'form-hint';
                    hint.style.cssText = `
                        position: absolute;
                        background: rgba(0,0,0,0.8);
                        color: white;
                        padding: 8px 12px;
                        border-radius: 6px;
                        font-size: 12px;
                        z-index: 1000;
                        margin-top: 5px;
                        max-width: 300px;
                    `;
                    hint.textContent = tooltips[id];
                    
                    this.parentNode.style.position = 'relative';
                    this.parentNode.appendChild(hint);
                    
                    setTimeout(() => {
                        if (hint.parentNode) {
                            hint.remove();
                        }
                    }, 3000);
                });
            }
        });

        // Progressive enhancement for better performance
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('fade-in');
                    }
                });
            });

            document.querySelectorAll('.form-section').forEach(section => {
                observer.observe(section);
            });
        }

        // Error handling for network issues
        window.addEventListener('online', function() {
            document.body.style.filter = 'none';
            const offlineMsg = document.getElementById('offline-message');
            if (offlineMsg) offlineMsg.remove();
        });

        window.addEventListener('offline', function() {
            document.body.style.filter = 'grayscale(1)';
            const msg = document.createElement('div');
            msg.id = 'offline-message';
            msg.innerHTML = `
                <div style="
                    position: fixed;
                    top: 70px;
                    left: 50%;
                    transform: translateX(-50%);
                    background: #e74c3c;
                    color: white;
                    padding: 10px 20px;
                    border-radius: 5px;
                    z-index: 9999;
                    font-weight: 600;
                ">
                    <i class="fas fa-wifi me-2"></i>
                    Koneksi internet terputus
                </div>
            `;
            document.body.appendChild(msg);
        });

        console.log('Menu form scripts loaded successfully');
    </script>
</body>
</html>
       