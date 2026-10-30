<?php
if (PHP_SAPI !== 'cli') {
    exit('Jalankan melalui terminal.');
}

require_once __DIR__ . '/config/database.php';

$email = 'demo@waterwatch.local';
$password = 'WaterWatch123!';
$hash = password_hash($password, PASSWORD_DEFAULT);

try {
    $stmt = db()->prepare(
        'SELECT id FROM users WHERE email = ? LIMIT 1'
    );
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        $stmt = db()->prepare(
            'UPDATE users SET password_hash = ? WHERE email = ?'
        );
        $stmt->execute([$hash, $email]);
        echo "Password berhasil diperbarui.\n";
    } else {
        $stmt = db()->prepare(
            'INSERT INTO users (name, email, password_hash, role)
             VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([
            'Demo User',
            $email,
            $hash,
            'user'
        ]);
        echo "Akun demo berhasil dibuat.\n";
    }

    echo "Email: $email\n";
    echo "Password: $password\n";
} catch (Throwable $e) {
    echo "Gagal: " . $e->getMessage() . "\n";
}