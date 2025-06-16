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
            background: linear-gradient(135deg, #f5e8c7, #f9f1d8);
            /* Gradasi warna lembut */
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            overflow-x: hidden;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 40px;
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 1000;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            color: #2c3e50;
            text-decoration: none;
            font-size: 24px;
            font-weight: 700;
            transition: color 0.3s;
        }

        .navbar-brand:hover {
            color: #3498db;
        }

        .logo-nav {
            max-height: 80px; /* Increased logo size */
            width: auto;
            margin-right: 15px;
            transition: transform 0.3s ease;
        }

        .logo-nav:hover {
            transform: scale(1.1);
        }

        .btn-logout {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
            border: none;
            border-radius: 25px;
            padding: 10px 25px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 10px rgba(231, 76, 60, 0.3);
        }

        .btn-logout:hover {
            background: linear-gradient(135deg, #c0392b, #a93226);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(231, 76, 60, 0.4);
        }

        .main-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 40px 20px;
        }

        .options {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
            width: 100%;
            max-width: 450px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.6s ease-in-out;
        }

        .option-btn {
            background: linear-gradient(135deg, #333, #555);
            color: white;
            border: none;
            border-radius: 25px;
            padding: 15px 30px;
            font-size: 18px;
            cursor: pointer;
            width: 100%;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .option-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(120deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.6s ease;
        }

        .option-btn:hover::before {
            left: 100%;
        }

        .option-btn:hover {
            background: linear-gradient(135deg, #555, #777);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        @media (max-width: 768px) {
            .navbar {
                padding: 15px 20px;
            }

            .logo-nav {
                max-height: 60px; /* Adjusted for smaller screens */
            }

            .navbar-brand {
                font-size: 20px;
            }

            .options {
                max-width: 300px;
                padding: 20px;
            }

            .option-btn {
                font-size: 16px;
                padding: 12px 20px;
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
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
    <nav class="navbar">
        <a class="navbar-brand" href="#">
            <img src="logocruz.png" alt="Logo Cafe Cruz" class="logo-nav">
            Cafe & Work Space
        </a>
        <a href="?logout=true" class="btn-logout" onclick="return confirm('Yakin ingin logout?')">Logout</a>
    </nav>

    <!-- Main Content -->
    <div class="main-container">
        <div class="options">
            <a href="halamanmenu.php" class="option-btn">Daftar Menu</a>
            <a href="detailTambahmenu.php" class="option-btn">Tambah Menu</a>
            <a href="laporan.php" class="option-btn">Laporan Transaksi</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>