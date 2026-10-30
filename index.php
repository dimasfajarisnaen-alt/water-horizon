<?php

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/csrf.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Jika pengguna sudah login, langsung ke dashboard.
if (!empty($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validasi token CSRF.
        csrf_verify();

        $identity = trim($_POST['identity'] ?? '');
        $passwordInput = $_POST['password'] ?? '';

        if ($identity === '' || $passwordInput === '') {
            $error = 'Email dan password wajib diisi.';
        } elseif (!filter_var($identity, FILTER_VALIDATE_EMAIL)) {
            $error = 'Format email tidak valid.';
        } else {
            // Ambil akun berdasarkan email.
            $stmt = db()->prepare(
                'SELECT id, name, email, password_hash, role
                 FROM users
                 WHERE email = ?
                 LIMIT 1'
            );

            $stmt->execute([$identity]);
            $user = $stmt->fetch();

            // Verifikasi password yang sudah di-hash.
            if (
                $user &&
                password_verify($passwordInput, $user['password_hash'])
            ) {
                session_regenerate_id(true);

                $_SESSION['user_id'] = (int) $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['role'] = $user['role'];

                header('Location: dashboard.php');
                exit;
            }

            $error = 'Email atau password tidak sesuai.';
        }
    } catch (Throwable $e) {
        error_log('WaterWatch login error: ' . $e->getMessage());
        $error = 'Login gagal diproses. Periksa konfigurasi database dan tabel users.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — WaterWatch Horizon</title>

    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/auth.css">
</head>
<body class="auth-body">

<main class="auth-shell">

    <section class="auth-art">
        <img
            src="assets/images/logo-waterwatch.svg"
            alt="Logo WaterWatch Horizon"
            class="brand-logo"
        >

        <p class="eyebrow">SMART WATER MONITORING</p>

        <h1>
            Air bersih,<br>
            <em>masa depan lebih baik.</em>
        </h1>

        <p>
            Pantau kondisi saluran air, dokumentasikan temuan,
            dan bantu lingkungan bertindak lebih cepat.
        </p>

        <div class="water-art" aria-hidden="true"></div>

        <small>SDG 6 · Clean Water and Sanitation</small>
    </section>

    <section class="auth-panel">

        <div class="mobile-brand">
            <img
                src="assets/images/logo-waterwatch.svg"
                alt="Logo"
            >
            WATERWATCH HORIZON
        </div>

        <p class="eyebrow">SELAMAT DATANG KEMBALI</p>

        <h2>Masuk ke akun Anda</h2>

        <p class="muted">
            Lanjutkan pemantauan lingkungan Anda.
        </p>

        <?php if ($error !== ''): ?>
            <div class="alert error" role="alert">
                <?= htmlspecialchars(
                    $error,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="index.php" class="stack-form">

            <?= csrf_field() ?>

            <label for="identity">
                Email
                <input
                    id="identity"
                    name="identity"
                    type="email"
                    autocomplete="username"
                    placeholder="nama@email.com"
                    value="<?= htmlspecialchars(
                        $_POST['identity'] ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                    required
                >
            </label>

            <label for="password">
                Password
                <input
                    id="password"
                    name="password"
                    type="password"
                    autocomplete="current-password"
                    placeholder="Masukkan password"
                    required
                >
            </label>

            <button class="btn primary wide" type="submit">
                Masuk ke Dashboard <span>→</span>
            </button>

        </form>

        <p class="muted tiny">
            Untuk akun demo, pastikan akun sudah dibuat
            dan password sesuai dengan data pada database.
        </p>

        <p class="muted tiny">
            Gunakan hanya untuk pengujian lokal.
            Ganti kredensial sebelum deployment.
        </p>

    </section>

</main>

</body>
</html>