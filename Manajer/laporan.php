<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$periode = '';
$downloadSuccess = false;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['periode'])) {
        $periode = htmlspecialchars($_POST['periode']);
    }
    if (isset($_POST['download']) && !empty($periode)) {
        // Parse periode (format: YYYY-MM)
        $parts = explode('-', $periode);
        if (count($parts) == 2) {
            $year = (int) $parts[0];
            $month = (int) $parts[1];

            // Query data pesanan berdasarkan periode
            $stmt = mysqli_prepare(
                $koneksi,
                "SELECT DATE(tanggal_pesanan) as tanggal, 
                        SUM(total_bayar) as revenue, 
                        COUNT(*) as orders 
                 FROM pesanan 
                 WHERE YEAR(tanggal_pesanan) = ? AND MONTH(tanggal_pesanan) = ? AND status = 'done'
                 GROUP BY DATE(tanggal_pesanan) 
                 ORDER BY tanggal_pesanan ASC"
            );
            mysqli_stmt_bind_param($stmt, "ii", $year, $month);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            // Output CSV
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="laporan_' . $periode . '.csv"');

            $output = fopen('php://output', 'w');
            // Header CSV
            fputcsv($output, ['Tanggal', 'Revenue (IDR)', 'Jumlah Pesanan']);

            // Data rows
            $totalRevenue = 0;
            $totalOrders = 0;
            while ($row = mysqli_fetch_assoc($result)) {
                fputcsv($output, [$row['tanggal'], $row['revenue'], $row['orders']]);
                $totalRevenue += $row['revenue'];
                $totalOrders += $row['orders'];
            }

            // Total row
            fputcsv($output, ['TOTAL', $totalRevenue, $totalOrders]);

            fclose($output);
            mysqli_stmt_close($stmt);
            exit();
        }
    }
}

$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan - CRUZ Manager</title>
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

<body class="bg-cruz-cream text-cruz-charcoal font-sans antialiased min-h-screen flex flex-col">

    <!-- Navbar -->
    <nav class="bg-white border-b border-gray-100 py-5">
        <div class="container mx-auto px-6 flex justify-between items-center">
            <a href="halamanoption.php"
                class="text-xl font-serif font-bold text-cruz-charcoal tracking-widest uppercase">CRUZ.</a>
            <a href="halamanoption.php"
                class="text-xs uppercase tracking-widest text-gray-500 hover:text-cruz-gold transition">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </nav>

    <main class="flex-grow flex items-center justify-center p-6">
        <div class="w-full max-w-sm bg-white text-cruz-charcoal p-10 shadow-xl relative">
            <div class="absolute top-0 left-1/2 transform -translate-x-1/2 w-16 h-1 bg-cruz-gold"></div>

            <div class="text-center mb-10 mt-4">
                <h1 class="text-2xl font-serif italic mb-2">Unduh Laporan</h1>
                <p class="text-gray-400 text-xs uppercase tracking-widest">Sales Report</p>
            </div>

            <form method="POST" class="space-y-8">
                <div>
                    <label class="block text-xs uppercase tracking-widest text-gray-400 mb-3">Pilih Periode</label>
                    <input type="month" name="periode" value="<?= htmlspecialchars($periode) ?>" required
                        class="w-full border border-gray-200 bg-cruz-cream px-4 py-4 text-sm text-center focus:outline-none focus:border-cruz-gold transition">
                </div>

                <div class="flex gap-4">
                    <a href="halamanoption.php"
                        class="flex-1 text-center border border-gray-200 text-gray-500 py-3 text-xs uppercase tracking-widest hover:border-cruz-charcoal hover:text-cruz-charcoal transition">
                        Kembali
                    </a>
                    <button type="submit" name="download"
                        class="flex-1 bg-cruz-charcoal text-white py-3 text-xs uppercase tracking-widest hover:bg-cruz-gold transition">
                        <i class="fas fa-download mr-2"></i> Unduh
                    </button>
                </div>
            </form>
        </div>
    </main>

</body>

</html>