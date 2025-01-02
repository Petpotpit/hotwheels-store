<?php
session_start();
if (!isset($_SESSION['pengguna_level']) || $_SESSION['pengguna_level'] != 'admin') {
    header("Location: ../login.php");
    exit();
}
require('../functions.php');

// Pastikan ada parameter tanggal dari input
$start_date = '2024-01-01'; // Ganti dengan input dinamis jika diperlukan
$end_date = '2024-12-31';   // Ganti dengan input dinamis jika diperlukan

$conn = dbConnect();
$ringkasanPenjualan = getRingkasanPenjualan($conn, $start_date, $end_date);
$grafikPenjualan = getGrafikPenjualan($conn, $start_date, $end_date, 'DAY'); // Ubah menjadi WEEK atau MONTH jika diperlukan
$produkTerlaris = getProdukTerlaris($conn, $start_date, $end_date);
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card-custom {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <?php require('layout/sidebar.php'); ?>
    <div class="container mx-auto py-8 px-4">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Dashboard Admin</h1>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Total Penjualan -->
            <div class="card-custom bg-blue-500 text-white p-4 rounded-lg">
                <h2 class="text-lg font-semibold">Total Penjualan</h2>
                <p class="text-2xl font-bold mt-2">Rp <?= number_format($ringkasanPenjualan['total_pendapatan'], 2, ',', '.'); ?></p>
            </div>

            <!-- Jumlah Pesanan -->
            <div class="card-custom bg-green-500 text-white p-4 rounded-lg">
                <h2 class="text-lg font-semibold">Jumlah Pesanan</h2>
                <p class="text-2xl font-bold mt-2"><?= $ringkasanPenjualan['jumlah_transaksi']; ?></p>
            </div>
        </div>

        <!-- Grafik Penjualan -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Grafik Penjualan</h2>
            <canvas id="penjualanChart" class="w-full h-64"></canvas>
        </div>

        <!-- Tabel Produk Terlaris -->
        <div class="bg-white p-6 mt-8 rounded-lg shadow-md">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Produk Terlaris</h2>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">Nama Produk</th>
                        <th scope="col">Total Terjual</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($produkTerlaris as $index => $produk): ?>
                        <tr>
                            <td><?= $index + 1; ?></td>
                            <td><?= $produk['nama']; ?></td>
                            <td><?= $produk['total_terjual']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('penjualanChart').getContext('2d');
            const data = {
                labels: <?= json_encode(array_column($grafikPenjualan, 'tanggal')); ?>,
                datasets: [{
                    label: 'Total Penjualan Harian',
                    data: <?= json_encode(array_column($grafikPenjualan, 'total_harian')); ?>,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 2
                }]
            };

            const options = {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            };

            new Chart(ctx, {
                type: 'line',
                data: data,
                options: options
            });
        });
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
