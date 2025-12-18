<?php
require_once '../security_headers.php';
session_start();

// Hapus semua session dengan aman (termasuk menghapus cookie session)
secure_session_destroy();

// Redirect ke halaman login
header("Location: login.php");
exit();
?>