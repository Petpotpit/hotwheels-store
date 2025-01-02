<?php
session_start();
require('../functions.php');

// Ambil ID pesanan dari parameter URL
$pesanan_id = $_GET['pesanan_id'];

// Ambil data pesanan dari database
$conn = dbConnect();
$stmt = $conn->prepare("SELECT p.id, p.total, p.alamat, p.metode_pembayaran, p.metode_pengiriman, p.status_pengiriman, p.tanggal_transaksi, k.kode AS kupon_kode
                        FROM Pesanan p
                        LEFT JOIN kupon k ON p.kupon_id = k.id
                        WHERE p.id = ?");
$stmt->bind_param('i', $pesanan_id);
$stmt->execute();
$pesanan = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Ambil detail pesanan
$stmt = $conn->prepare("SELECT rp.produk_id, rp.jumlah, rp.harga, rp.subtotal, pr.nama, pr.gambar
                        FROM RincianPesanan rp
                        JOIN Produk pr ON rp.produk_id = pr.id
                        WHERE rp.pesanan_id = ?");
$stmt->bind_param('i', $pesanan_id);
$stmt->execute();
$detail_pesanan = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Success</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5">
        <h2 class="text-center">Pesanan Anda Telah Berhasil!</h2>

        <div class="row mt-4">
            <div class="col-md-6">
                <h5>Detail Pesanan</h5>
                <ul class="list-group">
                    <li class="list-group-item"><strong>ID Pesanan:</strong> <?= $pesanan['id'] ?></li>
                    <li class="list-group-item"><strong>Total:</strong> Rp <?= number_format($pesanan['total'], 0, ',', '.') ?></li>
                    <li class="list-group-item"><strong>Alamat Pengiriman:</strong> <?= htmlspecialchars($pesanan['alamat']) ?></li>
                    <li class="list-group-item"><strong>Metode Pembayaran:</strong> <?= ucfirst($pesanan['metode_pembayaran']) ?></li>
                    <li class="list-group-item"><strong>Metode Pengiriman:</strong> <?= ucfirst($pesanan['metode_pengiriman']) ?></li>
                    <li class="list-group-item"><strong>Status Pengiriman:</strong> <?= $pesanan['status_pengiriman'] ?></li>
                    <li class="list-group-item"><strong>Tanggal Transaksi:</strong> <?= date('d M Y H:i', strtotime($pesanan['tanggal_transaksi'])) ?></li>
                    <?php if ($pesanan['kupon_kode']): ?>
                        <li class="list-group-item"><strong>Voucher:</strong> <?= htmlspecialchars($pesanan['kupon_kode']) ?></li>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="col-md-6">
                <h5>Produk yang Dipesan</h5>
                <ul class="list-group">
                    <?php while ($item = $detail_pesanan->fetch_assoc()): ?>
                        <li class="list-group-item d-flex justify-content-between">
                            <div class="d-flex">
                                <img src="produk/<?= htmlspecialchars($item['gambar']) ?>" alt="<?= htmlspecialchars($item['nama']) ?>" style="width: 60px; height: 60px; object-fit: cover;" class="me-3">
                                <div>
                                    <p class="mb-0"><?= htmlspecialchars($item['nama']) ?> (<?= $item['jumlah'] ?>x)</p>
                                    <p class="text-muted mb-0">Rp <?= number_format($item['harga'], 0, ',', '.') ?> each</p>
                                </div>
                            </div>
                            <span class="fw-bold">Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></span>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="home.php" class="btn btn-primary">Kembali ke Beranda</a>
        </div>
    </div>
</body>
</html>
