<?php
session_start();

$keranjang = $_SESSION['keranjang'] ?? [];

if (empty($keranjang)) {
    header("Location: keranjang.php");
    exit;
}

$nomor_admin = "6282280095223";

$total = 0;

foreach ($keranjang as $item) {
    $total += $item['harga'] * $item['qty'];
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f5f7fb;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, .05);
        }

        .produk {
            border-bottom: 1px solid #eee;
            padding: 10px 0;
        }

        .total-box {
            background: #e8f5e9;
            padding: 15px;
            border-radius: 10px;
            margin-top: 15px;
        }
    </style>
</head>

<body>

    <div class="container mt-5 mb-5">

        <div class="row">

            <div class="col-lg-7">

                <div class="card p-4">

                    <h3 class="mb-4">
                        Data Pemesan
                    </h3>

                    <form action="proses_checkout.php" method="POST">

                        <div class="mb-3">
                            <label class="form-label">
                                Nama Lengkap <span style="color:red">*</span>
                            </label>

                            <input type="text" name="nama" id="nama" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Nomor WhatsApp <span style="color:red">*</span>
                            </label>

                            <input type="text" name="telepon" id="telepon" class="form-control" maxlength="13" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Alamat Lengkap <span style="color:red">*</span>
                            </label>

                            <textarea name="alamat" id="alamat" class="form-control" rows="4" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Catatan Untuk Penjual
                            </label>

                            <textarea name="catatan" id="catatan" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="keranjang.php" class="btn btn-secondary btn-lg flex-fill">
                                Kembali
                            </a>
                            <button type="submit" class="btn btn-success btn-lg flex-fill">
                                Buat Pesanan
                            </button>
                        </div>

                    </form>

                </div>

            </div>

            <div class="col-lg-5">

                <div class="card p-4">

                    <h4 class="mb-3">
                        Ringkasan Pesanan
                    </h4>

                    <?php foreach ($keranjang as $item): ?>

                        <?php
                        $subtotal = $item['harga'] * $item['qty'];
                        ?>

                        <div class="produk">

                            <strong>
                                <?= htmlspecialchars($item['nama_produk']); ?>
                            </strong>

                            <br>

                            <?= $item['qty']; ?> x
                            Rp <?= number_format($item['harga'], 0, ',', '.'); ?>

                            <br>

                            <strong>
                                Rp <?= number_format($subtotal, 0, ',', '.'); ?>
                            </strong>

                        </div>

                    <?php endforeach; ?>

                    <div class="total-box">

                        <h5 class="mb-0">
                            Total :
                            Rp <?= number_format($total, 0, ',', '.'); ?>
                        </h5>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <script>

        const telepon = document.getElementById('telepon');

        telepon.addEventListener('input', function () {

            this.value = this.value.replace(/\D/g, '');

            if (this.value.length > 13) {
                this.value = this.value.slice(0, 13);
            }

        });


    </script>

</body>

</html>