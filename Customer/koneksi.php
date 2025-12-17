<?php
$koneksi = mysqli_connect("localhost", "root", "janferi12345", "cruz");

if (!$koneksi) {
  die("Koneksi gagal: " . mysqli_connect_error());
}
