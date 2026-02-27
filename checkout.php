<?php
include 'koneksi.php';
session_start();

if (!isset($_SESSION['login']) || !isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['user']['id_user'] ?? $_SESSION['user']['id']; 
$user_query = mysqli_query($conn, "SELECT * FROM users WHERE id_user = '$id_user'");
$u = mysqli_fetch_assoc($user_query);

$pilihan_key = $_POST['produk_pilihan'] ?? [];
if(empty($pilihan_key)) {
    echo "<script>alert('Pilih produk dulu di keranjang!'); window.location='keranjang.php';</script>";
    exit;
}

$total_barang = 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Konfirmasi Pesanan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8f9fa; }
        .checkout-title { color: #2d3436; font-weight: 700; }
        .card-custom { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); }
        .form-label { font-weight: 600; color: #636e72; font-size: 0.85rem; margin-bottom: 8px; }
        .form-control, .form-select { 
            border-radius: 12px; border: 1.5px solid #edf2f7; padding: 12px 15px; transition: 0.3s;
        }
        .form-control:focus { border-color: #0d6efd; box-shadow: none; background: #fff; }
        .summary-sticky { position: sticky; top: 100px; }
        .product-item { background: #fff; border-radius: 15px; padding: 15px; margin-bottom: 12px; border: 1px solid #f1f1f1; }
        .info-alert { 
            background: #eef5ff; border: 1px solid #d0e3ff; color: #0056b3; 
            border-radius: 15px; padding: 15px; font-size: 0.85rem;
        }
        .payment-option {
            border: 2px solid #edf2f7; border-radius: 12px; padding: 15px; 
            cursor: pointer; transition: 0.2s; display: block; position: relative;
        }
        .payment-option:hover { border-color: #0d6efd; background: #f0f7ff; }
        .btn-confirm { border-radius: 15px; padding: 16px; font-weight: 700; letter-spacing: 0.5px; transition: 0.3s; }
        .btn-confirm:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(13, 110, 253, 0.2); }
    </style>
</head>
<body>

<nav class="navbar navbar-light bg-white border-bottom py-3">
    <div class="container d-flex justify-content-between">
        <a class="navbar-brand fw-bold text-primary" href="keranjang.php">
            <i class="fas fa-chevron-left me-2 small"></i>Kembali ke Keranjang
        </a>
        <span class="fw-bold">Checkout Safe <i class="fas fa-lock text-success ms-1 small"></i></span>
    </div>
</nav>

<div class="container py-5">
    <h2 class="checkout-title mb-4">Lengkapi Data Pengiriman</h2>
    
    <form action="proses_bayar.php" method="POST">
        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card card-custom p-4 p-md-5 mb-4 bg-white">
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">1</div>
                        <h5 class="m-0 fw-bold">Informasi Penerima</h5>
                    </div>

                    <?php foreach($pilihan_key as $k): ?>
                        <input type="hidden" name="item_keys[]" value="<?= $k ?>">
                    <?php endforeach; ?>
                    <input type="hidden" name="id_user" value="<?= $id_user ?>">

                    <div class="mb-4">
                        <label class="form-label text-uppercase">Nama Lengkap</label>
                        <input type="text" name="nama_pembeli" class="form-control" value="<?= $u['nama_lengkap'] ?? '' ?>" placeholder="Masukkan nama penerima..." required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-uppercase">No. WhatsApp Aktif</label>
                        <div class="input-group">
                            <span class="input-group-text border-0 bg-light">+62</span>
                            <input type="number" name="telepon" class="form-control" value="<?= $u['no_telp'] ?? '' ?>" placeholder="8123456xxx" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label text-uppercase">Provinsi</label>
                            <input type="text" name="provinsi" class="form-control" placeholder="Contoh: Jawa Barat" required>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label text-uppercase">Kota / Kabupaten</label>
                            <input type="text" name="kota" class="form-control" placeholder="Contoh: Subang" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-uppercase">Alamat Lengkap</label>
                        <textarea name="alamat_rumah" class="form-control" rows="3" placeholder="Nama Jalan, No. Rumah, RT/RW, Kecamatan, Desa..." required></textarea>
                    </div>

                    <hr class="my-5 opacity-50">

                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">2</div>
                        <h5 class="m-0 fw-bold">Metode Pembayaran</h5>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="payment-option">
                                <input type="radio" name="metode" value="Transfer" class="form-check-input me-2" checked>
                                <strong>Transfer Bank</strong>
                                <div class="small text-muted mt-1">BCA, BJB, DANA, QRIS</div>
                            </label>
                        </div>
                        <div class="col-md-6">
                            <label class="payment-option">
                                <input type="radio" name="metode" value="COD" class="form-check-input me-2">
                                <strong>COD (Bayar di Tempat)</strong>
                                <div class="small text-muted mt-1">Bayar saat paket sampai</div>
                            </label>
                        </div>
                    </div>

                    <div class="info-alert mt-4">
                        <div class="d-flex">
                            <i class="fas fa-truck-fast me-3 fs-4"></i>
                            <div>
                                <strong>Catatan Pengiriman:</strong><br>
                                Ongkos kirim akan dihitung manual oleh Admin. Kami akan menghubungi Anda via WhatsApp setelah pesanan dikonfirmasi untuk total akhir.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="summary-sticky">
                    <div class="card card-custom p-4 bg-white">
                        <h5 class="fw-bold mb-4">Pesanan Anda</h5>
                        
                        <div class="product-list mb-4">
                            <?php
                            foreach($pilihan_key as $k): 
                                if(isset($_SESSION['cart'][$k])):
                                    $item = $_SESSION['cart'][$k];
                                    $sub = $item['harga'] * $item['qty'];
                                    $total_barang += $sub;
                            ?>
                            <div class="d-flex align-items-center mb-3">
                                <div class="position-relative">
                                    <img src="img/<?= $item['foto'] ?>" width="70" height="70" class="rounded-3" style="object-fit: cover; border: 1px solid #eee;">
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-secondary">
                                        <?= $item['qty'] ?>
                                    </span>
                                </div>
                                <div class="ms-4 flex-grow-1">
                                    <h6 class="fw-bold mb-0 small text-dark"><?= $item['nama'] ?></h6>
                                    <small class="text-muted"><?= $item['varian'] ?></small>
                                </div>
                                <div class="text-end">
                                    <span class="fw-bold small">Rp <?= number_format($sub) ?></span>
                                </div>
                            </div>
                            <?php endif; endforeach; ?>
                        </div>

                        <hr class="opacity-50">

                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Total Harga Produk</span>
                            <span class="fw-bold">Rp <?= number_format($total_barang) ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Estimasi Ongkir</span>
                            <span class="text-primary fw-bold small">Menunggu Admin</span>
                        </div>

                        <div class="bg-light p-3 rounded-3 mb-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="h6 fw-bold mb-0">Total Bayar*</span>
                                <span class="h5 fw-bold text-primary mb-0">Rp <?= number_format($total_barang) ?></span>
                            </div>
                        </div>

                        <input type="hidden" name="ongkir" value="0">
                        <input type="hidden" name="total_final" value="<?= $total_barang ?>">

                        <button type="submit" name="bayar" class="btn btn-primary w-100 btn-confirm shadow-sm">
                            <i class="fas fa-check-circle me-2"></i>BUAT PESANAN SEKARANG
                        </button>
                        
                        <p class="text-center small text-muted mt-3 mb-0">
                            * Belum termasuk ongkir yang akan dihitung admin.
                        </p>
                    </div>

                    <div class="text-center mt-4">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/5c/Bank_Central_Asia.svg/1200px-Bank_Central_Asia.svg.png" height="20" class="mx-2 grayscale opacity-50">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/ad/Bank_Mandiri_logo_2016.svg/1200px-Bank_Mandiri_logo_2016.svg.png" height="15" class="mx-2 grayscale opacity-50">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/e/e1/Logo_dan_DANA.svg/1200px-Logo_dan_DANA.svg.png" height="20" class="mx-2 grayscale opacity-50">
                    </div>
                </div>
            </div>
        </div>
    </form> 
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>