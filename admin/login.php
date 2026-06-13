<?php
session_start();

require_once __DIR__ . '/../config/koneksi.php';

$error = '';

if (isset($_POST['login'])) {

    $username = mysqli_real_escape_string(
        $conn,
        trim($_POST['username'])
    );

    $password = $_POST['password'];

    $query = mysqli_query(
        $conn,
        "SELECT * FROM admin
         WHERE username = '$username'
         LIMIT 1"
    );

    if (mysqli_num_rows($query) > 0) {

        $admin = mysqli_fetch_assoc($query);

        if (password_verify($password, $admin['password'])) {

            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];

            header("Location: dashboard.php");
            exit;

        } else {
            $error = "Password salah";
        }

    } else {
        $error = "Username tidak ditemukan";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin</title>

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
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #198754, #0f5132);
            padding: 20px;
        }

        .login-card {
            width: 100%;
            max-width: 400px;
            background: #fff;
            border-radius: 20px;
            padding: 40px 35px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, .15);
            text-align: center;
        }

        .logo {
            width: 70px;
            height: 70px;
            background: #198754;
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            color: #fff;
            font-weight: 700;
        }

        h1 {
            font-size: 26px;
            margin-bottom: 10px;
            color: #212529;
        }

        p.subtitle {
            color: #6c757d;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .form-group {
            text-align: left;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 600;
            color: #212529;
        }

        input {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid #dee2e6;
            border-radius: 12px;
            font-size: 14px;
            outline: none;
            transition: .2s;
        }

        input:focus {
            border-color: #198754;
            box-shadow: 0 0 0 3px rgba(25, 135, 84, .15);
        }

        .btn-login {
            width: 100%;
            border: none;
            background: #198754;
            color: #fff;
            padding: 14px;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: .2s;
            margin-top: 10px;
        }

        .btn-login:hover {
            background: #146c43;
        }

        .btn-kembali {
            display: block;
            width: 100%;
            text-align: center;
            text-decoration: none;
            background: #6c757d;
            /* Warna abu-abu netral */
            color: #fff;
            padding: 14px;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            transition: .2s;
            margin-top: 12px;
            /* Memberi jarak dari tombol login */
        }

        .btn-kembali:hover {
            background: #5a6268;
            /* Warna abu-abu lebih gelap saat disentuh mause */
        }

        .error {
            background: #ffe5e5;
            color: #b42318;
            padding: 12px 14px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .footer {
            margin-top: 25px;
            font-size: 12px;
            color: #6c757d;
        }
    </style>
</head>

<body>

    <div class="login-card">
        <div class="logo">A</div>

        <h1>Login Admin</h1>
        <p class="subtitle">Masuk untuk mengelola produk dan pesanan</p>

        <!-- Pesan Error -->
        <?php if ($error != ''): ?>
            <div class="error">
                <?= htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <!-- Form Login -->
        <form method="POST" autocomplete="off">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Masukkan username" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Masukkan password" required>
            </div>

            <button type="submit" name="login" class="btn-login">
                Login
            </button>

            <a href="../index.php" class="btn-kembali">
                Kembali
            </a>
        </form>

        <div class="footer">
            &copy; <?= date('Y'); ?> Serba Serbi Mart Admin Panel
        </div>
    </div>

</body>

</html>