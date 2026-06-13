<?php
session_start();
require_once 'config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header("Location: checkout.php");
    exit;
}

if (empty($_SESSION['keranjang'])) {
    header("Location: keranjang.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Ambil Data Form
|--------------------------------------------------------------------------
*/

$nama = mysqli_real_escape_string($conn, trim($_POST['nama'] ?? ''));
$telepon = mysqli_real_escape_string($conn, trim($_POST['telepon'] ?? ''));
$alamat = mysqli_real_escape_string($conn, trim($_POST['alamat'] ?? ''));
$catatan = mysqli_real_escape_string($conn, trim($_POST['catatan'] ?? ''));

if ($nama == '' || $telepon == '' || $alamat == '') {
    die("Data pemesan belum lengkap.");
}

if (!preg_match('/^[0-9]+$/', $telepon)) {
    die("Nomor WhatsApp hanya boleh angka.");
}

if (strlen($telepon) > 13) {
    die("Nomor WhatsApp maksimal 13 digit.");
}

/*
|--------------------------------------------------------------------------
| Hitung Total
|--------------------------------------------------------------------------
*/

$total = 0;

foreach ($_SESSION['keranjang'] as $item) {

    $subtotal = $item['harga'] * $item['qty'];
    $total += $subtotal;
}

/*
|--------------------------------------------------------------------------
| Nomor Pesanan
|--------------------------------------------------------------------------
*/

$nomor_pesanan = "INV-" . date("YmdHis");

/*
|--------------------------------------------------------------------------
| Mulai Transaksi
|--------------------------------------------------------------------------
*/

mysqli_begin_transaction($conn);

try {

    /*
    |--------------------------------------------------------------------------
    | Simpan Order
    |--------------------------------------------------------------------------
    */

    $simpanOrder = mysqli_query(
        $conn,
        "INSERT INTO orders
        (
            nomor_pesanan,
            nama,
            telepon,
            alamat,
            catatan,
            total,
            status
        )
        VALUES
        (
            '$nomor_pesanan',
            '$nama',
            '$telepon',
            '$alamat',
            '$catatan',
            '$total',
            'Menunggu'
        )"
    );

    if (!$simpanOrder) {
        throw new Exception(mysqli_error($conn));
    }

    $order_id = mysqli_insert_id($conn);

    /*
    |--------------------------------------------------------------------------
    | Simpan Detail Pesanan
    |--------------------------------------------------------------------------
    */

    foreach ($_SESSION['keranjang'] as $item) {

        $produk_id = (int) $item['id'];
        $qty = (int) $item['qty'];
        $harga = (int) $item['harga'];

        /*
        |--------------------------------------------------------------------------
        | Cek Produk
        |--------------------------------------------------------------------------
        */

        $cekProduk = mysqli_query(
            $conn,
            "SELECT *
             FROM produk
             WHERE id='$produk_id'
             LIMIT 1"
        );

        if (mysqli_num_rows($cekProduk) == 0) {
            throw new Exception("Produk tidak ditemukan.");
        }

        $produk = mysqli_fetch_assoc($cekProduk);

        if ($produk['stok'] < $qty) {
            throw new Exception(
                "Stok produk " .
                $produk['nama_produk'] .
                " tidak mencukupi."
            );
        }

        $nama_produk = mysqli_real_escape_string(
            $conn,
            $produk['nama_produk']
        );

        $subtotal = $harga * $qty;

        /*
        |--------------------------------------------------------------------------
        | Simpan Detail
        |--------------------------------------------------------------------------
        */

        $detail = mysqli_query(
            $conn,
            "INSERT INTO order_detail
            (
                order_id,
                produk_id,
                nama_produk,
                harga,
                qty,
                subtotal
            )
            VALUES
            (
                '$order_id',
                '$produk_id',
                '$nama_produk',
                '$harga',
                '$qty',
                '$subtotal'
            )"
        );

        if (!$detail) {
            throw new Exception(mysqli_error($conn));
        }

        /*
        |--------------------------------------------------------------------------
        | Kurangi Stok
        |--------------------------------------------------------------------------
        */

        $updateStok = mysqli_query(
            $conn,
            "UPDATE produk
             SET stok = stok - $qty
             WHERE id='$produk_id'"
        );

        if (!$updateStok) {
            throw new Exception(mysqli_error($conn));
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Commit
    |--------------------------------------------------------------------------
    */

    mysqli_commit($conn);

    /*
    |--------------------------------------------------------------------------
    | Simpan Session Untuk Halaman Sukses
    |--------------------------------------------------------------------------
    */

    $_SESSION['last_order_id'] = $order_id;
    $_SESSION['nomor_pesanan'] = $nomor_pesanan;

    /*
    |--------------------------------------------------------------------------
    | Kosongkan Keranjang
    |--------------------------------------------------------------------------
    */

    unset($_SESSION['keranjang']);

    /*
    |--------------------------------------------------------------------------
    | Redirect
    |--------------------------------------------------------------------------
    */

    header("Location: checkout_sukses.php?id=" . $order_id);
    exit;

} catch (Exception $e) {

    mysqli_rollback($conn);

    die(
        "<h3>Terjadi Kesalahan</h3>
        <p>" . $e->getMessage() . "</p>"
    );
}