<?php
include 'koneksi.php';
session_start();

if (!isset($_SESSION['login'])) { header("location: login.php"); exit; }

$id_pesanan = mysqli_real_escape_string($conn, $_GET['id']);
$query = mysqli_query($conn, "SELECT * FROM pesanan WHERE id_pesanan = '$id_pesanan'");
$r = mysqli_fetch_assoc($query);

// Proteksi: Jika pesanan belum diinput ongkirnya oleh admin
if ($r['status'] == 'Menunggu Ongkir') {
    echo "<script>alert('Mohon tunggu admin mengisi ongkir terlebih dahulu!'); window.location='riwayat.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Pesanan #<?= $id_pesanan ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .card-pay { border: none; border-radius: 20px; overflow: hidden; }
        .payment-method-box { background: #fff; border: 1px solid #e9ecef; border-radius: 12px; padding: 15px; margin-bottom: 10px; }
        .account-number { font-family: 'Courier New', Courier, monospace; font-weight: bold; font-size: 1.1rem; color: #0d6efd; }
        .qris-img { max-width: 180px; border: 5px solid #fff; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            
            <div class="card card-pay shadow-sm mb-4">
                <div class="card-header bg-primary text-white text-center py-3">
                    <h5 class="mb-0 fw-bold">Konfirmasi Pembayaran</h5>
                    <small>ID Pesanan: #<?= $id_pesanan ?></small>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Produk</span>
                        <span>Rp <?= number_format($r['total_bayar'] - $r['ongkir']) ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Ongkos Kirim</span>
                        <span>Rp <?= number_format($r['ongkir']) ?></span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="h5 fw-bold">Total Bayar</span>
                        <span class="h4 fw-bold text-primary">Rp <?= number_format($r['total_bayar']) ?></span>
                    </div>
                </div>
            </div>

            <div class="card card-pay shadow-sm mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3"><i class="fas fa-wallet me-2 text-primary"></i> Pilih Metode Pembayaran:</h6>
                    
                    <div class="payment-method-box">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted d-block">Bank BCA</small>
                                <span class="account-number">0551549851</span>
                                <small class="d-block">ratna ugih</small>
                            </div>
                            <img src="https://upload.wikimedia.org/wikipedia/commons/5/5c/Bank_Central_Asia.svg" width="60" alt="BCA">
                        </div>
                    </div>

                    <div class="payment-method-box">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted d-block">Bank BJB</small>
                                <span class="account-number">01411978611011</span>
                                <small class="d-block text-truncate">ratna ugih</small>
                            </div>
                            <img src="data:image/png;base64,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" width="60" alt="BJB">
                        </div>
                    </div>

                    <div class="payment-method-box">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted d-block">E-Wallet DANA</small>
                                <span class="account-number">+62895-0196-5182</span>
                                <small class="d-block">a/n Toko Ana</small>
                            </div>
                            <img src="https://upload.wikimedia.org/wikipedia/commons/7/72/Logo_dana_blue.svg" width="60" alt="DANA">
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <p class="mb-2 fw-bold small text-uppercase text-muted">Atau Scan QRIS</p>
                        <img src="img/qris.jpeg" class="qris-img rounded" alt="QRIS">
                        <p class="small text-muted mt-2">Mendukung semua aplikasi pembayaran</p>
                    </div>
                </div>
            </div>

            <div class="card card-pay shadow-sm">
                <div class="card-body p-4">
                    <form action="proses_konfirmasi.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id_pesanan" value="<?= $id_pesanan ?>">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Upload Bukti Transfer</label>
                            <input type="file" name="bukti_transfer" class="form-control" required>
                            <div class="form-text">Pastikan nominal sesuai dengan total bayar.</div>
                        </div>
                        <button type="submit" name="kirim_bukti" class="btn btn-primary w-100 py-3 rounded-pill fw-bold">
                            KIRIM BUKTI PEMBAYARAN
                        </button>
                    </form>
                    <a href="riwayat.php" class="btn btn-link w-100 mt-2 text-decoration-none text-muted small">Kembali ke Riwayat</a>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>