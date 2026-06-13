<?php
require_once 'auth.php';
require_once __DIR__ . '/../config/koneksi.php';

// Menangkap kata kunci pencarian jika ada
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

if (!empty($search)) {
    $query = mysqli_query(
        $conn,
        "SELECT * FROM orders 
         WHERE nomor_pesanan LIKE '%$search%' 
         OR nama LIKE '%$search%' 
         ORDER BY id DESC"
    );
} else {
    $query = mysqli_query(
        $conn,
        "SELECT * FROM orders ORDER BY id DESC"
    );
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pesanan</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com">
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

        .sidebar {
            width: 260px;
            background: #198754;
            color: white;
            padding: 25px;
            flex-shrink: 0;
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

        .menu .active {
            background: rgba(255, 255, 255, .2);
        }

        .content {
            flex: 1;
            padding: 30px;
            overflow-x: hidden;
        }

        /* Container Judul & Search Box Judul Atas */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .page-title {
            color: #333;
        }

        /* Desain Search Box */
        .search-container {
            display: flex;
            gap: 8px;
            width: 100%;
            max-width: 350px;
        }

        .search-input {
            flex: 1;
            padding: 10px 15px;
            border: 1.5px solid #dddddd;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            border-color: #198754;
            box-shadow: 0 0 0 3px rgba(25, 135, 84, 0.15);
        }

        .btn-search {
            background: #198754;
            color: white;
            border: none;
            padding: 0 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            transition: 0.2s;
        }

        .btn-search:hover {
            background: #157347;
        }

        .btn-reset {
            background: #6c757d;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            text-align: center;
            transition: 0.2s;
        }

        .btn-reset:hover {
            background: #5c636a;
        }

        .card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, .05);
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            white-space: nowrap;
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
            vertical-align: middle;
        }

        tr:hover {
            background: #fafafa;
        }

        .badge {
            padding: 6px 12px;
            border-radius: 50px;
            color: white;
            font-size: 13px;
            font-weight: 600;
            display: inline-block;
            text-align: center;
            text-transform: capitalize;
        }

        .menunggu {
            background: #ffc107;
            color: #000;
        }

        .diproses {
            background: #0d6efd;
        }

        .selesai {
            background: #198754;
        }

        .dibatalkan {
            background: #dc3545;
        }

        .empty {
            padding: 40px;
            text-align: center;
            color: #777;
        }

        @media(max-width: 768px) {
            .wrapper {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
            }

            .content {
                padding: 20px 15px;
            }

            .search-container {
                max-width: 100%;
            }
        }

        .btn {
            text-decoration: none;
            padding: 8px 14px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.25s ease;
            cursor: pointer;
            border: none;
        }

        /* Tombol Detail */
        .btn-primary {
            background: linear-gradient(135deg, #0d6efd, #0b5ed7);
            color: #fff;
            box-shadow: 0 3px 8px rgba(13, 110, 253, 0.3);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #0b5ed7, #084298);
            transform: translateY(-2px);
        }

        /* Tombol Hapus */
        .btn-danger {
            background: linear-gradient(135deg, #dc3545, #b02a37);
            color: #fff;
            box-shadow: 0 3px 8px rgba(220, 53, 69, 0.3);
        }

        .btn-danger:hover {
            background: linear-gradient(135deg, #bb2d3b, #842029);
            transform: translateY(-2px);
        }
    </style>
</head>

<body>
    <div class="wrapper">

        <?php include 'sidebar.php'; ?>

        <div class="content">

            <!-- HEADER HALAMAN & KOTAK PENCARIAN -->
            <div class="page-header">
                <h2 class="page-title">Daftar Pesanan</h2>

                <form method="GET" action="" class="search-container">
                    <input type="text" name="search" class="search-input" placeholder="Cari No. Pesanan / Nama..."
                        value="<?= htmlspecialchars($search); ?>">
                    <button type="submit" class="btn-search">Cari</button>
                    <?php if (!empty($search)): ?>
                        <a href="orders.php" class="btn-reset">Reset</a>
                    <?php endif; ?>
                </form>
            </div>

            <div class="card">
                <?php if (mysqli_num_rows($query) > 0): ?>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>No Pesanan</th>
                                    <th>Nama</th>
                                    <th>Telepon</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                while ($order = mysqli_fetch_assoc($query)):
                                    $statusClass = strtolower($order['status']);
                                    ?>
                                    <tr>
                                        <td><?= $no++; ?></td>
                                        <td><strong><?= htmlspecialchars($order['nomor_pesanan']); ?></strong></td>
                                        <td><?= htmlspecialchars($order['nama']); ?></td>
                                        <td><?= htmlspecialchars($order['telepon']); ?></td>
                                        <td>Rp <?= number_format($order['total'], 0, ',', '.'); ?></td>
                                        <td>
                                            <span class="badge <?= $statusClass ?>">
                                                <?= htmlspecialchars($order['status']); ?>
                                            </span>
                                        </td>
                                        <td><?= date('d-m-Y H:i', strtotime($order['tanggal'])); ?></td>
                                        <td>
                                            <a href="detail_order.php?id=<?= $order['id']; ?>" class="btn btn-primary">
                                                Detail
                                            </a>

                                            <form action="hapus_order.php" method="POST" style="display:inline;">
                                                <input type="hidden" name="id" value="<?= $order['id']; ?>">
                                                <button type="submit" class="btn btn-danger"
                                                    onclick="return confirm('Yakin ingin menghapus pesanan ini?')">
                                                    Hapus
                                                </button>
                                            </form>
                                        </td>
                                    </tr>

                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty">
                        <h3>Tidak ada pesanan ditemukan</h3>
                        <p>Coba gunakan kata kunci pencarian nomor invoice lainnya.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>