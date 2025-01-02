<?php
require '../functions.php'; // Mengimpor file koneksi database dan fungsi lain
$conn = dbConnect(); // Menghubungkan ke database

// Menangani penambahan atau pengurangan stok
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $produk_id = $_POST['produk_id'];
    $aksi = $_POST['aksi']; // "tambah" atau "kurangi"
    $jumlah = $_POST['jumlah'];
    $admin_id = 1; // ID admin, ubah sesuai session login

    if ($aksi === 'tambah') {
        $sql_update = "UPDATE Produk SET stok = stok + ? WHERE id = ?";
        $sql_insert = "INSERT INTO ManajemenStok (produk_id, admin_id, jumlah_perubahan, aksi, tanggal) VALUES (?, ?, ?, 'stok masuk', NOW())";
    } elseif ($aksi === 'kurangi') {
        $sql_update = "UPDATE Produk SET stok = stok - ? WHERE id = ?";
        $sql_insert = "INSERT INTO ManajemenStok (produk_id, admin_id, jumlah_perubahan, aksi, tanggal) VALUES (?, ?, ?, 'stok keluar', NOW())";
    }

    // Perbarui stok di tabel Produk
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("ii", $jumlah, $produk_id);
    $stmt_update->execute();

    // Catat perubahan stok di tabel ManajemenStok
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("iii", $produk_id, $admin_id, $jumlah);
    $stmt_insert->execute();
}

// Pencarian Produk
$search_query = '';
if (isset($_GET['search'])) {
    $search_query = $_GET['search'];
    $sql_produk = "SELECT * FROM Produk WHERE nama LIKE ? OR kategori LIKE ?";
    $stmt = $conn->prepare($sql_produk);
    $search_param = "%$search_query%";
    $stmt->bind_param('ss', $search_param, $search_param);
} else {
    $sql_produk = "SELECT * FROM Produk";
    $stmt = $conn->prepare($sql_produk);
}

$stmt->execute();
$result_produk = $stmt->get_result();

// Filter berdasarkan kategori dan status stok
$kategori_filter = '';
if (isset($_GET['kategori'])) {
    $kategori_filter = $_GET['kategori'];
    $sql_produk = "SELECT * FROM Produk WHERE kategori = ?";
    $stmt = $conn->prepare($sql_produk);
    $stmt->bind_param('s', $kategori_filter);
}

$stmt->execute();
$result_produk = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Stok Produk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <?php require('layout/sidebar.php') ?>
    <div class="container mt-5">
        <h1 class="text-center text-2xl font-bold mb-4">Manajemen Stok Produk</h1>

        <!-- Form Pencarian -->
        <form class="mb-4" method="GET" action="manajemen_stok.php">
            <div class="flex items-center">
                <input type="text" name="search" placeholder="Cari produk..." value="<?= htmlspecialchars($search_query) ?>" class="form-control mr-2">
                <button type="submit" class="btn btn-primary">Cari</button>
            </div>
        </form>

        <!-- Filter Kategori -->
        <div class="mb-4">
            <form method="GET" action="manajemen_stok.php">
                <label for="kategori" class="form-label">Filter Kategori</label>
                <select name="kategori" id="kategori" class="form-select">
                    <option value="">Semua Kategori</option>
                    <option value="Super Car" <?= $kategori_filter == 'Super Car' ? 'selected' : '' ?>>Super Car</option>
                    <option value="Monster Truk" <?= $kategori_filter == 'Monster Truk' ? 'selected' : '' ?>>Monster Truk</option>
                    <option value="Collector" <?= $kategori_filter == 'Collector' ? 'selected' : '' ?>>Collector</option>
                </select>
                <button type="submit" class="btn btn-primary mt-2">Filter</button>
            </form>
        </div>

        <!-- Tabel Produk -->
        <table class="table table-striped">
            <thead class="bg-blue-500 text-white">
                <tr>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Stok Saat Ini</th>
                    <th>Stok Minimum</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result_produk->fetch_assoc()) { ?>
                <tr class="<?= $row['stok'] <= $row['stok_minimum'] ? 'bg-warning' : '' ?>">
                    <td><?= htmlspecialchars($row['nama']) ?></td>
                    <td><?= htmlspecialchars($row['kategori']) ?></td>
                    <td><?= htmlspecialchars($row['harga']) ?></td>
                    <td><?= htmlspecialchars($row['stok']) ?></td>
                    <td><?= htmlspecialchars($row['stok_minimum']) ?></td>
                    <td>
                        <!-- Form Tambah Stok -->
                        <form action="manajemen_stok.php" method="POST" class="d-inline">
                            <input type="hidden" name="produk_id" value="<?= $row['id'] ?>">
                            <input type="hidden" name="aksi" value="tambah">
                            <input type="number" name="jumlah" placeholder="Jumlah" class="form-control mb-2" required>
                            <button type="submit" class="btn btn-success btn-sm">Tambah Stok</button>
                        </form>
                        
                        <!-- Form Kurangi Stok -->
                        <form action="manajemen_stok.php" method="POST" class="d-inline">
                            <input type="hidden" name="produk_id" value="<?= $row['id'] ?>">
                            <input type="hidden" name="aksi" value="kurangi">
                            <input type="number" name="jumlah" placeholder="Jumlah" class="form-control mb-2" required>
                            <button type="submit" class="btn btn-danger btn-sm">Kurangi Stok</button>
                        </form>

                        <!-- Notifikasi jika Stok Minimum Tercapai -->
                        <?php if ($row['stok'] <= $row['stok_minimum']): ?>
                            <div class="text-danger mt-2">
                                <strong>Perhatian:</strong> Stok produk ini telah mencapai batas minimum!
                            </div>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

        <!-- Laporan Stok -->
        <div class="mt-5">
            <h3>Laporan Stok</h3>
            <form method="GET" action="laporan_stok.php">
                <label for="tanggal" class="form-label">Pilih Periode</label>
                <input type="date" name="tanggal" class="form-control mb-2">
                <button type="submit" class="btn btn-secondary">Lihat Laporan</button>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>
