<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../app/import_siswa.php';
require_once __DIR__ . '/../layout.php';

$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

if ($method === 'POST') {
    csrf_verify();

    if (!isset($_FILES['file']) || !is_array($_FILES['file'])) {
        flash_set('error', 'File Excel belum dipilih.');
        redirect('/admin/import/siswa');
    }

    $file = $_FILES['file'];
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        flash_set('error', 'Gagal mengupload file (cek ukuran atau koneksi).');
        redirect('/admin/import/siswa');
    }

    $name = (string)($file['name'] ?? '');
    $tmpName = (string)($file['tmp_name'] ?? '');
    $size = (int)($file['size'] ?? 0);

    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    if (!in_array($ext, ['xlsx', 'xls'], true)) {
        flash_set('error', 'Format tidak didukung. Gunakan file Excel (.xlsx / .xls).');
        redirect('/admin/import/siswa');
    }

    if ($size <= 0) {
        flash_set('error', 'File kosong.');
        redirect('/admin/import/siswa');
    }

    if ($size > 5 * 1024 * 1024) { // 5 MB
        flash_set('error', 'Ukuran file terlalu besar (maks 5 MB).');
        redirect('/admin/import/siswa');
    }

    $uploadDir = __DIR__ . '/../../../storage/uploads';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0775, true);
    }

    $target = $uploadDir . '/siswa_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    if (!move_uploaded_file($tmpName, $target)) {
        flash_set('error', 'Gagal memindahkan file upload.');
        redirect('/admin/import/siswa');
    }

    $result = import_siswa_from_excel($target);
    @unlink($target);

    if (!$result['ok']) {
        $msg = $result['message'] ?? 'Import gagal.';
        if (!empty($result['duplikat'])) {
            $msg .= ' NISN duplikat: ' . implode(', ', $result['duplikat']);
        }
        flash_set('error', $msg);
        redirect('/admin/import/siswa');
    }

    $count = (int)($result['count'] ?? 0);
    flash_set('success', "Berhasil import {$count} siswa dari Excel.");
    redirect('/admin/siswa');
}

admin_layout_start('Import Siswa (Excel)');
?>
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-4">
        <div>
            <h1 class="text-lg font-bold mb-1">Import Siswa dari Excel</h1>
            <p class="text-sm text-slate-300">Flow simpel: download template → isi → upload → validasi → masuk DB.</p>
        </div>
        <a href="<?= e(url_for('/admin/template/siswa')) ?>" class="inline-flex items-center gap-2 rounded-2xl bg-electric text-slate-50 px-3 py-2 text-xs font-semibold shadow-neobrutal hover:-translate-y-0.5 transition">
            Download Template ↗
        </a>
    </div>

    <div class="grid md:grid-cols-[1.1fr_0.9fr] gap-5">
        <div class="rounded-3xl border border-slate-800 bg-slate-950/40 p-4">
            <form method="post" enctype="multipart/form-data" class="space-y-4">
                <?= csrf_field() ?>
                <div>
                    <label class="block text-xs text-slate-300 mb-1" for="file">File Excel (.xlsx / .xls)</label>
                    <input id="file" name="file" type="file" accept=".xlsx,.xls" class="block w-full text-sm text-slate-200 file:mr-3 file:rounded-2xl file:border-0 file:bg-cyberlime file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-slate-900 hover:file:bg-lime-300" />
                    <p class="mt-1 text-[11px] text-slate-400">Maksimal ±5 MB. Gunakan template resmi agar urutan kolom sesuai.</p>
                </div>
                <button class="inline-flex items-center justify-center gap-2 rounded-2xl bg-electric text-slate-50 px-4 py-2 text-sm font-semibold shadow-neobrutal hover:-translate-y-0.5 transition">
                    Import Sekarang
                </button>
            </form>
        </div>

        <div class="rounded-3xl border border-slate-800 bg-slate-950/20 p-4 text-sm text-slate-200">
            <p class="text-[11px] uppercase tracking-[0.25em] text-slate-400 mb-2">Alur Import & Validasi</p>
            <ol class="list-decimal list-inside space-y-1.5 text-xs">
                <li>Download template Excel resmi.</li>
                <li>Isi kolom:
                    <ul class="list-disc list-inside ml-4 mt-1">
                        <li><strong>NISN</strong> (unik, wajib).</li>
                        <li><strong>Nama</strong> (wajib).</li>
                        <li><strong>ID Kelas</strong> (opsional, isi dengan ID dari tabel kelas).</li>
                        <li><strong>Alamat</strong> (opsional).</li>
                    </ul>
                </li>
                <li>Upload kembali ke halaman ini.</li>
                <li>Sistem akan:
                    <ul class="list-disc list-inside ml-4 mt-1">
                        <li>Menolak jika ada baris dengan NISN atau Nama kosong.</li>
                        <li>Memeriksa NISN duplikat di dalam file.</li>
                        <li>Memeriksa NISN yang sudah ada di database.</li>
                        <li>Jika ada duplikat: import dibatalkan dan daftar NISN bermasalah ditampilkan.</li>
                    </ul>
                </li>
                <li>Jika semua valid, data akan disimpan ke tabel <code>siswa</code> dengan transaksi (tanpa setengah-setengah).</li>
            </ol>
        </div>
    </div>
<?php
admin_layout_end();

