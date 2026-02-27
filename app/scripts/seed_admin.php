<?php

declare(strict_types=1);

// Simple seeder untuk membuat akun admin pertama

require_once __DIR__ . '/../../app/init.php';

$pdo = pdo();

$username = 'admin';
$password = 'admin123'; // ganti setelah login pertama

$stmt = $pdo->prepare('SELECT COUNT(*) AS c FROM users WHERE username = ?');
$stmt->execute([$username]);
$exists = (int)($stmt->fetch()['c'] ?? 0);

if ($exists > 0) {
    echo "User admin sudah ada.\n";
    exit(0);
}

$hash = password_hash($password, PASSWORD_DEFAULT);

$ins = $pdo->prepare('INSERT INTO users (username, password, role) VALUES (?, ?, ?)');
$ins->execute([$username, $hash, 'admin']);

echo "User admin berhasil dibuat.\n";
echo "Username : {$username}\n";
echo "Password : {$password}\n";

