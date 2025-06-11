<?php
// Koneksi ke database
include 'koneksi.php';

// Inisialisasi session jika belum dimulai
session_start();

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
        // Update jumlah jika sudah ada
        $_SESSION['keranjang'][$id_menu] += $jumlah;
    } else {
        // Tambah baru jika belum ada
        $_SESSION['keranjang'][$id_menu] = $jumlah;
    }
    
    // Redirect untuk menghindari form resubmission
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

// Update jumlah item
if (isset($_GET['update']) && isset($_GET['id']) && isset($_GET['jumlah'])) {
    $id_update = $_GET['id'];
    $jumlah_baru = $_GET['jumlah'];
    
    if ($jumlah_baru > 0) {
        $_SESSION['keranjang'][$id_update] = $jumlah_baru;
    } else {
        unset($_SESSION['keranjang'][$id_update]);
    }
    
    header("Location: keranjang.php");
    exit();
}

// Hitung total harga
$total_harga = 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Basket - CRUZ Coffee & Work Space</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
</head>
<body class="bg-yellow-100 text-gray-800">
    <div class="p-5">
        <!-- Header -->
        <header class="flex justify-between items-center mb-8">
            <a href="home.php">
                <img alt="CRUZ logo" src="image/logocruz.png" width="200" />
            </a>
            <div class="flex items-center space-x-4">
                <i class="fas fa-bars text-3xl cursor-pointer"></i>
                </div>
    </div>
        </header>
        
        <div class="container mx-auto max-w-4xl">

        <!-- Basket Title -->
        <h1 class="text-5xl font-normal mb-6 italic underline">Your Basket</h1>

        <!-- Basket Items -->
        <div class="flex flex-col space-y-4 mb-8">
            <?php
            if (empty($_SESSION['keranjang'])) {
                echo '<p class="text-center text-gray-500 my-8">Keranjang Anda kosong.</p>';
            } else {
                foreach ($_SESSION['keranjang'] as $id_menu => $jumlah) {
                    // Ambil detail menu dari database
                    $query = "SELECT * FROM menu WHERE id = ?";
                    $stmt = mysqli_prepare($koneksi, $query);
                    mysqli_stmt_bind_param($stmt, "i", $id_menu);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    
                    if ($row = mysqli_fetch_assoc($result)) {
                        $nama = $row['nama'];
                        $harga = $row['harga'];
                        $gambar = $row['gambar'];
                        $subtotal = $harga * $jumlah;
                        $total_harga += $subtotal;
            ?>
                <div class="bg-gray-800 text-white rounded-2xl p-4 flex items-center justify-between shadow-lg">
                    <div class="flex items-center space-x-4">
                        <img src="data:image/jpeg;base64,<?= base64_encode($gambar) ?>" 
                             alt="<?= $nama ?>" 
                             class="w-20 h-20 object-cover rounded-xl shadow-sm">
                        <div>
                            <h3 class="text-xl font-medium"><?= $nama ?></h3>
                            <p class="text-yellow-400 text-lg font-medium">Rp<?= number_format($harga, 0, ',', '.') ?></p>
                            <a href="#" class="text-yellow-400 hover:underline edit-item text-sm mt-1 inline-block" data-id="<?= $id_menu ?>">Edit</a>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <button class="w-8 h-8 bg-yellow-200 text-black rounded font-bold flex items-center justify-center hover:bg-yellow-300 transition decrease-btn" data-id="<?= $id_menu ?>">-</button>
                        <span class="px-2 text-lg font-medium quantity-display" data-id="<?= $id_menu ?>"><?= $jumlah ?></span>
                        <button class="w-8 h-8 bg-yellow-200 text-black rounded font-bold flex items-center justify-center hover:bg-yellow-300 transition increase-btn" data-id="<?= $id_menu ?>">+</button>
                    </div>
                </div>

            <?php
                    }
                }
            }
            ?>
        </div>

        <!-- Order Button -->
        <?php if (!empty($_SESSION['keranjang'])) { ?>
        <div class="flex justify-center mb-8">
            <a href="pembayaran.php" class="bg-gray-800 text-white px-12 py-3 rounded-full font-medium hover:bg-gray-700 transition duration-300 text-lg">
                Order Now
            </a>
        </div>
        <?php } ?>

        <!-- Back to Menu -->
        <div class="text-center">
            <a href="halamanmenu.php" class="text-blue-600 hover:underline text-lg">
                <i class="fas fa-arrow-left mr-1"></i> Continue Shopping
            </a>
        </div>
    </div>

    <script>
        // Handle quantity changes
        document.addEventListener('DOMContentLoaded', function() {
            // Decrease quantity
            document.querySelectorAll('.decrease-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const quantityDisplay = document.querySelector(`.quantity-display[data-id="${id}"]`);
                    let quantity = parseInt(quantityDisplay.textContent);
                    
                    if (quantity > 1) {
                        quantity--;
                        quantityDisplay.textContent = quantity;
                        updateCartItem(id, quantity);
                    } else {
                        if (confirm('Hapus item ini dari keranjang?')) {
                            window.location.href = `keranjang.php?hapus=${id}`;
                        }
                    }
                });
            });

            // Increase quantity
            document.querySelectorAll('.increase-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const quantityDisplay = document.querySelector(`.quantity-display[data-id="${id}"]`);
                    let quantity = parseInt(quantityDisplay.textContent);
                    
                    quantity++;
                    quantityDisplay.textContent = quantity;
                    updateCartItem(id, quantity);
                });
            });

            // Edit item
            document.querySelectorAll('.edit-item').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const id = this.getAttribute('data-id');
                    window.location.href = `halamanpesam.php?id=${id}`;
                });
            });

            // Function to update cart item
            function updateCartItem(id, quantity) {
                fetch(`keranjang.php?update=true&id=${id}&jumlah=${quantity}`)
                    .then(response => {
                        if (response.ok) {
                            // Optional: refresh the page to update totals
                            // window.location.reload();
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }
        });
    </script>
</body>
</html>