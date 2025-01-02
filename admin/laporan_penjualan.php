<?php
require_once '../functions.php'; // Sambungkan ke file functions.php

$conn = dbConnect(); // Koneksi ke database


// Ambil data untuk dashboard
$stats = getStatistics($conn);
$report = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $startDate = $_POST['start_date'] ?? null;
    $endDate = $_POST['end_date'] ?? null;
    $report = getReport($conn, $startDate, $endDate);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-light">
    <?php require('layout/sidebar.php') ?>
<div class="container py-5">
    <h1 class="text-center mb-4">Laporan Penjualan</h1>

    <!-- Dashboard Statistik -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5>Total Penjualan</h5>
                    <h3><?= $stats['total_penjualan'] ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5>Total Pendapatan</h5>
                    <h3>Rp <?= number_format($stats['total_pendapatan'], 2, ',', '.') ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5>Total Pelanggan</h5>
                    <h3><?= $stats['total_pelanggan'] ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Filter -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title">Filter Laporan</h5>
            <form method="POST">
                <div class="row">
                    <div class="col-md-4">
                        <label for="start_date" class="form-label">Tanggal Mulai</label>
                        <input type="date" id="start_date" name="start_date" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label for="end_date" class="form-label">Tanggal Selesai</label>
                        <input type="date" id="end_date" name="end_date" class="form-control">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Tampilkan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel Laporan -->
    <?php if ($report): ?>
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Hasil Laporan</h5>
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>ID Pesanan</th>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th>Total Harga</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php while ($row = $report->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['pesanan_id'] ?></td>
                            <td><?= $row['tanggal_transaksi'] ?></td>
                            <td><?= $row['pelanggan'] ?></td>
                            <td>Rp <?= number_format($row['total_harga'], 2, ',', '.') ?></td>
                            <td><?= $row['status_pengiriman'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
