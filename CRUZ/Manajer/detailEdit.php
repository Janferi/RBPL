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

// Proses update menu
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate required fields
    if (empty($_POST['nama']) || empty($_POST['harga'])) {
        $error = "Nama dan harga wajib diisi!";
    } else {
        $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
        $harga = (int)$_POST['harga'];
        $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi'] ?: ($menu_data['deskripsi'] ?? ''));

        // Handle file upload
        $gambar = null;
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
            $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            $file_type = $_FILES['gambar']['type'];
            $file_size = $_FILES['gambar']['size'];

            if (!in_array($file_type, $allowed_types)) {
                $error = "Format gambar tidak didukung! Gunakan JPEG, JPG, PNG, atau GIF.";
            } elseif ($file_size > 5000000) { // 5MB max, consistent with detailTambahMenu.php
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
                // Refresh menu data
                $stmt_menu = mysqli_prepare($koneksi, $query_menu);
                mysqli_stmt_bind_param($stmt_menu, "i", $id);
                mysqli_stmt_execute($stmt_menu);
                $result_menu = mysqli_stmt_get_result($stmt_menu);
                $menu_data = mysqli_fetch_assoc($result_menu);
                mysqli_stmt_close($stmt_menu);
            } else {
                $error = "Gagal memperbarui menu: " . mysqli_error($koneksi);
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
    <title>Edit Menu - Cafe Cruz</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5e8c7;
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 40px;
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            color: #2c3e50;
            text-decoration: none;
            font-size: 24px;
            font-weight: 700;
        }

        .logo-nav {
            max-height: 50px;
            margin-right: 10px;
        }

        .btn-logout {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
            border: none;
            border-radius: 20px;
            padding: 10px 20px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn-logout:hover {
            background: linear-gradient(135deg, #c0392b, #a93226);
        }

        .main-container {
            flex: 1;
            padding: 40px 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .form-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .form-input {
            width: 100%;
            padding: 12px 15px;
            border: none;
            border-radius: 15px;
            background: #333;
            color: #fff;
            font-size: 16px;
            outline: none;
            transition: background 0.3s;
        }

        .form-input:focus {
            background: #444;
        }

        .file-upload {
            width: 100%;
            padding: 12px 15px;
            border: none;
            border-radius: 15px;
            background: #333;
            color: #fff;
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
        }

        .file-upload input {
            display: none;
        }

        .file-upload label {
            margin: 0;
            cursor: pointer;
            width: 100%;
            text-align: left;
        }

        .btn-group {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .btn-back {
            background: linear-gradient(135deg, #6c757d, #5a6268);
            color: white;
            border: none;
            border-radius: 15px;
            padding: 12px 30px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
            width: 32%;
        }

        .btn-back:hover {
            background: linear-gradient(135deg, #5a6268, #495057);
        }

        .btn-cancel {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
            border: none;
            border-radius: 15px;
            padding: 12px 30px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
            width: 32%;
        }

        .btn-cancel:hover {
            background: linear-gradient(135deg, #c0392b, #a93226);
        }

        .btn-edit {
            background: #333;
            color: white;
            border: none;
            border-radius: 15px;
            padding: 12px 30px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
            width: 32%;
        }

        .btn-edit:hover {
            background: #555;
        }

        .alert {
            border-radius: 12px;
            padding: 15px 20px;
            margin-bottom: 20px;
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

        @media (max-width: 768px) {
            .navbar {
                padding: 15px 20px;
            }

            .logo-nav {
                max-height: 40px;
            }

            .form-card {
                padding: 20px;
            }

            .btn-group {
                flex-direction: column;
                gap: 10px;
            }

            .btn-back,
            .btn-cancel,
            .btn-edit {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <a class="navbar-brand" href="halamanoption.php">
            <img src="logocruz.png" alt="Logo Cafe Cruz" class="logo-nav">
            Cafe & Work Space
        </a>
        <a href="?logout=true" class="btn-logout" onclick="return confirm('Yakin ingin logout?')">Logout</a>
    </nav>

    <!-- Main Content -->
    <div class="main-container">
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

            <form method="POST" action="" enctype="multipart/form-data">
                <div class="form-group">
                    <label class="form-label">Nama :</label>
                    <input type="text" class="form-input" name="nama" required value="<?php echo htmlspecialchars($menu_data['nama']); ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Harga :</label>
                    <input type="number" class="form-input" name="harga" required min="1000" max="1000000" value="<?php echo $menu_data['harga']; ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Deskripsi :</label>
                    <input type="text" class="form-input" name="deskripsi" value="<?php echo htmlspecialchars($menu_data['deskripsi'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Gambar (JPEG, JPG, PNG, GIF) :</label>
                    <label class="file-upload">
                        <input type="file" name="gambar" accept="image/jpeg,image/jpg,image/png,image/gif">
                        <span>Choose File</span>
                        <i class="fas fa-upload"></i>
                    </label>
                </div>

                <div class="btn-group">
                    <a href="halamanmenu.php" class="btn-back">Kembali</a>
                    <button type="button" class="btn-cancel" onclick="window.location.href='halamanmenu.php'">Batal</button>
                    <button type="submit" class="btn-edit">Edit</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
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
    </script>
</body>
</html>