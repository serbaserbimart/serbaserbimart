<?php
require_once 'auth.php';
require_once __DIR__ . '/../config/koneksi.php';

$pesan = '';

if (isset($_POST['simpan'])) {

    $nama_produk = mysqli_real_escape_string($conn, trim($_POST['nama_produk']));
    $harga = (int) $_POST['harga'];
    $stok = (int) $_POST['stok'];
    $deskripsi = mysqli_real_escape_string($conn, trim($_POST['deskripsi']));

    /*
    |--------------------------------------------------------------------------
    | Upload Foto
    |--------------------------------------------------------------------------
    */
    $foto = $_FILES['foto']['name'];
    $tmp = $_FILES['foto']['tmp_name'];

    if ($foto == '') {
        $pesan = "Foto produk wajib diupload.";
    } else {
        $ext = strtolower(pathinfo($foto, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($ext, $allowed)) {
            $pesan = "Format gambar harus JPG, PNG, atau WEBP.";
        } else {
            $namaBaru = time() . '_' . rand(1000, 9999) . '.' . $ext;
            $folder = "../upload/";

            if (!is_dir($folder)) {
                mkdir($folder, 0777, true);
            }

            if (move_uploaded_file($tmp, $folder . $namaBaru)) {
                $insert = mysqli_query(
                    $conn,
                    "INSERT INTO produk (nama_produk, harga, stok, foto, deskripsi) 
                     VALUES ('$nama_produk', '$harga', '$stok', '$namaBaru', '$deskripsi')"
                );

                if ($insert) {
                    // DIUBAH: Otomatis kembali ke dashboard.php membawa status sukses
                    header("Location: dashboard.php?success=1");
                    exit;
                } else {
                    $pesan = "Gagal menyimpan ke database: " . mysqli_error($conn);
                }
            } else {
                $pesan = "Gagal mengunggah berkas foto.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk Baru - Admin</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <style>
        /* ==========================================================================
   1. RESET DAN DASAR (GLOBAL STYLES)
   ========================================================================== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background: #f4f6f9;
            color: #212529;
        }

        /* ==========================================================================
   2. TATA LETAK UTAMA (LAYOUT SYSTEM)
   ========================================================================== */
        .wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* Menu Samping (Sidebar) */
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
            transition: background 0.3s ease;
        }

        .menu a:hover {
            background: rgba(255, 255, 255, .15);
        }

        .menu a.active {
            background: rgba(255, 255, 255, .2);
            font-weight: 600;
        }

        /* Area Konten Utama */
        .content {
            flex: 1;
            padding: 40px;

        }

        /* Container Formulir (Berada di dalam .content) */
        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
        }

        /* ==========================================================================
   3. KOMPONEN KARTU & FORMULIR (CARDS & FORMS)
   ========================================================================== */
        .card {
            background: white;
            padding: 40px;
            border-radius: 20px;
            border: 1px solid #eef2f5;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .04);
        }

        .header-title {
            font-size: 24px;
            font-weight: 700;
            color: #0f5132;
            margin-bottom: 30px;
            position: relative;
            padding-bottom: 12px;
        }

        .header-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 4px;
            background-color: #198754;
            border-radius: 2px;
        }

        /* Grid Pembagi Baris Input */
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-group {
            margin-bottom: 22px;
        }

        .form-group.full-width {
            grid-column: span 2;
        }

        label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
        }

        input,
        textarea {
            width: 100%;
            padding: 13px 16px;
            font-size: 14px;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 12px;
            outline: none;
            transition: all 0.2s ease;
        }

        input:focus,
        textarea:focus {
            background: #ffffff;
            border-color: #198754;
            box-shadow: 0 0 0 4px rgba(25, 135, 84, 0.12);
        }

        input[type="file"] {
            background: #ffffff;
            padding: 10px;
            cursor: pointer;
        }

        textarea {
            resize: vertical;
            min-height: 120px;
        }

        /* ==========================================================================
   4. TOMBOL & NOTIFIKASI (BUTTONS & ALERTS)
   ========================================================================== */
        .action {
            display: flex;
            gap: 12px;
            margin-top: 10px;
        }

        .btn {
            flex: 1;
            border: none;
            padding: 14px 24px;
            font-size: 15px;
            font-weight: 600;
            text-align: center;
            text-decoration: none;
            border-radius: 12px;
            cursor: pointer;
            color: white;
            transition: background 0.2s ease;
        }

        .btn-success {
            background: #198754;
        }

        .btn-success:hover {
            background: #146c43;
        }

        .btn-secondary {
            background: #6c757d;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .alert {
            padding: 14px 18px;
            font-size: 14px;
            font-weight: 500;
            border-radius: 12px;
            margin-bottom: 25px;
        }

        .alert-danger {
            background: #ffe5e5;
            color: #b42318;
            border: 1px solid #fecdca;
        }

        /* ==========================================================================
   5. PENGATURAN RESPONSIF LAYAR HP (MEDIA QUERIES)
   ========================================================================== */
        @media (max-width: 768px) {
            .wrapper {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
            }

            .content {
                padding: 20px;
            }

            .card {
                padding: 25px;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .form-group.full-width {
                grid-column: span 1;
            }

            .action {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>

    <div class="wrapper">

        <body>

            <div class="wrapper">

                <?php include 'sidebar.php'; ?>

                <div class="content">

                    <!-- Content -->
                    <div class="content">

                        <div class="container">

                            <div class="card">

                                <h2 class="header-title">Tambah Produk Baru</h2>

                                <?php if ($pesan != ''): ?>
                                    <div class="alert alert-danger">
                                        ⚠️ <?= htmlspecialchars($pesan); ?>
                                    </div>
                                <?php endif; ?>

                                <form method="POST" enctype="multipart/form-data" autocomplete="off">

                                    <div class="form-row">

                                        <div class="form-group full-width">
                                            <label>Nama Produk</label>
                                            <input type="text" name="nama_produk" placeholder="Contoh: Minyak Goreng 1L"
                                                required>
                                        </div>

                                        <div class="form-group">
                                            <label>Harga Jual (Rp)</label>
                                            <input type="number" name="harga" min="0" required>
                                        </div>

                                        <div class="form-group">
                                            <label>Jumlah Stok</label>
                                            <input type="number" name="stok" min="0" required>
                                        </div>

                                        <div class="form-group full-width">
                                            <label>Foto Produk</label>
                                            <input type="file" name="foto" accept=".jpg,.jpeg,.png,.webp" required>
                                        </div>

                                        <div class="form-group full-width">
                                            <label>Deskripsi Produk</label>
                                            <textarea name="deskripsi"
                                                placeholder="Tuliskan spesifikasi atau detail produk..."></textarea>
                                        </div>

                                    </div>

                                    <div class="action">

                                        <button type="submit" name="simpan" class="btn btn-success">
                                            💾 Simpan Produk
                                        </button>

                                    </div>

                                </form>

                            </div>

                        </div>

                    </div>
                </div>

            </div>

        </body>

    </div>



    </div>

</body>

</html>