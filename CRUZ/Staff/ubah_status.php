<?php
include("koneksi.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id_pesanan = $_POST['id_pesanan'];
  $current_status = $_POST['current_status'];

  // Tentukan status selanjutnya
  if ($current_status === 'pending') {
    $next_status = 'verified';
  } elseif ($current_status === 'verified') {
    $next_status = 'done';
  } else {
    // Done = sudah final, tidak berubah lagi
    header("Location: dashboard.php");
    exit;
  }

  // Update ke database
  $query = "UPDATE pesanan SET status = '$next_status' WHERE id_pesanan = '$id_pesanan'";
  mysqli_query($koneksi, $query);

  // Redirect balik ke halaman utama
  header("Location: dashboard.php"); // Ganti dengan file utama kamu
  exit;
}
?>
