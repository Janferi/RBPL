<?php
session_start();

// Cek apakah user sudah login sebagai manajer
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Koneksi ke database
include 'koneksi.php';

// Cek apakah ID menu ada
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Hapus menu dari database
    $stmt = mysqli_prepare($koneksi, "DELETE FROM menu WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    
    if (mysqli_stmt_execute($stmt)) {
        // Redirect dengan pesan sukses
        header("Location: halamantambahmenu.php?success=Menu berhasil dihapus");
    } else {
        // Redirect dengan pesan error
        header("Location: halamantambahmenu.php?error=Gagal menghapus menu");
    }
} else {
    // Redirect jika ID tidak valid
    header("Location: halamantambahmenu.php?error=ID menu tidak valid");
}

exit();
?>