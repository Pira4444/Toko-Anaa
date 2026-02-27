<?php 
include 'koneksi.php'; 
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toko Ana - Katalog Online Premium</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8f9fa; color: #2d3436; }
        
        /* Navbar Styling */
        .navbar { background: rgba(255, 255, 255, 0.95) !important; backdrop-filter: blur(10px); border-bottom: 1px solid #eee; }
        .navbar-brand { letter-spacing: 1px; }
        .nav-link { font-weight: 600; color: #4b6584 !important; transition: 0.2s; }
        .nav-link:hover { color: #0d6efd !important; }

        /* Hero Section */
        .hero { 
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('https://images.unsplash.com/photo-1441986300917-64674bd600d8?auto=format&fit=crop&w=1200&q=80'); 
            background-size: cover; background-position: center; color: white; padding: 120px 0; border-radius: 0 0 50px 50px;
        }

        /* Product Card */
        .product-card { 
            border: none; border-radius: 20px; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); 
            background: white; position: relative;
        }
        .product-card:hover { transform: translateY(-10px); box-shadow: 0 20px 30px rgba(0,0,0,0.08); }
        .img-product { height: 280px; object-fit: cover; border-radius: 20px 20px 0 0; }
        
        .card-body { padding: 1.25rem; }
        .price-tag { font-size: 1.1rem; color: #0d6efd; font-weight: 700; }
        .stok-badge { font-size: 0.7rem; background: #f1f2f6; color: #747d8c; padding: 4px 10px; border-radius: 8px; font-weight: 600; }

        /* Modal Styling */
        .modal-content { border-radius: 25px; overflow: hidden; }
        .img-preview-modal { width: 120px; height: 120px; object-fit: cover; border-radius: 15px; }
        .total-box { background: #f8f9fa; border: 2px dashed #dee2e6; border-radius: 15px; padding: 15px; }
        
        .btn-buy-now { border-radius: 12px; padding: 12px; font-weight: 700; letter-spacing: 0.5px; transition: 0.3s; }
        .btn-buy-now:hover { background: #0b5ed7; transform: scale(1.02); }

        .nav-profile-img { width: 35px; height: 35px; object-fit: cover; border-radius: 12px; border: 2px solid #0d6efd; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg sticky-top py-3">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary fs-4" href="index.php">ANA<span class="text-dark">STORE</span></a>
        <button class="navbar-toggler border-0 shadow-none" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <?php
                $menus = mysqli_query($conn, "SELECT * FROM navbar WHERE parent_id = 0");
                while($m = mysqli_fetch_assoc($menus)){
                    $id_m = $m['id'];
                    $subs = mysqli_query($conn, "SELECT * FROM navbar WHERE parent_id = $id_m");
                    if($m['nama_menu'] == 'Profil') continue;
                    if(mysqli_num_rows($subs) > 0){
                        echo "<li class='nav-item dropdown'>
                                <a class='nav-link dropdown-toggle px-3' href='#' data-bs-toggle='dropdown'>{$m['nama_menu']}</a>
                                <ul class='dropdown-menu border-0 shadow-sm rounded-3'>";
                        while($s = mysqli_fetch_assoc($subs)) {
                            echo "<li><a class='dropdown-item py-2' href='index.php?kat={$s['id']}'>{$s['nama_menu']}</a></li>";
                        }
                        echo "</ul></li>";
                    } else {
                        echo "<li class='nav-item'><a class='nav-link px-3' href='index.php?kat={$m['id']}'>{$m['nama_menu']}</a></li>";
                    }
                }
                ?>
                <li class="nav-item">
                    <a class="nav-link fw-bold text-dark ms-lg-2 position-relative" href="keranjang.php">
                        <i class="fas fa-shopping-bag me-1"></i>
                        <span class="d-lg-none">Keranjang</span>
                    </a>
                </li>
                <li class="nav-item ms-lg-3 py-2 py-lg-0">
                    <?php if(isset($_SESSION['login'])): ?>
                        <div class="dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center p-0" href="#" data-bs-toggle="dropdown">
                                <img src="https://ui-avatars.com/api/?name=<?= $_SESSION['user']['nama_lengkap'] ?>&background=0D6EFD&color=fff&bold=true" class="nav-profile-img me-2">
                                <span class="small fw-bold d-none d-lg-inline"><?= explode(' ', $_SESSION['user']['nama_lengkap'])[0] ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                                <li><a class="dropdown-item py-2" href="riwayat.php"><i class="fas fa-box-open me-2 text-muted"></i>Pesanan Saya</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger py-2" href="logout.php"><i class="fas fa-power-off me-2"></i>Keluar</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <div class="d-flex gap-2">
                            <a href="login.php" class="btn btn-outline-primary btn-sm rounded-pill px-4">Login</a>
                        </div>
                    <?php endif; ?>
                </li>
            </ul>
        </div>
    </div>
</nav>

<header class="hero shadow-sm mx-lg-4 mt-lg-3">
    <div class="container text-center">
        <h1 class="display-3 fw-bold mb-3">Elevate Your Style</h1>
        <p class="lead opacity-75 mb-0">Kurasi fashion terbaik untuk penampilan yang tak terlupakan.</p>
    </div>
</header>

<div class="container my-5">
    <div class="row g-4">
        <?php
        // ... (Logika filter tetap sama seperti kode awal kamu)
        $filter = "";
        if (isset($_GET['kat']) && !empty($_GET['kat'])) {
            $id_kat = (int)$_GET['kat'];
            $cek_nav = mysqli_query($conn, "SELECT parent_id FROM navbar WHERE id = $id_kat");
            $data_nav = mysqli_fetch_assoc($cek_nav);
            if ($data_nav && $data_nav['parent_id'] == 0) {
                $filter = "WHERE id_kategori = $id_kat OR id_kategori IN (SELECT id FROM navbar WHERE parent_id = $id_kat)";
            } else { $filter = "WHERE id_kategori = $id_kat"; }
        }

        $produk = mysqli_query($conn, "SELECT * FROM produk $filter ORDER BY id_produk DESC");
        
        while($p = mysqli_fetch_assoc($produk)){ 
            $id_p = $p['id_produk'];
            $galeri = mysqli_query($conn, "SELECT * FROM galeri_produk WHERE id_produk = $id_p");
        ?>
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card product-card">
                <img src="img/<?= $p['foto'] ?>" class="img-product" alt="Produk">
                <div class="card-body">
                    <span class="stok-badge mb-2 d-inline-block">Tersedia: <?= $p['stok'] ?></span>
                    <h5 class="card-title fw-bold h6 text-truncate" title="<?= $p['nama_produk'] ?>"><?= $p['nama_produk'] ?></h5>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <span class="price-tag">Rp<?= number_format($p['harga']) ?></span>
                        <button class="btn btn-primary rounded-circle shadow-sm" data-bs-toggle="modal" data-bs-target="#modalBeli<?= $id_p ?>">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalBeli<?= $id_p ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0">
                    <form action="keranjang.php" method="POST">
                        <input type="hidden" name="id_produk" value="<?= $id_p ?>">
                        <input type="hidden" name="id_galeri" id="id_galeri_hidden_<?= $id_p ?>" value="">
                        
                        <div class="modal-header border-0 pb-0">
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body pt-0">
                            <div class="d-flex align-items-start mb-4">
                                <img id="img-modal-<?= $id_p ?>" src="img/<?= $p['foto'] ?>" data-default="img/<?= $p['foto'] ?>" class="img-preview-modal me-3 shadow-sm border">
                                <div>
                                    <h5 class="fw-bold mb-1"><?= $p['nama_produk'] ?></h5>
                                    <h4 class="text-primary fw-bold mb-0">Rp<?= number_format($p['harga']) ?></h4>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="small fw-bold mb-2 text-muted">PILIH VARIAN</label>
                                <select name="varian_nama" class="form-select select-varian py-2 rounded-3" data-id="<?= $id_p ?>" required>
                                    <option value="" data-harga="<?= $p['harga'] ?>" data-image="" data-id_galeri="">-- Klik untuk memilih warna --</option>
                                    <?php 
                                    mysqli_data_seek($galeri, 0); 
                                    while($rowV = mysqli_fetch_assoc($galeri)){
                                        $hrg_v = ($rowV['harga_varian'] > 0) ? $rowV['harga_varian'] : $p['harga'];
                                        echo "<option value='{$rowV['warna']}' data-harga='{$hrg_v}' data-image='{$rowV['foto_tambahan']}' data-id_galeri='{$rowV['id_galeri']}'>{$rowV['warna']} (Rp ".number_format($hrg_v).")</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="small fw-bold mb-2 text-muted">JUMLAH</label>
                                <div class="input-group">
                                    <input type="number" name="qty" class="form-control input-qty text-center fw-bold rounded-3" value="1" min="1" data-id="<?= $id_p ?>" required>
                                </div>
                            </div>

                            <div class="total-box">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="small fw-bold text-muted text-uppercase">Subtotal</span>
                                    <h4 class="fw-bold mb-0 text-dark">Rp<span id="textTotal<?= $id_p ?>"><?= number_format($p['harga']) ?></span></h4>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0 p-3">
                            <button type="submit" name="add_to_cart" class="btn btn-primary w-100 btn-buy-now shadow">
                                <i class="fas fa-cart-plus me-2"></i>TAMBAH KE KERANJANG
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
</div>

<footer class="bg-white py-5 mt-5 border-top">
    <div class="container text-center text-muted small">
        <p class="mb-0 fw-bold">&copy; 2026 Toko Ana Store. All Rights Reserved.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// JS Tetap sama seperti punya kamu, sudah optimal
document.querySelectorAll('.select-varian, .input-qty').forEach(element => {
    element.addEventListener('input', function() {
        const id = this.getAttribute('data-id');
        const modal = document.querySelector('#modalBeli' + id);
        const selected = modal.querySelector('.select-varian option:checked');
        const harga = parseInt(selected.getAttribute('data-harga')) || 0;
        const qty = parseInt(modal.querySelector('.input-qty').value) || 1;
        const total = harga * qty;
        modal.querySelector('#textTotal' + id).innerText = total.toLocaleString('id-ID');
        const fotoBaru = selected.getAttribute('data-image');
        const imgTarget = document.getElementById('img-modal-' + id);
        if(fotoBaru && fotoBaru.trim() !== "") {
            imgTarget.src = 'img/' + fotoBaru;
        } else {
            imgTarget.src = imgTarget.getAttribute('data-default');
        }
        const idGaleri = selected.getAttribute('data-id_galeri');
        document.getElementById('id_galeri_hidden_' + id).value = idGaleri;
    });
});
</script>
</body>
</html>