<?php
session_start();
include 'config\koneksi.php';

$jumlahKeranjang = 0;
if (isset($_SESSION['keranjang'])) {
    foreach ($_SESSION['keranjang'] as $item) {
        $jumlahKeranjang += $item['qty'];
    }
}

$cari = $_GET['cari'] ?? '';
$cari = mysqli_real_escape_string($conn, $cari);

if ($cari != '') {
    $query = mysqli_query($conn, "SELECT * FROM produk WHERE nama_produk LIKE '%$cari%' ORDER BY id DESC");
} else {
    $query = mysqli_query($conn, "SELECT * FROM produk ORDER BY id DESC");
}

if (!$query)
    die(mysqli_error($conn));

$bannerQuery = mysqli_query($conn, "SELECT * FROM banner WHERE aktif = 1 ORDER BY urutan ASC LIMIT 3");
$dataBanner = [];
if ($bannerQuery) {
    while ($rowBanner = mysqli_fetch_assoc($bannerQuery)) {
        $dataBanner[] = $rowBanner;
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Serba Serbi Mart</title>
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
            background: #f8f9fa;
            color: #333;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: auto;
        }

        /* NAVBAR */
        .navbar {
            background: #198754;
            padding: 15px 0;
            position: sticky;
            top: 0;
            z-index: 999;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .1);
        }

        .nav-wrapper {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar-brand {
            color: white;
            text-decoration: none;
            font-size: 24px;
            font-weight: 700;
        }

        .nav-menu-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn-keranjang {
            background: #ffc107;
            color: #000;
            text-decoration: none;
            padding: 10px 18px;
            border-radius: 30px;
            font-weight: 600;
            transition: background 0.2s;
        }

        .btn-keranjang:hover {
            background: #e0a800;
        }

        /* MAIN */
        .main-content {
            padding: 40px 0;
        }

        .section-title {
            font-size: 28px;
            margin-bottom: 20px;
        }

        .search-box {
            margin-bottom: 35px;
        }

        .search-outer {
            max-width: 700px;
            margin: auto;
            position: relative;
        }

        .search-bar {
            display: flex;
            align-items: center;
            gap: 8px;
            background: white;
            border: 1.5px solid #dee2e6;
            border-radius: 12px;
            padding: 10px 14px;
            transition: border-color .2s, box-shadow .2s;
        }

        .search-bar:focus-within {
            border-color: #198754;
            box-shadow: 0 0 0 3px rgba(25, 135, 84, .12);
        }

        .search-bar input {
            flex: 1;
            border: none;
            outline: none;
            background: transparent;
            font-size: 15px;
            color: #333;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .search-bar input::placeholder {
            color: #adb5bd;
        }

        .search-btn-new {
            background: #198754;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 8px 18px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background .15s;
        }

        .search-btn-new:hover {
            background: #146c43;
        }

        .clear-btn {
            display: none;
            align-items: center;
            justify-content: center;
            background: none;
            border: none;
            cursor: pointer;
            color: #999;
            width: 24px;
            height: 24px;
            border-radius: 50%;
        }

        /* GRID PRODUK */
        .grid-produk {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 25px;
        }

        .card-produk {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, .08);
            transition: .3s;
        }

        .card-produk:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, .12);
        }

        .card-habis {
            opacity: .8;
        }

        .img-container {
            position: relative;
            height: 220px;
            overflow: hidden;
            background: #eee;
            cursor: pointer;
        }

        .img-produk {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .badge-stok {
            position: absolute;
            top: 10px;
            right: 10px;
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .stok-ada {
            background: #198754;
        }

        .stok-habis {
            background: #dc3545;
        }

        .card-body {
            padding: 18px;
        }

        .nama-produk {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 10px;
            min-height: 48px;
            cursor: pointer;
        }

        .nama-produk:hover {
            color: #198754;
        }

        .harga {
            color: #198754;
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .stok {
            font-size: 13px;
            color: #666;
            margin-bottom: 15px;
        }

        .btn-beli {
            display: block;
            width: 100%;
            text-align: center;
            text-decoration: none;
            background: #198754;
            color: white;
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
            border: none;
            cursor: pointer;
        }

        .btn-beli:hover {
            background: #146c43;
        }

        .btn-disabled {
            background: #6c757d;
            cursor: not-allowed;
        }

        .produk-kosong {
            grid-column: 1/-1;
            text-align: center;
            padding: 50px;
        }

        .qty-box {
            margin-bottom: 12px;
        }

        .qty-box label {
            display: block;
            font-size: 13px;
            margin-bottom: 5px;
            color: #666;
        }

        .qty-input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 10px;
            outline: none;
            font-size: 14px;
        }

        .qty-input:focus {
            border-color: #198754;
        }

        /* BANNER */
        .banner-wrapper {
            width: 100%;
            margin: 0 0 30px;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, .12);
            position: relative;
            background: #e9ecef;
        }

        .banner-track {
            display: flex;
            transition: transform 0.6s cubic-bezier(.77, 0, .18, 1);
            will-change: transform;
        }

        .banner-slide {
            min-width: 100%;
            height: 280px;
        }

        .banner-slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .banner-empty {
            width: 100%;
            height: 180px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #adb5bd;
            font-size: 15px;
            background: #f1f3f5;
        }

        .banner-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0, 0, 0, .35);
            color: white;
            border: none;
            width: 38px;
            height: 38px;
            border-radius: 50%;
            font-size: 18px;
            cursor: pointer;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background .2s;
        }

        .banner-btn:hover {
            background: rgba(0, 0, 0, .6);
        }

        .banner-btn.prev {
            left: 12px;
        }

        .banner-btn.next {
            right: 12px;
        }

        .banner-dots {
            position: absolute;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 7px;
        }

        .banner-dots span {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .5);
            cursor: pointer;
            transition: background .3s, transform .3s;
        }

        .banner-dots span.active {
            background: white;
            transform: scale(1.3);
        }

        /* MODAL */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal-overlay.open {
            display: flex;
        }

        .modal-box {
            background: white;
            border-radius: 20px;
            width: 100%;
            max-width: 780px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, .25);
            animation: modalIn .25s ease;
        }

        @keyframes modalIn {
            from {
                opacity: 0;
                transform: translateY(30px) scale(.97);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .modal-img {
            width: 100%;
            height: 320px;
            object-fit: cover;
            border-radius: 20px 20px 0 0;
            display: block;
        }

        .modal-body {
            padding: 28px;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 16px;
            gap: 12px;
        }

        .modal-nama {
            font-size: 22px;
            font-weight: 700;
            color: #222;
            line-height: 1.3;
        }

        .modal-close {
            background: #f1f3f5;
            border: none;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            font-size: 18px;
            cursor: pointer;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background .2s;
        }

        .modal-close:hover {
            background: #e9ecef;
        }

        .modal-harga {
            font-size: 26px;
            font-weight: 700;
            color: #198754;
            margin-bottom: 12px;
        }

        .modal-stok-badge {
            display: inline-block;
            padding: 5px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 18px;
        }

        .modal-stok-badge.ada {
            background: #d1fae5;
            color: #065f46;
        }

        .modal-stok-badge.habis {
            background: #fee2e2;
            color: #991b1b;
        }

        .modal-divider {
            height: 1px;
            background: #f0f0f0;
            margin: 18px 0;
        }

        .modal-desc-label {
            font-size: 13px;
            font-weight: 600;
            color: #999;
            text-transform: uppercase;
            letter-spacing: .05em;
            margin-bottom: 8px;
        }

        .modal-desc {
            font-size: 15px;
            color: #555;
            line-height: 1.7;
        }

        .modal-form {
            margin-top: 20px;
        }

        .modal-form .qty-box label {
            font-size: 14px;
            font-weight: 600;
            color: #444;
        }

        @media(max-width: 768px) {
            .banner-slide {
                height: 180px;
            }

            .modal-img {
                height: 220px;
            }

            .modal-nama {
                font-size: 18px;
            }

            .modal-harga {
                font-size: 22px;
            }
        }

        .navbar-brand {
            color: white;
            text-decoration: none;
            font-size: 24px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .navbar-brand img {
            height: 55px;
            width: auto;
            object-fit: contain;
            margin-top: -8px;
            margin-bottom: -8px;
        }
    </style>
</head>

<body>

    <nav class="navbar">
        <div class="container">
            <div class="nav-wrapper">
                <a href="index.php" class="navbar-brand">
                    <img src="upload/logo.png" alt="Logo">
                    Serba Serbi Mart
                </a>
                <div class="nav-menu-right">
                    <a href="keranjang.php" class="btn-keranjang">
                        🛒 Keranjang (<?= $jumlahKeranjang; ?>)
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main class="main-content">
        <div class="container">

            <!-- BANNER -->
            <div class="banner-wrapper" id="bannerWrapper">
                <?php if (empty($dataBanner)): ?>
                    <div class="banner-empty">Belum ada banner promosi</div>
                <?php else: ?>
                    <div class="banner-track" id="bannerTrack">
                        <?php foreach ($dataBanner as $rowBanner): ?>
                            <div class="banner-slide">
                                <img src="upload/banner/<?= htmlspecialchars($rowBanner['gambar']); ?>" alt="Banner">
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if (count($dataBanner) > 1): ?>
                        <button class="banner-btn prev" onclick="bannerMove(-1)">&#8249;</button>
                        <button class="banner-btn next" onclick="bannerMove(1)">&#8250;</button>
                        <div class="banner-dots" id="bannerDots">
                            <?php foreach ($dataBanner as $i => $b): ?>
                                <span class="<?= $i === 0 ? 'active' : ''; ?>" onclick="bannerGo(<?= $i; ?>)"></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <!-- SEARCH -->
            <div class="search-box">
                <form method="GET">
                    <div class="search-outer">
                        <div class="search-bar">
                            <span class="icon-search">🔍</span>
                            <input type="text" name="cari" placeholder="Cari produk yang Anda inginkan..."
                                value="<?= htmlspecialchars($cari); ?>">
                            <?php if (!empty($cari)): ?>
                                <a href="index.php" class="clear-btn" style="display:flex;text-decoration:none;">✕</a>
                            <?php endif; ?>
                            <button type="submit" class="search-btn-new">Cari</button>
                        </div>
                    </div>
                </form>
                <h2 class="section-title" style="margin-top:25px;">Daftar Produk</h2>
            </div>

            <!-- GRID PRODUK -->
            <div class="grid-produk">
                <?php if (mysqli_num_rows($query) > 0): ?>
                    <?php while ($produk = mysqli_fetch_assoc($query)): ?>
                        <?php
                        $stok = $produk['stok'];
                        $foto = "upload/" . $produk['foto'];
                        if (!file_exists($foto))
                            $foto = "upload/no-image.png";
                        $desc = !empty($produk['deskripsi']) ? $produk['deskripsi'] : 'Tidak ada deskripsi.';
                        // Encode data untuk modal
                        $modalData = htmlspecialchars(json_encode([
                            'nama' => $produk['nama_produk'],
                            'harga' => number_format($produk['harga'], 0, ',', '.'),
                            'stok' => $stok,
                            'foto' => $foto,
                            'deskripsi' => $desc,
                            'id' => $produk['id'],
                        ]), ENT_QUOTES);
                        ?>
                        <div class="card-produk <?= $stok <= 0 ? 'card-habis' : ''; ?>">
                            <!-- Klik gambar atau nama → buka modal -->
                            <div class="img-container" onclick='bukaModal(<?= $modalData; ?>)'>
                                <span class="badge-stok <?= $stok > 0 ? 'stok-ada' : 'stok-habis'; ?>">
                                    <?= $stok > 0 ? 'Tersedia' : 'Habis'; ?>
                                </span>
                                <img loading="lazy" src="<?= $foto; ?>" alt="<?= htmlspecialchars($produk['nama_produk']); ?>"
                                    class="img-produk">
                            </div>
                            <div class="card-body">
                                <h5 class="nama-produk" onclick='bukaModal(<?= $modalData; ?>)'>
                                    <?= htmlspecialchars($produk['nama_produk']); ?>
                                </h5>
                                <p class="harga">Rp <?= number_format($produk['harga'], 0, ',', '.'); ?></p>
                                <p class="stok">Sisa Stok: <strong><?= $stok; ?></strong></p>
                                <?php if ($stok > 0): ?>
                                    <form action="tambah_keranjang.php" method="POST">
                                        <input type="hidden" name="id" value="<?= $produk['id']; ?>">
                                        <div class="qty-box">
                                            <label>Jumlah</label>
                                            <input type="number" name="qty" min="1" max="<?= $stok; ?>" value="1" class="qty-input">
                                        </div>
                                        <button type="submit" class="btn-beli">+ Tambah Keranjang</button>
                                    </form>
                                <?php else: ?>
                                    <button class="btn-beli btn-disabled" disabled>Stok Habis</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="produk-kosong">
                        <h3>Belum ada produk.</h3>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </main>

    <!-- MODAL DETAIL PRODUK -->
    <div class="modal-overlay" id="modalOverlay" onclick="tutupModalLuar(event)">
        <div class="modal-box" id="modalBox">
            <img src="" alt="" class="modal-img" id="modalImg">
            <div class="modal-body">
                <div class="modal-header">
                    <h2 class="modal-nama" id="modalNama"></h2>
                    <button class="modal-close" onclick="tutupModal()">✕</button>
                </div>
                <div class="modal-harga" id="modalHarga"></div>
                <span class="modal-stok-badge" id="modalStokBadge"></span>
                <div class="modal-divider"></div>
                <div class="modal-desc-label">Deskripsi Produk</div>
                <p class="modal-desc" id="modalDesc"></p>
                <div class="modal-form" id="modalForm"></div>
            </div>
        </div>
    </div>

    <script>
        // ===== BANNER =====
        (function () {
            const track = document.getElementById('bannerTrack');
            const dots = document.querySelectorAll('#bannerDots span');
            if (!track) return;
            const total = track.children.length;
            let current = 0, timer;

            function goTo(i) {
                current = (i + total) % total;
                track.style.transform = `translateX(-${current * 100}%)`;
                dots.forEach((d, j) => d.classList.toggle('active', j === current));
            }
            function startAuto() { clearInterval(timer); timer = setInterval(() => goTo(current + 1), 4000); }

            window.bannerMove = (d) => { goTo(current + d); startAuto(); };
            window.bannerGo = (i) => { goTo(i); startAuto(); };
            track.parentElement.addEventListener('mouseenter', () => clearInterval(timer));
            track.parentElement.addEventListener('mouseleave', startAuto);
            if (total > 1) startAuto();
        })();

        // ===== MODAL =====
        function bukaModal(data) {
            document.getElementById('modalImg').src = data.foto;
            document.getElementById('modalImg').alt = data.nama;
            document.getElementById('modalNama').textContent = data.nama;
            document.getElementById('modalHarga').textContent = 'Rp ' + data.harga;
            document.getElementById('modalDesc').textContent = data.deskripsi;

            const badge = document.getElementById('modalStokBadge');
            if (data.stok > 0) {
                badge.textContent = '✓ Tersedia · Sisa ' + data.stok + ' item';
                badge.className = 'modal-stok-badge ada';
            } else {
                badge.textContent = '✕ Stok Habis';
                badge.className = 'modal-stok-badge habis';
            }

            const formEl = document.getElementById('modalForm');
            if (data.stok > 0) {
                formEl.innerHTML = `
            <form action="tambah_keranjang.php" method="POST">
                <input type="hidden" name="id" value="${data.id}">
                <div class="qty-box">
                    <label>Jumlah</label>
                    <input type="number" name="qty" min="1" max="${data.stok}" value="1" class="qty-input">
                </div>
                <button type="submit" class="btn-beli">+ Tambah Keranjang</button>
            </form>`;
            } else {
                formEl.innerHTML = `<button class="btn-beli btn-disabled" disabled>Stok Habis</button>`;
            }

            document.getElementById('modalOverlay').classList.add('open');
            document.body.style.overflow = 'hidden';
        }

        function tutupModal() {
            document.getElementById('modalOverlay').classList.remove('open');
            document.body.style.overflow = '';
        }

        function tutupModalLuar(e) {
            if (e.target === document.getElementById('modalOverlay')) tutupModal();
        }

        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') tutupModal(); });
    </script>
</body>

</html>