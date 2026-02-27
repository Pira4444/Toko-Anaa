<?php
include 'koneksi.php';
session_start();

if (isset($_POST['bayar'])) {
    $id_user = $_SESSION['user']['id_user'] ?? $_SESSION['user']['id']; 
    $nama_pembeli   = mysqli_real_escape_string($conn, $_POST['nama_pembeli']);
    $telepon        = mysqli_real_escape_string($conn, $_POST['telepon']);
    
    // --- GABUNGKAN ALAMAT (DI PROSES_BAYAR.PHP) ---
$alamat_rumah   = mysqli_real_escape_string($conn, $_POST['alamat_rumah']);
$kota           = mysqli_real_escape_string($conn, $_POST['kota']);
$provinsi       = mysqli_real_escape_string($conn, $_POST['provinsi']);

// Kita susun per baris menggunakan \n (newline)
// Urutannya: Jalan/Rumah -> Kota -> Provinsi
$alamat_final = $alamat_rumah . "\n" . $kota . "\n" . $provinsi;

// Masukkan ke query INSERT pada bagian kolom 'alamat'
$query_p = "INSERT INTO pesanan (..., alamat, ...) VALUES (..., '$alamat_final', ...)";
    
    $total_final    = (int)$_POST['total_final']; 
    $metode         = $_POST['metode']; 
    $tgl_pesan      = date('Y-m-d H:i:s');

    $query_p = "INSERT INTO pesanan (id_user, nama_pembeli, telepon, alamat, total_bayar, ongkir, metode_pembayaran, bukti_transfer, status, tgl_pesan) 
                VALUES ('$id_user', '$nama_pembeli', '$telepon', '$alamat_lengkap', '$total_final', 0, '$metode', '', 'Menunggu Ongkir', '$tgl_pesan')";
    
    if (mysqli_query($conn, $query_p)) {
        $id_pesanan_baru = mysqli_insert_id($conn);
        // Proses simpan detail barang...
        if (isset($_POST['item_keys'])) {
            foreach ($_POST['item_keys'] as $key) {
                if (isset($_SESSION['cart'][$key])) {
                    $item = $_SESSION['cart'][$key];
                    $id_pr = $item['id_produk'];
                    $qty   = $item['qty'];
                    $var   = mysqli_real_escape_string($conn, $item['varian']);
                    $harga = $item['harga'];
                    $sub   = $harga * $qty;

                    mysqli_query($conn, "INSERT INTO detail_pesanan (id_pesanan, id_produk, varian, qty, jumlah, subtotal) 
                                        VALUES ('$id_pesanan_baru', '$id_pr', '$var', '$qty', '$harga', '$sub')");
                    unset($_SESSION['cart'][$key]);
                }
            }
        }
        echo "<script>alert('Pesanan Berhasil!'); window.location='riwayat.php';</script>";
    }
}
?>