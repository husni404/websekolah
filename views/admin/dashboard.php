<?php

declare(strict_types=1);

require_once __DIR__ . '/layout.php';

$pdo = pdo();
$counts = [
    'siswa' => (int)$pdo->query('SELECT COUNT(*) AS c FROM siswa')->fetch()['c'] ?? 0,
    'guru'  => (int)$pdo->query('SELECT COUNT(*) AS c FROM guru')->fetch()['c'] ?? 0,
    'kelas' => (int)$pdo->query('SELECT COUNT(*) AS c FROM kelas')->fetch()['c'] ?? 0,
];

admin_layout_start('Dashboard');
?>
    <div class="flex items-start justify-between gap-4 mb-4">
        <div>
            <h1 class="text-lg font-bold mb-1">Dashboard</h1>
            <p class="text-sm text-slate-300">Snapshot cepat data sekolah.</p>
        </div>
        <a href="<?= e(url_for('/admin/import/siswa')) ?>" class="inline-flex items-center gap-2 rounded-2xl bg-electric text-slate-50 px-3 py-2 text-xs font-semibold shadow-neobrutal hover:-translate-y-0.5 transition">
            Import Excel Siswa ↗
        </a>
    </div>

    <div class="grid sm:grid-cols-3 gap-4">
        <div class="rounded-3xl border border-slate-800 bg-slate-950/40 p-4 shadow-neobrutal">
            <p class="text-[11px] uppercase tracking-[0.25em] text-slate-400 mb-2">Siswa</p>
            <p class="text-3xl font-extrabold"><?= e((string)$counts['siswa']) ?></p>
            <p class="text-xs text-slate-300 mt-1">Total siswa di database.</p>
        </div>
        <div class="rounded-3xl border border-slate-800 bg-slate-950/40 p-4 shadow-neobrutal">
            <p class="text-[11px] uppercase tracking-[0.25em] text-slate-400 mb-2">Guru</p>
            <p class="text-3xl font-extrabold"><?= e((string)$counts['guru']) ?></p>
            <p class="text-xs text-slate-300 mt-1">Total guru/staff pengajar.</p>
        </div>
        <div class="rounded-3xl border border-slate-800 bg-slate-950/40 p-4 shadow-neobrutal">
            <p class="text-[11px] uppercase tracking-[0.25em] text-slate-400 mb-2">Kelas</p>
            <p class="text-3xl font-extrabold"><?= e((string)$counts['kelas']) ?></p>
            <p class="text-xs text-slate-300 mt-1">Jumlah kelas terdaftar.</p>
        </div>
    </div>

    <div class="mt-6 rounded-3xl border border-slate-800 bg-slate-950/30 p-4">
        <p class="text-[11px] uppercase tracking-[0.25em] text-slate-400 mb-2">Next</p>
        <ul class="text-sm text-slate-300 space-y-1">
            <li>- CRUD Siswa (sudah siap di menu “Data Siswa”).</li>
            <li>- Import Excel dengan validasi duplikat NISN.</li>
            <li>- Berikutnya: guru/kelas/konten + chart beneran.</li>
        </ul>
    </div>
<?php
admin_layout_end();

