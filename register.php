<?php
include 'koneksi.php';

if (isset($_POST['register'])) {
    $nama     = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $email    = mysqli_real_escape_string($conn, $_POST['email_asli']); 
    $password = $_POST['pass_asli']; 
    $confirm  = $_POST['confirm_asli']; 

    if ($password !== $confirm) {
        echo "<script>alert('Konfirmasi password tidak sesuai!');</script>";
    } else {
        $cek_user = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
        if (mysqli_num_rows($cek_user) > 0) {
            echo "<script>alert('Email sudah digunakan!');</script>";
        } else {
            // MENGGUNAKAN MD5: Mengganti password_hash menjadi md5()
            $pass_aman = md5($password); 
            
            $query = "INSERT INTO users (nama_lengkap, email, password, role) 
                      VALUES ('$nama', '$email', '$pass_aman', 'user')";
            
            if (mysqli_query($conn, $query)) {
                echo "<script>alert('Registrasi Berhasil!'); window.location='login.php';</script>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - Toko Ana</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f4f7f6; }
        .card-register { border-radius: 20px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        .btn-register { border-radius: 10px; padding: 12px; font-weight: bold; }
        input[readonly] { background-color: white !important; cursor: pointer; }
    </style>
</head>
<body>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card card-register p-4">
                <div class="text-center mb-4">
                    <h3 class="fw-bold text-primary">Daftar Akun</h3>
                    <p class="text-muted">Lengkapi data untuk mulai belanja</p>
                </div>

                <form action="" method="POST" autocomplete="off">
                    <input type="text" style="display:none" tabindex="-1">
                    <input type="password" style="display:none" tabindex="-1">

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="form-control" placeholder="Masukkan nama" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Alamat Email</label>
                        <input type="email" name="email_asli" class="form-control" 
                               readonly onfocus="this.removeAttribute('readonly');" 
                               placeholder="nama@email.com" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Password</label>
                        <div class="input-group">
                            <input type="password" name="pass_asli" id="passInput" class="form-control" 
                                   readonly onfocus="this.removeAttribute('readonly');" 
                                   placeholder="Minimal 6 karakter" required>
                            <button class="btn btn-outline-secondary" type="button" id="btnToggle">
                                <i class="fas fa-eye" id="eyeIcon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Konfirmasi Password</label>
                        <div class="input-group">
                            <input type="password" name="confirm_asli" id="confirmPassInput" class="form-control" 
                                   readonly onfocus="this.removeAttribute('readonly');" 
                                   placeholder="Ulangi password" required>
                            <button class="btn btn-outline-secondary" type="button" id="btnConfirmToggle">
                                <i class="fas fa-eye" id="eyeIconConfirm"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" name="register" class="btn btn-primary w-100 btn-register shadow-sm mt-3">DAFTAR SEKARANG</button>
                    
                    <div class="text-center mt-4">
                        <small>Sudah punya akun? <a href="login.php" class="text-decoration-none fw-bold">Login di sini</a></small>
                    </div>
                </form>
            </div>
            
            <div class="text-center mt-3">
                <a href="index.php" class="text-muted small text-decoration-none">&larr; Kembali ke Beranda</a>
            </div>
        </div>
    </div>
</div>

<script>
    function setupToggle(inputId, buttonId, iconId) {
        const input = document.getElementById(inputId);
        const btn = document.getElementById(buttonId);
        const icon = document.getElementById(iconId);
        btn.addEventListener('click', function() {
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });
    }
    setupToggle('passInput', 'btnToggle', 'eyeIcon');
    setupToggle('confirmPassInput', 'btnConfirmToggle', 'eyeIconConfirm');
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>