<?php
  $koneksi = mysqli_connect("localhost", "root", "", "cruz");

  // Check connection
  if (!$koneksi) {
      die("Koneksi gagal: " . mysqli_connect_error());
  }
?>