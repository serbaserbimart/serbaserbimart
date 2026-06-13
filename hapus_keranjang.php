<?php
session_start();

$id = intval($_GET['id']);

if(isset($_SESSION['keranjang'][$id])){
    unset($_SESSION['keranjang'][$id]);
}

header("Location: keranjang.php");
exit;
?>