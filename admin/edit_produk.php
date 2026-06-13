<?php
require_once 'auth.php';
require_once __DIR__ . '/../config/koneksi.php';

if (!isset($_GET['id'])) {
    header("Location: produk.php");
    exit;
}

$id = (int) $_GET['id'];

$query = mysqli_query(
    $conn,
    "SELECT * FROM produk WHERE id='$id' LIMIT 1"
);

if (mysqli_num_rows($query) == 0) {
    header("Location: produk.php");
    exit;
}

$produk = mysqli_fetch_assoc($query);

$pesan = '';

if (isset($_POST['update'])) {

    $nama_produk = mysqli_real_escape_string(
        $conn,
        trim($_POST['nama_produk'])
    );

    $harga = (int) $_POST['harga'];

    $stok = (int) $_POST['stok'];

    $deskripsi = mysqli_real_escape_string(
        $conn,
        trim($_POST['deskripsi'])
    );

    $fotoBaru = $produk['foto'];

    /*
    |--------------------------------------------------------------------------
    | Jika upload foto baru
    |--------------------------------------------------------------------------
    */

    if (!empty($_FILES['foto']['name'])) {

        $ext = strtolower(
            pathinfo(
                $_FILES['foto']['name'],
                PATHINFO_EXTENSION
            )
        );

        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($ext, $allowed)) {

            $pesan = "Format gambar harus JPG, PNG atau WEBP.";

        } else {

            $namaFile = time() . '_' . rand(1000,9999) . '.' . $ext;

            $folder = "../upload/";

            move_uploaded_file(
                $_FILES['foto']['tmp_name'],
                $folder . $namaFile
            );

            /*
            |--------------------------------------------------------------------------
            | Hapus foto lama
            |--------------------------------------------------------------------------
            */

            if (
                !empty($produk['foto']) &&
                file_exists($folder . $produk['foto'])
            ) {
                unlink($folder . $produk['foto']);
            }

            $fotoBaru = $namaFile;
        }
    }

    if ($pesan == '') {

        $update = mysqli_query(
            $conn,
            "UPDATE produk SET
                nama_produk='$nama_produk',
                harga='$harga',
                stok='$stok',
                deskripsi='$deskripsi',
                foto='$fotoBaru'
             WHERE id='$id'"
        );

        if ($update) {

            header("Location: produk.php?update=1");
            exit;

        } else {

            $pesan = mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Edit Produk</title>

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Plus Jakarta Sans',sans-serif;
}

body{
    background:#f5f7fb;
}

.container{
    max-width:900px;
    margin:40px auto;
    padding:20px;
}

.card{
    background:white;
    border-radius:15px;
    padding:30px;
    box-shadow:0 5px 15px rgba(0,0,0,.05);
}

h2{
    margin-bottom:25px;
}

.form-group{
    margin-bottom:20px;
}

label{
    display:block;
    margin-bottom:8px;
    font-weight:600;
}

input,
textarea{
    width:100%;
    padding:12px;
    border:1px solid #ddd;
    border-radius:10px;
    outline:none;
}

textarea{
    min-height:120px;
    resize:vertical;
}

.preview{
    width:150px;
    border-radius:10px;
    margin-top:10px;
}

.btn{
    border:none;
    padding:12px 20px;
    border-radius:10px;
    cursor:pointer;
    color:white;
    font-weight:600;
    text-decoration:none;
}

.btn-success{
    background:#198754;
}

.btn-secondary{
    background:#6c757d;
}

.action{
    display:flex;
    gap:10px;
}

.alert{
    padding:12px;
    border-radius:10px;
    margin-bottom:20px;
}

.alert-danger{
    background:#f8d7da;
    color:#842029;
}

</style>
</head>
<body>

<div class="container">

    <div class="card">

        <h2>Edit Produk</h2>

        <?php if($pesan != ''): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($pesan); ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">

            <div class="form-group">
                <label>Nama Produk</label>
                <input
                    type="text"
                    name="nama_produk"
                    value="<?= htmlspecialchars($produk['nama_produk']); ?>"
                    required>
            </div>

            <div class="form-group">
                <label>Harga</label>
                <input
                    type="number"
                    name="harga"
                    value="<?= $produk['harga']; ?>"
                    required>
            </div>

            <div class="form-group">
                <label>Stok</label>
                <input
                    type="number"
                    name="stok"
                    value="<?= $produk['stok']; ?>"
                    required>
            </div>

            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="deskripsi"><?= htmlspecialchars($produk['deskripsi']); ?></textarea>
            </div>

            <div class="form-group">

                <label>Foto Saat Ini</label>

                <img
                    src="../upload/<?= htmlspecialchars($produk['foto']); ?>"
                    class="preview"
                    alt="Foto Produk">

            </div>

            <div class="form-group">

                <label>Ganti Foto (Opsional)</label>

                <input
                    type="file"
                    name="foto"
                    accept=".jpg,.jpeg,.png,.webp">

            </div>

            <div class="action">

                <button
                    type="submit"
                    name="update"
                    class="btn btn-success">
                    Simpan Perubahan
                </button>

                <a
                    href="produk.php"
                    class="btn btn-secondary">
                    Kembali
                </a>

            </div>

        </form>

    </div>

</div>

</body>
</html>