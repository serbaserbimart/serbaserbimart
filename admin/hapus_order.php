<?php
require_once 'auth.php';
require_once __DIR__ . '/../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $id = intval($_POST['id']);

    // hapus data order
    $delete = mysqli_query($conn, "DELETE FROM orders WHERE id=$id");

    if ($delete) {
        header("Location: orders.php?msg=hapus_sukses");
    } else {
        echo "Gagal menghapus data!";
    }

} else {
    header("Location: orders.php");
}