<?php
// 1. Tampilkan error agar tidak layar putih
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'koneksi.php';
session_start();

// 2. Proteksi: Jika belum login, tendang ke login.php
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

// 3. Ambil ID User dari session
$id_user = $_SESSION['user']['id_user'] ?? $_SESSION['user']['id']; 

// 4. Query ambil data pesanan milik user ini
$query = mysqli_query($conn, "SELECT * FROM pesanan WHERE id_user = '$id_user' ORDER BY tgl_pesan DESC");

// 5. Logika Hapus (Jika ada kiriman ID untuk dihapus)
if (isset($_GET['hapus'])) {
    $id_hapus = mysqli_real_escape_string($conn, $_GET['hapus']);
    
    // Cek dulu statusnya, jangan sampai pesanan yang sudah lunas dihapus user
    $cek = mysqli_query($conn, "SELECT status FROM pesanan WHERE id_pesanan = '$id_hapus' AND id_user = '$id_user'");
    $data_cek = mysqli_fetch_assoc($cek);

    if ($data_cek['status'] == 'Menunggu Ongkir' || $data_cek['status'] == 'Menunggu Pembayaran') {
        // Hapus detail dulu baru pesanan (karena relasi database)
        mysqli_query($conn, "DELETE FROM detail_pesanan WHERE id_pesanan = '$id_hapus'");
        mysqli_query($conn, "DELETE FROM pesanan WHERE id_pesanan = '$id_hapus'");
        
        echo "<script>alert('Pesanan berhasil dibatalkan/dihapus.'); window.location='riwayat.php';</script>";
    } else {
        echo "<script>alert('Pesanan ini tidak bisa dihapus karena sudah diproses/lunas.'); window.location='riwayat.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pesanan Saya - Toko Ana</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">Riwayat Pesanan</h3>
        <a href="index.php" class="btn btn-outline-secondary btn-sm">Kembali ke Toko</a>
    </div>

    <?php if (mysqli_num_rows($query) == 0) : ?>
        <div class="alert alert-info text-center">
            Kamu belum pernah melakukan pemesanan.
        </div>
    <?php else : ?>
        <div class="table-responsive shadow-sm rounded-4 bg-white p-3">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Tgl Pesan</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($query)) : ?>
                        <tr>
                            <td><?= date('d M Y', strtotime($row['tgl_pesan'])) ?></td>
                            <td class="fw-bold text-primary">
                                <?php if($row['status'] == 'Menunggu Ongkir'): ?>
                                    <small class="text-muted">Tunggu Ongkir</small>
                                <?php else: ?>
                                    Rp <?= number_format($row['total_bayar']) ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($row['status'] == 'Menunggu Ongkir'): ?>
                                    <span class="badge bg-secondary"><?= $row['status'] ?></span>
                                <?php elseif($row['status'] == 'Menunggu Pembayaran'): ?>
                                    <span class="badge bg-warning text-dark"><?= $row['status'] ?></span>
                                <?php else: ?>
                                    <span class="badge bg-success"><?= $row['status'] ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if($row['status'] == 'Menunggu Pembayaran'): ?>
                                    <a href="bayar.php?id=<?= $row['id_pesanan'] ?>" class="btn btn-sm btn-primary">Bayar</a>
                                <?php endif; ?>

                                <?php if($row['status'] == 'Menunggu Ongkir' || $row['status'] == 'Menunggu Pembayaran'): ?>
                                    <a href="?hapus=<?= $row['id_pesanan'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Batalkan pesanan ini?')">
                                        <i class="fas fa-trash-alt"></i>
                                    </td>
                                <?php else: ?>
                                    <span class="text-muted small">No Action</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>