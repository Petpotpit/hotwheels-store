<?php
// Fungsi untuk membuat koneksi ke database
function dbConnect() {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "toko_hotwheels";
    
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Koneksi gagal: " . $conn->connect_error);
    }
    return $conn;
}

// Fungsi untuk mendapatkan ringkasan penjualan dalam periode tertentu
function getRingkasanPenjualan($conn, $start_date, $end_date) {
    $query = "SELECT SUM(total) AS total_pendapatan, COUNT(id) AS jumlah_transaksi 
              FROM Pesanan 
              WHERE tanggal_transaksi BETWEEN ? AND ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ss', $start_date, $end_date);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Fungsi untuk mendapatkan grafik penjualan harian, mingguan, atau bulanan
function getGrafikPenjualan($conn, $start_date, $end_date, $interval = 'DAY') {
    $query = "SELECT DATE(tanggal_transaksi) AS tanggal, SUM(total) AS total_harian 
              FROM Pesanan 
              WHERE tanggal_transaksi BETWEEN ? AND ? 
              GROUP BY DATE(tanggal_transaksi)
              ORDER BY tanggal ASC";
    if ($interval === 'WEEK') {
        $query = "SELECT WEEK(tanggal_transaksi) AS minggu, SUM(total) AS total_harian 
                  FROM Pesanan 
                  WHERE tanggal_transaksi BETWEEN ? AND ? 
                  GROUP BY WEEK(tanggal_transaksi)
                  ORDER BY minggu ASC";
    } elseif ($interval === 'MONTH') {
        $query = "SELECT MONTH(tanggal_transaksi) AS bulan, SUM(total) AS total_harian 
                  FROM Pesanan 
                  WHERE tanggal_transaksi BETWEEN ? AND ? 
                  GROUP BY MONTH(tanggal_transaksi)
                  ORDER BY bulan ASC";
    }

    $stmt = $conn->prepare($query);
    $stmt->bind_param('ss', $start_date, $end_date);
    $stmt->execute();
    $result = $stmt->get_result();
    $grafik = [];
    while ($row = $result->fetch_assoc()) {
        $grafik[] = $row;
    }
    return $grafik;
}

// Fungsi untuk mendapatkan 5 produk terlaris dalam periode tertentu
function getProdukTerlaris($conn, $start_date, $end_date) {
    $query = "SELECT p.nama, SUM(rp.jumlah) AS total_terjual 
              FROM RincianPesanan rp
              JOIN Produk p ON rp.produk_id = p.id
              JOIN Pesanan ps ON rp.pesanan_id = ps.id
              WHERE ps.tanggal_transaksi BETWEEN ? AND ? 
              GROUP BY rp.produk_id
              ORDER BY total_terjual DESC LIMIT 5";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ss', $start_date, $end_date);
    $stmt->execute();
    $result = $stmt->get_result();
    $produk_terlaris = [];
    while ($row = $result->fetch_assoc()) {
        $produk_terlaris[] = $row;
    }
    return $produk_terlaris;
}
function tambahKupon($data) {
    $conn = dbConnect();
    $kode = $data['kode'];
    $deskripsi = $data['deskripsi'] ?: null;
    $diskon = $data['diskon'];
    $tanggal_berlaku = $data['tanggal_berlaku'];
    $tanggal_kadaluarsa = $data['tanggal_kadaluarsa'];

    $stmt = $conn->prepare("INSERT INTO kupon (kode, deskripsi, diskon, tanggal_berlaku, tanggal_kadaluarsa) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdss", $kode, $deskripsi, $diskon, $tanggal_berlaku, $tanggal_kadaluarsa);

    if ($stmt->execute()) {
        $stmt->close();
        return "Kode kupon berhasil ditambahkan!";
    } else {
        $error = $stmt->error;
        $stmt->close();
        return "Gagal menambahkan kupon: $error";
    }
}

function editKupon($data) {
    $conn = dbConnect();
    $id = $data['id'];
    $kode = $data['kode'];
    $deskripsi = $data['deskripsi'] ?: null;
    $diskon = $data['diskon'];
    $tanggal_berlaku = $data['tanggal_berlaku'];
    $tanggal_kadaluarsa = $data['tanggal_kadaluarsa'];

    $stmt = $conn->prepare("UPDATE kupon SET kode = ?, deskripsi = ?, diskon = ?, tanggal_berlaku = ?, tanggal_kadaluarsa = ? WHERE id = ?");
    $stmt->bind_param("ssdssi", $kode, $deskripsi, $diskon, $tanggal_berlaku, $tanggal_kadaluarsa, $id);

    if ($stmt->execute()) {
        $stmt->close();
        return "Kode kupon berhasil diperbarui!";
    } else {
        $error = $stmt->error;
        $stmt->close();
        return "Gagal memperbarui kupon: $error";
    }
}

function hapusKupon($id) {
    $conn = dbConnect();
    $stmt = $conn->prepare("DELETE FROM kupon WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $stmt->close();
        return "Kode kupon berhasil dihapus!";
    } else {
        $error = $stmt->error;
        $stmt->close();
        return "Gagal menghapus kupon: $error";
    }
}

function ambilKupons() {
    $conn = dbConnect();
    $result = $conn->query("SELECT * FROM kupon");
    return $result->fetch_all(MYSQLI_ASSOC);
}

function ambilKuponById($id) {
    $conn = dbConnect();
    $stmt = $conn->prepare("SELECT * FROM kupon WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $result;
}


function addToWishlist($conn, $produk_id, $pengguna_id) {
    // Periksa apakah produk sudah ada di wishlist
    $query = "SELECT * FROM Wishlist WHERE produk_id = ? AND pengguna_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $produk_id, $pengguna_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Jika produk belum ada di wishlist, tambahkan produk ke wishlist
    if ($result->num_rows == 0) {
        $query = "INSERT INTO Wishlist (produk_id, pengguna_id) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $produk_id, $pengguna_id);
        $stmt->execute();
        return "Produk berhasil ditambahkan ke wishlist!";
    } else {
        return "Produk sudah ada di wishlist!";
    }
}

// Fungsi untuk mendapatkan produk dalam wishlist berdasarkan pengguna_id// Di file functions.php
function getWishlist($conn, $pengguna_id) {
    $query = "SELECT Wishlist.*, Produk.nama, Produk.harga, Produk.gambar 
              FROM Wishlist 
              JOIN Produk ON Wishlist.produk_id = Produk.id 
              WHERE Wishlist.pengguna_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $pengguna_id);
    $stmt->execute();
    return $stmt->get_result();
}

// Fungsi untuk login pengguna
function loginPengguna($email, $password) {
    $conn = dbConnect();
    $stmt = $conn->prepare("SELECT * FROM Pengguna WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['pengguna_id'] = $row['id'];
            $_SESSION['pengguna_level'] = $row['role'];
            if ($row['role'] == 'admin') {
                header(header: "Location: admin/dashboard.php");
                exit();
            } else {
                header( "Location: user/home.php");
                exit();
            }
        } else {
            echo "Password salah";
        }
    } else {
        echo "Email tidak ditemukan";
    }
    $stmt->close();
    $conn->close();
}
// Fungsi untuk registrasi pengguna
function registrasiPengguna($nama, $email, $password, $role) {
    $conn = dbConnect();
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $conn->prepare("INSERT INTO Pengguna (nama, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nama, $email, $hashed_password, $role);
    if ($stmt->execute()) {
        echo "Pengguna berhasil diregistrasi";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
}   

// Fungsi: Ambil statistik utama
function getStatistics($conn) {
    $stats = [];
    $stats['total_penjualan'] = $conn->query("SELECT COUNT(*) AS total FROM Pesanan")->fetch_assoc()['total'];
    $stats['total_pendapatan'] = $conn->query("SELECT SUM(RP.harga * RP.jumlah) AS total FROM Pesanan P JOIN RincianPesanan RP ON P.id = RP.pesanan_id")->fetch_assoc()['total'];
    $stats['total_pelanggan'] = $conn->query("SELECT COUNT(*) AS total FROM Pengguna WHERE role = 'pelanggan'")->fetch_assoc()['total'];
    return $stats;
}

// Fungsi: Ambil laporan berdasarkan filter
function getReport($conn, $startDate = null, $endDate = null) {
    $query = "SELECT P.id AS pesanan_id, P.tanggal_transaksi, PG.nama AS pelanggan, 
              SUM(RP.harga * RP.jumlah) AS total_harga, P.status_pengiriman 
              FROM Pesanan P 
              JOIN Pengguna PG ON P.pengguna_id = PG.id 
              JOIN RincianPesanan RP ON P.id = RP.pesanan_id 
              WHERE 1=1";

    if ($startDate && $endDate) {
        $query .= " AND P.tanggal_transaksi BETWEEN '$startDate' AND '$endDate'";
    }

    $query .= " GROUP BY P.id ORDER BY P.tanggal_transaksi DESC";
    return $conn->query($query);
}

// Fungsi untuk mendapatkan semua pengguna
function getAllUsers($conn) {
    $query = "SELECT * FROM Pengguna ORDER BY role ASC, nama ASC";
    return $conn->query($query);
}

// Fungsi untuk menambahkan pengguna baru
function addUser($conn, $nama, $email, $password, $role, $alamat, $telepon) {
    $passwordHash = password_hash($password, PASSWORD_BCRYPT);
    $query = "INSERT INTO Pengguna (nama, email, password, role, alamat, telepon) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ssssss', $nama, $email, $passwordHash, $role, $alamat, $telepon);
    return $stmt->execute();
}

// Fungsi untuk mengedit pengguna
function editUser($conn, $id, $nama, $email, $role, $alamat, $telepon) {
    $query = "UPDATE Pengguna SET nama = ?, email = ?, role = ?, alamat = ?, telepon = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('sssssi', $nama, $email, $role, $alamat, $telepon, $id);
    return $stmt->execute();
}

function getRandomCollectorProducts($conn, $limit = 4) {
    $query = "SELECT * FROM Produk WHERE kategori = 'Collector' ORDER BY RAND() LIMIT ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result) {
        die("Query gagal: " . $conn->error);
    }

    return $result;
}

function getProductReviews($conn, $produk_id) {
    $query = "SELECT r.*, u.nama FROM ulasan r 
              INNER JOIN pengguna u ON r.pengguna_id = u.id 
              WHERE r.produk_id = ? ORDER BY r.tanggal DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $produk_id);
    $stmt->execute();
    return $stmt->get_result();
}

function getProductDetails($conn, $produk_id) {
    $query = "SELECT * FROM Produk WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $produk_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        return null; // Jika produk tidak ditemukan
    }
}
function getFeaturedProducts($conn, $limit = 8) {
    $query = "SELECT * FROM Produk WHERE stok > 0 ORDER BY id DESC LIMIT ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    return $stmt->get_result();
}
function addProductReview($conn, $produk_id, $pengguna_id, $rating, $komentar) {
    // Pastikan rating dan komentar valid
    if ($rating < 1 || $rating > 5) {
        return "Rating harus antara 1 dan 5.";
    }

    // Menambahkan ulasan ke tabel Ulasan
    $query = "INSERT INTO Ulasan (produk_id, pengguna_id, rating, komentar, tanggal) 
              VALUES (?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iiis", $produk_id, $pengguna_id, $rating, $komentar);

    if ($stmt->execute()) {
        return "Ulasan Anda telah berhasil ditambahkan!";
    } else {
        return "Terjadi kesalahan saat menambahkan ulasan. Silakan coba lagi.";
    }
}

function hitungTotalKeranjang($pengguna_id) {
    $conn = dbConnect();
    $stmt = $conn->prepare("SELECT k.jumlah, p.harga FROM Keranjang k 
                            JOIN Produk p ON k.produk_id = p.id 
                            WHERE k.pengguna_id = ?");
    $stmt->bind_param("i", $pengguna_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $total = 0;
    while ($row = $result->fetch_assoc()) {
        // Pastikan harga diubah menjadi float untuk perhitungan
        $harga = (float) $row['harga'];
        $total += $row['jumlah'] * $harga; // Hitung subtotal
    }
    $stmt->close();
    $conn->close();
    return $total; // Mengembalikan total belanja
}
// Fungsi untuk mendapatkan keranjang pengguna
function getKeranjang() {
    // Mengambil pengguna_id dari sesi
    if (!isset($_SESSION['pengguna_id'])) {
        // Jika pengguna tidak login, kembalikan array kosong
        return [];
    }

    $pengguna_id = $_SESSION['pengguna_id']; // Ambil pengguna_id dari sesi

    // Koneksi ke database
    $conn = dbConnect();

    // Query untuk mendapatkan produk di keranjang berdasarkan pengguna_id
    $stmt = $conn->prepare("SELECT k.id, k.jumlah, p.id AS produk_id, p.nama, p.harga, p.gambar, p.stok, p.stok_minimum
        FROM Keranjang k
        JOIN Produk p ON k.produk_id = p.id
        WHERE k.pengguna_id = ?");
    $stmt->bind_param('i', $pengguna_id); // Menggunakan $pengguna_id dari sesi
    $stmt->execute();
    $result = $stmt->get_result();

    // Mengecek apakah ada produk di keranjang
    if ($result->num_rows == 0) {
        // Jika keranjang kosong, kembalikan array kosong
        $stmt->close();
        $conn->close();
        return [];
    }

    // Mengambil semua data dan mengembalikannya sebagai array asosiatif
    $keranjang = $result->fetch_all(MYSQLI_ASSOC);

    $stmt->close();
    $conn->close();

    // Mengembalikan array keranjang
    return $keranjang;
}

function addToCart($produk_id, $jumlah, $pengguna_id, $conn) {
    // Pastikan pengguna sudah login
    if (!$pengguna_id) {
        return "Anda harus login terlebih dahulu untuk menambahkan produk ke keranjang.";
    }

    // Mulai transaksi
    $conn->begin_transaction();

    try {
        // Periksa apakah stok mencukupi
        $stokCheckQuery = "SELECT stok FROM Produk WHERE id = ?";
        $stmt = $conn->prepare($stokCheckQuery);
        $stmt->bind_param("i", $produk_id);
        $stmt->execute();
        $stokResult = $stmt->get_result();
        $stokData = $stokResult->fetch_assoc();

        if (!$stokData || $stokData['stok'] < $jumlah) {
            return "Stok produk tidak mencukupi.";
        }

        // Periksa apakah produk sudah ada di keranjang
        $checkQuery = "SELECT * FROM Keranjang WHERE produk_id = ? AND pengguna_id = ?";
        $stmt = $conn->prepare($checkQuery);
        $stmt->bind_param("ii", $produk_id, $pengguna_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Update jumlah jika produk sudah ada
            $updateQuery = "UPDATE Keranjang SET jumlah = jumlah + ? WHERE produk_id = ? AND pengguna_id = ?";
            $stmt = $conn->prepare($updateQuery);
            $stmt->bind_param("iii", $jumlah, $produk_id, $pengguna_id);
            $stmt->execute();
        } else {
            // Tambahkan produk baru ke keranjang
            $insertQuery = "INSERT INTO Keranjang (produk_id, pengguna_id, jumlah) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($insertQuery);
            $stmt->bind_param("iii", $produk_id, $pengguna_id, $jumlah);
            $stmt->execute();
        }

        // Kurangi stok produk
        $updateStokQuery = "UPDATE Produk SET stok = stok - ? WHERE id = ?";
        $stmt = $conn->prepare($updateStokQuery);
        $stmt->bind_param("ii", $jumlah, $produk_id);
        $stmt->execute();

        // Commit transaksi
        $conn->commit();

        return "Produk berhasil ditambahkan ke keranjang!";
    } catch (Exception $e) {
        // Rollback jika terjadi kesalahan
        $conn->rollback();
        return "Terjadi kesalahan: " . $e->getMessage();
    }
}
function getProduk($conn, $search = '', $filter = '') {
    $query = "SELECT * FROM Produk WHERE 1";

    if ($search) {
        $query .= " AND nama LIKE '%$search%'";
    }

    if ($filter) {
        $query .= " AND kategori = '$filter'";
    }

    return $conn->query($query);
}
function updateQuantity($keranjang_id, $pengguna_id, $jumlah_baru) {
    $conn = dbConnect();

    // Ambil informasi produk dari keranjang
    $stmt = $conn->prepare("SELECT produk_id, jumlah FROM Keranjang WHERE id = ? AND pengguna_id = ?");
    $stmt->bind_param("ii", $keranjang_id, $pengguna_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $item = $result->fetch_assoc();
    $produk_id = $item['produk_id'];
    $jumlah_sekarang = $item['jumlah'];

    if ($jumlah_baru > 0) {
        // Update kuantitas di keranjang
        $stmt = $conn->prepare("UPDATE Keranjang SET jumlah = ? WHERE id = ? AND pengguna_id = ?");
        $stmt->bind_param("iii", $jumlah_baru, $keranjang_id, $pengguna_id);
        $stmt->execute();

        // Perbarui stok di tabel Produk
        $selisih = $jumlah_baru - $jumlah_sekarang; // Hitung perbedaan jumlah
        $stmt = $conn->prepare("UPDATE Produk SET stok = stok - ? WHERE id = ?");
        $stmt->bind_param("ii", $selisih, $produk_id);
        $stmt->execute();
    } else {
        // Jika jumlah baru 0, hapus produk dari keranjang
        deleteProductFromCart($keranjang_id, $pengguna_id);
    }

    $stmt->close();
    $conn->close();
}

// Fungsi untuk menghapus produk dari keranjang
function deleteProductFromCart($keranjang_id, $pengguna_id) {
    $conn = dbConnect();

    // Ambil informasi produk dari keranjang
    $stmt = $conn->prepare("SELECT produk_id, jumlah FROM Keranjang WHERE id = ? AND pengguna_id = ?");
    $stmt->bind_param("ii", $keranjang_id, $pengguna_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $item = $result->fetch_assoc();
    $produk_id = $item['produk_id'];
    $jumlah = $item['jumlah'];

    // Hapus produk dari keranjang
    $stmt = $conn->prepare("DELETE FROM Keranjang WHERE id = ? AND pengguna_id = ?");
    $stmt->bind_param("ii", $keranjang_id, $pengguna_id);
    $stmt->execute();

    // Tambahkan kembali jumlah ke stok produk
    $stmt = $conn->prepare("UPDATE Produk SET stok = stok + ? WHERE id = ?");
    $stmt->bind_param("ii", $jumlah, $produk_id);
    $stmt->execute();

    $stmt->close();
    $conn->close();
}

function applyVoucher($kode_voucher, $pengguna_id) {
    $conn = dbConnect();

    // Cek apakah kode voucher valid
    $stmt = $conn->prepare("
        SELECT diskon 
        FROM kupon 
        WHERE kode = ? 
        AND tanggal_berlaku <= NOW() 
        AND tanggal_kadaluarsa >= NOW()
    ");
    $stmt->bind_param("s", $kode_voucher);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $voucher = $result->fetch_assoc();
        $diskon = (int)$voucher['diskon']; // Diskon dalam nominal (misal Rp)

        $stmt->close();
        $conn->close();

        return $diskon; // Mengembalikan nilai diskon
    } else {
        $stmt->close();
        $conn->close();

        return 0; // Kode tidak valid atau kedaluwarsa
    }
}
// Fungsi untuk mendapatkan biaya pengiriman berdasarkan metode pengiriman
function getShippingCost($metode_pengiriman) {
    switch ($metode_pengiriman) {
        case 'ekspres':
            return 15000; // Biaya pengiriman ekspres Rp 15.000
        case 'standar':
        default:
            return 10000; // Biaya pengiriman standar
    }

}

// Fungsi untuk mengambil data kupon berdasarkan ID
// Fungsi untuk mengambil semua kupon dari database
function getAllKupons() {
    $conn = dbConnect();
    
    $stmt = $conn->prepare("SELECT * FROM kupon");
    $stmt->execute();
    
    $result = $stmt->get_result();
    $kupons = $result->fetch_all(MYSQLI_ASSOC);
    
    $stmt->close();
    $conn->close();
    
    return $kupons;
}
// Fungsi untuk mengambil data pengguna
function getUserData($conn, $pengguna_id) {
    $query = "SELECT * FROM Pengguna WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $pengguna_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}
// Fungsi untuk mengambil riwayat pesanan pengguna
function getUserOrders($conn, $pengguna_id) {
    $query = "SELECT Pesanan.*, GROUP_CONCAT(Produk.nama SEPARATOR ', ') AS produk_nama 
              FROM Pesanan 
              JOIN RincianPesanan ON Pesanan.id = RincianPesanan.pesanan_id 
              JOIN Produk ON RincianPesanan.produk_id = Produk.id 
              WHERE Pesanan.pengguna_id = ? 
              GROUP BY Pesanan.id 
              ORDER BY Pesanan.tanggal_transaksi DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $pengguna_id);
    $stmt->execute();
    return $stmt->get_result();
}