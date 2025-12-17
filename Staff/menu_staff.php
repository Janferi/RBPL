<?php
session_start();
include("koneksi.php");

// Cek apakah user sudah login sebagai staff
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
  header("Location: login_staff.php");
  exit;
}

$category = isset($_GET['category']) ? $_GET['category'] : 'all';
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Menu - CRUZ</title>
  <link rel="stylesheet" href="style.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />

</head>

<body>

  <div class="header">
    <div class="logo-menu">
      <img src="images/logocruz.png" alt="CRUZ Logo">
    </div>
    <img src="images/sidebar.png" id="menu-toggle" alt="Toggle Menu" class="menu-toggle" />
  </div>

  <?php
  // Category sudah didefinisikan di atas
  ?>

  <div class="category-menu">
    <a href="?category=all">
      <button class="category-button <?= ($category == 'all') ? 'active' : '' ?>">All</button>
    </a>

    <?php
    $cat_query = "SELECT DISTINCT category FROM menu";
    $cat_result = mysqli_query($koneksi, $cat_query);

    if ($cat_result) {
      while ($cat_row = mysqli_fetch_assoc($cat_result)) {
        $cat_name = $cat_row['category'];
        $is_active = ($category == $cat_name) ? 'active' : '';
        echo '<a href="?category=' . urlencode($cat_name) . '">';
        echo '<button class="category-button ' . $is_active . '">' . htmlspecialchars($cat_name) . '</button>';
        echo '</a>';
      }
    }
    ?>
  </div>

  <div class="menu-container">
    <?php
    // Category sudah didefinisikan di atas
    
    // Query berdasarkan kategori
    if ($category == 'all') {
      $query = "SELECT * FROM menu";
      $result = mysqli_query($koneksi, $query);
    } else {
      $stmt = mysqli_prepare($koneksi, "SELECT * FROM menu WHERE category = ?");
      mysqli_stmt_bind_param($stmt, "s", $category);
      mysqli_stmt_execute($stmt);
      $result = mysqli_stmt_get_result($stmt);
    }

    // Tampilkan menu
    if (mysqli_num_rows($result) > 0) {
      while ($row = mysqli_fetch_assoc($result)) {
        $name = htmlspecialchars($row['nama']);
        $price = number_format($row['harga'], 0, ',', '.');
        $imageData = base64_encode($row['gambar']);
        $imageSrc = 'data:image/jpeg;base64,' . $imageData;
        ?>
        <div class="menu-card">
          <img src="<?= $imageSrc ?>" alt="<?= $name ?>">
          <div class="menu-name"><?= $name ?></div>
          <div class="menu-price">Rp<?= $price ?></div>
        </div>
        <?php
      }
    } else {
      echo '<p class="text-center text-gray-600">Menu tidak tersedia.</p>';
    }
    ?>
  </div>
  <!-- <div class="menu-card">
      <img src="images/shrimp_fried_rice.png" alt="Shrimp Fried Rice">
      <div class="menu-name">Shrimp Fried Rice</div>
      <div class="menu-price">Rp27.000</div>
    </div>

    <div class="menu-card">
      <img src="images/chicken_matah_rice.png" alt="Chicken Matah Rice">
      <div class="menu-name">Chicken Matah Rice</div>
      <div class="menu-price">Rp23.000</div>
    </div>

    <div class="menu-card">
      <img src="images/dimsum_mentai.png" alt="Dimsum Mentai">
      <div class="menu-name">Dimsum Mentai</div>
      <div class="menu-price">Rp19.000</div>
    </div>

    <div class="menu-card">
      <img src="images/latte.png" alt="Latte">
      <div class="menu-name">Latte</div>
      <div class="menu-price">Rp26.000</div>
    </div>

    <div class="menu-card">
      <img src="images/es_kopi_creamer.png" alt="Es Kopi Creamer">
      <div class="menu-name">Es Kopi Creamer</div>
      <div class="menu-price">Rp32.000</div>
    </div>

    <div class="menu-card">
      <img src="images/es_kopi_susu_cruz.png" alt="Es Kopi Susu Cruz">
      <div class="menu-name">Es Kopi Susu Cruz</div>
      <div class="menu-price">Rp32.000</div>
    </div>
  </div> -->


  <!-- Side Navigation Menu -->
  <!-- Sidebar (default hidden & slide-in from right) -->
  <div id="side-menu"
    class="fixed top-0 right-0 h-full w-64 bg-[#DBD19C] z-50 transform translate-x-full transition-transform duration-300 ease-in-out shadow-lg">

    <!-- Close button -->
    <div id="close-menu" class="text-3xl cursor-pointer absolute top-7 left-8">&#9776;</div>

    <!-- Menu Items -->
    <div class="flex flex-col space-y-4 ml-4 mt-20">
      <!-- Dashboard -->
      <a href="dashboard.php" class="flex items-center gap-2 px-4 py-2 rounded-md hover:bg-[#E9DFA5] transition">
        <img src="images/dashboard.png" alt="Dashboard Icon" class="w-6 h-6" />
        <span class="text-[#4C4C4C] text-[20px] font-medium">Dashboard</span>
      </a>

      <!-- Menu (active) -->
      <a href="menu_staff.php" class="flex items-center gap-2 px-4 py-2 rounded-md bg-[#F7F0C6]">
        <img src="images/menu.png" alt="Menu Icon" class="w-6 h-6" />
        <span class="text-[#4C4C4C] text-[20px] font-medium">Menu</span>
      </a>

      <!-- Logout -->
      <a href="login_staff.php" class="flex items-center gap-2 px-4 py-2 rounded-md hover:bg-[#E9DFA5] transition">
        <img src="images/logout.png" alt="Logout Icon" class="w-6 h-6" />
        <span class="text-[#A65959] text-[20px] font-medium">Logout</span>
      </a>
    </div>
  </div>

  <!-- Overlay -->
  <div id="overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden"></div>

  <!-- JavaScript -->
  <script>
    const toggle = document.getElementById('menu-toggle');
    const closeBtn = document.getElementById('close-menu');
    const sideMenu = document.getElementById('side-menu');
    const overlay = document.getElementById('overlay');

    document.getElementById('menu-toggle').addEventListener('click', function () {
      sideMenu.classList.remove('translate-x-full');
      overlay.classList.remove('hidden');
    });

    toggle.addEventListener('click', () => {
      sideMenu.classList.remove('translate-x-full');
      overlay.classList.remove('hidden');
    });

    closeBtn.addEventListener('click', () => {
      sideMenu.classList.add('translate-x-full');
      overlay.classList.add('hidden');
    });

    overlay.addEventListener('click', () => {
      sideMenu.classList.add('translate-x-full');
      overlay.classList.add('hidden');
    });
  </script>


</body>

</html>