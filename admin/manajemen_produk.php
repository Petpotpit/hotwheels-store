<?php
include '../functions.php';

// Penambahan Produk
if (isset($_POST['add_product'])) {
    $conn = dbConnect();
    $nama = $_POST['nama'];
    $deskripsi = $_POST['deskripsi'];
    $harga = $_POST['harga'];
    $kategori = $_POST['kategori'];
    $stok = $_POST['stok'];
    $stok_minimum = $_POST['stok_minimum'];
    $diskon = $_POST['diskon'] ?: 0; 


    // Penanganan unggahan gambar
// Penanganan unggahan gambar
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../user/produk/'; // Path tujuan folder
        $gambarName = basename($_FILES['gambar']['name']); // Hanya nama file
        $uploadFile = $uploadDir . $gambarName; // Path lengkap untuk pemindahan

        // Validasi direktori tujuan
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true); // Buat direktori jika belum ada
        }

        // Pindahkan file ke folder
        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $uploadFile)) {
            $stmt = $conn->prepare("INSERT INTO Produk (nama, deskripsi, harga, gambar, kategori, stok, stok_minimum, diskon) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssdssiid", $nama, $deskripsi, $harga, $gambarName, $kategori, $stok, $stok_minimum, $diskon); // Simpan hanya nama file
            if ($stmt->execute()) {
                $message = "Produk berhasil ditambahkan.";
            } else {
                $message = "Error: " . $stmt->error;
            }
        } else {
            $message = "Gagal mengunggah gambar. Pastikan folder tujuan memiliki izin tulis.";
        }
    } else {
        $message = "Tidak ada gambar yang diunggah atau terjadi kesalahan.";
    }

    $stmt->close();
    $conn->close();
}

// Pengeditan Produk
if (isset($_POST['edit_product'])) {
    $conn = dbConnect();
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $deskripsi = $_POST['deskripsi'];
    $harga = $_POST['harga'];
    $kategori = $_POST['kategori'];  
    $stok = $_POST['stok'];
    $stok_minimum = $_POST['stok_minimum'];
    $diskon = $_POST['diskon'];

    $gambarUpdate = "";
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../user/produk/';
        $gambarName = basename($_FILES['gambar']['name']);
        $uploadFile = $uploadDir . $gambarName;

        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $uploadFile)) {
            $gambarUpdate = ", gambar = '$uploadFile'";
        }
    }

    $stmt = $conn->prepare("UPDATE Produk SET nama = ?, deskripsi = ?, harga = ?, kategori = ?, stok = ?, stok_minimum = ?, diskon = ? $gambarUpdate WHERE id = ?");
    $stmt->bind_param("ssdsiiii", $nama, $deskripsi, $harga, $kategori, $stok, $stok_minimum, $diskon, $id);
    if ($stmt->execute()) {
        $message = "Produk berhasil diperbarui.";
    } else {
        $message = "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}

// Penghapusan Produk
if (isset($_GET['delete_id'])) {
    $conn = dbConnect();
    $id = $_GET['delete_id'];

    $stmt = $conn->prepare("DELETE FROM Produk WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $message = "Produk berhasil dihapus.";
    } else {
        $message = "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}

// Ambil daftar produk
$conn = dbConnect();
$result = $conn->query("SELECT * FROM Produk ORDER BY kategori, nama");
$produkList = $result->fetch_all(MYSQLI_ASSOC);
$conn->close();

// Ambil Daftar Kategori
$conn = dbConnect();
$sql = "SELECT DISTINCT kategori FROM Produk WHERE kategori IS NOT NULL";
$result = $conn->query($sql);
$kategoris = [];
while ($row = $result->fetch_assoc()) {
    $kategoris[] = $row['kategori'];
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Produk</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.1.2/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php require('layout/sidebar.php') ?>
    <div class="container mt-5">
        <h2 class="text-xl font-semibold mb-4">Manajemen Produk</h2>
        <?php if (isset($message)): ?>
            <div class="alert alert-info"><?= $message; ?></div>
        <?php endif; ?>
        
        <!-- Filter Kategori -->
        <div class="mb-4">
            <label for="filterKategori" class="form-label">Filter Berdasarkan Kategori:</label>
            <select id="filterKategori" class="form-control" onchange="filterByCategory()">
                <option value="all">Semua</option>
                <?php foreach ($kategoris as $kategori): ?>
                    <option value="<?= $kategori; ?>"><?= $kategori; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Daftar Produk -->
        <h3 class="text-lg font-semibold">Daftar Produk</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Deskripsi</th>
                    <th>Harga</th>
                    <th>Kategori</th>
                    <th>Stok</th>
                    <th>Stok Minimum</th>
                    <th>Diskon</th>
                    <th>Gambar</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="productTable">
                <?php foreach ($produkList as $produk): ?>
                    <tr data-category="<?= $produk['kategori']; ?>">
                        <td><?= $produk['nama']; ?></td>
                        <td><?= $produk['deskripsi']; ?></td>
                        <td><?= $produk['harga']; ?></td>
                        <td><?= $produk['kategori']; ?></td>
                        <td><?= $produk['stok']; ?></td>
                        <td><?= $produk['stok_minimum']; ?></td>
                        <td><?= $produk['diskon']; ?>%</td>
                        <td><img src="../user/produk/<?= $produk['gambar']; ?>" alt="Gambar Produk" width="50"></td>
                        <td>
                            <a href="manajemen_produk.php?edit_id=<?= $produk['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                            <a href="manajemen_produk.php?delete_id=<?= $produk['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus produk ini?')">Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Form Tambah/Edit Produk -->
        <h3 class="text-lg font-semibold mt-4"><?= isset($produk['id']) ? 'Edit Produk' : 'Tambah Produk'; ?></h3>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= isset($produk['id']) ? $produk['id'] : ''; ?>">
            <div class="form-group">
                <label for="nama">Nama Produk</label>
                <input type="text" class="form-control" id="nama" name="nama" value="<?= isset($produk['nama']) ? $produk['nama'] : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="deskripsi">Deskripsi Produk</label>
                <textarea class="form-control" id="deskripsi" name="deskripsi" required><?= isset($produk['deskripsi']) ? $produk['deskripsi'] : ''; ?></textarea>
            </div>
            <div class="form-group">
                <label for="harga">Harga Produk</label>
                <input type="number" class="form-control" id="harga" name="harga" value="<?= isset($produk['harga']) ? $produk['harga'] : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="kategori">Kategori Produk</label>
                <select class="form-control" id="kategori" name="kategori" required>
                    <option value="Super Car" <?= isset($produk['kategori']) && $produk['kategori'] == 'Super Car' ? 'selected' : ''; ?>>Super Car</option>
                    <option value="Monster Truk" <?= isset($produk['kategori']) && $produk['kategori'] == 'Monster Truk' ? 'selected' : ''; ?>>Monster Truk</option>
                    <option value="Collector" <?= isset($produk['kategori']) && $produk['kategori'] == 'Collector' ? 'selected' : ''; ?>>Collector</option>
                </select>
            </div>
            <div class="form-group">
                <label for="stok">Stok Produk</label>
                <input type="number" class="form-control" id="stok" name="stok" value="<?= isset($produk['stok']) ? $produk['stok'] : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="stok_minimum">Stok Minimum</label>
                <input type="number" class="form-control" id="stok_minimum" name="stok_minimum" value="<?= isset($produk['stok_minimum']) ? $produk['stok_minimum'] : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="diskon">Diskon (%)</label>
                <input type="number" class="form-control" id="diskon" name="diskon" value="<?= isset($produk['diskon']) ? $produk['diskon'] : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="gambar">Gambar Produk</label>
                <input type="file" class="form-control-file" id="gambar" name="gambar">
            </div>
            <button type="submit" class="btn btn-success" name="<?= isset($produk['id']) ? 'edit_product' : 'add_product'; ?>"><?= isset($produk['id']) ? 'Simpan Perubahan' : 'Tambah Produk'; ?></button>
        </form>
    </div>

    <script>
        function filterByCategory() {
            var category = document.getElementById('filterKategori').value;
            var rows = document.querySelectorAll('#productTable tr');
            rows.forEach(function(row) {
                if (category == 'all' || row.getAttribute('data-category') == category) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>
