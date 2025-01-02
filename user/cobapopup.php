<?php
require '../functions.php';
// Ambil data kupon berdasarkan ID atau dapatkan semua kupon
$kupons = getAllKupons();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Kupon</title>
    <!-- Menggunakan Tailwind CSS untuk styling -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- Menggunakan Bootstrap untuk tambahan styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        // Fungsi untuk menyalin kode kupon ke clipboard
        function copyKuponCode(code) {
            const el = document.createElement('textarea');
            el.value = code;
            document.body.appendChild(el);
            el.select();
            document.execCommand('copy');
            document.body.removeChild(el);
            alert('Kode kupon berhasil disalin: ' + code);
        }

        // Fungsi untuk menunjukkan karakter maskot dan memberikan hadiah virtual
        function showMascot() {
            alert("Hai! Saya adalah Maskot Kupon! Gunakan kupon dan dapatkan hadiah seperti stiker atau gambar digital!");
        }
    </script>
</head>
<body class="bg-gray-50">

    <!-- Header dan Navigasi -->
    <header class="bg-indigo-600 text-white py-4 mb-6">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-3xl font-bold">Daftar Kupon</h1>
            <button onclick="showMascot()" class="bg-yellow-500 px-4 py-2 rounded-full text-white hover:bg-yellow-600">
                🎉 Temui Maskot
            </button>
        </div>
    </header>

    <!-- Bagian Utama -->
    <div class="container mx-auto p-8">
        <h2 class="text-2xl font-semibold text-gray-800 mb-6">Gunakan Kupon Anda dan Dapatkan Hadiah Menarik!</h2>

        <!-- Grid untuk Menampilkan Kupon -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($kupons as $kupon): ?>
                <div class="bg-white p-6 rounded-lg shadow-lg border border-gray-200 transition-all transform hover:scale-105 hover:shadow-xl">
                    <h3 class="text-xl font-semibold text-indigo-600 mb-3"><?php echo $kupon['kode']; ?></h3>
                    <p class="text-gray-600 mb-4"><strong>Deskripsi:</strong> <?php echo $kupon['deskripsi']; ?></p>
                    <p class="text-gray-700 mb-4"><strong>Diskon:</strong> <span class="text-green-600"><?php echo $kupon['diskon']; ?>%</span></p>
                    <p class="text-gray-500 mb-6"><strong>Periode:</strong> <?php echo date('d M Y', strtotime($kupon['tanggal_berlaku'])); ?> hingga <?php echo date('d M Y', strtotime($kupon['tanggal_kadaluarsa'])); ?></p>
                    <div class="flex items-center justify-between">
                        <!-- Tombol Salin Kode -->
                        <button onclick="copyKuponCode('<?php echo $kupon['kode']; ?>')" class="bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600 transition duration-300 ease-in-out">
                            Salin Kode Kupon
                        </button>
                        <!-- Ikon Keranjang untuk Menunjukkan Penukaran -->
                        <button class="bg-green-500 text-white py-2 px-4 rounded-md hover:bg-green-600 transition duration-300 ease-in-out">
                            🛒 Tukar Kupon
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Bagian Poin dan Hadiah -->
    <div class="bg-yellow-100 py-6 mt-8">
        <div class="container mx-auto text-center">
            <h3 class="text-2xl font-semibold text-gray-800">Kumpulkan Poin dan Tukarkan dengan Hadiah!</h3>
            <p class="text-gray-600">Dapatkan poin setiap kali menggunakan kupon dan tukarkan dengan stiker digital atau akses ke mini games eksklusif!</p>
            <div class="mt-4">
                <button class="bg-blue-500 text-white py-3 px-6 rounded-full hover:bg-blue-600">
                    Cek Poin Saya
                </button>
            </div>
        </div>
    </div>

</body>
</html>
