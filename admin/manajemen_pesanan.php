<?php
include '../functions.php';

// Mengubah status pengiriman
if (isset($_POST['update_status'])) {
    $conn = dbConnect();
    $id = $_POST['id'];
    $status_pengiriman = $_POST['status_pengiriman'];
    $stmt = $conn->prepare("UPDATE Pesanan SET status_pengiriman = ? WHERE id = ?");
    $stmt->bind_param("si", $status_pengiriman, $id);
    if ($stmt->execute()) {
        $message = "Status pengiriman berhasil diperbarui";
    } else {
        $message = "Error: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
}

// Menampilkan daftar pesanan
$conn = dbConnect();
$sql = "SELECT Pesanan.id, Pesanan.tanggal_transaksi, Pesanan.total, Pesanan.status_pengiriman, Pesanan.alamat, Pesanan.metode_pembayaran, Pesanan.metode_pengiriman, Pengguna.nama AS nama_pelanggan
        FROM Pesanan
        JOIN Pengguna ON Pesanan.pengguna_id = Pengguna.id
        ORDER BY Pesanan.tanggal_transaksi DESC";
$result = $conn->query($sql);
$pesanan_list = [];
while ($row = $result->fetch_assoc()) {
    $pesanan_list[] = $row;
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pesanan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php require('layout/sidebar.php') ?>

    <div class="container">
        <h2 class="my-4 text-center">Manajemen Pesanan</h2>
        
        <!-- Pesan jika ada status perubahan -->
        <?php if (isset($message)): ?>
            <div class="alert alert-info"><?= $message; ?></div>
        <?php endif; ?>

        <!-- Daftar Pesanan -->
        <div class="card" id="Daftar-Pesanan">
            <div class="card-header">
                <h3 class="card-title">Daftar Pesanan</h3>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID Pesanan</th>
                            <th>Nama Pelanggan</th>
                            <th>Tanggal Transaksi</th>
                            <th>Total</th>
                            <th>Status Pengiriman</th>
                            <th>Metode Pembayaran</th>
                            <th>Metode Pengiriman</th>
                            <th>Alamat</th>
                            <th>Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pesanan_list as $pesanan): ?>
                            <tr>
                                <td><?= $pesanan['id']; ?></td>
                                <td><?= $pesanan['nama_pelanggan']; ?></td>
                                <td><?= $pesanan['tanggal_transaksi']; ?></td>
                                <td>Rp <?= number_format($pesanan['total'], 2, ',', '.'); ?></td>
                                <td><span class="badge bg-secondary"><?= $pesanan['status_pengiriman']; ?></span></td>
                                <td><?= ucfirst(str_replace('_', ' ', $pesanan['metode_pembayaran'])); ?></td>
                                <td><?= ucfirst(str_replace('_', ' ', $pesanan['metode_pengiriman'])); ?></td>
                                <td><?= htmlspecialchars($pesanan['alamat']); ?></td>
                                <td><a href="manajemen_pesanan.php?view_id=<?= $pesanan['id']; ?>" class="btn btn-info btn-sm">Lihat Detail</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Status Pesanan dan Detail Pesanan -->
        <?php if (isset($_GET['view_id'])): ?>
            <?php
            $conn = dbConnect();
            $view_id = $_GET['view_id'];
            $stmt = $conn->prepare("SELECT RincianPesanan.*, Produk.nama AS nama_produk
                                    FROM RincianPesanan
                                    JOIN Produk ON RincianPesanan.produk_id = Produk.id
                                    WHERE pesanan_id = ?");
            $stmt->bind_param("i", $view_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $rincian_pesanan = [];
            while ($row = $result->fetch_assoc()) {
                $rincian_pesanan[] = $row;
            }
            $stmt->close();

            $stmt = $conn->prepare("SELECT status_pengiriman, alamat, metode_pembayaran, metode_pengiriman FROM Pesanan WHERE id = ?");
            $stmt->bind_param("i", $view_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $status_pesanan = $result->fetch_assoc();
            $stmt->close();
            $conn->close();
            ?>
            <!-- Ubah Status Pesanan -->
            <div class="card" id="Status-Pesanan">
                <div class="card-header">
                    <h3 class="card-title">Status Pengiriman</h3>
                </div>
                <div class="card-body">
                    <form method="post" action="">
                        <input type="hidden" name="id" value="<?= $view_id; ?>">
                        <div class="form-group">
                            <label for="status_pengiriman">Ubah Status Pengiriman</label>
                            <select class="form-control" id="status_pengiriman" name="status_pengiriman" required>
                                <option value="Menunggu Konfirmasi" <?= $status_pesanan['status_pengiriman'] == 'Menunggu Konfirmasi' ? 'selected' : ''; ?>>Menunggu Konfirmasi</option>
                                <option value="Sedang Diproses" <?= $status_pesanan['status_pengiriman'] == 'Sedang Diproses' ? 'selected' : ''; ?>>Sedang Diproses</option>
                                <option value="Dikirim" <?= $status_pesanan['status_pengiriman'] == 'Dikirim' ? 'selected' : ''; ?>>Dikirim</option>
                                <option value="Selesai" <?= $status_pesanan['status_pengiriman'] == 'Selesai' ? 'selected' : ''; ?>>Selesai</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary" name="update_status">Update Status</button>
                    </form>
                </div>
            </div>

            <!-- Informasi Pesanan -->
            <div class="card mt-4">
                <div class="card-header">
                    <h3 class="card-title">Informasi Pesanan</h3>
                </div>
                <div class="card-body">
                    <p><strong>Alamat:</strong> <?= htmlspecialchars($status_pesanan['alamat']); ?></p>
                    <p><strong>Metode Pembayaran:</strong> <?= ucfirst(str_replace('_', ' ', $status_pesanan['metode_pembayaran'])); ?></p>
                    <p><strong>Metode Pengiriman:</strong> <?= ucfirst(str_replace('_', ' ', $status_pesanan['metode_pengiriman'])); ?></p>
                </div>
            </div>

            <!-- Rincian Pesanan -->
            <div class="card mt-4">
                <div class="card-header">
                    <h3 class="card-title">Rincian Pesanan</h3>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Nama Produk</th>
                                <th>Jumlah</th>
                                <th>Harga</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rincian_pesanan as $rincian): ?>
                                <tr>
                                    <td><?= $rincian['nama_produk']; ?></td>
                                    <td><?= $rincian['jumlah']; ?></td>
                                    <td>Rp <?= number_format($rincian['harga'], 2, ',', '.'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
