<?php

declare(strict_types=1);

require_once __DIR__ . '/../layout.php';

csrf_verify();

$id = trim((string)($_POST['id'] ?? ''));
if ($id === '' || !ctype_digit($id)) {
    flash_set('error', 'ID siswa tidak valid.');
    redirect('/admin/siswa');
}

$pdo = pdo();
$del = $pdo->prepare('DELETE FROM siswa WHERE id = ?');
$del->execute([(int)$id]);

flash_set('success', 'Siswa berhasil dihapus.');
redirect('/admin/siswa');

