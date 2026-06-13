<?php
session_start();

require 'config/koneksi.php';

$keranjang = $_SESSION['keranjang'] ?? [];

$stok_produk = [];
if (!empty($keranjang)) {
    $ids = implode(',', array_map('intval', array_keys($keranjang)));
    $result = mysqli_query($conn, "SELECT id, stok FROM produk WHERE id IN ($ids)");
    while ($row = mysqli_fetch_assoc($result)) {
        $stok_produk[$row['id']] = $row['stok'];
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f5f7fb;
        }

        .produk-img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 10px;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, .05);
        }
    </style>
</head>

<body>

    <div class="container mt-5">
        <div class="card p-4">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>🛒 Keranjang Belanja</h2>
                <a href="index.php" class="btn btn-secondary">Lanjut Belanja</a>
            </div>

            <?php if (empty($keranjang)): ?>
                <div class="alert alert-warning mb-0">Keranjang masih kosong.</div>
            <?php else: ?>

                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="table-success">
                            <tr>
                                <th>Foto</th>
                                <th>Produk</th>
                                <th>Harga</th>
                                <th>Qty</th>
                                <th>Subtotal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $total = 0;
                            foreach ($keranjang as $item):
                                $subtotal = $item['harga'] * $item['qty'];
                                $total += $subtotal;
                                $stok = $stok_produk[$item['id']] ?? 0;
                                ?>
                                <tr>
                                    <td>
                                        <img src="upload/<?= htmlspecialchars($item['foto']); ?>" class="produk-img" alt="">
                                    </td>
                                    <td><?= htmlspecialchars($item['nama_produk']); ?></td>
                                    <td>Rp <?= number_format($item['harga'], 0, ',', '.'); ?></td>
                                    <td>
                                        <div class="d-flex align-items-center justify-content-center gap-2">
                                            <button class="btn btn-outline-secondary btn-sm btn-qty"
                                                data-id="<?= $item['id']; ?>" data-action="kurang">−</button>

                                            <span id="qty-<?= $item['id']; ?>" class="fw-semibold px-1">
                                                <?= $item['qty']; ?>
                                            </span>

                                            <button class="btn btn-outline-secondary btn-sm btn-qty"
                                                data-id="<?= $item['id']; ?>" data-action="tambah" data-stok="<?= $stok; ?>"
                                                <?= $item['qty'] >= $stok ? 'disabled' : ''; ?>>+</button>
                                        </div>
                                    </td>
                                    <td id="subtotal-<?= $item['id']; ?>">
                                        Rp <?= number_format($subtotal, 0, ',', '.'); ?>
                                    </td>
                                    <td>
                                        <a href="hapus_keranjang.php?id=<?= $item['id']; ?>" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Hapus produk dari keranjang?')">Hapus</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-end">Total Belanja</th>
                                <th id="total-belanja">
                                    Rp <?= number_format($total, 0, ',', '.'); ?>
                                </th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="text-end">
                    <a href="checkout.php" class="btn btn-success btn-lg">Checkout</a>
                </div>

            <?php endif; ?>
        </div>
    </div>

    <script>
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.btn-qty');
            if (!btn) return;

            const id = btn.dataset.id;
            const action = btn.dataset.action;
            const btnTambah = document.querySelector(`.btn-qty[data-id="${id}"][data-action="tambah"]`);
            const stok = parseInt(btnTambah.dataset.stok);
            const qtyEl = document.getElementById('qty-' + id);
            const qtyNow = parseInt(qtyEl.textContent.trim());

            if (action === 'tambah' && qtyNow >= stok) {
                alert('Stok tidak mencukupi! Stok tersisa: ' + stok);
                return;
            }

            fetch('update_keranjang.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id=${encodeURIComponent(id)}&action=${encodeURIComponent(action)}`
            })
                .then(r => r.json())
                .then(data => {
                    if (data.hapus) {
                        location.reload();
                        return;
                    }
                    if (data.error) {
                        alert(data.pesan ?? 'Terjadi kesalahan');
                        return;
                    }

                    qtyEl.textContent = data.qty;
                    document.getElementById('subtotal-' + id).textContent = 'Rp ' + data.subtotal;
                    document.getElementById('total-belanja').textContent = 'Rp ' + data.total;

                    btnTambah.disabled = parseInt(data.qty) >= stok;
                });
        });
    </script>

</body>

</html>