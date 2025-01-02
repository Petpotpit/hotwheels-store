<?php
require('../functions.php');

if (isset($_POST['id'])) {
    $id = $_POST['id'];

    // Menyiapkan query untuk menghapus produk dari wishlist
    $conn = dbConnect();
    $stmt = $conn->prepare("DELETE FROM Wishlist WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "Produk berhasil dihapus!";
    } else {
        echo "Gagal menghapus produk.";
    }
    $stmt->close();
    $conn->close();
}
?>
