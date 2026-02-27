<?php 
include 'koneksi.php';
$id_kat = $_GET['id']; // Ambil ID dari URL navbar
$nama_hal = mysqli_query($conn, "SELECT nama_menu FROM navbar WHERE id='$id_kat'");
$nh = mysqli_fetch_assoc($nama_hal);
?>

<div class="container mt-4">
    <h2>Kategori: <?php echo $nh['nama_menu']; ?></h2>
    <div class="row">
        <?php
        $barang = mysqli_query($conn, "SELECT * FROM produk WHERE id_kategori = '$id_kat'");
        if(mysqli_num_rows($barang) > 0){
            while($b = mysqli_fetch_assoc($barang)){ ?>
                <div class="col-md-3 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h5><?= $b['nama_produk'] ?></h5>
                            <p class="text-primary fw-bold">Rp <?= number_format($b['harga']) ?></p>
                            <button class="btn btn-outline-success btn-sm w-100">Beli Sekarang</button>
                        </div>
                    </div>
                </div>
            <?php } 
        } else {
            echo "<div class='alert alert-warning'>Belum ada barang di kategori ini.</div>";
        } ?>
    </div>
</div>