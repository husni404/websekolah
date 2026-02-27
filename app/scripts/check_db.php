<?php

declare(strict_types=1);

require_once __DIR__ . '/../../app/init.php';

echo "== Cek Koneksi Database ==\n";

try {
    $pdo = pdo();
    $stmt = $pdo->query('SELECT DATABASE() AS db');
    $row = $stmt->fetch();
    $dbName = $row['db'] ?? '(tidak terdeteksi)';
    echo "Status  : OK\n";
    echo "Database: {$dbName}\n";
} catch (Throwable $e) {
    echo "Status  : GAGAL\n";
    echo "Error   : " . $e->getMessage() . "\n";
    echo "Hint    : Pastikan MySQL jalan, schema.sql sudah di-import, dan config/config.php sudah sesuai.\n";
}

