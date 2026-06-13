<?php
require_once 'auth.php';
require_once __DIR__ . '/../config/koneksi.php';

$query = mysqli_query($conn, "SELECT * FROM produk ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Produk</title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background: #f5f7fb;
        }

        /* Layout */
        .wrapper {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 260px;
            background: #198754;
            color: white;
            padding: 25px;
        }

        .logo {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 40px;
        }

        .menu {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .menu a {
            color: white;
            text-decoration: none;
            padding: 12px 15px;
            border-radius: 10px;
            transition: .3s;
        }

        .menu a:hover {
            background: rgba(255, 255, 255, .15);
        }

        .content {
            flex: 1;
            padding: 30px;
        }

        /* Content */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        h2 {
            color: #333;
        }

        .btn {
            text-decoration: none;
            padding: 10px 18px;
            border-radius: 8px;
            color: white;
            font-weight: 600;
        }

        .btn-success {
            background: #198754;
        }

        .btn-primary {
            background: #0d6efd;
        }

        .btn-danger {
            background: #dc3545;
        }

        .table-container {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, .05);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #198754;
            color: white;
            padding: 15px;
            text-align: left;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }

        td img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 10px;
        }

        .action {
            display: flex;
            gap: 5px;
        }

        .alert {
            background: #d1e7dd;
            color: #0f5132;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .active {
            background: rgba(255, 255, 255, .2);
        }


        .no-data {
            text-align: center;
            padding: 40px !important;
            color: #6c757d;
            font-weight: 500;
            font-size: 15px;
        }

        @media(max-width:768px) {

            .wrapper {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
            }

        }
    </style>
</head>

<body>

    <div class="wrapper">

        <?php include 'sidebar.php'; ?>

        <div class="content">

            <!-- Content -->
            <div class="content">

                <div class="header">

                    <h2>Kelola Produk</h2>

                </div>

                <?php if (isset($_GET['success'])): ?>
                    <div class="alert">
                        Produk berhasil ditambahkan.
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['update'])): ?>
                    <div class="alert">
                        Produk berhasil diperbarui.
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['hapus'])): ?>
                    <div class="alert">
                        Produk berhasil dihapus.
                    </div>
                <?php endif; ?>

                <div class="table-container">

                    <table>

                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Foto</th>
                                <th>Nama Produk</th>
                                <th>Harga</th>
                                <th>Stok</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php
                            if (mysqli_num_rows($query) > 0) {
                                $no = 1;
                                while ($produk = mysqli_fetch_assoc($query)):
                                    ?>

                                    <tr>

                                        <td><?= $no++; ?></td>

                                        <td>
                                            <img src="../upload/<?= htmlspecialchars($produk['foto']); ?>" alt="">
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($produk['nama_produk']); ?>
                                        </td>

                                        <td>
                                            Rp <?= number_format($produk['harga'], 0, ',', '.'); ?>
                                        </td>

                                        <td>
                                            <?= $produk['stok']; ?>
                                        </td>

                                        <td>

                                            <div class="action">

                                                <a href="edit_produk.php?id=<?= $produk['id']; ?>" class="btn btn-primary">
                                                    Edit
                                                </a>

                                                <a href="hapus_produk.php?id=<?= $produk['id']; ?>" class="btn btn-danger"
                                                    onclick="return confirm('Yakin ingin menghapus produk ini?')">
                                                    Hapus
                                                </a>

                                            </div>

                                        </td>

                                    </tr>

                                    <?php
                                endwhile;
                            } else {
                                ?>
                                <tr>
                                    <td colspan="6" class="no-data">
                                        📭 Belum ada data produk yang terdaftar saat ini.
                                    </td>
                                </tr>
                            <?php } ?>
                            </div>

                            </div>
                        </tbody>
            </table>
        </div>
    </div>
    </div>
</body>

</html>