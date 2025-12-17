<?php
session_start();
include 'koneksi.php';

// Cek apakah user sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Cek apakah ID diberikan
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: halamanmenu.php?error=" . urlencode("ID menu tidak valid!"));
    exit();
}

$id = (int) $_GET['id'];

// Hapus menu menggunakan prepared statement
$query = "DELETE FROM menu WHERE id = ?";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    header("Location: halamanmenu.php?success=" . urlencode("Menu berhasil dihapus!"));
} else {
    mysqli_stmt_close($stmt);
    header("Location: halamanmenu.php?error=" . urlencode("Gagal menghapus menu. Silakan coba lagi."));
}
exit();
?>