<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['login'])) {
    echo "<script>alert('Silakan login terlebih dahulu!'); window.location='login.php';</script>";
    exit();
}

// --- 1. LOGIKA MENAMBAH KE KERANJANG ---
if (isset($_POST['add_to_cart'])) {
    $id_p = $_POST['id_produk'];
    $id_v = $_POST['id_galeri']; 
    $qty  = isset($_POST['qty']) ? (int)$_POST['qty'] : 1;

    $query_p = mysqli_query($conn, "SELECT * FROM produk WHERE id_produk = '$id_p'");
    $p = mysqli_fetch_assoc($query_p);

    $nama_tampil = $p['nama_produk'];
    $harga_final = $p['harga'];
    $foto_tampil = $p['foto'];
    $varian_nama = "Standar";

    if (!empty($id_v)) {
        $query_v = mysqli_query($conn, "SELECT * FROM galeri_produk WHERE id_galeri = '$id_v'");
        if (mysqli_num_rows($query_v) > 0) {
            $g = mysqli_fetch_assoc($query_v);
            $harga_final = ($g['harga_varian'] > 0) ? $g['harga_varian'] : $p['harga'];
            $varian_nama = $g['warna'];
            if (!empty($g['foto_tambahan'])) {
                $foto_tampil = $g['foto_tambahan'];
            }
        }
    }

    $cart_id = $id_p . "_" . $id_v;

    if (isset($_SESSION['cart'][$cart_id])) {
        $_SESSION['cart'][$cart_id]['qty'] += $qty;
    } else {
        $_SESSION['cart'][$cart_id] = [
            'id_produk' => $id_p,
            'id_galeri' => $id_v,
            'nama'      => $nama_tampil,
            'varian'    => $varian_nama,
            'harga'     => $harga_final,
            'foto'      => $foto_tampil,
            'qty'       => $qty
        ];
    }
    header("location: keranjang.php");
    exit();
}

// --- 2. LOGIKA HAPUS ITEM ---
if (isset($_GET['hapus'])) {
    $key = $_GET['hapus'];
    unset($_SESSION['cart'][$key]);
    header("location: keranjang.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja - Toko Ana</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Inter', sans-serif; }
        .cart-card { border: none; border-radius: 15px; overflow: hidden; }
        .product-img { width: 80px; height: 80px; object-fit: cover; border-radius: 10px; }
        .table thead { background-color: #fff; border-bottom: 2px solid #f1f1f1; }
        .table thead th { font-weight: 600; color: #6c757d; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.5px; }
        .summary-box { background: #fff; border-radius: 15px; padding: 25px; position: sticky; top: 20px; }
        .btn-checkout { border-radius: 10px; padding: 12px; font-weight: 700; transition: 0.3s; }
        .btn-checkout:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(25, 135, 84, 0.3); }
        .badge-varian { background-color: #e9ecef; color: #495057; font-weight: 500; font-size: 0.75rem; }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="d-flex align-items-center mb-4">
        <a href="index.php" class="btn btn-white shadow-sm rounded-circle me-3"><i class="fas fa-arrow-left"></i></a>
        <h2 class="fw-bold m-0">Keranjang Belanja</h2>
    </div>

    <?php if (empty($_SESSION['cart'])) : ?>
        <div class="text-center py-5 shadow-sm bg-white rounded-4">
            <img src="https://cdn-icons-png.flaticon.com/512/2038/2038854.png" width="150" class="mb-4 opacity-50">
            <h4 class="text-muted">Wah, keranjangmu masih kosong!</h4>
            <p class="text-secondary mb-4">Yuk, cari produk favoritmu dan isi keranjang ini.</p>
            <a href="index.php" class="btn btn-primary px-5 py-2 rounded-pill fw-bold">Mulai Belanja</a>
        </div>
    <?php else : ?>
        <form action="checkout.php" method="POST">
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card cart-card shadow-sm">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-4" width="50">
                                            <input type="checkbox" id="selectAll" class="form-check-input" checked onclick="toggleAll(this)">
                                        </th>
                                        <th>Produk</th>
                                        <th>Harga</th>
                                        <th width="100">Qty</th>
                                        <th>Subtotal</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $grand_total = 0;
                                    foreach ($_SESSION['cart'] as $key => $item): 
                                        $subtotal = $item['harga'] * $item['qty'];
                                        $grand_total += $subtotal;
                                    ?>
                                    <tr>
                                        <td class="ps-4">
                                            <input type="checkbox" name="produk_pilihan[]" value="<?= $key ?>" class="form-check-input item-check" checked>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="img/<?= $item['foto'] ?>" class="product-img me-3">
                                                <div>
                                                    <div class="fw-bold text-dark"><?= $item['nama'] ?></div>
                                                    <span class="badge badge-varian mt-1"><?= $item['varian'] ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="text-muted small text-decoration-line-through d-block" style="font-size: 0.7rem;"></span> Rp <?= number_format($item['harga']) ?></td>
                                        <td>
                                            <div class="fw-bold text-center border rounded py-1 bg-light"><?= $item['qty'] ?></div>
                                        </td>
                                        <td class="fw-bold text-primary">Rp <?= number_format($subtotal) ?></td>
                                        <td class="text-center">
                                            <a href="keranjang.php?hapus=<?= $key ?>" class="text-danger" onclick="return confirm('Hapus item ini?')">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small class="text-muted"><i class="fas fa-info-circle me-1"></i> Centang produk yang ingin kamu bayar saja.</small>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="summary-box shadow-sm">
                        <h5 class="fw-bold mb-4">Ringkasan Belanja</h5>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Total Harga (<?= count($_SESSION['cart']) ?> barang)</span>
                            <span class="fw-bold">Rp <?= number_format($grand_total) ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Biaya Admin</span>
                            <span class="text-success fw-bold">Gratis</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <span class="h6 fw-bold m-0">Total Bayar</span>
                            <span class="h5 fw-bold text-primary m-0">Rp <?= number_format($grand_total) ?></span>
                        </div>
                        <button type="submit" class="btn btn-success w-100 btn-checkout mb-3">
                            Beli Sekarang (<?= count($_SESSION['cart']) ?>)
                        </button>
                        <a href="index.php" class="btn btn-outline-secondary w-100 border-0 fw-bold shadow-none">
                            <i class="fas fa-shopping-cart me-2"></i>Tambah Produk Lain
                        </a>
                    </div>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>

<script>
    function toggleAll(source) {
        checkboxes = document.getElementsByName('produk_pilihan[]');
        for(var i=0, n=checkboxes.length;i<n;i++) {
            checkboxes[i].checked = source.checked;
        }
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>