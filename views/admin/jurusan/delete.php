<?php

declare(strict_types=1);

require_once __DIR__ . '/../layout.php';

csrf_verify();

$id = trim((string)($_POST['id'] ?? ''));
if ($id === '' || !ctype_digit($id)) {
    flash_set('error', 'ID jurusan tidak valid.');
    redirect('/admin/jurusan');
}

$pdo = pdo();

$del = $pdo->prepare('DELETE FROM jurusan WHERE id = ?');
$del->execute([(int)$id]);

flash_set('success', 'Jurusan berhasil dihapus.');
redirect('/admin/jurusan');

