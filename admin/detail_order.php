<?php
require_once 'auth.php';
require_once __DIR__ . '/../config/koneksi.php';

if (!isset($_GET['id'])) {
    header("Location: orders.php");
    exit;
}

$id = (int) $_GET['id'];

/*
|--------------------------------------------------------------------------
| Update Status
|--------------------------------------------------------------------------
*/
if (isset($_POST['update_status'])) {

    $status = mysqli_real_escape_string(
        $conn,
        $_POST['status']
    );

    $update = mysqli_query(
        $conn,
        "UPDATE orders
         SET status='$status'
         WHERE id='$id'"
    );
    if ($update) {
        echo "<script>
                alert('Status pesanan berhasil diperbarui!');
                window.location.href = 'orders.php';
              </script>";
        exit;
    }
}


/*
|--------------------------------------------------------------------------
| Ambil Data Order
|--------------------------------------------------------------------------
*/
$order = mysqli_query(
    $conn,
    "SELECT * FROM orders
     WHERE id='$id'
     LIMIT 1"
);

if (mysqli_num_rows($order) == 0) {
    die("Pesanan tidak ditemukan");
}

$data = mysqli_fetch_assoc($order);

/*
|--------------------------------------------------------------------------
| Ambil Detail Produk
|--------------------------------------------------------------------------
*/
$detail = mysqli_query(
    $conn,
    "SELECT * FROM order_detail
     WHERE order_id='$id'"
);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Detail Pesanan</title>

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
        }

        .menu a:hover {
            background: rgba(255, 255, 255, .15);
        }

        .content {
            flex: 1;
            padding: 30px;
        }

        .card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, .05);
        }

        h2 {
            margin-bottom: 20px;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #198754;
            color: white;
            padding: 12px;
            text-align: left;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }

        .info-table th {
            width: 220px;
        }

        .badge {
            padding: 8px 15px;
            border-radius: 50px;
            color: white;
            font-size: 14px;
        }

        .menunggu {
            background: #ffc107;
            color: black;
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

        .btn {
            border: none;
            padding: 12px 18px;
            border-radius: 8px;
            cursor: pointer;
            color: white;
            text-decoration: none;
            font-weight: 600;
        }

        .btn-success {
            background: #198754;
        }

        .btn-secondary {
            background: #6c757d;
        }

        select {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        .alert {
            background: #d1e7dd;
            color: #0f5132;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        /* Container utama formulir status */
        .form-status-wrapper {
            background: #ffffff;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
        }

        /* Menyusun elemen secara horizontal */
        .form-group-inline {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
        }

        /* Wadah kustom untuk dropdown select */
        .select-container {
            flex: 1;
            min-width: 200px;
        }

        /* Desain dropdown modern */
        .form-select-custom {
            width: 100%;
            padding: 11px 15px;
            font-size: 14px;
            font-weight: 500;
            color: #333333;
            background-color: #ffffff;
            border: 1.5px solid #dddddd;
            border-radius: 8px;
            cursor: pointer;
            outline: none;
            transition: all 0.3s ease;
        }

        .form-select-custom:focus {
            border-color: #198754;
            box-shadow: 0 0 0 3px rgba(25, 135, 84, 0.15);
        }

        /* Utilitas Tombol Baru */
        .btn-success {
            background: #198754;
            border: none;
            cursor: pointer;
        }

        .btn-success:hover {
            background: #157347;
        }

        .btn-secondary {
            background: #6c757d;
        }

        .btn-secondary:hover {
            background: #5c636a;
        }

        /* Responsif untuk layar HP */
        @media (max-width: 576px) {
            .form-group-inline {
                flex-direction: column;
                align-items: stretch;
            }

            .select-container {
                width: 100%;
            }

            .form-group-inline .btn {
                text-align: center;
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="wrapper">

        <?php include 'sidebar.php'; ?>

        <div class="content">
            <?php if (isset($_GET['success'])): ?>
                <div class="alert">
                    Status pesanan berhasil diperbarui.
                </div>
            <?php endif; ?>

            <div class="card">

                <h2>
                    Detail Pesanan
                </h2>

                <table class="info-table">

                    <tr>
                        <th>Nomor Pesanan</th>
                        <td><?= htmlspecialchars($data['nomor_pesanan']); ?></td>
                    </tr>

                    <tr>
                        <th>Nama</th>
                        <td><?= htmlspecialchars($data['nama']); ?></td>
                    </tr>

                    <tr>
                        <th>WhatsApp</th>
                        <td><?= htmlspecialchars($data['telepon']); ?></td>
                    </tr>

                    <tr>
                        <th>Alamat</th>
                        <td><?= nl2br(htmlspecialchars($data['alamat'])); ?></td>
                    </tr>

                    <tr>
                        <th>Catatan</th>
                        <td>
                            <?= !empty($data['catatan'])
                                ? htmlspecialchars($data['catatan'])
                                : '-'; ?>
                        </td>
                    </tr>

                    <tr>
                        <th>Total</th>
                        <td>
                            Rp <?= number_format($data['total'], 0, ',', '.'); ?>
                        </td>
                    </tr>

                    <tr>
                        <th>Tanggal</th>
                        <td><?= $data['tanggal']; ?></td>
                    </tr>

                    <tr>
                        <th>Status Saat Ini</th>
                        <td>

                            <?php
                            $class = strtolower($data['status']);
                            ?>

                            <span class="badge <?= $class ?>">
                                <?= $data['status']; ?>
                            </span>

                        </td>
                    </tr>

                </table>

            </div>

            <div class="card">

                <h2>
                    Produk Yang Dipesan
                </h2>

                <table>

                    <thead>

                        <tr>
                            <th>Produk</th>
                            <th>Harga</th>
                            <th>Qty</th>
                            <th>Subtotal</th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php while ($item = mysqli_fetch_assoc($detail)): ?>

                            <tr>

                                <td>
                                    <?= htmlspecialchars($item['nama_produk']); ?>
                                </td>

                                <td>
                                    Rp <?= number_format($item['harga'], 0, ',', '.'); ?>
                                </td>

                                <td>
                                    <?= $item['qty']; ?>
                                </td>

                                <td>
                                    Rp <?= number_format($item['subtotal'], 0, ',', '.'); ?>
                                </td>

                            </tr>

                        <?php endwhile; ?>

                    </tbody>

                </table>

            </div>

            <div class="card">

                <h2>
                    Update Status Pesanan
                </h2>

                <form method="POST" class="form-status-wrapper">
                    <div class="form-group-inline">
                        <div class="select-container">
                            <select name="status" class="form-select-custom" required>
                                <option value="Menunggu" <?= $data['status'] == 'Menunggu' ? 'selected' : ''; ?>>Menunggu
                                </option>
                                <option value="Diproses" <?= $data['status'] == 'Diproses' ? 'selected' : ''; ?>>Diproses
                                </option>
                                <option value="Selesai" <?= $data['status'] == 'Selesai' ? 'selected' : ''; ?>>Selesai
                                </option>
                                <option value="Dibatalkan" <?= $data['status'] == 'Dibatalkan' ? 'selected' : ''; ?>>
                                    Dibatalkan</option>
                            </select>
                        </div>
                        <button type="submit" name="update_status" class="btn btn-success">
                            Simpan Status
                        </button>

                        <a href="orders.php" class="btn btn-secondary">
                            Kembali
                        </a>
                    </div>
                </form>



            </div>

        </div>

    </div>

</body>

</html>