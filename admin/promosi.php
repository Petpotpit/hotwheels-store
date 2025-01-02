<?php
require '../functions.php';

// $conn = dbConnect(); // Fungsi ini diasumsikan ada di file functions.php untuk koneksi database
$message = null;
$edit_kupon = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['tambah_kupon'])) {
        $message = tambahKupon($_POST);
    } elseif (isset($_POST['edit_kupon'])) {
        $message = editKupon($_POST);
    }
}

if (isset($_GET['hapus_id'])) {
    $message = hapusKupon($_GET['hapus_id']);
}

if (isset($_GET['edit_id'])) {
    $edit_kupon = ambilKuponById($_GET['edit_id']);
}

$kupons = ambilKupons();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promosi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <?php require('layout/sidebar.php') ?>
<div class="container my-5">
    <h1 class="text-2xl font-bold text-center mb-5">Manajemen Kode Kupon</h1>

    <?php if ($message): ?>
    <div class="alert alert-info">
        <?= htmlspecialchars($message); ?>
    </div>
<?php endif; ?>


    <!-- Form Tambah atau Edit Kupon -->
    <div class="card shadow mb-5">
        <div class="card-header bg-primary text-white">
            <?= $edit_kupon ? "Edit Kode Kupon: " . htmlspecialchars($edit_kupon['kode']) : "Tambah Kode Kupon Baru"; ?>
        </div>
        <div class="card-body">
            <form method="POST" action="promosi.php">
                <input type="hidden" name="<?= $edit_kupon ? 'edit_kupon' : 'tambah_kupon'; ?>" value="1">
                <?php if ($edit_kupon): ?>
                    <input type="hidden" name="id" value="<?= $edit_kupon['id']; ?>">
                <?php endif; ?>
                <div class="mb-3">
                    <label for="kode" class="form-label">Kode Kupon</label>
                    <input type="text" id="kode" name="kode" class="form-control"
                           value="<?= htmlspecialchars($edit_kupon['kode'] ?? '') ?>" placeholder="Masukkan kode kupon"
                           required>
                </div>
                <div class="mb-3">
                    <label for="deskripsi" class="form-label">Deskripsi</label>
                    <textarea id="deskripsi" name="deskripsi" class="form-control" rows="3"
                              placeholder="Masukkan deskripsi kupon"><?= htmlspecialchars($edit_kupon['deskripsi'] ?? '') ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="diskon" class="form-label">Diskon (%)</label>
                    <input type="number" id="diskon" name="diskon" class="form-control" step="0.01" min="0" max="100"
                           value="<?= htmlspecialchars($edit_kupon['diskon'] ?? '') ?>" required>
                </div>
                <div class="mb-3">
                    <label for="tanggal_berlaku" class="form-label">Tanggal Mulai</label>
                    <input type="datetime-local" id="tanggal_berlaku" name="tanggal_berlaku" class="form-control"
                           value="<?= $edit_kupon ? date('Y-m-d\TH:i', strtotime($edit_kupon['tanggal_berlaku'])) : '' ?>"
                           required>
                </div>
                <div class="mb-3">
                    <label for="tanggal_kadaluarsa" class="form-label">Tanggal Kadaluarsa</label>
                    <input type="datetime-local" id="tanggal_kadaluarsa" name="tanggal_kadaluarsa" class="form-control"
                           value="<?= $edit_kupon ? date('Y-m-d\TH:i', strtotime($edit_kupon['tanggal_kadaluarsa'])) : '' ?>"
                           required>
                </div>
                <button type="submit" class="btn <?= $edit_kupon ? 'btn-warning' : 'btn-primary'; ?> w-full">
                    <?= $edit_kupon ? "Perbarui Kupon" : "Tambahkan Kupon"; ?>
                </button>
            </form>
        </div>
    </div>

    <!-- Daftar Kode Kupon -->
    <div class="card shadow">
        <div class="card-header bg-secondary text-white">Daftar Kode Kupon</div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Kode</th>
                    <th>Deskripsi</th>
                    <th>Diskon (%)</th>
                    <th>Tanggal Berlaku</th>
                    <th>Tanggal Kadaluarsa</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                <?php if (count($kupons) > 0): ?>
                    <?php foreach ($kupons as $index => $kupon): ?>
                        <tr>
                            <td><?= $index + 1; ?></td>
                            <td><?= htmlspecialchars($kupon['kode']); ?></td>
                            <td><?= htmlspecialchars($kupon['deskripsi'] ?: 'Tidak ada deskripsi'); ?></td>
                            <td><?= htmlspecialchars($kupon['diskon']); ?></td>
                            <td><?= htmlspecialchars($kupon['tanggal_berlaku']); ?></td>
                            <td><?= htmlspecialchars($kupon['tanggal_kadaluarsa']); ?></td>
                            <td>
                                <a href="promosi.php?edit_id=<?= $kupon['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="promosi.php?hapus_id=<?= $kupon['id']; ?>" 
                                class="btn btn-danger btn-sm"
                                onclick="return confirm('Apakah Anda yakin ingin menghapus kupon ini?');">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">Tidak ada kupon tersedia.</td>

                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

