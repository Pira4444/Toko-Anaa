<?php 
include 'koneksi.php';
$id = $_GET['id']; // Ambil ID produk dari URL

// Ambil data produk utama
$query = mysqli_query($conn, "SELECT * FROM produk WHERE id_produk = '$id'");
$p = mysqli_fetch_assoc($query);

// Jika ID tidak ditemukan, balik ke index
if(!$p){ header("location: index.php"); exit; }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title><?= $p['nama_produk'] ?> - Toko Ana</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .img-main { width: 100%; border-radius: 15px; transition: 0.3s; }
        .price-text { color: #4e73df; font-weight: bold; font-size: 1.8rem; }
    </style>
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row bg-white p-4 rounded shadow-sm">
        
        <div class="col-md-6 mb-4">
            <img id="main-image" src="img/<?= $p['foto'] ?>" class="img-main shadow">
        </div>

        <div class="col-md-6">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item active">Detail Produk</li>
                </ol>
            </nav>
            
            <h1 class="fw-bold"><?= $p['nama_produk'] ?></h1>
            <div class="price-text mb-3">
                Rp <span id="display-price"><?= number_format($p['harga']) ?></span>
            </div>
            
            <hr>

            <form action="keranjang.php" method="POST">
                <input type="hidden" name="id_produk" value="<?= $p['id_produk'] ?>">
                
                <div class="mb-3">
                    <label class="fw-bold mb-2">Pilih Varian Warna/Jenis:</label>
                    <select id="variant-select" name="id_galeri" class="form-select" onchange="updateView()" required>
                        <option value="" 
                                data-price="<?= $p['harga'] ?>" 
                                data-image="<?= $p['foto'] ?>">
                            -- Pilih Varian --
                        </option>

                        <?php 
                        $galeri = mysqli_query($conn, "SELECT * FROM galeri_produk WHERE id_produk = '$id'");
                        while($g = mysqli_fetch_assoc($galeri)): 
                            // Gunakan harga varian jika ada, jika 0 gunakan harga utama
                            $harga_final = ($g['harga_varian'] > 0) ? $g['harga_varian'] : $p['harga'];
                        ?>
                            <option value="<?= $g['id_galeri'] ?>" 
                                    data-price="<?= $harga_final ?>" 
                                    data-image="<?= $g['foto_tambahan'] ?>">
                                <?= $g['warna'] ?> (Rp <?= number_format($harga_final) ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="fw-bold mb-2">Jumlah:</label>
                    <input type="number" name="qty" class="form-control" value="1" min="1" style="width: 100px;">
                </div>

                <button type="submit" class="btn btn-primary btn-lg w-100">
                    <i class="fas fa-shopping-cart me-2"></i> Tambah ke Keranjang
                </button>
            </form>
        </div>
    </div>
</div>



<script>
function updateView() {
    const select = document.getElementById('variant-select');
    const selectedOption = select.options[select.selectedIndex];
    
    // Ambil data dari atribut data-
    const price = selectedOption.getAttribute('data-price');
    const image = selectedOption.getAttribute('data-image');
    
    // Update Harga dengan format ribuan
    if (price) {
        document.getElementById('display-price').innerText = new Intl.NumberFormat('id-ID').format(price);
    }
    
    // Update Foto
    if (image && image !== "") {
        document.getElementById('main-image').src = 'img/' + image;
    }
}
</script>

</body>
</html>