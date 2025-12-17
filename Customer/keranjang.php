<?php
// Koneksi ke database
include 'koneksi.php';

// Inisialisasi session jika belum dimulai
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Inisialisasi keranjang jika belum ada
if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = array();
}

// Tambah item ke keranjang
if (isset($_POST['id_menu']) && isset($_POST['jumlah'])) {
    $id_menu = $_POST['id_menu'];
    $jumlah = $_POST['jumlah'];

    // Cek apakah menu sudah ada di keranjang
    if (isset($_SESSION['keranjang'][$id_menu])) {
        $_SESSION['keranjang'][$id_menu] += $jumlah;
    } else {
        $_SESSION['keranjang'][$id_menu] = $jumlah;
    }
    header("Location: keranjang.php");
    exit();
}

// Hapus item dari keranjang
if (isset($_GET['hapus'])) {
    $id_hapus = $_GET['hapus'];
    if (isset($_SESSION['keranjang'][$id_hapus])) {
        unset($_SESSION['keranjang'][$id_hapus]);
    }
    header("Location: keranjang.php");
    exit();
}

// Update jumlah item via AJAX
if (isset($_GET['update']) && isset($_GET['id']) && isset($_GET['jumlah'])) {
    $id_update = $_GET['id'];
    $jumlah_baru = $_GET['jumlah'];

    if ($jumlah_baru > 0) {
        $_SESSION['keranjang'][$id_update] = $jumlah_baru;
    } else {
        unset($_SESSION['keranjang'][$id_update]);
    }
    echo "ok";
    exit();
}

// Hitung total harga
$total_harga = 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart - CRUZ Coffee</title>
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
                        'cruz-gray': '#888888',
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
    <nav class="w-full bg-white border-b border-gray-100 py-6">
        <div class="container mx-auto px-6 flex justify-between items-center">
            <a href="home.php" class="text-xl font-serif font-bold text-cruz-charcoal tracking-widest uppercase">
                CRUZ.
            </a>
            <a href="halamanmenu.php"
                class="text-xs uppercase tracking-widest text-gray-500 hover:text-cruz-gold transition">
                Continue Shopping
            </a>
        </div>
    </nav>

    <div class="container mx-auto px-6 py-12 flex-grow max-w-5xl">
        <h1 class="text-4xl font-serif text-cruz-black mb-12 text-center italic">Your Selection</h1>

        <?php if (empty($_SESSION['keranjang'])) { ?>
            <div class="text-center py-20">
                <p class="text-gray-400 font-light text-lg mb-8">It seems your cart is empty.</p>
                <a href="halamanmenu.php"
                    class="px-8 py-3 border border-cruz-gold text-cruz-gold text-xs uppercase tracking-widest hover:bg-cruz-gold hover:text-white transition">
                    Explore Menu
                </a>
            </div>
        <?php } else { ?>

            <div class="flex flex-col lg:flex-row gap-8 lg:gap-16">
                <!-- Items Section -->
                <div class="lg:w-2/3">
                    
                    <!-- Desktop Table (Hidden on Mobile) -->
                    <table class="w-full text-left border-collapse hidden md:table">
                        <thead>
                            <tr class="text-xs uppercase tracking-widest text-gray-400 border-b border-gray-200">
                                <th class="pb-4 font-normal">Product</th>
                                <th class="pb-4 font-normal text-center">Qty</th>
                                <th class="pb-4 font-normal text-right">Total</th>
                                <th class="pb-4 font-normal"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php
                            foreach ($_SESSION['keranjang'] as $id_menu => $jumlah) {
                                $query = "SELECT * FROM menu WHERE id = ?";
                                $stmt = mysqli_prepare($koneksi, $query);
                                mysqli_stmt_bind_param($stmt, "i", $id_menu);
                                mysqli_stmt_execute($stmt);
                                $result = mysqli_stmt_get_result($stmt);

                                if ($row = mysqli_fetch_assoc($result)) {
                                    $nama = htmlspecialchars($row['nama']);
                                    $harga = $row['harga'];
                                    $gambar = $row['gambar'];
                                    $subtotal = $harga * $jumlah;
                                    $total_harga += $subtotal;
                                    ?>
                                    <tr class="group">
                                        <td class="py-6">
                                            <div class="flex items-center">
                                                <div class="w-16 h-16 bg-gray-100 overflow-hidden mr-6">
                                                    <img src="data:image/jpeg;base64,<?= base64_encode($gambar) ?>"
                                                        alt="<?= $nama ?>" class="w-full h-full object-cover">
                                                </div>
                                                <div>
                                                    <h3 class="text-lg font-serif text-cruz-black"><?= $nama ?></h3>
                                                    <p class="text-xs text-gray-400 mt-1">IDR
                                                        <?= number_format($harga, 0, ',', '.') ?></p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-6 text-center">
                                            <div class="inline-flex items-center border border-gray-200 px-3 py-1">
                                                <button class="text-gray-400 hover:text-cruz-black transition decrease-btn"
                                                    data-id="<?= $id_menu ?>">-</button>
                                                <span class="mx-4 font-serif text-cruz-black quantity-display"
                                                    data-id="<?= $id_menu ?>"><?= $jumlah ?></span>
                                                <button class="text-gray-400 hover:text-cruz-black transition increase-btn"
                                                    data-id="<?= $id_menu ?>">+</button>
                                            </div>
                                        </td>
                                        <td class="py-6 text-right font-light text-cruz-charcoal">
                                            IDR <?= number_format($subtotal, 0, ',', '.') ?>
                                        </td>
                                        <td class="py-6 text-right">
                                            <a href="keranjang.php?hapus=<?= $id_menu ?>"
                                                class="text-gray-300 hover:text-red-400 transition"
                                                onclick="return confirm('Remove?');">
                                                <i class="fas fa-times"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                        </tbody>
                    </table>

                    <!-- Mobile Cards (Hidden on Desktop) -->
                    <div class="md:hidden space-y-4">
                        <?php
                        // Reset total for mobile view
                        $total_harga_mobile = 0;
                        foreach ($_SESSION['keranjang'] as $id_menu => $jumlah) {
                            $query = "SELECT * FROM menu WHERE id = ?";
                            $stmt = mysqli_prepare($koneksi, $query);
                            mysqli_stmt_bind_param($stmt, "i", $id_menu);
                            mysqli_stmt_execute($stmt);
                            $result = mysqli_stmt_get_result($stmt);

                            if ($row = mysqli_fetch_assoc($result)) {
                                $nama = htmlspecialchars($row['nama']);
                                $harga = $row['harga'];
                                $gambar = $row['gambar'];
                                $subtotal = $harga * $jumlah;
                                $total_harga_mobile += $subtotal;
                                ?>
                                <div class="bg-white p-4 shadow-sm flex items-start gap-4">
                                    <!-- Image -->
                                    <div class="w-20 h-20 bg-gray-100 overflow-hidden flex-shrink-0">
                                        <img src="data:image/jpeg;base64,<?= base64_encode($gambar) ?>"
                                            alt="<?= $nama ?>" class="w-full h-full object-cover">
                                    </div>
                                    <!-- Info -->
                                    <div class="flex-grow">
                                        <div class="flex justify-between items-start mb-2">
                                            <h3 class="text-sm font-serif text-cruz-black"><?= $nama ?></h3>
                                            <a href="keranjang.php?hapus=<?= $id_menu ?>"
                                                class="text-gray-300 hover:text-red-400 transition"
                                                onclick="return confirm('Remove?');">
                                                <i class="fas fa-times text-xs"></i>
                                            </a>
                                        </div>
                                        <p class="text-xs text-gray-400 mb-3">IDR <?= number_format($harga, 0, ',', '.') ?></p>
                                        <div class="flex justify-between items-center">
                                            <div class="inline-flex items-center border border-gray-200 px-2 py-1">
                                                <button class="text-gray-400 hover:text-cruz-black transition decrease-btn text-sm"
                                                    data-id="<?= $id_menu ?>">-</button>
                                                <span class="mx-3 font-serif text-cruz-black text-sm quantity-display"
                                                    data-id="<?= $id_menu ?>"><?= $jumlah ?></span>
                                                <button class="text-gray-400 hover:text-cruz-black transition increase-btn text-sm"
                                                    data-id="<?= $id_menu ?>">+</button>
                                            </div>
                                            <span class="font-light text-cruz-charcoal text-sm">IDR <?= number_format($subtotal, 0, ',', '.') ?></span>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                        }
                        ?>
                    </div>
                </div>

                <!-- Summary -->
                <div class="lg:w-1/3">
                    <div class="bg-white p-10 shadow-xl shadow-gray-200/50 sticky top-10">
                        <h2 class="text-xl font-serif mb-8 italic">Order Summary</h2>

                        <div class="flex justify-between mb-4 text-sm font-light text-gray-600">
                            <span>Subtotal</span>
                            <span>IDR <?= number_format($total_harga, 0, ',', '.') ?></span>
                        </div>
                        <div class="flex justify-between mb-8 text-sm font-light text-gray-600">
                            <span>Tax & Service</span>
                            <span>Included</span>
                        </div>

                        <div class="w-full h-[1px] bg-gray-100 mb-8"></div>

                        <div class="flex justify-between text-xl font-serif text-cruz-black mb-10">
                            <span>Total</span>
                            <span>IDR <?= number_format($total_harga, 0, ',', '.') ?></span>
                        </div>

                        <a href="pembayaran.php"
                            class="block w-full text-center bg-cruz-charcoal text-white py-4 text-xs uppercase tracking-[0.2em] hover:bg-cruz-gold transition duration-300 shadow-lg">
                            Checkout
                        </a>
                    </div>
                </div>
            </div>

        <?php } ?>
    </div>

    <!-- Minimal Footer -->
    <footer class="bg-white border-t border-gray-100 py-6 text-center">
        <p class="text-cruz-charcoal text-[10px] uppercase tracking-widest">&copy; 2024 CRUZ Coffee</p>
    </footer>

    <!-- Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Decrease quantity
            document.querySelectorAll('.decrease-btn').forEach(button => {
                button.addEventListener('click', function () {
                    const id = this.getAttribute('data-id');
                    const quantityDisplay = document.querySelector(`.quantity-display[data-id="${id}"]`);
                    let quantity = parseInt(quantityDisplay.textContent);

                    if (quantity > 1) {
                        quantity--;
                        quantityDisplay.textContent = quantity;
                        updateCartItem(id, quantity);
                    } else {
                        if (confirm('Remove item?')) {
                            window.location.href = `keranjang.php?hapus=${id}`;
                        }
                    }
                });
            });

            // Increase quantity
            document.querySelectorAll('.increase-btn').forEach(button => {
                button.addEventListener('click', function () {
                    const id = this.getAttribute('data-id');
                    const quantityDisplay = document.querySelector(`.quantity-display[data-id="${id}"]`);
                    let quantity = parseInt(quantityDisplay.textContent);

                    quantity++;
                    quantityDisplay.textContent = quantity;
                    updateCartItem(id, quantity);
                });
            });

            // Function to update cart item
            function updateCartItem(id, quantity) {
                fetch(`keranjang.php?update=true&id=${id}&jumlah=${quantity}`)
                    .then(response => {
                        if (response.ok) {
                            window.location.reload();
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }
        });
    </script>
</body>

</html>