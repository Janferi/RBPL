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

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Manajer - Cafe Cruz</title>
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
            padding: 10px 0; /* Tambah padding untuk mengakomodasi logo yang lebih besar */
        }

        .navbar-brand {
            font-weight: 700;
            color: #2c3e50 !important;
            font-size: 24px;
            display: flex;
            align-items: center;
        }

        .logo-nav {
            max-height: 60px; /* Diperbesar dari 40px menjadi 60px */
            width: auto;
            margin-right: 15px; /* Tambah margin untuk spacing yang lebih baik */
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
            transition: transform 0.3s ease; /* Tambah efek transisi */
        }

        .logo-nav:hover {
            transform: scale(1.05); /* Efek hover untuk logo */
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

        .main-container {
            padding: 100px 0 40px 0; /* Tambah padding top untuk mengakomodasi navbar yang lebih tinggi */
            min-height: 100vh;
        }

        .welcome-section {
            text-align: center;
            margin-bottom: 50px;
        }

        .welcome-title {
            color: white;
            font-size: 48px;
            font-weight: 700;
            margin-bottom: 15px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .welcome-subtitle {
            color: rgba(255, 255, 255, 0.9);
            font-size: 20px;
            font-weight: 400;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
        }

        .options-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .option-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            border-radius: 20px;
            padding: 40px 30px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }

        .option-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.6s ease;
        }

        .option-card:hover::before {
            left: 100%;
        }

        .option-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
        }

        .option-icon {
            font-size: 64px;
            margin-bottom: 20px;
            background: linear-gradient(135deg, #3498db, #2980b9);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            filter: drop-shadow(2px 2px 4px rgba(0, 0, 0, 0.1));
        }

        .option-card.kelola .option-icon {
            background: linear-gradient(135deg, #27ae60, #229954);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .option-card.tambah .option-icon {
            background: linear-gradient(135deg, #f39c12, #e67e22);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .option-card.laporan .option-icon {
            background: linear-gradient(135deg, #9b59b6, #8e44ad);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .option-title {
            font-size: 24px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 15px;
        }

        .option-description {
            color: #7f8c8d;
            font-size: 16px;
            margin-bottom: 25px;
            line-height: 1.6;
        }

        .btn-option {
            background: linear-gradient(135deg, #3498db, #2980b9);
            border: none;
            border-radius: 15px;
            padding: 12px 30px;
            color: white;
            font-weight: 600;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
            text-decoration: none;
            display: inline-block;
        }

        .btn-option:hover {
            background: linear-gradient(135deg, #2980b9, #21618c);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(52, 152, 219, 0.4);
            color: white;
            text-decoration: none;
        }

        .option-card.kelola .btn-option {
            background: linear-gradient(135deg, #27ae60, #229954);
            box-shadow: 0 4px 15px rgba(39, 174, 96, 0.3);
        }

        .option-card.kelola .btn-option:hover {
            background: linear-gradient(135deg, #229954, #1e8449);
            box-shadow: 0 6px 20px rgba(39, 174, 96, 0.4);
        }

        .option-card.tambah .btn-option {
            background: linear-gradient(135deg, #f39c12, #e67e22);
            box-shadow: 0 4px 15px rgba(243, 156, 18, 0.3);
        }

        .option-card.tambah .btn-option:hover {
            background: linear-gradient(135deg, #e67e22, #d68910);
            box-shadow: 0 6px 20px rgba(243, 156, 18, 0.4);
        }

        .option-card.laporan .btn-option {
            background: linear-gradient(135deg, #9b59b6, #8e44ad);
            box-shadow: 0 4px 15px rgba(155, 89, 182, 0.3);
        }

        .option-card.laporan .btn-option:hover {
            background: linear-gradient(135deg, #8e44ad, #7d3c98);
            box-shadow: 0 6px 20px rgba(155, 89, 182, 0.4);
        }

        .user-info {
            color: #2c3e50;
            font-weight: 600;
            margin-right: 15px;
        }

        @media (max-width: 768px) {
            .logo-nav {
                max-height: 45px; /* Logo sedikit lebih kecil di mobile */
                margin-right: 10px;
            }
            
            .navbar-brand {
                font-size: 20px; /* Font brand lebih kecil di mobile */
            }
            
            .welcome-title {
                font-size: 36px;
            }

            .welcome-subtitle {
                font-size: 18px;
            }

            .options-grid {
                grid-template-columns: 1fr;
                padding: 0 15px;
            }

            .option-card {
                padding: 30px 20px;
            }

            .option-icon {
                font-size: 48px;
            }

            .main-container {
                padding: 90px 0 40px 0; /* Sesuaikan padding untuk mobile */
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

        .slide-in-1 {
            animation-delay: 0.2s;
        }

        .slide-in-2 {
            animation-delay: 0.4s;
        }

        .slide-in-3 {
            animation-delay: 0.6s;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="logocruz.png" alt="Logo Cafe Cruz" class="logo-nav">
                Cafe Cruz Manager
            </a>
            <div class="d-flex align-items-center">
                <span class="user-info d-none d-sm-inline">
                    <i class="fas fa-user-circle me-2"></i>
                    Selamat datang, <?php echo htmlspecialchars($username); ?>
                </span>
                <a href="?logout=true" class="btn btn-logout" onclick="return confirm('Yakin ingin logout?')">
                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-container">
        <div class="container">
            <!-- Welcome Section -->
            <div class="welcome-section fade-in">
                <h1 class="welcome-title">Dashboard Manajer</h1>
                <p class="welcome-subtitle">Kelola Cafe Cruz dengan mudah dan efisien</p>
            </div>

            <!-- Options Grid -->
            <div class="options-grid">
                <!-- Kelola Menu -->
                <div class="option-card kelola fade-in slide-in-1">
                    <div class="option-icon">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <h3 class="option-title">Kelola Menu</h3>
                    <p class="option-description">
                        Kelola semua menu yang tersedia di cafe. Edit, hapus, atau lihat detail menu yang sudah ada.
                    </p>
                    <a href="halamanmenu.php" class="btn btn-option">
                        <i class="fas fa-cogs me-2"></i>Kelola Menu
                    </a>
                </div>

                <!-- Tambah Menu -->
                <div class="option-card tambah fade-in slide-in-2">
                    <div class="option-icon">
                        <i class="fas fa-plus-circle"></i>
                    </div>
                    <h3 class="option-title">Tambah Menu</h3>
                    <p class="option-description">
                        Tambahkan menu baru ke dalam daftar menu cafe. Lengkapi dengan nama, harga, dan deskripsi.
                    </p>
                    <a href="detailTambahmenu.php" class="btn btn-option">
                        <i class="fas fa-plus me-2"></i>Tambah Menu
                    </a>
                </div>

                <!-- Laporan Transaksi -->
                <div class="option-card laporan fade-in slide-in-3">
                    <div class="option-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3 class="option-title">Laporan Transaksi</h3>
                    <p class="option-description">
                        Lihat laporan lengkap transaksi cafe, analisis penjualan, dan statistik pendapatan.
                    </p>
                    <a href="laporan.php" class="btn btn-option">
                        <i class="fas fa-file-alt me-2"></i>Lihat Laporan
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Smooth scrolling animation
        document.addEventListener('DOMContentLoaded', function() {
            // Add stagger animation to cards
            const cards = document.querySelectorAll('.option-card');
            cards.forEach((card, index) => {
                card.style.animationDelay = `${0.2 + (index * 0.2)}s`;
            });

            // Add click animation
            cards.forEach(card => {
                card.addEventListener('click', function(e) {
                    if (!e.target.classList.contains('btn-option')) {
                        const link = this.querySelector('.btn-option');
                        if (link) {
                            link.click();
                        }
                    }
                });
            });
        });

        // Welcome message with time
        function updateWelcomeMessage() {
            const hour = new Date().getHours();
            let greeting = 'Selamat Datang';

            if (hour < 12) {
                greeting = 'Selamat Pagi';
            } else if (hour < 17) {
                greeting = 'Selamat Siang';
            } else {
                greeting = 'Selamat Sore';
            }

            const titleElement = document.querySelector('.welcome-title');
            if (titleElement) {
                titleElement.textContent = greeting + ', Manager!';
            }
        }

        // Call on page load
        updateWelcomeMessage();

        // Add hover sound effect (optional)
        document.querySelectorAll('.option-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-10px) scale(1.02)';
            });

            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });
    </script>
</body>

</html>