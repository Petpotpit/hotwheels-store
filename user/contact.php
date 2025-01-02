<!DOCTYPE html>
<html lang="en">
<?php
session_start();
require('../functions.php');

$title = "Contact Us - Toko Hotwheels";
require('layout/header.php');

$conn = dbConnect();

// Fungsi untuk menyimpan pesan kontak
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $subjek = $_POST['subjek'];
    $pesan = $_POST['pesan'];

    $query = "INSERT INTO PesanKontak (nama, email, subjek, pesan) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssss", $nama, $email, $subjek, $pesan);
    if ($stmt->execute()) {
        $success_message = "Pesan Anda telah terkirim.";
    } else {
        $error_message = "Terjadi kesalahan saat mengirim pesan Anda. Silakan coba lagi.";
    }
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Toko Hotwheels</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

    <!-- Hero Section -->
    <section id="page-header" class="text-center bg-cover bg-center py-20 text-white shadow-lg" style="background-image: url('../image/banner.png');">
        <div class="container mx-auto">
            <h1 class="text-5xl font-bold uppercase tracking-widest text-blue-400 ">#let's talk</h1>
            <p class="text-xl font-light mt-4 text-blue-400">LEAVE A MESSAGE. We love to hear from you!</p>
        </div>
    </section>

    <!-- Contact Details Section -->
    <section id="contact-details" class="py-12 bg-white shadow-md rounded-lg">
        <div class="container mx-auto text-center">
            <div class="row g-4">
                <div class="col-md-3">
                    <h4 class="text-xl font-bold">Address</h4>
                    <p class="text-gray-600">123 Hotwheels St, Toy City</p>
                </div>
                <div class="col-md-3">
                    <h4 class="text-xl font-bold">Email</h4>
                    <p class="text-gray-600">contact@hotwheelsstore.com</p>
                </div>
                <div class="col-md-3">
                    <h4 class="text-xl font-bold">Phone</h4>
                    <p class="text-gray-600">+1 234 567 890</p>
                </div>
                <div class="col-md-3">
                    <h4 class="text-xl font-bold">Hours</h4>
                    <p class="text-gray-600">Mon - Fri: 9:00 - 17:00</p>
                </div>
            </div>
            <div id="map" class="w-full h-96 mt-8 rounded-lg shadow-lg"></div>
        </div>
    </section>

    <!-- Contact Form Section -->
    <section id="form-details" class="py-12">
        <div class="container mx-auto px-4">
            <h2 class="text-4xl font-bold text-center mb-8">Contact Us</h2>
            <?php if (isset($success_message)): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php elseif (isset($error_message)): ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php endif; ?>
            <form action="contact.php" method="post" class="bg-white shadow-md rounded-lg p-8">
                <div class="mb-4">
                    <label for="nama" class="block text-lg font-semibold mb-2">Name</label>
                    <input type="text" class="form-control block w-full border-gray-300 rounded-md p-3 text-lg" id="nama" name="nama" required>
                </div>
                <div class="mb-4">
                    <label for="email" class="block text-lg font-semibold mb-2">Email</label>
                    <input type="email" class="form-control block w-full border-gray-300 rounded-md p-3 text-lg" id="email" name="email" required>
                </div>
                <div class="mb-4">
                    <label for="subjek" class="block text-lg font-semibold mb-2">Subject</label>
                    <input type="text" class="form-control block w-full border-gray-300 rounded-md p-3 text-lg" id="subjek" name="subjek" required>
                </div>
                <div class="mb-4">
                    <label for="pesan" class="block text-lg font-semibold mb-2">Message</label>
                    <textarea class="form-control block w-full border-gray-300 rounded-md p-3 text-lg" id="pesan" name="pesan" rows="5" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary w-full text-lg py-3">Send Message</button>
            </form>
        </div>
    </section>

    <?php require('layout/footer.php'); ?>

    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&callback=initMap" async defer></script>
    <script>
        function initMap() {
            var location = {lat: -34.397, lng: 150.644};
            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 8,
                center: location
            });
            var marker = new google.maps.Marker({
                position: location,
                map: map
            });
        }
    </script>
</body>
</html>
