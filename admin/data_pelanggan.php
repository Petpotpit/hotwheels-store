<?php
include '../functions.php';

// Mengubah informasi pelanggan
if (isset($_POST['update_profile'])) {
    $conn = dbConnect();
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $telepon = $_POST['telepon'];
    $alamat = $_POST['alamat'];
    $stmt = $conn->prepare("UPDATE Pengguna SET nama = ?, email = ?, telepon = ?, alamat = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $nama, $email, $telepon, $alamat, $id);
    if ($stmt->execute()) {
        $message = "Profil pelanggan berhasil diperbarui";
    } else {
        $message = "Error: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
}

// Menampilkan detail pelanggan
if (isset($_GET['view_id'])) {
    $conn = dbConnect();
    $view_id = $_GET['view_id'];
    $stmt = $conn->prepare("SELECT * FROM Pengguna WHERE id = ?");
    $stmt->bind_param("i", $view_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $pelanggan = $result->fetch_assoc();
    $stmt->close();

    $stmt = $conn->prepare("SELECT * FROM Pesanan WHERE pengguna_id = ?");
    $stmt->bind_param("i", $view_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $riwayat_pesanan = [];
    while ($row = $result->fetch_assoc()) {
        $riwayat_pesanan[] = $row;
    }
    $stmt->close();

    $stmt = $conn->prepare("SELECT Produk.nama FROM Wishlist 
                            JOIN Produk ON Wishlist.produk_id = Produk.id 
                            WHERE Wishlist.pengguna_id = ?");
    $stmt->bind_param("i", $view_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $wishlist = [];
    while ($row = $result->fetch_assoc()) {
        $wishlist[] = $row['nama'];
    }
    $stmt->close();

    $conn->close();
}

// Mencari pelanggan
$search_result = [];
if (isset($_POST['search'])) {
    $conn = dbConnect();
    $keyword = $_POST['keyword'];
    $stmt = $conn->prepare("SELECT * FROM Pengguna WHERE nama LIKE ? OR email LIKE ? OR telepon LIKE ?");
    $search_keyword = "%".$keyword."%";
    $stmt->bind_param("sss", $search_keyword, $search_keyword, $search_keyword);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $search_result[] = $row;
    }
    $stmt->close();
    $conn->close();
}
// Mendapatkan daftar semua pelanggan 
$conn = dbConnect(); 
$sql = "SELECT * FROM Pengguna"; 
$result = $conn->query($sql); 
$all_users = []; 
while ($row = $result->fetch_assoc()) { 
    $all_users[] = $row; 
} 
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pelanggan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style> 
    .container { 
        margin-top: 20px; 
    } 
    .card { 
        margin-bottom: 20px; 
    } 
    .form-group { 
        margin-bottom: 15px; 
    } 
    .navbar { 
        margin-bottom: 20px; 
    } 
    .alert { 
        margin-bottom: 20px; 
    } 
</style>
</head>
<body>
    <?php require('layout/sidebar.php') ?>
    <div class="container">
        <h2 class="my-4 text-center">Data Pelanggan</h2>
        <?php if (isset($message)): ?>
            <div class="alert alert-info"><?= $message; ?></div>
        <?php endif; ?>

        <!-- Form Pencarian Pelanggan -->
        <div class="card" id="Cari-Pelanggan">
            <div class="card-header">
                <h3 class="card-title">Cari Pelanggan</h3>
            </div>
            <div class="card-body">
                <form method="post" action="">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="Cari berdasarkan nama, email, atau nomor telepon" name="keyword" required>
                        <button class="btn btn-primary" type="submit" name="search">Cari</button>
                    </div>
                </form>
                <?php if (!empty($search_result)): ?>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Telepon</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($search_result as $result): ?>
                                <tr>
                                    <td><?= $result['nama']; ?></td>
                                    <td><?= $result['email']; ?></td>
                                    <td><?= $result['telepon']; ?></td>
                                    <td><a href="data_pelanggan.php?view_id=<?= $result['id']; ?>" class="btn btn-info btn-sm">Lihat Detail</a></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>

        <!-- Daftar Pelanggan --> 
         <div class="card" id="Daftar-Pelanggan"> 
            <div class="card-header"> 
                <h3 class="card-title">Daftar Pelanggan</h3> 
            </div> 
            <div class="card-body"> 
                <table class="table table-striped"> 
                    <thead> 
                        <tr> 
                            <th>Nama</th> 
                            <th>Email</th> 
                            <th>Telepon</th> 
                            <th>Aksi</th> 
                        </tr> 
                    </thead> 
                    <tbody> 
                        <?php foreach ($all_users as $user): ?>
                             <tr> 
                                <td><?= $user['nama']; ?></td> 
                                <td><?= $user['email']; ?></td> 
                                <td><?= $user['telepon']; ?></td> 
                                <td><a href="data_pelanggan.php?view_id=<?= $user['id']; ?>" class="btn btn-info btn-sm">Lihat Detail</a></td>
                             </tr> 
                        <?php endforeach; ?> 
                    </tbody> 
                </table> 
            </div> 
        </div>

        <!-- Profil Pelanggan -->
        <?php if (isset($_GET['view_id'])): ?>
            <div class="card" id="Profil-Pelanggan">
                <div class="card-header">
                    <h3 class="card-title">Profil Pelanggan</h3>
                </div>
                <div class="card-body">
                    <form method="post" action="">
                        <input type="hidden" name="id" value="<?= $pelanggan['id']; ?>">
                        <div class="form-group">
                            <label for="nama">Nama</label>
                            <input type="text" class="form-control" id="nama" name="nama" value="<?= $pelanggan['nama']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= $pelanggan['email']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="telepon">Telepon</label>
                            <input type="text" class="form-control" id="telepon" name="telepon" value="<?= $pelanggan['telepon']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="alamat">Alamat Pengiriman</label>
                            <textarea class="form-control" id="alamat" name="alamat" required><?= $pelanggan['alamat']; ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary" name="update_profile">Update Profil</button>
                    </form>
                </div>
            </div>

            <!-- Wishlist Pelanggan -->
            <div class="card mt-4" id="Wishlist-Pelanggan">
                <div class="card-header">
                    <h3 class="card-title">Wishlist Pelanggan</h3>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <?php foreach ($wishlist as $item): ?>
                            <li class="list-group-item"><?= $item; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <!-- Riwayat Pesanan Pelanggan -->
            <div class="card mt-4" id="Riwayat-Pesanan-Pelanggan">
                <div class="card-header">
                    <h3 class="card-title">Riwayat Pesanan Pelanggan</h3>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID Pesanan</th>
                                <th>Tanggal Transaksi</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($riwayat_pesanan as $pesanan): ?>
                                <tr>
                                    <td><?= $pesanan['id']; ?></td>
                                    <td><?= $pesanan['tanggal_transaksi']; ?></td>
                                    <td>Rp <?= number_format($pesanan['total'], 2, ',', '.'); ?></td>
                                    <td><?= $pesanan['status_pengiriman']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
