<?php
require_once '../security_headers.php';
session_start();

include("koneksi.php");

// if(isset($_GET['logout']) && $_GET['logout'] == 'sukses') {
//     $sukses = "Logout berhasil";
// }

if (isset($_POST["login"])) {
    // Validasi CSRF token
    if (!csrf_validate_token()) {
        $error = "Security token tidak valid. Silakan refresh halaman.";
    } else {
        $username = sanitize_input(trim($_POST["username"]));
        $password = trim($_POST["password"]);

        if (!empty($username) && !empty($password)) {
            // Prepared statement untuk mencegah SQL Injection
            $stmt = mysqli_prepare($koneksi, "SELECT * FROM staff WHERE username = ?");
            mysqli_stmt_bind_param($stmt, "s", $username);
            mysqli_stmt_execute($stmt);
            $q1 = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($q1) == 1) {
                $q2 = mysqli_fetch_array($q1);
                if ($password === $q2["password"]) {
                    // Regenerate session ID untuk mencegah session fixation
                    secure_session_regenerate();
                    $_SESSION["login"] = true;
                    $_SESSION["staff_username"] = $username;
                    mysqli_stmt_close($stmt);
                    header("refresh:1;url=dashboard.php?login=sukses");
                    exit;
                } else {
                    $error = "Password yang dimasukkan salah!";
                }
            } else {
                $error = "Username tidak terdaftar!";
            }
            mysqli_stmt_close($stmt);
        } else {
            $error = "Masukkan username dan password";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .alert-popup {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #ff4d4d;
            color: white;
            padding: 16px 24px;
            border-radius: 8px;
            z-index: 9999;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            animation: fadeOut 1s forwards;
            /* animasi 3 detik lalu hilang */
        }

        @keyframes fadeOut {
            0% {
                opacity: 1;
            }

            80% {
                opacity: 1;
            }

            100% {
                opacity: 0;
                display: none;
            }
        }
    </style>
</head>

<body class="login">
    <div class="container">
        <div class="image-section">
            <div class="background-layer">
                <div class="overlay"></div>
            </div>
            <img src="images/logocruz.png" alt="CRUZ Logo">
        </div>
        <?php if (!empty($error)): ?>
            <div class="alert-popup">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        <div class="form-section">
            <h1>Log In</h1>
            <form action="" method="post">
                <?php echo csrf_token_field(); ?>
                <input type="text" placeholder="Username" class="input-field" id="username" name="username"
                    value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>">
                <input type="password" placeholder="Password" class="input-field" id="password" name="password"
                    value="<?php echo isset($password) ? htmlspecialchars($password) : ''; ?>">
                <input type="submit" name="login" value="Sign In" class="btn">
            </form>
        </div>
    </div>
</body>

</html>