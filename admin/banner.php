<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../config/koneksi.php';

$error = '';
$success = '';

// ✅ FIX 1: Auto-buat folder upload/banner/ jika belum ada
$uploadDir = __DIR__ . '/../upload/banner/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Hitung banner aktif
$jumlahBanner = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM banner WHERE aktif = 1")
)['total'];

// Tambah banner
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['gambar'])) {

    if ($jumlahBanner >= 3) {
        $error = 'Maksimal 3 banner aktif. Hapus banner lama terlebih dahulu.';

    } else {
        $file = $_FILES['gambar'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $error = 'Gagal menerima file. Coba lagi.';

        } elseif (!in_array($ext, $allowed)) {
            $error = 'Format file harus JPG, PNG, atau WEBP.';

        } elseif ($file['size'] > 2 * 1024 * 1024) {
            $error = 'Ukuran file maksimal 2MB.';

        } else {
            $namaFile = 'banner_' . time() . '_' . rand(100, 999) . '.' . $ext;
            $tujuan = $uploadDir . $namaFile; // ✅ FIX 2: pakai variable $uploadDir

            if (move_uploaded_file($file['tmp_name'], $tujuan)) {
                $urutan = intval($jumlahBanner) + 1;
                mysqli_query($conn, "INSERT INTO banner (gambar, urutan) VALUES ('$namaFile', $urutan)");
                $success = 'Banner berhasil ditambahkan!';
                $jumlahBanner++;
            } else {
                $error = 'Gagal memindahkan file. Periksa permission folder upload/banner/.';
            }
        }
    }
}

// Hapus banner
if (isset($_GET['hapus'])) {
    $hapusId = intval($_GET['hapus']);
    $row = mysqli_fetch_assoc(
        mysqli_query($conn, "SELECT gambar FROM banner WHERE id = $hapusId")
    );
    if ($row) {
        $filePath = $uploadDir . $row['gambar'];
        if (file_exists($filePath))
            unlink($filePath);
        mysqli_query($conn, "DELETE FROM banner WHERE id = $hapusId");
        header("Location: banner.php?success=hapus");
        exit;
    }
}

// Toggle aktif/nonaktif
if (isset($_GET['toggle'])) {
    $toggleId = intval($_GET['toggle']);
    mysqli_query($conn, "UPDATE banner SET aktif = NOT aktif WHERE id = $toggleId");
    header("Location: banner.php");
    exit;
}

$banners = mysqli_query($conn, "SELECT * FROM banner ORDER BY urutan ASC");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Banner</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
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

        .alert {
            padding: 14px 18px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 14px;
            font-weight: 600;
        }

        .alert-success {
            background: #ecfdf3;
            color: #027a48;
            border: 1px solid #d1fadf;
        }

        .alert-danger {
            background: #fff1f2;
            color: #be123c;
            border: 1px solid #fecdd3;
        }

        .card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, .05);
            padding: 25px;
            margin-bottom: 25px;
        }

        .card h2 {
            font-size: 18px;
            margin-bottom: 20px;
            color: #333;
        }

        .upload-area {
            border: 2px dashed #d1d5db;
            border-radius: 12px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: .2s;
        }

        .upload-area:hover {
            border-color: #198754;
            background: #f0fdf4;
        }

        .upload-area input {
            display: none;
        }

        .upload-area p {
            color: #6b7280;
            font-size: 14px;
            margin-top: 8px;
        }

        .preview-img {
            max-width: 100%;
            max-height: 180px;
            border-radius: 10px;
            margin-top: 15px;
            display: none;
        }

        .btn-upload {
            background: #198754;
            color: white;
            border: none;
            padding: 12px 28px;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 15px;
            transition: .2s;
        }

        .btn-upload:hover {
            background: #146c43;
        }

        .btn-upload:disabled {
            background: #9ca3af;
            cursor: not-allowed;
        }

        .quota {
            font-size: 13px;
            color: #6b7280;
            margin-top: 8px;
        }

        .quota span {
            font-weight: 700;
            color: #198754;
        }

        .banner-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }

        .banner-item {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
        }

        .banner-item img {
            width: 100%;
            height: 160px;
            object-fit: cover;
            display: block;
        }

        .banner-info {
            padding: 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .banner-info .urutan {
            font-size: 13px;
            color: #6b7280;
        }

        .badge {
            font-size: 12px;
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: 600;
        }

        .badge-aktif {
            background: #dcfce7;
            color: #166534;
        }

        .badge-nonaktif {
            background: #f3f4f6;
            color: #6b7280;
        }

        .banner-actions {
            padding: 0 14px 14px;
            display: flex;
            gap: 8px;
        }

        .btn-sm {
            padding: 7px 14px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: .2s;
            display: inline-block;
        }

        .btn-toggle {
            background: #f59e0b;
            color: white;
        }

        .btn-toggle:hover {
            background: #d97706;
        }

        .btn-hapus {
            background: #ef4444;
            color: white;
        }

        .btn-hapus:hover {
            background: #dc2626;
        }

        .empty {
            text-align: center;
            padding: 40px;
            color: #9ca3af;
            font-size: 15px;
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
    </style>
</head>

<body>
    <div class="wrapper">

        <?php include __DIR__ . '/sidebar.php';?>

        <main class="content">
            <div class="header">
                <h1>🖼️ Kelola Banner</h1>
                <div class="admin-name"><?= htmlspecialchars($_SESSION['admin_username']); ?></div>
            </div>

            <?php if ($success): ?>
                <div class="alert alert-success">✅ <?= $success; ?></div>
            <?php endif; ?>
            <?php if (isset($_GET['success']) && $_GET['success'] === 'hapus'): ?>
                <div class="alert alert-success">✅ Banner berhasil dihapus.</div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-danger">⚠️ <?= $error; ?></div>
            <?php endif; ?>

            <!-- Form Upload -->
            <div class="card">
                <h2>Tambah Banner Baru</h2>
                <form method="POST" enctype="multipart/form-data">
                    <div class="upload-area" onclick="document.getElementById('inputGambar').click()">
                        <input type="file" id="inputGambar" name="gambar" accept="image/*"
                            onchange="previewBanner(this)">
                        <div style="font-size: 40px;">🖼️</div>
                        <p>Klik untuk pilih gambar banner (landscape, maks. 2MB)</p>
                        <p>Format: JPG, PNG, WEBP</p>
                        <img id="previewImg" class="preview-img" alt="Preview">
                    </div>
                    <div class="quota">
                        Slot banner: <span><?= $jumlahBanner; ?>/3</span> terpakai
                    </div>
                    <br>
                    <button type="submit" class="btn-upload" <?= $jumlahBanner >= 3 ? 'disabled' : ''; ?>>
                        Upload Banner
                    </button>
                </form>
            </div>

            <!-- Daftar Banner -->
            <div class="card">
                <h2>Banner Saat Ini</h2>
                <?php if (mysqli_num_rows($banners) === 0): ?>
                    <div class="empty">Belum ada banner. Upload banner pertama kamu!</div>
                <?php else: ?>
                    <div class="banner-grid">
                        <?php while ($b = mysqli_fetch_assoc($banners)): ?>
                            <div class="banner-item">
                                <img src="../upload/banner/<?= htmlspecialchars($b['gambar']); ?>" alt="Banner">
                                <div class="banner-info">
                                    <span class="urutan">Urutan #<?= $b['urutan']; ?></span>
                                    <span class="badge <?= $b['aktif'] ? 'badge-aktif' : 'badge-nonaktif'; ?>">
                                        <?= $b['aktif'] ? 'Aktif' : 'Nonaktif'; ?>
                                    </span>
                                </div>
                                <div class="banner-actions">
                                    <a href="banner.php?toggle=<?= $b['id']; ?>" class="btn-sm btn-toggle">
                                        <?= $b['aktif'] ? 'Nonaktifkan' : 'Aktifkan'; ?>
                                    </a>
                                    <a href="banner.php?hapus=<?= $b['id']; ?>" class="btn-sm btn-hapus"
                                        onclick="return confirm('Hapus banner ini?')">Hapus</a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php endif; ?>
            </div>

        </main>
    </div>

    <script>
        function previewBanner(input) {
            const img = document.getElementById('previewImg');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = e => {
                    img.src = e.target.result;
                    img.style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>

</html>