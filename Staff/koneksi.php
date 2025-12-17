<?php 
$host = "localhost";
$user = "root";
$pass = "janferi12345";
$db = "cruz";

$koneksi = mysqli_connect($host, $user, $pass, $db);
if (!$koneksi) {
    die("Tidak bisa terkoneksi ke database");
}

?>