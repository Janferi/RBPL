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

// Handle success/error messages from deletion
$success = isset($_GET['success']) ? htmlspecialchars($_GET['success']) : '';
$error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';

// Ambil data menu berdasarkan filter
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
    <title>Kelola Menu - Cafe Cruz</title>
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
            padding: 30px 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .filters {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            max-width: 1200px;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .filter-btn {
            background: #333;
            color: white;
            border: none;
            border-radius: 20px;
            padding: 10px 20px;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .filter-btn.active {
            background: linear-gradient(135deg, #27ae60, #229954);
        }

        .filter-btn:hover, .filter-btn.active:hover {
            background: linear-gradient(135deg, #229954, #1e8449);
        }

        .add-btn {
            background: linear-gradient(135deg, #27ae60, #229954);
            color: white;
            border: none;
            border-radius: 20px;
            padding: 10px 20px;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .add-btn:hover {
            background: linear-gradient(135deg, #229954, #1e8449);
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            width: 100%;
            max-width: 1200px;
            padding: 0 10px;
        }

        .menu-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 15px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }

        .menu-card:hover {
            transform: translateY(-5px);
        }

        .menu-image {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 10px;
        }

        .menu-name {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .menu-price {
            color: #27ae60;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .edit-btn {
            background: linear-gradient(135deg, #f39c12, #e67e22);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 5px 10px;
            font-size: 12px;
            cursor: pointer;
            transition: background-color 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .edit-btn:hover {
            background: linear-gradient(135deg, #e67e22, #d68910);
        }

        .delete-btn {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 5px 10px;
            font-size: 12px;
            cursor: pointer;
            transition: background-color 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .delete-btn:hover {
            background: linear-gradient(135deg, #c0392b, #a93226);
        }

        .alert {
            border-radius: 12px;
            padding: 15px 20px;
            margin-bottom: 20px;
            font-weight: 500;
            width: 100%;
            max-width: 1200px;
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
            .filters {
                flex-direction: column;
                align-items: flex-start;
            }
            .menu-grid {
                grid-template-columns: 1fr;
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

        <div class="filters">
            <a href="?category=all" class="filter-btn <?php echo ($category === 'all') ? 'active' : ''; ?>">All</a>
            <a href="?category=Main Food" class="filter-btn <?php echo ($category === 'Main Food') ? 'active' : ''; ?>">Main Food</a>
            <a href="?category=Snack" class="filter-btn <?php echo ($category === 'Snack') ? 'active' : ''; ?>">Snack</a>
            <a href="?category=Espresso Based" class="filter-btn <?php echo ($category === 'Espresso Based') ? 'active' : ''; ?>">Espresso Based</a>
            <a href="?category=Milk Based" class="filter-btn <?php echo ($category === 'Milk Based') ? 'active' : ''; ?>">Milk Based</a>
            <a href="?category=Refresher" class="filter-btn <?php echo ($category === 'Refresher') ? 'active' : ''; ?>">Refresher</a>
            <a href="detailTambahMenu.php" class="add-btn">Tambah Menu <i class="fas fa-plus"></i></a>
        </div>

        <!-- Menu Grid -->
        <div class="menu-grid">
            <?php if ($result_menu && mysqli_num_rows($result_menu) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result_menu)): ?>
                    <div class="menu-card">
                        <?php if (!empty($row['gambar'])): ?>
                            <img src="data:image/jpeg;base64,<?php echo base64_encode($row['gambar']); ?>" alt="<?php echo htmlspecialchars($row['nama']); ?>" class="menu-image">
                        <?php else: ?>
                            <div class="menu-image" style="background: #f8f9fa; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-image text-muted"></i>
                            </div>
                        <?php endif; ?>
                        <div class="menu-name"><?php echo htmlspecialchars($row['nama']); ?></div>
                        <div class="menu-price">Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></div>
                        <div class="d-flex justify-content-center gap-2">
                            <a href="detailEdit.php?id=<?php echo $row['id']; ?>" class="edit-btn">Edit <i class="fas fa-edit"></i></a>
                            <a href="deletemenu.php?id=<?php echo $row['id']; ?>" class="delete-btn" onclick="return confirm('Yakin ingin menghapus menu <?php echo htmlspecialchars($row['nama']); ?>?')">Hapus <i class="fas fa-trash"></i></a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="text-align: center; width: 100%; color: #7f8c8d; padding: 20px;">
                    <i class="fas fa-utensils" style="font-size: 48px; margin-bottom: 10px;"></i>
                    <h4>Belum Ada Menu</h4>
                    <p>Silakan tambahkan menu pertama</p>
                </div>
            <?php endif; ?>
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