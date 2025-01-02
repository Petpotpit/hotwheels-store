-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 18, 2024 at 08:32 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `toko_hotwheels`
--

-- --------------------------------------------------------

--
-- Table structure for table `kasir`
--

CREATE TABLE `kasir` (
  `id` int(11) NOT NULL,
  `transaksi_id` int(11) DEFAULT NULL,
  `produk_id` int(11) DEFAULT NULL,
  `jumlah` int(11) DEFAULT NULL,
  `total_harga` decimal(10,2) DEFAULT NULL,
  `tanggal_transaksi` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `keranjang`
--

CREATE TABLE `keranjang` (
  `id` int(11) NOT NULL,
  `pengguna_id` int(11) DEFAULT NULL,
  `produk_id` int(11) DEFAULT NULL,
  `jumlah` int(11) NOT NULL,
  `tanggal_ditambahkan` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kupon`
--

CREATE TABLE `kupon` (
  `id` int(11) NOT NULL,
  `kode` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `diskon` decimal(10,2) NOT NULL,
  `tanggal_berlaku` datetime NOT NULL,
  `tanggal_kadaluarsa` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kupon`
--

INSERT INTO `kupon` (`id`, `kode`, `deskripsi`, `diskon`, `tanggal_berlaku`, `tanggal_kadaluarsa`) VALUES
(1, 'DISKON10', 'Diskon 10% untuk semua produk', 10.00, '2024-01-01 00:00:00', '2024-12-31 23:59:00'),
(2, 'PROMO50', 'Potongan harga Rp50.000', 50000.00, '2024-01-01 00:00:00', '2024-12-30 23:59:59');

-- --------------------------------------------------------

--
-- Table structure for table `manajemenstok`
--

CREATE TABLE `manajemenstok` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `produk_id` int(11) NOT NULL,
  `tanggal` datetime NOT NULL,
  `aksi` enum('stok masuk','stok keluar') NOT NULL,
  `jumlah_perubahan` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `manajemenstok`
--

INSERT INTO `manajemenstok` (`id`, `admin_id`, `produk_id`, `tanggal`, `aksi`, `jumlah_perubahan`) VALUES
(1, 2, 1, '2024-11-29 10:00:00', 'stok masuk', 50),
(2, 7, 2, '2024-11-29 11:00:00', 'stok masuk', 100),
(3, 2, 3, '2024-11-29 12:00:00', 'stok keluar', 20),
(4, 7, 4, '2024-11-29 13:00:00', 'stok masuk', 30),
(5, 2, 5, '2024-11-29 14:00:00', 'stok keluar', 10),
(6, 7, 6, '2024-11-29 15:00:00', 'stok keluar', 5),
(7, 2, 7, '2024-11-29 16:00:00', 'stok masuk', 200),
(8, 7, 8, '2024-11-29 17:00:00', 'stok keluar', 50),
(9, 2, 9, '2024-11-29 18:00:00', 'stok masuk', 150),
(10, 7, 10, '2024-11-29 19:00:00', 'stok keluar', 60),
(11, 2, 11, '2024-11-29 20:00:00', 'stok masuk', 80),
(13, 2, 13, '2024-11-29 22:00:00', 'stok masuk', 90),
(14, 7, 14, '2024-11-29 23:00:00', 'stok keluar', 25),
(15, 2, 15, '2024-11-29 00:00:00', 'stok masuk', 120),
(16, 7, 16, '2024-11-30 01:00:00', 'stok keluar', 40),
(17, 2, 17, '2024-11-30 02:00:00', 'stok masuk', 60),
(18, 7, 18, '2024-11-30 03:00:00', 'stok keluar', 30);

-- --------------------------------------------------------

--
-- Table structure for table `pengguna`
--

CREATE TABLE `pengguna` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('pelanggan','admin') NOT NULL,
  `alamat` text DEFAULT NULL,
  `telepon` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengguna`
--

INSERT INTO `pengguna` (`id`, `nama`, `email`, `password`, `role`, `alamat`, `telepon`) VALUES
(1, 'rin', 'a@gmail.com', '$2y$10$7KGfCzaZgeDSwC4ioV/tmeL3FRjCk9.vt6fk1ZyldROQuPzU0uwgK', 'pelanggan', '', ''),
(2, 'patrick', 'patrick@gmail.com', '$2y$10$BN41p0nnMa225Zlw187usuigEmil5dS3ON28DXN7OGY0DHtUN.5Hu', 'admin', NULL, NULL),
(3, 'all', 'all@gmail.com', '$2y$10$Aww/mjU9TnxsOi/mTH6dDuS8nHz7Co2XuOvh3K85HG0o.pr86sjkC', 'pelanggan', 'bergas', '087828534944'),
(7, 'all', 'alleuy@gmail.com', '$2y$10$Y5PwQ1D4EDeFo6jqG9Cu9uBibCqp7fp7H1CXUVTqTKF1mWF.yrQ8y', 'admin', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `pesanan`
--

CREATE TABLE `pesanan` (
  `id` int(11) NOT NULL,
  `pengguna_id` int(11) DEFAULT NULL,
  `tanggal_transaksi` datetime NOT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `metode_pembayaran` enum('paypal','e-wallet') DEFAULT NULL,
  `metode_pengiriman` enum('standar','ekspres') NOT NULL,
  `kupon_id` int(11) DEFAULT NULL,
  `status_pengiriman` enum('Menunggu Konfirmasi','Sedang Diproses','Dikirim','Selesai') DEFAULT 'Menunggu Konfirmasi'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pesanan`
--

INSERT INTO `pesanan` (`id`, `pengguna_id`, `tanggal_transaksi`, `total`, `alamat`, `metode_pembayaran`, `metode_pengiriman`, `kupon_id`, `status_pengiriman`) VALUES
(24, 3, '2024-12-10 11:26:28', 285000.00, 'bergas', 'paypal', 'standar', NULL, 'Dikirim');

-- --------------------------------------------------------

--
-- Table structure for table `pesankontak`
--

CREATE TABLE `pesankontak` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subjek` varchar(255) NOT NULL,
  `pesan` text NOT NULL,
  `tanggal_kirim` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `harga` decimal(10,2) NOT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `kategori` varchar(255) DEFAULT NULL,
  `stok` int(11) DEFAULT 0,
  `stok_minimum` int(11) DEFAULT 0,
  `diskon` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`id`, `nama`, `deskripsi`, `harga`, `gambar`, `kategori`, `stok`, `stok_minimum`, `diskon`) VALUES
(1, 'Hotwheels Red Car', 'hotwheels x super', 50000.00, 'images.jpeg', 'Super Car', 100, 10, 0.00),
(2, 'Hotwheels Blue Car', 'Mobil Hotwheels warna biru', 60000.00, 'images.jpeg', 'Super Car', 100, 10, 0.00),
(3, 'Hotwheels Green Car', 'Mobil Hotwheels warna hijau', 55000.00, 'images.jpeg', 'Super Car', 100, 10, 0.00),
(4, 'Monster Truck Blaze', 'Monster Truck Blaze with large wheels and powerful engine.', 75000.00, 'images.jpeg', 'Monster Truk', 100, 10, 0.00),
(5, 'Monster Truck Crusher', 'Monster Truck Crusher with crushing power and rugged design.', 80000.00, 'images.jpeg', 'Monster Truk', 100, 10, 0.00),
(6, 'Monster Truck Max-D', 'Monster Truck Max-D with extreme power and durability.', 85000.00, 'images.jpeg', 'Monster Truk', 100, 10, 0.00),
(7, 'Monster Truck Grave Digger', 'Monster Truck Grave Digger with iconic design and strength.', 90000.00, 'images.jpeg', 'Monster Truk', 100, 10, 0.00),
(8, 'Monster Truck Zombie', 'Monster Truck Zombie with thrilling design and performance.', 95000.00, 'images.jpeg', 'Monster Truk', 100, 10, 0.00),
(9, 'Super Car Ferrari', 'Super Car Ferrari with sleek design and high speed.', 150000.00, 'images.jpeg', 'Super Car', 100, 10, 0.00),
(10, 'Super Car Lamborghini', 'Super Car Lamborghini with iconic design and luxury.', 155000.00, 'images.jpeg', 'Super Car', 100, 10, 0.00),
(11, 'Super Car Bugatti', 'Super Car Bugatti with ultimate performance and exclusivity.', 160000.00, 'images.jpeg', 'Super Car', 100, 10, 0.00),
(13, 'Super Car Porsche v3', 'Super Car Porsche with timeless design and dynamic performance.', 5550000.00, 'images.jpeg', 'Collector', 100, 10, 0.00),
(14, 'Collector Edition Classic', 'Collector Edition Classic with vintage design and rare features.', 200000.00, 'images.jpeg', 'Collector', 100, 10, 0.00),
(15, 'Collector Edition Limit', 'Collector Edition Limited with limited production and exclusive details.', 210000.00, 'images.jpeg', 'Collector', 100, 10, 0.00),
(16, 'Collector Edition Sig', 'Collector Edition Signature with autograph and special packaging.', 220000.00, 'images.jpeg', 'Collector', 100, 10, 0.00),
(17, 'Collector Edition', 'Collector Edition Anniversary with unique anniversary theme.', 230000.00, 'images.jpeg', 'Collector', 100, 10, 0.00),
(18, 'Collector Edition Gold', 'Collector Edition Gold with golden finish and premium packaging.', 240000.00, 'images.jpeg', 'Collector', 100, 10, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `rincianpesanan`
--

CREATE TABLE `rincianpesanan` (
  `id` int(11) NOT NULL,
  `pesanan_id` int(11) DEFAULT NULL,
  `produk_id` int(11) DEFAULT NULL,
  `jumlah` int(11) DEFAULT NULL,
  `harga` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rincianpesanan`
--

INSERT INTO `rincianpesanan` (`id`, `pesanan_id`, `produk_id`, `jumlah`, `harga`, `subtotal`) VALUES
(5, 24, 3, 5, 55000.00, 275000.00);

-- --------------------------------------------------------

--
-- Table structure for table `ulasan`
--

CREATE TABLE `ulasan` (
  `id` int(11) NOT NULL,
  `produk_id` int(11) DEFAULT NULL,
  `pengguna_id` int(11) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `komentar` text DEFAULT NULL,
  `tanggal` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ulasan`
--

INSERT INTO `ulasan` (`id`, `produk_id`, `pengguna_id`, `rating`, `komentar`, `tanggal`) VALUES
(1, 1, 1, 5, 'Produk yang sangat bagus!', '2024-01-05 00:00:00'),
(2, 2, 2, 4, 'Cukup puas dengan produk ini.', '2024-01-06 00:00:00'),
(3, 3, 3, 3, 'Biasa saja.', '2024-01-07 00:00:00'),
(4, 3, 3, 4, 'qqqqqq', '2024-12-07 10:31:53');

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL,
  `pengguna_id` int(11) DEFAULT NULL,
  `produk_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wishlist`
--

INSERT INTO `wishlist` (`id`, `pengguna_id`, `produk_id`) VALUES
(9, NULL, 18),
(10, NULL, 18),
(11, NULL, 18),
(12, NULL, 18),
(13, NULL, 18),
(14, NULL, 18),
(15, NULL, 18),
(16, NULL, 18),
(17, NULL, 18),
(18, 3, 3);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `kasir`
--
ALTER TABLE `kasir`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaksi_id` (`transaksi_id`),
  ADD KEY `produk_id` (`produk_id`);

--
-- Indexes for table `keranjang`
--
ALTER TABLE `keranjang`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_keranjang_pengguna` (`pengguna_id`),
  ADD KEY `fk_keranjang_produk` (`produk_id`);

--
-- Indexes for table `kupon`
--
ALTER TABLE `kupon`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `manajemenstok`
--
ALTER TABLE `manajemenstok`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`),
  ADD KEY `produk_id` (`produk_id`);

--
-- Indexes for table `pengguna`
--
ALTER TABLE `pengguna`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pesanan_pengguna` (`pengguna_id`),
  ADD KEY `kupon_id` (`kupon_id`);

--
-- Indexes for table `pesankontak`
--
ALTER TABLE `pesankontak`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rincianpesanan`
--
ALTER TABLE `rincianpesanan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_rincianpesanan_pesanan` (`pesanan_id`),
  ADD KEY `fk_rincianpesanan_produk` (`produk_id`);

--
-- Indexes for table `ulasan`
--
ALTER TABLE `ulasan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `produk_id` (`produk_id`),
  ADD KEY `pengguna_id` (`pengguna_id`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pengguna_id` (`pengguna_id`),
  ADD KEY `produk_id` (`produk_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `kasir`
--
ALTER TABLE `kasir`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `keranjang`
--
ALTER TABLE `keranjang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `kupon`
--
ALTER TABLE `kupon`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `manajemenstok`
--
ALTER TABLE `manajemenstok`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `pengguna`
--
ALTER TABLE `pengguna`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `pesankontak`
--
ALTER TABLE `pesankontak`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `rincianpesanan`
--
ALTER TABLE `rincianpesanan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `ulasan`
--
ALTER TABLE `ulasan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `kasir`
--
ALTER TABLE `kasir`
  ADD CONSTRAINT `kasir_ibfk_1` FOREIGN KEY (`transaksi_id`) REFERENCES `pesanan` (`id`),
  ADD CONSTRAINT `kasir_ibfk_2` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`);

--
-- Constraints for table `keranjang`
--
ALTER TABLE `keranjang`
  ADD CONSTRAINT `fk_keranjang_pengguna` FOREIGN KEY (`pengguna_id`) REFERENCES `pengguna` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_keranjang_produk` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `keranjang_ibfk_1` FOREIGN KEY (`pengguna_id`) REFERENCES `pengguna` (`id`),
  ADD CONSTRAINT `keranjang_ibfk_2` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`);

--
-- Constraints for table `manajemenstok`
--
ALTER TABLE `manajemenstok`
  ADD CONSTRAINT `manajemenstok_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `pengguna` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `manajemenstok_ibfk_2` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD CONSTRAINT `fk_pesanan_pengguna` FOREIGN KEY (`pengguna_id`) REFERENCES `pengguna` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pesanan_ibfk_1` FOREIGN KEY (`pengguna_id`) REFERENCES `pengguna` (`id`),
  ADD CONSTRAINT `pesanan_ibfk_2` FOREIGN KEY (`kupon_id`) REFERENCES `kupon` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `rincianpesanan`
--
ALTER TABLE `rincianpesanan`
  ADD CONSTRAINT `fk_rincianpesanan_pesanan` FOREIGN KEY (`pesanan_id`) REFERENCES `pesanan` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_rincianpesanan_produk` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rincianpesanan_ibfk_1` FOREIGN KEY (`pesanan_id`) REFERENCES `pesanan` (`id`),
  ADD CONSTRAINT `rincianpesanan_ibfk_2` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`);

--
-- Constraints for table `ulasan`
--
ALTER TABLE `ulasan`
  ADD CONSTRAINT `ulasan_ibfk_1` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`),
  ADD CONSTRAINT `ulasan_ibfk_2` FOREIGN KEY (`pengguna_id`) REFERENCES `pengguna` (`id`);

--
-- Constraints for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`pengguna_id`) REFERENCES `pengguna` (`id`),
  ADD CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
