<?php
require_once '../security_headers.php';
session_start();
include("koneksi.php");

// Cek apakah user sudah login sebagai staff
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
  header("Location: login_staff.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Customer Order</title>
  <link rel="stylesheet" href="style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="dashboard">
  <aside>
    <div>
      <div class="logo"><img src="images/logocruz.png" alt="CRUZ Logo"></div>
      <nav>
        <a href="#" class="active"><img src="images/dashboard.png" alt="Dashboard Icon">Dashboard</a>
        <a href="menu_staff.php"><img src="images/menu.png" alt="Menu Icon">Menu</a>
        <a href="login_staff.php" class="logout"><img src="images/logout.png" alt="Logout Icon">Logout</a>
      </nav>
    </div>
  </aside>
  <!-- ini daftarnya dari admin gasi -->
  <div class="main">
    <div class="header">
      <h1>Customer Order</h1>
      <div class="profile">
        <img src="images/profile.png" alt="Foto Profil" />
        <div>
          <div>Esekiel Janferi</div>
          <small>Staff</small>
        </div>
      </div>
    </div>

    <div class="table">
      <table>
        <thead>
          <tr>
            <th>Order No.</th>
            <th>Date & Time</th>
            <th>Name</th>
            <th>Payment Methods</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php
          // Pagination logic
          $limit = 5;
          $page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
          $start = ($page - 1) * $limit;

          // Hitung total data
          $total_query = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM pesanan");
          $total_data = mysqli_fetch_assoc($total_query)['total'];
          $total_pages = max(1, ceil($total_data / $limit));

          // Query dengan prepared statement untuk pagination
          $stmt = mysqli_prepare($koneksi, "SELECT * FROM pesanan ORDER BY id_pesanan DESC LIMIT ?, ?");
          mysqli_stmt_bind_param($stmt, "ii", $start, $limit);
          mysqli_stmt_execute($stmt);
          $q1 = mysqli_stmt_get_result($stmt);
          while ($data = mysqli_fetch_assoc($q1)) {
            ?>
            <tr>
              <td><?= $data['id_pesanan'] ?></td>
              <td><?= $data['tanggal_pesanan'] ?>, <?= $data['waktu_pesanan'] ?></td>
              <td>
                <?php
                $details = json_decode($data['detail_pesanan'], true); // decode JSON jadi array
                $result = [];
                foreach ($details as $item) {
                  $result[] = $item['nama'] . ' (' . $item['jumlah'] . ')';
                }
                echo implode('<br>', $result); // gabung semua jadi string
                ?>
              </td>
              <td><?= $data['payment_method'] ?></td>
              <td>
                <form method="POST" action="ubah_status.php" style="display:inline;">
                  <input type="hidden" name="id_pesanan" value="<?= $data['id_pesanan'] ?>">
                  <input type="hidden" name="current_status" value="<?= $data['status'] ?>">
                  <?php if ($data['status'] == 'pending') { ?>
                    <button type="submit" class="status pending">Pending</button>
                  <?php } elseif ($data['status'] == 'verified') { ?>
                    <button type="submit" class="status verified">Verified</button>
                  <?php } elseif ($data['status'] == 'done') { ?>
                    <span class="status done">Done</span>
                  <?php } ?>
                </form>
              </td>
            </tr>
          <?php } ?>
        </tbody>
        <tfoot>
          <tr>
            <td colspan="5">
              <div class="pagination-wrapper">
                <div class="pagination-left">
                  <?php if ($page > 1): ?>
                    <a href="?page=<?= $page - 1 ?>" class="footer-table">Previous page</a>
                  <?php else: ?>
                    <span class="footer-table disabled">Previous page</span>
                  <?php endif; ?>
                </div>

                <div class="pagination-center">
                  <?php for ($i = 1; $i <= max(1, $total_pages); $i++): ?>
                    <a href="?page=<?= $i ?>" class="<?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
                  <?php endfor; ?>
                </div>

                <div class="pagination-right">
                  <?php if ($page < $total_pages): ?>
                    <a href="?page=<?= $page + 1 ?>" class="footer-table">Next page</a>
                  <?php else: ?>
                    <span class="footer-table disabled">Next page</span>
                  <?php endif; ?>
                </div>
              </div>
            </td>
          </tr>
        </tfoot>
      </table>


    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>