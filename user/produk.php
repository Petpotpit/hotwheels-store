<?php
session_start();
require('../functions.php');

$conn = dbConnect();
$produk_id = $_GET['id']; // ID produk dari URL
$pengguna_id = $_SESSION['pengguna_id']; // Asumsikan pengguna telah login

// Cek apakah ada parameter untuk menambah ke wishlist
if (isset($_GET['add_to_wishlist'])) {
    // Menambahkan produk ke wishlist
    $message = addToWishlist($conn, $produk_id, $pengguna_id);
    echo "<script>alert('$message');</script>";  // Menampilkan pesan untuk user
}

// Dapatkan detail produk untuk ditampilkan di halaman
$query = "SELECT * FROM Produk WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $produk_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

// Cek jika tombol "Add to Cart" ditekan
if (isset($_POST['add_to_cart'])) {
    $produk_id = intval($_POST['produk_id']); // Ambil ID produk dari input form
    $jumlah = intval($_POST['jumlah']); // Ambil jumlah dari input form
    $pengguna_id = $_SESSION['pengguna_id']; // Ambil ID pengguna yang sedang login

    // Validasi jumlah
    if ($jumlah < 1) {
        $cart_message = "Jumlah produk harus minimal 1.";
    } else {
        // Panggil fungsi untuk menambahkan produk ke keranjang
        $cart_message = addToCart($produk_id, $jumlah, $pengguna_id, $conn);
    }
}

// Cek jika tombol "Add Review" ditekan
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_review'])) {
    $rating = $_POST['rating'];
    $komentar = $_POST['komentar'];
    addProductReview($conn, $produk_id, $pengguna_id, $rating, $komentar);
    $review_message = "Ulasan Anda telah ditambahkan!";
}

$product = getProductDetails($conn, $produk_id);
$reviews = getProductReviews($conn, $produk_id);
$featuredProducts = getFeaturedProducts($conn); // Get featured products
$collectorProducts = getRandomCollectorProducts($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['nama']); ?> - Toko Hotwheels</title>
    <script src="https://kit.fontawesome.com/e7ea333cb2.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 text-gray-800">

<?php include 'layout/header.php'; ?>

<section id="prodetails" class="container mx-auto px-4 py-12">
    <div class="flex flex-col md:flex-row justify-between gap-12">
        <!-- Product Images -->
        <div class="w-full md:w-2/5">
            <div class="border rounded-lg overflow-hidden shadow-lg">
                <img src="../user/produk/<?php echo $product['gambar']; ?>" class="w-full" id="MainImg" alt="<?php echo htmlspecialchars($product['nama']); ?>">
            </div>
            <div class="small-img-group flex mt-4 gap-4 justify-center">
                <?php for ($i = 0; $i < 4; $i++): ?>
                <div class="small-img-col w-1/4">
                    <img src="../user/produk/<?php echo $product['gambar']; ?>" class="w-full rounded-lg cursor-pointer transition-transform duration-300 transform hover:scale-105" alt="Thumbnail">
                </div>
                <?php endfor; ?>
            </div>
        </div>

        <!-- Product Details -->
        <div class="w-full md:w-3/5">
            <h6 class="text-sm text-gray-500">Home / <?php echo htmlspecialchars($product['kategori']); ?></h6>
            <h4 class="text-3xl font-bold text-gray-900 mb-4"><?php echo htmlspecialchars($product['nama']); ?></h4>
            <h2 class="text-blue-600 text-4xl mb-4 font-semibold">Rp<?php echo number_format($product['harga']); ?></h2>
            <p class="text-lg mb-4">Stok: 
                <?php 
                    if (isset($product['stok']) && $product['stok'] > 0) {
                        echo $product['stok'] . " unit tersedia";
                    } else {
                        echo "Stok tidak tersedia";
                    }
                ?>
            </p>
            <form action="produk.php?id=<?php echo $produk_id; ?>" method="post" class="flex items-center space-x-4 mt-4">
                <input type="hidden" name="produk_id" value="<?php echo $produk_id; ?>">
                <input type="number" name="jumlah" value="1" min="1" max="<?php echo $product['stok']; ?>" required class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500">
                <button type="submit" class="bg-blue-400 text-white py-2 px-6 rounded-md hover:bg-blue-500 transition-colors" name="add_to_cart">Add To Cart</button>
            </form>
            <a href="produk.php?id=<?php echo $produk_id; ?>&add_to_wishlist=true" class="mt-4 inline-block text-white bg-blue-400 py-2 px-6 rounded-md hover:bg-blue-500 transition-colors">
                <i class="fas fa-heart"></i> Tambah ke Wishlist
            </a>
            <?php if (isset($cart_message)): ?>
                <p class="mt-2 text-green-600 font-medium"><?php echo $cart_message; ?></p>
            <?php endif; ?>

            <h4 class="mt-8 text-2xl font-bold">Product Details</h4>
            <p class="text-lg text-gray-700 leading-relaxed"><?php echo nl2br(htmlspecialchars($product['deskripsi'])); ?></p>
        </div>
    </div>
</section>

<section id="reviews" class="container mx-auto px-4 py-12">
    <h2 class="text-3xl font-bold mb-6 text-center">Ulasan Produk</h2>
    <div class="review-form mb-8">
        <form action="produk.php?id=<?php echo $produk_id; ?>" method="post" class="space-y-6 bg-white p-6 rounded-lg shadow-md">
            <div class="form-group">
                <label for="rating" class="block text-lg text-gray-700 font-semibold">Rating:</label>
                <select id="rating" name="rating" required class="block w-full px-4 py-2 border border-gray-300 rounded-md mt-2 focus:outline-none focus:ring-2 focus:ring-pink-500">
                    <option value="1">1 - Sangat Buruk</option>
                    <option value="2">2 - Buruk</option>
                    <option value="3">3 - Biasa Saja</option>
                    <option value="4">4 - Baik</option>
                    <option value="5">5 - Sangat Baik</option>
                </select>
            </div>
            <div class="form-group">
                <label for="komentar" class="block text-lg text-gray-700 font-semibold">Komentar:</label>
                <textarea id="komentar" name="komentar" rows="4" required class="block w-full px-4 py-2 border border-gray-300 rounded-md mt-2 focus:outline-none focus:ring-2 focus:ring-pink-500"></textarea>
            </div>
            <button type="submit" class="bg-pink-600 text-white py-2 px-6 rounded-md hover:bg-pink-700 transition-colors" name="add_review">Submit Review</button>
        </form>
        <?php if (isset($review_message)): ?>
            <p class="mt-2 text-green-600 font-medium text-center"><?php echo $review_message; ?></p>
        <?php endif; ?>
    </div>
    <div class="review-list space-y-6">
        <?php while ($review = $reviews->fetch_assoc()): ?>
            <div class="review-item bg-white p-6 rounded-lg shadow-md">
                <h4 class="text-xl font-semibold text-gray-800"><?php echo htmlspecialchars($review['nama']); ?></h4>
                <p class="text-lg text-gray-600">Rating: <?php echo $review['rating']; ?>/5</p>
                <p class="text-lg text-gray-700 leading-relaxed"><?php echo htmlspecialchars($review['komentar']); ?></p>
                <p class="text-sm text-gray-500"><small><?php echo htmlspecialchars($review['tanggal']); ?></small></p>
            </div>
        <?php endwhile; ?>
    </div>
</section>

<section id="product1" class="container mx-auto px-4 py-12">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-8 text-blue-600 transition-colors duration-300 hover:text-blue-800">Hot Wheels Collector</h2>
        <div class="row g-4">
            <?php if ($collectorProducts->num_rows > 0): ?>
                <?php while ($row = $collectorProducts->fetch_assoc()): ?>
                    <div class="col-md-3">
                        <div class="bg-white rounded-lg shadow hover:shadow-xl transition-shadow duration-300 overflow-hidden cursor-pointer" onclick="window.location.href='produk.php?id=<?php echo $row['id']; ?>'">
                            <img src="../user/produk/<?php echo $row['gambar']; ?>" alt="<?php echo htmlspecialchars($row['nama']); ?>" class="w-full h-48 object-cover hover:opacity-90">
                        <div class="p-4 text-center">
                           <span class="text-sm text-gray-500 block transition-colors duration-300 hover:text-gray-700"><?php echo htmlspecialchars($row['kategori']); ?></span>
                           <h5 class="font-semibold mt-2 text-lg text-gray-800 transition-transform duration-300 hover:scale-105"><?php echo htmlspecialchars($row['nama']); ?></h5>
                            <div class="flex justify-center mt-2 text-yellow-400">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                                <h4 class="text-lg font-bold text-gray-800 mt-4">Rp<?php echo number_format($row['harga']); ?></h4>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-center text-gray-500">Tidak ada produk dalam kategori Collector.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include 'layout/footer.php'; ?>

<script src="../assets/js/script.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
