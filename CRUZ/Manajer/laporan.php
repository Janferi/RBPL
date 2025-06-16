<?php
// Start session at the top
session_start();

// Include database connection (adjust path as needed)
include 'koneksi.php';

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Handle form submission for date range or download
$periode = '';
$downloadSuccess = false;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['periode'])) {
        $periode = $_POST['periode'];
    }
    if (isset($_POST['download']) && !empty($periode)) {
        // Placeholder for report download (replace with actual data)
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="laporan_' . $periode . '.csv"');
        echo "Date,Revenue,Orders\n"; // Example data
        $downloadSuccess = true;
        exit();
    }
}

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan - Cafe Cruz</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f5e8c7, #f9f1d8);
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
            padding: 15px 30px;
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 1000;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            color: #2c3e50;
            text-decoration: none;
            font-size: 22px;
            font-weight: 700;
            transition: color 0.3s ease;
        }

        .navbar-brand:hover {
            color: #3498db;
        }

        .logo-nav {
            max-height: 45px;
            margin-right: 10px;
            transition: transform 0.3s ease;
        }

        .navbar-brand:hover .logo-nav {
            transform: scale(1.05);
        }

        .btn-logout {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
            border: none;
            border-radius: 20px;
            padding: 8px 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(231, 76, 60, 0.3);
        }

        .btn-logout:hover {
            background: linear-gradient(135deg, #c0392b, #a93226);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(231, 76, 60, 0.4);
        }

        .main-container {
            flex: 1;
            padding: 40px 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .form-card {
            background: linear-gradient(135deg, #333, #444);
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 320px;
            text-align: center;
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-weight: 600;
            color: #ffd700;
            margin-bottom: 12px;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .periode-input {
            width: 100%;
            padding: 12px 15px;
            border: none;
            border-radius: 15px;
            background: #fff;
            color: #333;
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .periode-input:hover {
            transform: scale(1.02);
        }

        .periode-input input {
            border: none;
            background: none;
            width: 80%;
            color: #333;
            font-size: 16px;
        }

        .periode-input .calendar-icon {
            color: #ffd700;
            font-size: 18px;
        }

        .btn-group {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin-top: 10px;
        }

        .btn-back {
            background: linear-gradient(135deg, #6c757d, #5a6268);
            color: white;
            border: none;
            border-radius: 15px;
            padding: 10px 20px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.3);
            flex: 1;
        }

        .btn-back:hover {
            background: linear-gradient(135deg, #5a6268, #495057);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.4);
        }

        .btn-unduh {
            background: linear-gradient(135deg, #333, #555);
            color: #ffd700;
            border: none;
            border-radius: 15px;
            padding: 10px 20px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.3);
            flex: 1;
        }

        .btn-unduh:hover {
            background: linear-gradient(135deg, #555, #666);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.4);
            color: #fff;
        }

        .alert {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(135deg, #27ae60, #229954);
            color: white;
            padding: 10px 20px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            display: none;
            animation: slideIn 0.5s ease, fadeOut 2.5s ease forwards;
        }

        @keyframes slideIn {
            from { top: -50px; opacity: 0; }
            to { top: 20px; opacity: 1; }
        }

        @keyframes fadeOut {
            to { opacity: 0; }
        }

        @media (max-width: 768px) {
            .navbar {
                padding: 10px 15px;
            }
            .logo-nav {
                max-height: 40px;
            }
            .form-card {
                padding: 20px;
                max-width: 280px;
            }
            .form-label {
                font-size: 14px;
            }
            .btn-group {
                flex-direction: column;
                gap: 8px;
            }
            .btn-back, .btn-unduh {
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
            CRUZ
        </a>
        <a href="?logout=true" class="btn-logout" onclick="return confirm('Yakin ingin logout?')">Logout</a>
    </nav>

    <!-- Main Content -->
    <div class="main-container">
        <div class="form-card">
            <form method="POST" action="" id="reportForm">
                <div class="form-group">
                    <label class="form-label">Pilih periode</label>
                    <div class="periode-input">
                        <input type="month" name="periode" value="<?php echo htmlspecialchars($periode); ?>" required>
                        
                    </div>
                </div>
                <div class="btn-group">
                    <a href="halamanoption.php" class="btn-back">Kembali</a>
                    <button type="submit" name="download" class="btn-unduh">Unduh</button>
                </div>
            </form>
            <?php if ($downloadSuccess): ?>
                <div class="alert" id="downloadAlert">Laporan berhasil diunduh!</div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-submit form on month change
        document.querySelector('input[type="month"]').addEventListener('change', function() {
            this.form.submit();
        });

        // Show download alert if success flag is set
        document.addEventListener('DOMContentLoaded', function() {
            const alert = document.getElementById('downloadAlert');
            if (alert) {
                alert.style.display = 'block';
                setTimeout(() => {
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                }, 2500);
            }
        });
    </script>
</body>

</html>