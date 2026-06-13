<?php
require_once 'auth.php';
require_once __DIR__ . '/../config/koneksi.php';

// Cek apakah ada ID
if (!isset($_GET['id'])) {
    header("Location: produk.php");
    exit;
}

$id = (int) $_GET['id'];

// Ambil data produk
$query = mysqli_query(
    $conn,
    "SELECT * FROM produk WHERE id = '$id' LIMIT 1"
);

if (mysqli_num_rows($query) == 0) {
    header("Location: produk.php");
    exit;
}

$produk = mysqli_fetch_assoc($query);

// Hapus file foto jika ada
if (!empty($produk['foto'])) {

    $pathFoto = "../upload/" . $produk['foto'];

    if (file_exists($pathFoto)) {
        unlink($pathFoto);
    }
}

// Hapus data dari database
$hapus = mysqli_query(
    $conn,
    "DELETE FROM produk WHERE id = '$id'"
);

if ($hapus) {

    header("Location: produk.php?hapus=1");
    exit;

} else {

    echo "Gagal menghapus produk: " . mysqli_error($conn);
}
?>