<?php
session_start();
require('../functions.php');

// Ambil ID pengguna dari session
$pengguna_id = $_SESSION['pengguna_id'];

// Ambil data keranjang
$keranjang = getKeranjang($pengguna_id);
$subtotal = hitungTotalKeranjang($keranjang);

// Ambil informasi pengguna
$conn = dbConnect();
$stmt = $conn->prepare("SELECT nama, email, alamat, telepon FROM Pengguna WHERE id = ?");
$stmt->bind_param('i', $pengguna_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Hitung Subtotal Produk
$subtotal = 0;
foreach ($keranjang as $item) {
    $subtotal += $item['harga'] * $item['jumlah'];
}

// Ambil metode pengiriman dari input (POST) atau default
$metode_pengiriman = isset($_POST['metode_pengiriman']) ? $_POST['metode_pengiriman'] : 'standar';
$shipping_cost = getShippingCost($metode_pengiriman);

// Validasi Voucher
$diskon = 0;
if (!empty($_POST['kode_voucher']) && isset($_POST['apply_voucher'])) {
    $kode_voucher = $_POST['kode_voucher'];
    $diskon = applyVoucher($kode_voucher, $pengguna_id);
}

// Total Akhir
$total = $subtotal + $shipping_cost - $diskon;

// Menangani pengajuan form checkout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout'])) {
    $alamat_pengiriman = $_POST['alamat'];
    $metode_pembayaran = $_POST['metode_pembayaran'];

    // Process voucher, if provided
    $kode_voucher = !empty($_POST['kode_voucher']) ? $_POST['kode_voucher'] : null;
    $kupon_id = null;
    if ($kode_voucher) {
        // Get voucher ID from the database
        $stmt = $conn->prepare("SELECT id FROM kupon WHERE kode = ?");
        $stmt->bind_param('s', $kode_voucher);
        $stmt->execute();
        $result = $stmt->get_result();
        $kupon = $result->fetch_assoc();
        $kupon_id = $kupon['id'] ?? null;
        $stmt->close();
    }

    // Insert order
    $stmt = $conn->prepare("INSERT INTO Pesanan (pengguna_id, tanggal_transaksi, total, alamat, metode_pembayaran, metode_pengiriman, kupon_id, status_pengiriman) 
                            VALUES (?, NOW(), ?, ?, ?, ?, ?, 'Menunggu Konfirmasi')");
    $stmt->bind_param('idssss', $pengguna_id, $total, $alamat_pengiriman, $metode_pembayaran, $metode_pengiriman, $kupon_id);
    $stmt->execute();
    $pesanan_id = $stmt->insert_id;
    $stmt->close();

    // Save order details
    foreach ($keranjang as $item) {
        $item_subtotal = $item['harga'] * $item['jumlah'];
        $stmt = $conn->prepare("INSERT INTO RincianPesanan (pesanan_id, produk_id, jumlah, harga, subtotal) 
                                VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('iiidi', $pesanan_id, $item['produk_id'], $item['jumlah'], $item['harga'], $item_subtotal);
        $stmt->execute();
        $stmt->close();
    }

    // Hapus semua item di keranjang pengguna yang telah membuat pesanan
    $stmt = $conn->prepare("DELETE FROM Keranjang WHERE pengguna_id = ?");
    $stmt->bind_param('i', $pengguna_id);
    $stmt->execute();
    $stmt->close();

    // Redirect to success page
    header("Location: success.php?pesanan_id=$pesanan_id");
    exit();
}
require('layout/header.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-pink-100">
    <div class="container-fluid vh-100 d-flex align-items-center justify-content-center">
        <div class="row w-100 shadow-lg bg-white rounded-lg overflow-hidden">
            <!-- Left Section -->
            <div class="col-lg-7 col-md-12 bg-gray-50 p-5">
                <h2 class="text-3xl font-bold text-gray-800 mb-6">Checkout</h2>
                <form action="" method="POST">
                    <!-- Shipping Details -->
                    <div class="mb-6">
                        <h5 class="text-lg font-semibold text-gray-700 mb-3">Shipping Details</h5>
                        <div class="form-group mb-3">
                            <label for="name" class="text-gray-600">Name</label>
                            <input type="text" id="name" name="nama" class="form-control rounded-md border-gray-300" value="<?= htmlspecialchars($user['nama']) ?>" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="address" class="text-gray-600">Address</label>
                            <input type="text" id="address" name="alamat" class="form-control rounded-md border-gray-300" value="<?= htmlspecialchars($user['alamat']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="email" class="text-gray-600">Email</label>
                            <input type="email" id="email" name="email" class="form-control rounded-md border-gray-300" value="<?= htmlspecialchars($user['email']) ?>" required>
                        </div>
                    </div>

                    <!-- Payment Details -->
                    <div>
                        <h5 class="text-lg font-semibold text-gray-700 mb-3">Payment Method</h5>
                        <div class="form-group mb-3">
                            <label for="payment-method" class="text-gray-600">Choose Method</label>
                            <select id="payment-method" name="metode_pembayaran" class="form-control rounded-md border-gray-300" required>
                                <option value="paypal">PayPal</option>
                                <option value="e-wallet">E-Wallet</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="card-name" class="text-gray-600">Name on Card</label>
                            <input type="text" id="card-name" name="card-name" class="form-control rounded-md border-gray-300" placeholder="Cardholder's Name" required>
                        </div>
                        <div class="form-group">
                            <label for="card-number" class="text-gray-600">Card Number</label>
                            <input type="text" id="card-number" name="card-number" class="form-control rounded-md border-gray-300" placeholder="1234 5678 9012 3456" required>
                        </div>
                    </div>

                    <button type="submit" name="checkout" class="mt-5 btn btn-success w-100 rounded-lg shadow-md">Purchase</button>
                </form>
            </div>
            <!-- Right Section -->
            <div class="col-lg-5 col-md-12 bg-green-200 text-white p-5">
                <h2 class="text-3xl font-bold mb-6">Your Order</h2>
                <div class="bg-white rounded-lg p-4 space-y-4 shadow-sm text-black">
                    <!-- Order Items -->
                    <?php 
                    foreach ($keranjang as $item): 
                        $item_subtotal = $item['harga'] * $item['jumlah'];
                    ?>
                        <div class="d-flex justify-content-between align-items-center border-b pb-3">
                            <div class="d-flex align-items-center gap-3">
                                <img src="produk/<?= htmlspecialchars($item['gambar']) ?>" class="rounded-md" alt="<?= htmlspecialchars($item['nama']) ?>" style="width: 60px; height: 60px;">
                                <div>
                                    <p class="mb-0"><?= htmlspecialchars($item['nama']) ?> (<?= $item['jumlah'] ?>x)</p>
                                    <small class="text-gray-500">Harga satuan: Rp <?= number_format($item['harga'], 0, ',', '.') ?></small>
                                </div>
                            </div>
                            <p class="mb-0 font-semibold">Rp <?= number_format($item_subtotal, 0, ',', '.') ?></p>
                        </div>
                    <?php endforeach; ?>

                    <!-- Voucher Input -->
                    <div class="mb-4">
                        <label for="voucher" class="block text-sm font-medium text-gray-700">Voucher Belanja</label>
                        <div class="flex items-center space-x-2">
                            <input type="text" id="voucher" name="kode_voucher" class="flex-1 border border-gray-300 p-2 rounded-l-md" placeholder="Masukkan Kode Voucher" value="<?= htmlspecialchars($kode_voucher ?? '') ?>">
                            <button type="submit" name="apply_voucher" class="bg-pink-500 hover:bg-pink-600 text-white px-4 py-2 rounded-r-md">Pakai</button>
                        </div>
                    </div>

                    <!-- Shipping Method -->
<!-- Shipping Method -->
<div class="mb-4">
    <label for="shipping-method" class="block text-sm font-medium text-gray-700">Shipping Method</label>
    <select id="shipping-method" name="metode_pengiriman" class="form-control rounded-md border-gray-300" required>
        <option value="standar" <?= $metode_pengiriman === 'standar' ? 'selected' : '' ?>>Standar (+Rp 10.000)</option>
        <option value="ekspres" <?= $metode_pengiriman === 'ekspres' ? 'selected' : '' ?>>Ekspres (+Rp 15.000)</option>
    </select>
</div>


                    <!-- Subtotal, Shipping Cost, Discount, and Total -->
                    <div class="d-flex justify-content-between mt-3">
                        <h5>Subtotal</h5>
                        <h5>Rp <?= number_format($subtotal, 0, ',', '.') ?></h5>
                    </div>
                    <div class="d-flex justify-content-between">
                        <h5>Shipping</h5>
                        <h5>Rp <?= number_format($shipping_cost, 0, ',', '.') ?></h5>
                    </div>
                    <div class="d-flex justify-content-between">
                        <h5>Discount</h5>
                        <h5>-Rp <?= number_format($diskon, 0, ',', '.') ?></h5>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between font-bold">
                        <h4>Total</h4>
                        <h4>Rp <?= number_format($total, 0, ',', '.') ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
