<?php
include 'koneksi.php';
session_start();

if (isset($_POST['kirim_bukti'])) {
    $id_pesanan = $_POST['id_pesanan'];
    
    // Konfigurasi Upload Gambar
    $nama_file = $_FILES['bukti_transfer']['name'];
    $ukuran_file = $_FILES['bukti_transfer']['size'];
    $error = $_FILES['bukti_transfer']['error'];
    $tmp_name = $_FILES['bukti_transfer']['tmp_name'];

    // 1. Cek apakah ada gambar yang diupload
    if ($error === 4) {
        echo "<script>alert('Pilih gambar bukti transfer terlebih dahulu!'); window.location='bayar.php?id=$id_pesanan';</script>";
        exit;
    }

    // 2. Cek ekstensi gambar (hanya jpg, jpeg, png)
    $ekstensiValid = ['jpg', 'jpeg', 'png'];
    $ekstensiGambar = explode('.', $nama_file);
    $ekstensiGambar = strtolower(end($ekstensiGambar));

    if (!in_array($ekstensiGambar, $ekstensiValid)) {
        echo "<script>alert('Format file harus JPG, JPEG, atau PNG!'); window.location='bayar.php?id=$id_pesanan';</script>";
        exit;
    }

    // 3. Cek ukuran file (Max 2MB)
    if ($ukuran_file > 2000000) {
        echo "<script>alert('Ukuran file terlalu besar! Maksimal 2MB'); window.location='bayar.php?id=$id_pesanan';</script>";
        exit;
    }

    // 4. Generate nama file baru (agar tidak bentrok)
    $namaFileBaru = "BUKTI_" . $id_pesanan . "_" . time() . "." . $ekstensiGambar;

    // 5. Tentukan folder tujuan (pastikan folder img/bukti sudah ada)
    $folder_tujuan = 'img/bukti/';
    if (!is_dir($folder_tujuan)) {
        mkdir($folder_tujuan, 0777, true);
    }

    // 6. Pindahkan file dan Update Database
    if (move_uploaded_file($tmp_name, $folder_tujuan . $namaFileBaru)) {
        
        // Update status pesanan dan simpan nama file bukti
        $status_baru = "Menunggu Verifikasi";
        $update = mysqli_query($conn, "UPDATE pesanan SET 
                    status = '$status_baru', 
                    bukti_transfer = '$namaFileBaru' 
                    WHERE id_pesanan = '$id_pesanan'");

        if ($update) {
            echo "<script>alert('Bukti transfer berhasil dikirim! Mohon tunggu admin memverifikasi pesanan Anda.'); window.location='riwayat.php';</script>";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    } else {
        echo "<script>alert('Gagal mengupload gambar!'); window.location='bayar.php?id=$id_pesanan';</script>";
    }
} else {
    header("Location: riwayat.php");
    exit;
}
?>