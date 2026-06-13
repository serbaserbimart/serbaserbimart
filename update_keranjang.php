<?php
session_start();

require 'config/koneksi.php';

$id     = $_POST['id'] ?? null;
$action = $_POST['action'] ?? null;

if (!$id || !$action || !isset($_SESSION['keranjang'][$id])) {
    echo json_encode(['error' => 'invalid']);
    exit;
}

if ($action === 'tambah') {
    $id_int = intval($id);
    $result = mysqli_query($conn, "SELECT stok FROM produk WHERE id = $id_int");
    $produk = mysqli_fetch_assoc($result);
    $stok   = $produk['stok'] ?? 0;

    if ($_SESSION['keranjang'][$id]['qty'] >= $stok) {
        echo json_encode(['error' => 'stok_habis', 'pesan' => 'Stok tidak mencukupi']);
        exit;
    }

    $_SESSION['keranjang'][$id]['qty']++;

} elseif ($action === 'kurang') {
    $_SESSION['keranjang'][$id]['qty']--;
    if ($_SESSION['keranjang'][$id]['qty'] <= 0) {
        unset($_SESSION['keranjang'][$id]);
        echo json_encode(['hapus' => true]);
        exit;
    }
}

$item     = $_SESSION['keranjang'][$id];
$qty      = $item['qty'];
$subtotal = $item['harga'] * $qty;

$total = 0;
foreach ($_SESSION['keranjang'] as $v) {
    $total += $v['harga'] * $v['qty'];
}

echo json_encode([
    'qty'      => $qty,
    'subtotal' => number_format($subtotal, 0, ',', '.'),
    'total'    => number_format($total, 0, ',', '.'),
]);