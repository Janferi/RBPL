<?php
session_start();
include 'koneksi.php';

// Jika sudah login, redirect ke halaman option
if (isset($_SESSION['username'])) {
    header("Location: halamanoption.php");
    exit();
}
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);
    
    // Query untuk cek login
    $query = "SELECT * FROM manajer WHERE username = '$username' AND password = '$password'";
    $result = mysqli_query($koneksi, $query);
    
    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['username'] = $row['username'];
        $_SESSION['id_manajer'] = $row['id'];
        header("Location: halamanoption.php");
        exit();
    } else {
        $error = "Username atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Manajer - Cafe Cruz</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3)), url('../image/backgroundhome.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
            padding: 0;
        }

        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            width: 100%;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(5px);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            padding: 40px;
            width: 100%;
            max-width: 400px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .logo-container {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo {
            max-width: 150px; /* Increased to match image size */
            height: auto;
            margin-bottom: 15px;
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.2));
        }

        .login-title {
            color: #2c3e50;
            font-weight: 700;
            font-size: 28px;
            margin-bottom: 10px;
            text-align: center;
        }

        .login-subtitle {
            color: #7f8c8d;
            font-size: 16px;
            text-align: center;
            margin-bottom: 30px;
        }

        .form-floating {
            margin-bottom: 20px;
        }

        .form-control {
            border: none;
            border-radius: 25px;
            padding: 12px 20px;
            font-size: 16px;
            background: #f5e8c7; /* Beige color to match image */
            color: #333;
            transition: all 0.3s ease;
            text-align: center;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .form-control:focus {
            background: #fff;
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
            outline: none;
        }

        .form-floating > label {
            color: #6c757d;
            font-weight: 500;
            position: absolute;
            left: 20px;
            top: 10px;
            transform: none;
            opacity: 0;
            transition: all 0.3s ease;
        }

        .form-floating:focus-within > label,
        .form-floating:not(:placeholder-shown) > label {
            opacity: 1;
            transform: translateY(-20px);
            color: #3498db;
            font-size: 12px;
        }

        .btn-login {
            background: #f5e8c7; /* Beige to match image */
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            font-size: 18px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(245, 232, 199, 0.3);
            color: #333;
            width: 100%;
        }

        .btn-login:hover {
            background: #e0d4b1;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(245, 232, 199, 0.4);
            color: #2c3e50;
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .alert {
            border-radius: 12px;
            border: none;
            padding: 15px;
            margin-bottom: 20px;
            font-weight: 500;
            text-align: center;
        }

        .alert-danger {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
        }

        .input-group-text {
            display: none; /* Removed to match image simplicity */
        }

        .password-toggle {
            display: none; /* Removed to match image simplicity */
        }

        .footer-text {
            text-align: center;
            margin-top: 30px;
            color: #7f8c8d;
            font-size: 14px;
        }

        .loading {
            display: none;
        }

        .btn-login.loading .btn-text {
            display: none;
        }

        .btn-login.loading .loading {
            display: inline-block;
        }

        @media (max-width: 576px) {
            .login-card {
                padding: 30px 20px;
                margin: 10px;
            }
            
            .login-title {
                font-size: 24px;
            }
            
            .logo {
                max-width: 120px; /* Slightly smaller on mobile */
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="logo-container">
                <img src="../image/logocruz.PNG" alt="Logo Cafe Cruz" class="logo">
                <h2 class="login-title">Login Manajer</h2>
                <p class="login-subtitle">Selamat datang di Cafe Cruz</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="" id="loginForm">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
                    <label for="username"><i class="fas fa-user me-2"></i>Username</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                    <label for="password"><i class="fas fa-lock me-2"></i>Password</label>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-login">
                        <span class="btn-text">
                            <i class="fas fa-sign-in-alt me-2"></i>Masuk
                        </span>
                        <span class="loading">
                            <i class="fas fa-spinner fa-spin me-2"></i>Memproses...
                        </span>
                    </button>
                </div>
            </form>

            <div class="footer-text">
                <i class="fas fa-coffee me-1"></i>
                Sistem Manajemen Cafe Cruz
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        // Loading effect saat submit
        document.getElementById('loginForm').addEventListener('submit', function() {
            const submitBtn = document.querySelector('.btn-login');
            submitBtn.classList.add('loading');
            submitBtn.disabled = true;
        });

        // Auto focus ke username saat halaman dimuat
        window.addEventListener('load', function() {
            document.getElementById('username').focus();
        });

        // Enter key navigation
        document.getElementById('username').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                document.getElementById('password').focus();
            }
        });
    </script>
</body>
</html>