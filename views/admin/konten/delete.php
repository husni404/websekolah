<?php

declare(strict_types=1);

require_once __DIR__ . '/../layout.php';

csrf_verify();

$id = trim((string)($_POST['id'] ?? ''));
if ($id === '' || !ctype_digit($id)) {
    flash_set('error', 'ID konten tidak valid.');
    redirect('/admin/konten');
}

$pdo = pdo();

$del = $pdo->prepare('DELETE FROM konten WHERE id = ?');
$del->execute([(int)$id]);

flash_set('success', 'Konten berhasil dihapus.');
redirect('/admin/konten');

