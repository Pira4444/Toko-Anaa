<?php
$conn = mysqli_connect("localhost", "root", "", "toko_db");
if (!$conn) { die("Koneksi Gagal: " . mysqli_connect_error()); }
?>