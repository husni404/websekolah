<?php

declare(strict_types=1);

require_once __DIR__ . '/../layout.php';

csrf_verify();

$id = trim((string)($_POST['id'] ?? ''));
if ($id === '' || !ctype_digit($id)) {
    flash_set('error', 'ID guru tidak valid.');
    redirect('/admin/guru');
}

$pdo = pdo();

// Cek apakah guru sedang menjadi wali kelas
$chk = $pdo->prepare('SELECT COUNT(*) AS c FROM kelas WHERE id_guru = ?');
$chk->execute([(int)$id]);
$inUse = (int)($chk->fetch()['c'] ?? 0);
if ($inUse > 0) {
    flash_set('error', 'Tidak bisa menghapus: guru masih terdaftar sebagai wali kelas.');
    redirect('/admin/guru');
}

$del = $pdo->prepare('DELETE FROM guru WHERE id = ?');
$del->execute([(int)$id]);

flash_set('success', 'Guru berhasil dihapus.');
redirect('/admin/guru');

