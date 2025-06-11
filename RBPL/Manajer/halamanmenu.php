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

// Proses hapus menu
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $query = "DELETE FROM menu WHERE id = $id";
    if (mysqli_query($koneksi, $query)) {
        $success = "Menu berhasil dihapus!";
    } else {
        $error = "Gagal menghapus menu!";
    }
}

// Ambil data menu
$query_menu = "SELECT * FROM menu ORDER BY id DESC";
$result_menu = mysqli_query($koneksi, $query_menu);

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Menu - Cafe Cruz</title>
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
            padding: 30px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.2);
            margin-bottom: 30px;
        }

        .table-container {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .table {
            margin-bottom: 0;
            background: white;
        }

        .table th {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            font-weight: 600;
            border: none;
            padding: 15px 12px;
            text-align: center;
            font-size: 14px;
        }

        .table td {
            padding: 15px 12px;
            vertical-align: middle;
            border-bottom: 1px solid #f8f9fa;
            text-align: center;
        }

        .table tbody tr:hover {
            background: rgba(52, 152, 219, 0.05);
            transition: all 0.3s ease;
        }

        .menu-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        .menu-name {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .menu-category {
            color: #7f8c8d;
            font-size: 12px;
            background: #ecf0f1;
            padding: 3px 8px;
            border-radius: 12px;
            display: inline-block;
        }

        .menu-price {
            font-weight: 700;
            color: #27ae60;
            font-size: 16px;
        }

        .menu-description {
            color: #7f8c8d;
            font-size: 13px;
            max-width: 200px;
            text-align: left;
        }

        .btn-action {
            border: none;
            border-radius: 8px;
            padding: 6px 12px;
            font-size: 12px;
            font-weight: 600;
            margin: 2px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-edit {
            background: linear-gradient(135deg, #f39c12, #e67e22);
            color: white;
            box-shadow: 0 2px 8px rgba(243, 156, 18, 0.3);
        }

        .btn-edit:hover {
            background: linear-gradient(135deg, #e67e22, #d68910);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(243, 156, 18, 0.4);
            color: white;
        }

        .btn-delete {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
            box-shadow: 0 2px 8px rgba(231, 76, 60, 0.3);
        }

        .btn-delete:hover {
            background: linear-gradient(135deg, #c0392b, #a93226);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(231, 76, 60, 0.4);
            color: white;
        }

        .btn-add {
            background: linear-gradient(135deg, #27ae60, #229954);
            border: none;
            border-radius: 15px;
            padding: 12px 25px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(39, 174, 96, 0.3);
            text-decoration: none;
            display: inline-block;
            margin-bottom: 20px;
        }

        .btn-add:hover {
            background: linear-gradient(135deg, #229954, #1e8449);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(39, 174, 96, 0.4);
            color: white;
            text-decoration: none;
        }

        .alert {
            border-radius: 12px;
            border: none;
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

        .user-info {
            color: #2c3e50;
            font-weight: 600;
            margin-right: 15px;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #7f8c8d;
        }

        .empty-icon {
            font-size: 64px;
            margin-bottom: 20px;
            color: #bdc3c7;
        }

        .stats-row {
            display: flex;
            justify-content: space-around;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            min-width: 150px;
            margin: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .stat-number {
            font-size: 32px;
            font-weight: 700;
            color: #2c3e50;
        }

        .stat-label {
            color: #7f8c8d;
            font-size: 14px;
            margin-top: 5px;
        }

        @media (max-width: 768px) {
            .page-title {
                font-size: 32px;
            }

            .content-card {
                padding: 20px;
                margin: 0 10px 20px 10px;
            }

            .table {
                font-size: 12px;
            }

            .menu-image {
                width: 60px;
                height: 60px;
            }

            .table th,
            .table td {
                padding: 8px 6px;
            }

            .stats-row {
                flex-direction: column;
                align-items: center;
            }

            .stat-card {
                margin: 5px 0;
                min-width: 120px;
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
                <a href="halamanoption.php" class="btn btn-back">
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
                <h1 class="page-title">Kelola Menu</h1>
                <p class="page-subtitle">Kelola semua menu yang tersedia di Cafe Cruz</p>
            </div>

            <!-- Statistics -->
            <?php
            $total_menu = mysqli_num_rows($result_menu);
            $query_kategori = "SELECT DISTINCT category FROM menu";
            $result_kategori = mysqli_query($koneksi, $query_kategori);
            $total_kategori = mysqli_num_rows($result_kategori);
            ?>

            <div class="stats-row fade-in">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $total_menu; ?></div>
                    <div class="stat-label">Total Menu</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $total_kategori; ?></div>
                    <div class="stat-label">Kategori</div>
                </div>
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

                <!-- Add Menu Button -->
                <a href="detailTambahMenu.php" class="btn btn-add">
                    <i class="fas fa-plus me-2"></i>Tambah Menu Baru
                </a>

                <!-- Menu Table -->
                <?php if ($total_menu > 0): ?>
                    <div class="table-container">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 5%;">No</th>
                                    <th style="width: 15%;">Gambar</th>
                                    <th style="width: 20%;">Nama & Kategori</th>
                                    <th style="width: 15%;">Harga</th>
                                    <th style="width: 30%;">Deskripsi</th>
                                    <th style="width: 15%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                mysqli_data_seek($result_menu, 0); // Reset pointer
                                while ($row = mysqli_fetch_assoc($result_menu)):
                                ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td>
                                            <?php if (!empty($row['gambar'])): ?>
                                                <img src="data:image/jpeg;base64,<?php echo base64_encode($row['gambar']); ?>"
                                                    alt="<?php echo htmlspecialchars($row['nama']); ?>"
                                                    class="menu-image">
                                            <?php else: ?>
                                                <div class="menu-image d-flex align-items-center justify-content-center" style="background: #f8f9fa;">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="menu-name"><?php echo htmlspecialchars($row['nama']); ?></div>
                                            <span class="menu-category"><?php echo htmlspecialchars($row['category']); ?></span>
                                        </td>
                                        <td>
                                            <div class="menu-price">Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></div>
                                        </td>
                                        <td>
                                            <div class="menu-description"><?php echo htmlspecialchars($row['deskripsi']); ?></div>
                                        </td>
                                        <td>
                                            <a href="detailEdit.php?id=<?php echo $row['id']; ?>"
                                                class="btn btn-action btn-edit"
                                                title="Edit Menu">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a href="?hapus=<?php echo $row['id']; ?>"
                                                class="btn btn-action btn-delete"
                                                onclick="return confirm('Yakin ingin menghapus menu <?php echo htmlspecialchars($row['nama']); ?>?')"
                                                title="Hapus Menu">
                                                <i class="fas fa-trash"></i> Hapus
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-utensils"></i>
                        </div>
                        <h4>Belum Ada Menu</h4>
                        <p>Silakan tambahkan menu pertama untuk cafe Anda</p>
                        <a href="detailTambahMenu.php" class="btn btn-add mt-3">
                            <i class="fas fa-plus me-2"></i>Tambah Menu Pertama
                        </a>
                    </div>
                <?php endif; ?>
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

        // Smooth hover effects
        document.querySelectorAll('.table tbody tr').forEach(row => {
            row.addEventListener('mouseenter', function() {
                this.style.transform = 'scale(1.02)';
                this.style.transition = 'all 0.3s ease';
            });

            row.addEventListener('mouseleave', function() {
                this.style.transform = 'scale(1)';
            });
        });

        // Loading state untuk tombol hapus
        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', function(e) {
                if (confirm(this.getAttribute('onclick').match(/'([^']*)'/)[1])) {
                    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menghapus...';
                    this.style.pointerEvents = 'none';
                }
            });
        });
    </script>
</body>

</html>