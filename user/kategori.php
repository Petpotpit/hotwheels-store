<!DOCTYPE html>
<html lang="en">
<?php
session_start();
require('../functions.php');

$title = "Kategori Produk";
require('layout/header.php');

$conn = dbConnect();
$produk_id = isset($_GET['id']) ? $_GET['id'] : null;
$userId = $_SESSION['pengguna_id'];

if (isset($_GET['add_to_wishlist'])) {
    // Menambahkan produk ke wishlist
    $message = addToWishlist($conn, $produk_id, $pengguna_id);
    echo $message;
}

// Dapatkan detail produk untuk ditampilkan di halaman
$query = "SELECT * FROM Produk WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $produk_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

$search = $_GET['search'] ?? '';
$filter = $_GET['filter'] ?? '';

$products = getProduk($conn, $search, $filter);
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategori Produk - Toko Hotwheels</title>
    <script src="https://kit.fontawesome.com/e7ea333cb2.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap">
</head>

<body class="bg-gray-100 font-roboto">

<main class="bg-white py-12">
    <div class="container mx-auto px-6">

        <!-- Header -->
        <h1 class="text-3xl font-bold text-center text-gray-800 mb-8 uppercase tracking-wider">Kategori Produk</h1>

        <!-- Search and Filter -->
        <form class="flex justify-center mb-8 space-x-6" method="GET" action="kategori.php">
            <div class="flex items-center space-x-4">
                <input class="form-input px-4 py-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" type="search" name="search" placeholder="Cari produk..." value="<?php echo $search; ?>">
                <div class="flex space-x-4">
                    <input type="radio" class="hidden" id="monster_truck" name="filter" value="Monster Truk" <?php echo $filter == 'Monster Truk' ? 'checked' : ''; ?> onchange="this.form.submit();">
                    <label class="cursor-pointer inline-block bg-gray-200 hover:bg-blue-500 text-gray-700 hover:text-white py-2 px-4 rounded-full transition-colors" for="monster_truck">Monster Truk</label>

                    <input type="radio" class="hidden" id="super_car" name="filter" value="Super Car" <?php echo $filter == 'Super Car' ? 'checked' : ''; ?> onchange="this.form.submit();">
                    <label class="cursor-pointer inline-block bg-gray-200 hover:bg-blue-500 text-gray-700 hover:text-white py-2 px-4 rounded-full transition-colors" for="super_car">Super Car</label>

                    <input type="radio" class="hidden" id="collector" name="filter" value="Collector" <?php echo $filter == 'Collector' ? 'checked' : ''; ?> onchange="this.form.submit();">
                    <label class="cursor-pointer inline-block bg-gray-200 hover:bg-blue-500 text-gray-700 hover:text-white py-2 px-4 rounded-full transition-colors" for="collector">Collector</label>
                </div>
            </div>

            <button class="btn px-6 py-2 bg-blue-600 text-white rounded-full hover:bg-blue-700 focus:outline-none transition-colors">Cari</button>
        </form>

        <!-- Product List -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <?php while ($row = $products->fetch_assoc()): ?>
                <div class="pro bg-white rounded-lg shadow-md overflow-hidden transform transition duration-300 hover:scale-105">
                    <img src="../user/produk/<?php echo $row['gambar']; ?>" alt="<?php echo htmlspecialchars($row['nama']); ?>" class="w-full h-48 object-cover">
                    <div class="p-4">
                        <span class="text-gray-500 text-sm uppercase tracking-wider"><?php echo htmlspecialchars($row['kategori']); ?></span>
                        <h5 class="text-lg font-semibold text-gray-800 mt-2"><?php echo htmlspecialchars($row['nama']); ?></h5>
                        <div class="flex items-center space-x-1 my-2">
                            <i class="fas fa-star text-yellow-400"></i>
                            <i class="fas fa-star text-yellow-400"></i>
                            <i class="fas fa-star text-yellow-400"></i>
                            <i class="fas fa-star text-yellow-400"></i>
                            <i class="fas fa-star text-yellow-400"></i>
                        </div>
                        <h4 class="text-lg text-blue-600 font-semibold">Rp<?php echo number_format($row['harga']); ?></h4>
                    </div>
                    <button class="w-full py-2 bg-blue-600 text-white text-center font-semibold rounded-b-lg hover:bg-blue-700 transition-colors" onclick="window.location.href='produk.php?id=<?php echo $row['id']; ?>'">Lihat Produk</button>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</main>

<?php require('layout/footer.php'); ?>

</body>

</html>
