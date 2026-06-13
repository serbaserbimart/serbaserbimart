<?php
session_start();
include 'config/koneksi.php';

if (!isset($_POST['id'])) {
    header("Location: index.php");
    exit;
}

$id = (int) $_POST['id'];

$qty = isset($_POST['qty'])
    ? (int) $_POST['qty']
    : 1;

if ($qty < 1) {
    $qty = 1;
}

// Ambil produk dari database
$query = mysqli_query(
    $conn,
    "SELECT * FROM produk WHERE id = '$id' LIMIT 1"
);

if (mysqli_num_rows($query) == 0) {
    header("Location: index.php");
    exit;
}

$produk = mysqli_fetch_assoc($query);

// Cek stok
if ($produk['stok'] <= 0) {
    header("Location: index.php");
    exit;
}

// Buat session keranjang jika belum ada
if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}

// Jika produk sudah ada di keranjang
if (isset($_SESSION['keranjang'][$id])) {

    $qtyBaru =
        $_SESSION['keranjang'][$id]['qty']
        + $qty;

    if ($qtyBaru > $produk['stok']) {
        $qtyBaru = $produk['stok'];
    }

    $_SESSION['keranjang'][$id]['qty']
        = $qtyBaru;

} else {

    // Tambahkan produk baru ke keranjang
    $_SESSION['keranjang'][$id] = [
        'id' => $produk['id'],
        'nama_produk' => $produk['nama_produk'],
        'harga' => $produk['harga'],
        'foto' => $produk['foto'],
        'qty' => min($qty, $produk['stok'])
    ];
}

// Kembali ke halaman utama
header("Location: index.php");
exit;
?>