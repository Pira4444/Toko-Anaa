<?php
include 'koneksi.php';
session_start();

// Proteksi login
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['user']['id_user'] ?? $_SESSION['user']['id']; 

// Ambil data pesanan user ini dari yang terbaru
$query = mysqli_query($conn, "SELECT * FROM pesanan WHERE id_user = '$id_user' ORDER BY tgl_pesan DESC");

// Fitur Hapus Pesanan
if (isset($_GET['hapus'])) {
    $id_hapus = mysqli_real_escape_string($conn, $_GET['hapus']);
    $cek = mysqli_query($conn, "SELECT status FROM pesanan WHERE id_pesanan = '$id_hapus' AND id_user = '$id_user'");
    $d = mysqli_fetch_assoc($cek);

    if ($d['status'] == 'Menunggu Ongkir' || $d['status'] == 'Menunggu Pembayaran') {
        mysqli_query($conn, "DELETE FROM detail_pesanan WHERE id_pesanan = '$id_hapus'");
        mysqli_query($conn, "DELETE FROM pesanan WHERE id_pesanan = '$id_hapus'");
        echo "<script>alert('Pesanan berhasil dibatalkan.'); window.location='riwayat.php';</script>";
    } else {
        echo "<script>alert('Pesanan sudah diproses, tidak bisa dibatalkan.'); window.location='riwayat.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pesanan - Toko Ana</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary-color: #0d6efd; --soft-bg: #f8f9fa; }
        body { background-color: var(--soft-bg); font-family: 'Inter', sans-serif; }
        .order-card { border: none; border-radius: 15px; transition: transform 0.2s; }
        .order-card:hover { transform: translateY(-3px); }
        .status-badge { padding: 0.5rem 0.8rem; border-radius: 8px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; }
        .table thead { background-color: #fff; }
        .table thead th { border-bottom: 2px solid #eee; color: #666; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; }
        .btn-action { border-radius: 8px; font-weight: 600; font-size: 0.85rem; }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h3 class="fw-bold m-0"><i class="fas fa-shopping-bag text-primary me-2"></i>Riwayat Pesanan</h3>
            <p class="text-muted small m-0">Pantau status pesanan dan pembayaran kamu di sini.</p>
        </div>
        <div class="col-auto">
            <a href="index.php" class="btn btn-white shadow-sm btn-action border text-primary">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Toko
            </a>
        </div>
    </div>

    <?php if (mysqli_num_rows($query) == 0) : ?>
        <div class="text-center py-5 bg-white shadow-sm rounded-4">
            <img src="https://cdn-icons-png.flaticon.com/512/2038/2038854.png" width="120" class="mb-3 opacity-50">
            <h5 class="text-muted">Kamu belum punya pesanan nih.</h5>
            <a href="index.php" class="btn btn-primary mt-3 px-4 rounded-pill">Mulai Cari Produk</a>
        </div>
    <?php else : ?>
        <div class="card order-card shadow-sm p-3">
            <div class="table-responsive">
                <table class="table table-borderless align-middle">
                    <thead>
                        <tr>
                            <th class="ps-3">Detail Pesanan</th>
                            <th>Status Pembayaran</th>
                            <th>Total Tagihan</th>
                            <th class="text-center">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($query)) : ?>
                        <tr class="border-bottom">
                            <td class="ps-3 py-4">
                                <span class="d-block fw-bold text-dark mb-1">Order #<?= $row['id_pesanan'] ?></span>
                                <span class="d-block text-muted small"><i class="far fa-calendar-alt me-1"></i> <?= date('d M Y, H:i', strtotime($row['tgl_pesan'])) ?></span>
                            </td>
                            <td>
                                <?php 
                                $status = $row['status'];
                                if($status == 'Menunggu Ongkir') {
                                    echo '<span class="status-badge bg-light text-secondary border"><i class="fas fa-truck-loading me-1"></i> '.$status.'</span>';
                                } elseif($status == 'Menunggu Pembayaran') {
                                    echo '<span class="status-badge bg-warning-subtle text-warning border border-warning-subtle"><i class="fas fa-clock me-1"></i> '.$status.'</span>';
                                } elseif($status == 'Menunggu Verifikasi') {
                                    echo '<span class="status-badge bg-info-subtle text-info border border-info-subtle"><i class="fas fa-search-dollar me-1"></i> '.$status.'</span>';
                                } else {
                                    echo '<span class="status-badge bg-success-subtle text-success border border-success-subtle"><i class="fas fa-check-circle me-1"></i> '.$status.'</span>';
                                }
                                ?>
                            </td>
                            <td>
                                <?php if($row['status'] == 'Menunggu Ongkir'): ?>
                                    <span class="text-muted small fst-italic">Proses hitung ongkir...</span>
                                <?php else: ?>
                                    <span class="fw-bold text-primary">Rp <?= number_format($row['total_bayar']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <?php if($row['status'] == 'Menunggu Pembayaran'): ?>
                                        <a href="bayar.php?id=<?= $row['id_pesanan'] ?>" class="btn btn-sm btn-primary btn-action px-3">
                                            Bayar Sekarang
                                        </a>
                                    <?php endif; ?>

                                    <?php if($row['status'] == 'Menunggu Ongkir' || $row['status'] == 'Menunggu Pembayaran'): ?>
                                        <a href="?hapus=<?= $row['id_pesanan'] ?>" class="btn btn-sm btn-outline-danger btn-action ms-2" onclick="return confirm('Batalkan pesanan ini?')">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-light border btn-action text-muted" disabled>Selesai</button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>