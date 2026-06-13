<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../config/koneksi.php';

/*
|--------------------------------------------------------------------------
| Statistik Dashboard
|--------------------------------------------------------------------------
*/

$totalProduk = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT COUNT(*) AS total FROM produk"
    )
)['total'];

$stokHabis = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT COUNT(*) AS total FROM produk WHERE stok <= 0"
    )
)['total'];

$totalStok = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT SUM(stok) AS total FROM produk"
    )
)['total'];

$totalStok = $totalStok ?? 0;

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard Admin</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

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

        .wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */

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
            transition: .2s;
        }

        .menu a:hover {
            background: rgba(255, 255, 255, .15);
        }

        /* Content */

        .content {
            flex: 1;
            padding: 30px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 28px;
        }

        .admin-name {
            background: white;
            padding: 10px 15px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, .05);
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, .05);
        }

        .card h3 {
            font-size: 14px;
            color: #777;
            margin-bottom: 10px;
        }

        .card .value {
            font-size: 32px;
            font-weight: 700;
            color: #198754;
        }

        .action {
            margin-top: 30px;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .btn {
            text-decoration: none;
            padding: 12px 20px;
            border-radius: 10px;
            color: white;
            font-weight: 600;
        }

        .btn-primary {
            background: #198754;
        }

        .btn-warning {
            background: #f59e0b;
        }

        .btn-danger {
            background: #dc3545;
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

        <body>

            <div class="wrapper">

                <?php include 'sidebar.php'; ?>

                <main class="content">

                    <div class="header">
                        <h1>Dashboard</h1>

                        <div class="admin-name">
                            <?= htmlspecialchars($_SESSION['admin_username']); ?>
                        </div>
                    </div>

                    <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
                        <div style="
            background:#ecfdf3;
            color:#027a48;
            border:1px solid #d1fadf;
            padding:14px 18px;
            border-radius:12px;
            margin-bottom:25px;
            font-size:14px;
            font-weight:600;
        ">
                            ✅ Produk baru berhasil ditambahkan ke katalog toko!
                        </div>
                    <?php endif; ?>

                    <div class="cards">

                        <div class="card">
                            <h3>Total Produk</h3>
                            <div class="value">
                                <?= $totalProduk; ?>
                            </div>
                        </div>

                        <div class="card">
                            <h3>Produk Stok Habis</h3>
                            <div class="value">
                                <?= $stokHabis; ?>
                            </div>
                        </div>

                        <div class="card">
                            <h3>Total Stok Barang</h3>
                            <div class="value">
                                <?= $totalStok; ?>
                            </div>
                        </div>
                        <div class="card">
                            <h3>Banner Aktif</h3>
                            <div class="value">
                                <?= mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM banner WHERE aktif = 1"))['total']; ?>
                                <span style="font-size:16px; color:#9ca3af;">/3</span>
                            </div>
                        </div>

                    </div>

                </main>

            </div>

        </body>

</html>