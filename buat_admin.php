<?php

require_once 'config/koneksi.php';

$username = 'admin';
$password = password_hash('123456', PASSWORD_DEFAULT);

$query = mysqli_query(
    $conn,
    "INSERT INTO admin(username,password)
     VALUES('$username','$password')"
);

if($query){
    echo "Admin berhasil dibuat";
}else{
    echo mysqli_error($conn);
}