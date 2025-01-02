<!DOCTYPE html>
<html lang="en">

<?php
require('functions.php');

if (isset($_POST['Signup'])) {
    registrasiPengguna($_POST['pengguna_nama'], $_POST['pengguna_email'], $_POST['pengguna_password'], 'pelanggan');
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Toko Hotwheels</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>
        <div class="text-center login-register-page" >
        <div class="wrapper">
        <h1 class="text-white mb-4">Toko Hotwheels</h1>
        <h3 class="text-white mb-4">Silahkan <b>Daftar</b></h3>
        <div class="row justify-content-center">
            <div class="">
                <form action="" method="post">
                    <div class="input-box">
                        <input type="text" name="pengguna_nama" id="pengguna_nama" placeholder="Nama Lengkap" class="form-control" required>
                        <i class='bx bxs-user' ></i>
                    </div>
                    <div class="input-box">
                        <input type="email" name="pengguna_email" id="pengguna_email" placeholder="Email" class="form-control" required>
                        <i class='bx bx-envelope'></i>
                    </div>
                    <div class="input-box">
                        <input type="password" name="pengguna_password" id="pengguna_password" placeholder="Password" class="form-control" required>
                        <i class='bx bxs-lock'></i>
                    </div>
                    <div class="input-box ">
                        <button type="submit" name="Signup" class="btn ">Signup</button>
                    </div>
                    <a class="text-white" href="login.php">Login</a>
                </form>
            </div>
        </div>
        </div>
    </div>
</body>

</html>
