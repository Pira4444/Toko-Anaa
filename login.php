<?php
ob_start();
include 'koneksi.php';
session_start();

// Jika sudah login, langsung lempar ke index (biar tidak login dua kali)
if (isset($_SESSION['login'])) {
    // Tambahan logika: jika admin yang login, lempar ke dashboard admin
    if ($_SESSION['user']['role'] == 'admin') {
        header("Location: admin/admin.php");
    } else {
        header("Location: index.php");
    }
    exit;
}

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email_asli']);
    $pass  = $_POST['pass_asli'];

    $query = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
    
    if (mysqli_num_rows($query) === 1) {
        $row = mysqli_fetch_assoc($query);
        
        // PENGGUNAAN MD5: Mengganti password_verify menjadi md5()
        if (md5($pass) == $row['password']) {
            $_SESSION['login'] = true;
            $_SESSION['user']  = $row;

            // Arahkan berdasarkan role
            if ($row['role'] == 'admin') {
                header("Location: admin/admin.php"); // Sesuai nama file di folder adminmu
            } else {
                header("Location: index.php");
            }
            exit;
        }
    }
    $error = true;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Toko Ana</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f4f7f6; }
        .card-login { border-radius: 20px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        input[readonly] { background-color: white !important; cursor: text; }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card card-login p-4 mt-5">
                <h3 class="fw-bold text-center text-primary mb-4">Login</h3>

                <?php if(isset($error)): ?>
                    <div class="alert alert-danger py-2 small text-center">Email atau password salah!</div>
                <?php endif; ?>

                <form action="" method="POST" autocomplete="off">
                    <input type="text" style="display:none">
                    <input type="password" style="display:none">

                    <div class="mb-3">
                        <label class="small fw-bold">Email</label>
                        <input type="email" name="email_asli" class="form-control" 
                               readonly onfocus="this.removeAttribute('readonly');" 
                               placeholder="Masukkan email" required>
                    </div>

                    <div class="mb-3">
                        <label class="small fw-bold">Password</label>
                        <div class="input-group">
                            <input type="password" name="pass_asli" id="passInput" class="form-control" 
                                   readonly onfocus="this.removeAttribute('readonly');" 
                                   placeholder="Masukkan password" required>
                            <button class="btn btn-outline-secondary" type="button" id="btnToggle">
                                <i class="fas fa-eye" id="eyeIcon"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" name="login" class="btn btn-primary w-100 fw-bold py-2 mt-2">MASUK</button>
                    
                    <div class="text-center mt-4">
                        <small>Belum punya akun? <a href="register.php" class="text-decoration-none fw-bold">Daftar sekarang</a></small>
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
    const passInput = document.getElementById('passInput');
    const btnToggle = document.getElementById('btnToggle');
    const eyeIcon = document.getElementById('eyeIcon');

    btnToggle.addEventListener('click', function() {
        if (passInput.type === 'password') {
            passInput.type = 'text';
            eyeIcon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            passInput.type = 'password';
            eyeIcon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    });
</script>

</body>
</html>