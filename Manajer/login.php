<?php
session_start();
include 'koneksi.php';

if (isset($_SESSION['username'])) {
    header("Location: halamanoption.php");
    exit();
}

$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Prepared statement untuk mencegah SQL Injection
    $stmt = mysqli_prepare($koneksi, "SELECT * FROM manajer WHERE username = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        // Verifikasi password
        if ($row['password'] === $password) {
            $_SESSION['username'] = $row['username'];
            $_SESSION['id_manajer'] = $row['id'];
            mysqli_stmt_close($stmt);
            header("Location: halamanoption.php");
            exit();
        } else {
            $error = "Username atau password salah!";
        }
    } else {
        $error = "Username atau password salah!";
    }
    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Login - CRUZ</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Manrope:wght@200;300;400;500;600&display=swap"
        rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'cruz-black': '#121212',
                        'cruz-charcoal': '#1a1a1a',
                        'cruz-gold': '#C5A065',
                        'cruz-cream': '#F5F5F0',
                    },
                    fontFamily: {
                        'serif': ['"Playfair Display"', 'serif'],
                        'sans': ['"Manrope"', 'sans-serif'],
                    },
                }
            }
        }
    </script>
</head>

<body class="min-h-screen flex items-center justify-center font-sans antialiased p-6 relative">
    <!-- Background Image -->
    <div class="absolute inset-0 bg-cover bg-center bg-fixed"
        style="background-image: url('../image/backgroundhome.jpg');"></div>
    <div class="absolute inset-0 bg-black/70"></div>

    <div class="w-full max-w-md relative z-10">
        <!-- Login Card -->
        <div class="bg-white p-10 md:p-14 text-center relative shadow-2xl">
            <!-- Golden Accent -->
            <div class="absolute top-0 left-1/2 transform -translate-x-1/2 w-16 h-1 bg-cruz-gold"></div>

            <!-- Logo/Brand -->
            <div class="mb-10 mt-4">
                <h1 class="text-4xl font-serif text-cruz-charcoal tracking-widest mb-2">CRUZ.</h1>
                <p class="text-cruz-gold text-xs uppercase tracking-[0.3em]">Manager Console</p>
            </div>

            <?php if ($error): ?>
                <div class="bg-red-500/20 border border-red-500/40 text-red-400 text-sm p-3 mb-6 text-center">
                    <i class="fas fa-exclamation-triangle mr-2"></i><?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="" id="loginForm" class="space-y-6">
                <div>
                    <input type="text" name="username" placeholder="Username" required
                        class="w-full bg-cruz-cream border border-gray-200 text-cruz-charcoal placeholder-gray-400 px-4 py-4 text-sm tracking-wide focus:outline-none focus:border-cruz-gold transition">
                </div>

                <div>
                    <input type="password" name="password" placeholder="Password" required
                        class="w-full bg-cruz-cream border border-gray-200 text-cruz-charcoal placeholder-gray-400 px-4 py-4 text-sm tracking-wide focus:outline-none focus:border-cruz-gold transition">
                </div>

                <button type="submit"
                    class="w-full bg-cruz-gold text-white py-4 text-xs uppercase tracking-[0.2em] hover:bg-white hover:text-cruz-black transition duration-300 mt-4">
                    Sign In
                </button>
            </form>

            <p class="text-gray-500 text-[10px] uppercase tracking-widest mt-12">
                <i class="fas fa-lock mr-1"></i> Secure Access
            </p>
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function (e) {
            const btn = this.querySelector('button[type="submit"]');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Processing...';
            btn.disabled = true;
        });
    </script>
</body>

</html>