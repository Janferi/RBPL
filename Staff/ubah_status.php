<?php
require_once '../security_headers.php';
session_start();
include("koneksi.php");

// Cek apakah user sudah login sebagai staff
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
  header("Location: login_staff.php");
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id_pesanan = isset($_POST['id_pesanan']) ? (int) $_POST['id_pesanan'] : 0;
  $current_status = isset($_POST['current_status']) ? trim($_POST['current_status']) : '';

  // Validasi input
  if ($id_pesanan <= 0 || empty($current_status)) {
    header("Location: dashboard.php");
    exit;
  }

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

  // Update ke database menggunakan prepared statement
  $stmt = mysqli_prepare($koneksi, "UPDATE pesanan SET status = ? WHERE id_pesanan = ?");
  mysqli_stmt_bind_param($stmt, "si", $next_status, $id_pesanan);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_close($stmt);

  // Redirect balik ke halaman utama
  header("Location: dashboard.php");
  exit;
}
?>