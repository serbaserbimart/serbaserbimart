<?php
session_start();
require_once 'config/koneksi.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = (int) $_GET['id'];

$order = mysqli_query(
    $conn,
    "SELECT * FROM orders WHERE id='$id' LIMIT 1"
);

if (mysqli_num_rows($order) == 0) {
    die("Pesanan tidak ditemukan");
}

$data = mysqli_fetch_assoc($order);

$detail = mysqli_query(
    $conn,
    "SELECT * FROM order_detail
     WHERE order_id='$id'"
);

$nomor_admin = "6285169856603";
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Pesanan Berhasil</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f5f7fb;
        }

        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, .05);
        }

        .success-icon {
            font-size: 70px;
        }

        .badge-status {
            background: #198754;
            color: white;
            padding: 8px 15px;
            border-radius: 50px;
        }

        .table th {
            background: #198754;
            color: white;
        }
    </style>

</head>

<body>

    <div class="container py-5">

        <div class="row justify-content-center">

            <div class="col-lg-10">

                <div class="card p-4">

                    <div class="text-center mb-4">

                        <div class="success-icon">
                            ✅
                        </div>

                        <h2 class="mt-3">
                            Pesanan Berhasil Dibuat
                        </h2>

                        <p class="text-muted">
                            Terima kasih telah berbelanja.
                        </p>

                    </div>

                    <div class="row">

                        <div class="col-md-6">

                            <h5>Data Pemesan</h5>

                            <table class="table">

                                <tr>
                                    <th width="35%">Nomor Pesanan</th>
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
                                        <?= $data['catatan'] != '' ? htmlspecialchars($data['catatan']) : '-'; ?>
                                    </td>
                                </tr>

                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <span class="badge-status">
                                            <?= htmlspecialchars($data['status']); ?>
                                        </span>
                                    </td>
                                </tr>

                            </table>

                        </div>

                        <div class="col-md-6">

                            <h5>Ringkasan Pembayaran</h5>

                            <div class="alert alert-success">

                                <strong>
                                    Total Belanja
                                </strong>

                                <h3 class="mt-2">
                                    Rp <?= number_format($data['total'], 0, ',', '.'); ?>
                                </h3>

                            </div>

                        </div>

                    </div>

                    <hr>

                    <h5>Detail Produk</h5>

                    <div class="table-responsive">

                        <table class="table table-bordered">

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

                    <?php
                    // Reset pointer $detail agar bisa di-loop lagi
                    mysqli_data_seek($detail, 0);

                    $pesan = "*PESANAN BARU*\n";
                    $pesan .= "━━━━━━━━━━━━━━━━━━\n\n";

                    $pesan .= "*Nomor Pesanan:* " . $data['nomor_pesanan'] . "\n\n";

                    $pesan .= "*Data Pemesan*\n";
                    $pesan .= "Nama       : " . $data['nama'] . "\n";
                    $pesan .= "WhatsApp   : " . $data['telepon'] . "\n";
                    $pesan .= "Alamat     : " . $data['alamat'] . "\n";

                    if ($data['catatan'] != '') {
                        $pesan .= "Catatan    : " . $data['catatan'] . "\n";
                    }

                    $pesan .= "*Detail Pesanan*\n";
                    $pesan .= "━━━━━━━━━━━━━━━━━━\n";

                    $no = 1;
                    while ($item = mysqli_fetch_assoc($detail)) {
                        $pesan .= $no . ". " . $item['nama_produk'] . "\n";
                        $pesan .= "   Harga  : Rp " . number_format($item['harga'], 0, ',', '.') . "\n";
                        $pesan .= "   Qty    : " . $item['qty'] . " pcs\n";
                        $pesan .= "   Sub    : Rp " . number_format($item['subtotal'], 0, ',', '.') . "\n\n";
                        $no++;
                    }

                    $pesan .= "━━━━━━━━━━━━━━━━━━\n";
                    $pesan .= "*TOTAL : Rp " . number_format($data['total'], 0, ',', '.') . "*\n\n";
                    $pesan .= "Mohon segera dikonfirmasi. Terima kasih!";

                    $wa = "https://wa.me/" . $nomor_admin . "?text=" . urlencode($pesan);
                    ?>

                    <div class="text-center mt-4">

                        <a href="<?= $wa; ?>" target="_blank" class="btn btn-success btn-lg">

                            Kirim ke WhatsApp Admin

                        </a>

                        <a href="index.php" class="btn btn-secondary btn-lg">

                            Kembali Belanja

                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>

</body>

</html>