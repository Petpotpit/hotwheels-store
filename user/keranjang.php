<?php
session_start();
require '../functions.php';

$pengguna_id = $_SESSION['pengguna_id'];

// Proses hapus produk dari keranjang
if (isset($_POST['hapus'])) {
  $keranjang_id = $_POST['hapus']; // Ambil ID keranjang dari form
  deleteProductFromCart($keranjang_id, $pengguna_id); // Hapus produk dari keranjang
}
// Proses menambah/mengurangi kuantitas
if (isset($_POST['kurangi'])) {
  $keranjang_id = $_POST['kurangi'];
  $jumlah_sekarang = intval($_POST['jumlah_sekarang']);
  updateQuantity($keranjang_id, $pengguna_id, $jumlah_sekarang - 1);
}
if (isset($_POST['tambah'])) {
  $keranjang_id = $_POST['tambah'];
  $jumlah_sekarang = intval($_POST['jumlah_sekarang']);
  updateQuantity($keranjang_id, $pengguna_id, $jumlah_sekarang + 1);
}

// Proses menggunakan voucher
$total_belanja = hitungTotalKeranjang($pengguna_id);
$diskon_voucher = 0;
if (isset($_POST['apply_voucher'])) {
    $kode_voucher = $_POST['kode_voucher'];
    $diskon_voucher = applyVoucher($kode_voucher, $pengguna_id); // Mengembalikan nilai diskon
    if ($diskon_voucher > 0) {
        $total_belanja -= $diskon_voucher;
    }
    if ($total_belanja < 0) {
        $total_belanja = 0; // Tidak ada total negatif
    }
}

// Variabel untuk menampilkan error jika keranjang kosong
$checkout_error = null;

if (isset($_POST['checkout'])) {
  // Cek apakah keranjang kosong sebelum melanjutkan ke checkout
  $keranjang = getKeranjang($pengguna_id);
  if (is_array($keranjang) && count($keranjang) > 0) {
      header("Location: checkout.php");
      exit();
  } else {
      $checkout_error = "Keranjang kosong, tidak bisa melakukan checkout.";
  }
}


// Data keranjang
$keranjang = getKeranjang($pengguna_id);
// Cek apakah keranjang adalah array dan memiliki item
if (is_array($keranjang) && count($keranjang) > 0) {
    foreach ($keranjang as $item) {
        // Menampilkan item keranjang
    }
} else {
    // Menampilkan pesan jika keranjang kosong
    echo "<p ></p>";
}

require ('layout/header.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Keranjang Belanja</title>
  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 px-4">

  <div class="container mx-auto py-5">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Keranjang Belanja -->
      <div class="col-span-2">
        <div class="bg-white p-6 rounded-lg shadow-lg">
          <h5 class="text-2xl font-bold mb-6">Keranjang Belanja</h5>
          <table class="min-w-full table-auto border-collapse">
            <thead class="bg-gray-100">
              <tr>
                <th class="py-2 px-4 text-left">Produk</th>
                <th class="py-2 px-4 text-left">Harga</th>
                <th class="py-2 px-4 text-left">Jumlah</th>
                <th class="py-2 px-4 text-left">Hapus</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($keranjang as $item): ?>
              <tr class="border-t">
                <td class="py-4 px-4">
                  <p class="font-semibold"><?php echo $item['nama']; ?></p>
                </td>
                <td class="py-4 px-4">
                  <p class="font-bold">Rp <?php echo number_format($item['harga']); ?></p>
                </td>
                <td class="py-4 px-4">
                  <form method="POST" class="flex items-center space-x-2">
                    <input type="hidden" name="jumlah_sekarang" value="<?php echo $item['jumlah']; ?>">
                    <button class="bg-gray-300 hover:bg-gray-400 text-xl text-gray-600 py-1 px-3 rounded-full" name="kurangi" value="<?php echo $item['id']; ?>">-</button>
                    <input type="text" class="text-center py-1 px-4 border border-gray-300 rounded" value="<?php echo $item['jumlah']; ?>" readonly>
                    <button class="bg-gray-300 hover:bg-gray-400 text-xl text-gray-600 py-1 px-3 rounded-full" name="tambah" value="<?php echo $item['id']; ?>">+</button>
                  </form>
                </td>
                <td class="py-4 px-4">
                  <form method="POST">
                    <button class="bg-red-500 hover:bg-red-600 text-white py-1 px-3 rounded-lg" name="hapus" value="<?php echo $item['id']; ?>">Hapus</button>
                  </form>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>

          <div class="flex justify-between border-t pt-3 mt-3">
            <p class="font-semibold">Subtotal Untuk Produk:</p>
            <p class="font-bold">Rp <?php echo number_format(hitungTotalKeranjang($pengguna_id), 0, ',', '.'); ?></p>
          </div>

          <div class="flex justify-between text-green-600 border-t pt-3 mt-3">
            <p class="font-semibold">Diskon Voucher:</p>
            <p class="font-bold">-Rp <?php echo number_format($diskon_voucher); ?></p>
          </div>

          <div class="flex justify-between text-red-600 border-t pt-3 mt-3 text-xl font-bold">
              <p>Total Pembayaran:</p>
              <p>Rp <?php echo number_format($total_belanja); // Tambah biaya pengiriman ?></p>
          </div>
          <p class="text-sm text-gray-500 mt-1">*Belum termasuk biaya pengiriman</p>
        </div>
      </div>

      <!-- Info Pembayaran -->

      <div class="container mx-auto p-6">
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h5 class="text-2xl font-semibold mb-6">Info Pembayaran</h5>
            
            <form id="checkoutForm" method="POST">
                <div class="mb-4">
                    <label for="voucher" class="block text-sm font-medium text-gray-700">Voucher Belanja</label>
                    <div class="flex items-center space-x-2">
                        <input type="text" id="voucher" name="kode_voucher" class="flex-1 border border-gray-300 p-2 rounded-l-md" placeholder="Masukkan Kode Voucher">
                        <button type="submit" name="apply_voucher" class="bg-pink-500 hover:bg-pink-600 text-white px-4 py-2 rounded-r-md">Pakai</button>
                    </div>
                </div>

                <button 
                    type="submit" 
                    name="checkout" 
                    id="checkoutButton"
                    class="w-full py-3 bg-green-500 hover:bg-green-600 text-white rounded-md text-lg">
                    Checkout
                </button>
            </form>
        </div>
    </div>

    <!-- Modal Notifikasi -->
    <div id="notifModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-96">
            <h2 class="text-lg font-bold mb-4">Pemberitahuan</h2>
            <p id="notifMessage"></p>
            <div class="mt-4 flex justify-end">
                <button id="closeModal" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md">Tutup</button>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const checkoutError = <?php echo json_encode($checkout_error); ?>;
        const notifModal = document.getElementById('notifModal');
        const notifMessage = document.getElementById('notifMessage');
        const closeModal = document.getElementById('closeModal');

        if (checkoutError) {
            notifMessage.textContent = checkoutError;
            notifModal.classList.remove('hidden');
        }

        closeModal.addEventListener('click', function () {
            notifModal.classList.add('hidden');
        });
    });
    </script>
    </div>
  </div>
</body>
</html>

