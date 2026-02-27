<?php

declare(strict_types=1);

require_once __DIR__ . '/../layout.php';

csrf_verify();

$id = trim((string)($_POST['id'] ?? ''));
if ($id === '' || !ctype_digit($id)) {
    flash_set('error', 'ID kelas tidak valid.');
    redirect('/admin/kelas');
}

$pdo = pdo();

// Set siswa di kelas ini menjadi tanpa kelas (NULL) agar data tetap aman
$upd = $pdo->prepare('UPDATE siswa SET id_kelas = NULL WHERE id_kelas = ?');
$upd->execute([(int)$id]);

$del = $pdo->prepare('DELETE FROM kelas WHERE id_kelas = ?');
$del->execute([(int)$id]);

flash_set('success', 'Kelas berhasil dihapus. Siswa tetap tersimpan tanpa kelas.');
redirect('/admin/kelas');

