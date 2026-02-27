<?php

declare(strict_types=1);

require_once __DIR__ . '/../layout.php';

$pdo = pdo();

$q = trim((string)($_GET['q'] ?? ''));
$idKelas = trim((string)($_GET['id_kelas'] ?? ''));

$kelasList = $pdo->query('SELECT id_kelas, nama_kelas FROM kelas ORDER BY nama_kelas ASC')->fetchAll();

$where = [];
$params = [];
if ($q !== '') {
    $where[] = '(s.nisn LIKE ? OR s.nama LIKE ?)';
    $params[] = '%' . $q . '%';
    $params[] = '%' . $q . '%';
}
if ($idKelas !== '' && ctype_digit($idKelas)) {
    $where[] = 's.id_kelas = ?';
    $params[] = (int)$idKelas;
}

$sql = 'SELECT s.id, s.nisn, s.nama, s.id_kelas, s.created_at, k.nama_kelas
        FROM siswa s
        LEFT JOIN kelas k ON k.id_kelas = s.id_kelas';
if ($where) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
$sql .= ' ORDER BY s.created_at DESC, s.id DESC LIMIT 300';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

admin_layout_start('Data Siswa');
?>
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-4">
        <div>
            <h1 class="text-lg font-bold mb-1">Data Siswa</h1>
            <p class="text-sm text-slate-300">CRUD + filter kelas + pencarian cepat.</p>
        </div>
        <a href="<?= e(url_for('/admin/siswa/create')) ?>" class="inline-flex items-center gap-2 rounded-2xl bg-electric text-slate-50 px-3 py-2 text-xs font-semibold shadow-neobrutal hover:-translate-y-0.5 transition">
            + Tambah Siswa
        </a>
    </div>

    <form method="get" class="mb-4 grid md:grid-cols-[1fr_220px_auto] gap-3 items-end">
        <div>
            <label class="block text-xs text-slate-300 mb-1" for="q">Cari (NISN / Nama)</label>
            <input id="q" name="q" value="<?= e($q) ?>" class="w-full rounded-2xl border border-slate-700 bg-slate-950/60 px-3 py-2 text-sm outline-none focus:border-cyberlime" placeholder="contoh: 0099 atau 'Aulia'" />
        </div>
        <div>
            <label class="block text-xs text-slate-300 mb-1" for="id_kelas">Filter Kelas</label>
            <select id="id_kelas" name="id_kelas" class="w-full rounded-2xl border border-slate-700 bg-slate-950/60 px-3 py-2 text-sm outline-none focus:border-cyberlime">
                <option value="">Semua</option>
                <?php foreach ($kelasList as $k): ?>
                    <option value="<?= e((string)$k['id_kelas']) ?>" <?= ((string)$k['id_kelas'] === $idKelas) ? 'selected' : '' ?>>
                        <?= e((string)$k['nama_kelas']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button class="inline-flex items-center justify-center gap-2 rounded-2xl border-2 border-slate-100 bg-slate-50 text-slate-900 px-4 py-2 text-xs font-semibold shadow-neobrutal hover:-translate-y-0.5 transition">
            Terapkan
        </button>
    </form>

    <div class="rounded-3xl border border-slate-800 bg-slate-950/30 overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-800 text-xs text-slate-300 flex items-center justify-between">
            <span>Total tampil: <span class="font-semibold text-slate-50"><?= e((string)count($rows)) ?></span></span>
            <a href="<?= e(url_for('/admin/import/siswa')) ?>" class="hover:text-cyberlime transition">Import Excel →</a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="text-[11px] uppercase tracking-[0.2em] text-slate-400 bg-slate-950/40">
                    <tr>
                        <th class="text-left px-4 py-3">NISN</th>
                        <th class="text-left px-4 py-3">Nama</th>
                        <th class="text-left px-4 py-3">Kelas</th>
                        <th class="text-left px-4 py-3">Dibuat</th>
                        <th class="text-right px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-900/80">
                    <?php if (!$rows): ?>
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-slate-300">Belum ada data siswa.</td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($rows as $r): ?>
                        <tr class="hover:bg-slate-950/40 transition">
                            <td class="px-4 py-3 font-semibold text-slate-50"><?= e((string)$r['nisn']) ?></td>
                            <td class="px-4 py-3 text-slate-200"><?= e((string)$r['nama']) ?></td>
                            <td class="px-4 py-3 text-slate-300"><?= e((string)($r['nama_kelas'] ?? '—')) ?></td>
                            <td class="px-4 py-3 text-slate-400 text-xs"><?= e((string)$r['created_at']) ?></td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-2">
                                    <a class="inline-flex items-center rounded-2xl border border-slate-700 px-3 py-1.5 text-xs hover:border-cyberlime hover:text-cyberlime transition"
                                       href="<?= e(url_for('/admin/siswa/edit')) ?>?id=<?= e((string)$r['id']) ?>">
                                        Edit
                                    </a>
                                    <form method="post" action="<?= e(url_for('/admin/siswa/delete')) ?>" onsubmit="return confirm('Hapus siswa ini?')">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= e((string)$r['id']) ?>">
                                        <button class="inline-flex items-center rounded-2xl border border-red-500/50 px-3 py-1.5 text-xs text-red-200 hover:bg-red-500/10 transition">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php
admin_layout_end();

