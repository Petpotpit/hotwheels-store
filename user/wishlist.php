<!DOCTYPE html>
<html lang="en">
<?php
session_start();
require('../functions.php');

$title = "Wishlist";
require('layout/header.php');

$conn = dbConnect();
$userId = $_SESSION['pengguna_id'];

// Mengambil detail wishlist dari database
$wishlist = getWishlist($conn, $userId);
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wishlist</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.2.1/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <main>
        <div class="container mx-auto p-6">
            <h1 class="text-3xl font-semibold text-center text-gray-800 mb-6">Wishlist</h1>
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
    </main>

    <script>
        // Fungsi untuk menghapus produk dari wishlist
        function hapusWishlist(id) {
            if (confirm("Apakah Anda yakin ingin menghapus produk ini dari wishlist?")) {
                // Mengirim permintaan AJAX untuk menghapus produk dari wishlist
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "hapus_wishlist.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

                // Mengirim id produk untuk dihapus
                xhr.send("id=" + id);

                // Menangani respon dari server
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        // Menghapus baris yang sesuai di tabel
                        document.querySelector("#wishlist-row-" + id).remove();
                        alert("Produk berhasil dihapus dari wishlist!");
                    } else {
                        alert("Terjadi kesalahan saat menghapus produk.");
                    }
                };
            }
        }
    </script>

    <?php require('layout/footer.php'); ?>
</body>
</html>
