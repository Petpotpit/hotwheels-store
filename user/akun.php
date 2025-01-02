<!DOCTYPE html>
<html lang="en">
<?php
session_start();
require('../functions.php');

$title = "Akun Saya - Toko Hotwheels";
require('layout/header.php');

$conn = dbConnect();
$pengguna_id = $_SESSION['pengguna_id'];


// Fungsi untuk mengubah kata sandi
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_password'])) {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Validasi input
    if (empty($old_password) || empty($new_password) || empty($confirm_password)) {
        $password_message = "Semua kolom harus diisi!";
    } elseif ($new_password !== $confirm_password) {
        $password_message = "Password baru dan konfirmasi password tidak cocok!";
    } else {
        $userData = getUserData($conn, $pengguna_id);

        // Verifikasi password lama
        if (password_verify($old_password, $userData['password'])) {
            // Hash password baru
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Update password di database
            $query = "UPDATE Pengguna SET password = ? WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("si", $hashed_password, $pengguna_id);

            if ($stmt->execute()) {
                $password_message = "Password berhasil diperbarui!";
            } else {
                $password_message = "Terjadi kesalahan saat memperbarui password.";
            }
        } else {
            $password_message = "Password lama salah!";
        }
    }
}

$userData = getUserData($conn, $pengguna_id);
$orders = getUserOrders($conn, $pengguna_id);
$wishlist = getWishlist($conn, $pengguna_id);
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akun Saya - Toko Hotwheels</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.0.24/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
</head>
<body class="bg-gray-100 font-sans antialiased">

    <div class="content flex flex-col md:flex-row md:space-x-6 p-6">
        <div class="flex-1">
            <!-- Riwayat Pesanan -->
            <div id="riwayat_pesanan" class="container mx-auto p-6">
                <h2 class="text-3xl font-semibold text-center text-gray-800 mb-6">Riwayat Pesanan</h2>
                <?php if ($orders->num_rows > 0): ?>
                    <ul class="space-y-4">
                        <?php while ($order = $orders->fetch_assoc()): ?>
                            <li class="border-b pb-4">
                                <strong>Pesanan ID:</strong> <?php echo $order['id']; ?><br>
                                <strong>Tanggal:</strong> <?php echo $order['tanggal_transaksi']; ?><br>
                                <strong>Produk:</strong> <?php echo htmlspecialchars($order['produk_nama']); ?><br>
                                <strong>Total:</strong> Rp<?php echo number_format($order['total']); ?><br>
                                <strong>Status:</strong> <?php echo htmlspecialchars($order['status_pengiriman']); ?><br>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-gray-500">Anda belum memiliki riwayat pesanan.</p>
                <?php endif; ?>
            </div>

            <div id="wishlist" class="container mx-auto p-6">
            <h2 class="text-3xl font-semibold text-center text-gray-800 mb-6">Wishlist</h2>
            <?php if ($wishlist->num_rows > 0): ?>
                <div class="overflow-x-auto bg-white shadow-lg rounded-lg p-4">
                    <table class="min-w-full table-auto">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-gray-600 font-semibold">Produk</th>
                                <th class="px-6 py-3 text-left text-gray-600 font-semibold">Harga</th>
                                <th class="px-6 py-3 text-left text-gray-600 font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $wishlist->fetch_assoc()): ?>
                                <tr id="wishlist-row-<?php echo $row['id']; ?>" class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <img class="w-24 h-24 object-cover rounded-lg mr-4" src="../user/produk/<?php echo $row['gambar']; ?>" alt="<?php echo htmlspecialchars($row['nama']); ?>">
                                            <div class="text-gray-800 font-medium"><?php echo htmlspecialchars($row['nama']); ?></div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-gray-800 font-semibold">Rp<?php echo number_format($row['harga']); ?></td>
                                    <td class="px-6 py-4 flex space-x-3">
                                        <a href="keranjang.php?add=<?php echo $row['produk_id']; ?>" class="bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600 transition duration-200">Tambah ke Keranjang</a>
                                        <button class="bg-red-500 text-white py-2 px-4 rounded-lg hover:bg-red-600 transition duration-200" onclick="hapusWishlist(<?php echo $row['id']; ?>)">Hapus</button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-center text-gray-600 mt-6">Tidak ada produk dalam wishlist Anda.</p>
            <?php endif; ?>
        </div>

 
            <!-- Pengaturan Akun -->
            <div id="pengaturan_akun" class="container mx-auto p-6">
                <h2 class="text-3xl font-semibold text-center text-gray-800 mb-6">Pengaturan Akun</h2>
                <?php if (isset($password_message)): ?>
                    <p class="text-red-500 mb-4"><?php echo $password_message; ?></p>
                <?php endif; ?>
                <form action="account.php#pengaturan_akun" method="post">
                    <div class="mb-4">
                        <label for="old_password" class="block text-gray-700">Password Lama:</label>
                        <input type="password" class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" id="old_password" name="old_password" required>
                    </div>
                    <div class="mb-4">
                        <label for="new_password" class="block text-gray-700">Password Baru:</label>
                        <input type="password" class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" id="new_password" name="new_password" required>
                    </div>
                    <div class="mb-4">
                        <label for="confirm_password" class="block text-gray-700">Konfirmasi Password Baru:</label>
                        <input type="password" class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" id="confirm_password" name="confirm_password" required>
                    </div>
                    <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600 focus:outline-none" name="update_password">Ubah Kata Sandi</button>
                </form>
            </div>
        </div>
    </div>

    <?php require('layout/footer.php'); ?>

    <script>
        function openNav() {
            document.getElementById("mySidebar").style.width = "250px";
            document.querySelector(".content").style.marginLeft = "250px";
            document.getElementById("mySidebar").classList.remove('hidden');
        }

        function closeNav() {
            document.getElementById("mySidebar").style.width = "0";
            document.querySelector(".content").style.marginLeft = "0";
            document.getElementById("mySidebar").classList.add('hidden');
        }
    </script>
</body>

</html>
