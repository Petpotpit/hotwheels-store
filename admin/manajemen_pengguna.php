<?php
require_once '../functions.php'; // Mengimpor file fungsi utama

$conn = dbConnect(); // Koneksi ke database


// Proses form
$editSuccess = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_user'])) {
        $nama = $_POST['nama'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $role = $_POST['role'];
        $alamat = $_POST['alamat'];
        $telepon = $_POST['telepon'];
        addUser($conn, $nama, $email, $password, $role, $alamat, $telepon);
    } elseif (isset($_POST['edit_user'])) {
        $id = $_POST['id'];
        $nama = $_POST['nama'];
        $email = $_POST['email'];
        $role = $_POST['role'];
        $alamat = $_POST['alamat'];
        $telepon = $_POST['telepon'];
        if (editUser($conn, $id, $nama, $email, $role, $alamat, $telepon)) {
            $editSuccess = true; // Menandakan pengeditan berhasil
        }
    }
}

// Ambil data pengguna
$users = getAllUsers($conn);

// Ambil data pengguna untuk edit (jika ada)
$userToEdit = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM Pengguna WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $userToEdit = $result->fetch_assoc();
    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pengguna</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <?php require('layout/sidebar.php') ?>
<div class="container py-5">
    <h1 class="text-center text-2xl font-bold mb-4">Manajemen Pengguna</h1>

    <!-- Tabel Pengguna -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title text-lg font-semibold">Daftar Pengguna</h5>
            <table class="table table-striped border rounded">
                <thead class="bg-blue-500 text-white">
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Alamat</th>
                    <th>Telepon</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                <?php while ($row = $users->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['nama']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= ucfirst($row['role']) ?></td>
                        <td><?= htmlspecialchars($row['alamat']) ?></td>
                        <td><?= htmlspecialchars($row['telepon']) ?></td>
                        <td>
                            <a href="?edit=<?= $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Form Tambah/Edit Pengguna -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title text-lg font-semibold mb-3"><?= $userToEdit ? 'Edit Pengguna' : 'Tambah Pengguna' ?></h5>

            <?php if ($editSuccess): ?>
                <div class="alert alert-success" role="alert">
                    Pengeditan pengguna berhasil!
                </div>
            <?php endif; ?>

            <form method="POST">
                <input type="hidden" name="id" value="<?= $userToEdit['id'] ?? '' ?>">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <label for="nama" class="form-label">Nama</label>
                        <input type="text" id="nama" name="nama" class="form-control" value="<?= $userToEdit['nama'] ?? '' ?>" required>
                    </div>
                    <div>
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" class="form-control" value="<?= $userToEdit['email'] ?? '' ?>" required>
                    </div>
                    <div>
                        <label for="role" class="form-label">Role</label>
                        <select id="role" name="role" class="form-select" required>
                            <option value="admin" <?= isset($userToEdit) && $userToEdit['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                            <option value="pelanggan" <?= isset($userToEdit) && $userToEdit['role'] === 'pelanggan' ? 'selected' : '' ?>>Pelanggan</option>
                        </select>
                    </div>
                    <div>
                        <label for="password" class="form-label">Password</label>
                        <input type="password" id="password" name="password" class="form-control" <?= !$userToEdit ? 'required' : '' ?>>
                    </div>
                    <div>
                        <label for="alamat" class="form-label">Alamat</label>
                        <textarea id="alamat" name="alamat" class="form-control"><?= $userToEdit['alamat'] ?? '' ?></textarea>
                    </div>
                    <div>
                        <label for="telepon" class="form-label">Telepon</label>
                        <input type="text" id="telepon" name="telepon" class="form-control" value="<?= $userToEdit['telepon'] ?? '' ?>">
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit" name="<?= $userToEdit ? 'edit_user' : 'add_user' ?>" class="btn btn-primary w-full md:w-auto">
                        <?= $userToEdit ? 'Selesaikan Edit' : 'Tambah Pengguna' ?>
                    </button>
                </div>
            </form>

            <!-- Tombol Tambah Pengguna Setelah Edit -->
            <?php if ($editSuccess): ?>
                <a href="manajemen_pengguna.php" class="btn btn-success mt-3">Tambah Pengguna Baru</a>
            <?php endif; ?>

        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
