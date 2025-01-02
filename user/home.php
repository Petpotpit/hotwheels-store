<!DOCTYPE html>
<html lang="en">
<?php
session_start();
require('../functions.php');

$title = "Home - Toko Hotwheels";
require('layout/header.php');

$conn = dbConnect(); // ID produk dari URL
$pengguna_id = $_SESSION['pengguna_id']; // ID pengguna dari session
// Ambil produk kategori "Collector"
$collectorProducts = getRandomCollectorProducts($conn);

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


function convertYoutubeUrlToEmbed($url) {
    preg_match('/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $url, $matches);
    return isset($matches[1]) ? "https://www.youtube.com/embed/" . $matches[1] : null;
}

// URL Video dari Database atau Input Dinamis
$videoUrl = "https://www.youtube.com/embed/PlpvJfhac6I"; // Contoh URL
$embedUrl = convertYoutubeUrlToEmbed($videoUrl);

?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Toko Hotwheels</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <!-- Hero Section -->
    <section id="hero" class="relative bg-cover bg-center h-screen flex items-center justify-center " style="background-image: url('../image/banner.png');">
        <a href="https://shop.mattel.com/collections/hot-wheels-top-holiday-toys" class="absolute inset-0"></a>
    </section>

    <!-- Collector Products Section -->
    <section id="collector-products" class="py-12">
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

    <!-- Promo Video Section -->
    <section id="promo-video" class="py-12 bg-gray-50 border-t-4 border-green-500 border-b-4 border-blue-500">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold mb-6 text-purple-700 transition-transform duration-300 hover:scale-105">Check Out Our Latest Video!</h2>
            <div class="relative w-full max-w-3xl mx-auto rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                <iframe 
                    src="https://www.youtube.com/embed/PlpvJfhac6I" 
                    frameborder="0" 
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                    allowfullscreen 
                    class="w-full h-64 sm:h-96">
                </iframe>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="feature" class="py-12">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-8 text-indigo-600 transition-colors duration-300 hover:text-indigo-800">Keunggulan Toko Hotwheels</h2>
            <div class="flex flex-wrap justify-center gap-6">
                <div class="bg-white shadow-lg rounded-lg p-6 text-center w-60 hover:scale-105 transition-transform duration-300">
                    <i class="fas fa-shipping-fast text-4xl text-green-500 mb-4"></i>
                    <h6 class="font-semibold">Free Shipping</h6>
                </div>
                <div class="bg-white shadow-lg rounded-lg p-6 text-center w-60 hover:scale-105 transition-transform duration-300">
                    <i class="fas fa-lock text-4xl text-blue-500 mb-4"></i>
                    <h6 class="font-semibold">Security & Trust</h6>
                </div>
                <div class="bg-white shadow-lg rounded-lg p-6 text-center w-60 hover:scale-105 transition-transform duration-300">
                    <i class="fas fa-tag text-4xl text-red-500 mb-4"></i>
                    <h6 class="font-semibold">Discount & Promotions</h6>
                </div>
                <div class="bg-white shadow-lg rounded-lg p-6 text-center w-60 hover:scale-105 transition-transform duration-300">
                    <i class="fas fa-headset text-4xl text-orange-500 mb-4"></i>
                    <h6 class="font-semibold">Customer Service</h6>
                </div>
            </div>
        </div>
    </section>

    <?php require('layout/footer.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

    <!-- Section untuk Testimonial Pelanggan -->
    <!-- <section id="testimonials" class="section-p1">
        <div class="container">
            <h2>What Our Customers Say</h2>
            <div class="testimonial-container">
                <?php 
                // while ($row = $testimonials->fetch_assoc()): 
                ?>
                    <div class="testimonial">
                        <p><?php 
                        // echo htmlspecialchars($row['komentar']); 
                        ?></p>
                        <h5><?php 
                        // echo htmlspecialchars($row['pengguna_nama']); 
                        ?></h5>
                    </div>
                <?php 
            // endwhile; 
            ?>
            </div>
        </div>
    </section> -->