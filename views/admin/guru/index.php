<?php

declare(strict_types=1);

require_once __DIR__ . '/../layout.php';

$pdo = pdo();

$q = trim((string)($_GET['q'] ?? ''));

$where = [];
$params = [];
if ($q !== '') {
    $where[] = '(g.nip LIKE ? OR g.nama LIKE ? OR g.mapel LIKE ?)';
    $like = '%' . $q . '%';
    $params = [$like, $like, $like];
}

$sql = 'SELECT g.id, g.nip, g.nama, g.mapel, g.jabatan, g.kategori, g.tugas_tambahan, g.created_at
        FROM guru g';
if ($where) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
$sql .= ' ORDER BY g.nama ASC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

admin_layout_start('Data Guru');
?>
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-4">
        <div>
            <h1 class="text-lg font-bold mb-1">Data Guru</h1>
            <p class="text-sm text-slate-300">Manajemen guru, mapel, dan jabatan.</p>
        </div>
        <a href="<?= e(url_for('/admin/guru/create')) ?>" class="inline-flex items-center gap-2 rounded-2xl bg-electric text-slate-50 px-3 py-2 text-xs font-semibold shadow-neobrutal hover:-translate-y-0.5 transition">
            + Tambah Guru
        </a>
    </div>

    <form method="get" class="mb-4 grid md:grid-cols-[1fr_auto] gap-3 items-end">
        <div>
            <label class="block text-xs text-slate-300 mb-1" for="q">Cari (NIP / Nama / Mapel)</label>
            <input id="q" name="q" value="<?= e($q) ?>" class="w-full rounded-2xl border border-slate-700 bg-slate-950/60 px-3 py-2 text-sm outline-none focus:border-cyberlime" placeholder="contoh: 1989 atau 'Bahasa Indonesia'" />
        </div>
        <button class="inline-flex items-center justify-center gap-2 rounded-2xl border-2 border-slate-100 bg-slate-50 text-slate-900 px-4 py-2 text-xs font-semibold shadow-neobrutal hover:-translate-y-0.5 transition">
            Terapkan
        </button>
    </form>

    <div class="rounded-3xl border border-slate-800 bg-slate-950/30 overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-800 text-xs text-slate-300 flex items-center justify-between">
            <span>Total guru: <span class="font-semibold text-slate-50"><?= e((string)count($rows)) ?></span></span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="text-[11px] uppercase tracking-[0.2em] text-slate-400 bg-slate-950/40">
                    <tr>
                        <th class="text-left px-4 py-3">NIP</th>
                        <th class="text-left px-4 py-3">Nama</th>
                        <th class="text-left px-4 py-3">Mapel</th>
                        <th class="text-left px-4 py-3">Kategori</th>
                        <th class="text-left px-4 py-3">Tugas Tambahan</th>
                        <th class="text-right px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-900/80">
                    <?php if (!$rows): ?>
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-slate-300">Belum ada data guru.</td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($rows as $r): ?>
                        <tr class="hover:bg-slate-950/40 transition">
                            <td class="px-4 py-3 font-semibold text-slate-50"><?= e((string)$r['nip']) ?></td>
                            <td class="px-4 py-3 text-slate-200"><?= e((string)$r['nama']) ?></td>
                            <td class="px-4 py-3 text-slate-300"><?= e((string)($r['mapel'] ?? '—')) ?></td>
                            <td class="px-4 py-3 text-slate-300 text-xs">
                                <?php
                                $kat = (string)($r['kategori'] ?? 'guru');
                                if ($kat === 'kepala') {
                                    echo 'Kepala Sekolah';
                                } elseif ($kat === 'staff') {
                                    echo 'Staff / TU';
                                } else {
                                    echo 'Guru';
                                }
                                ?>
                            </td>
                            <td class="px-4 py-3 text-slate-300 text-xs"><?= e((string)($r['tugas_tambahan'] ?? '—')) ?></td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-2">
                                    <a class="inline-flex items-center rounded-2xl border border-slate-700 px-3 py-1.5 text-xs hover:border-cyberlime hover:text-cyberlime transition"
                                       href="<?= e(url_for('/admin/guru/edit')) ?>?id=<?= e((string)$r['id']) ?>">
                                        Edit
                                    </a>
                                    <form method="post" action="<?= e(url_for('/admin/guru/delete')) ?>" onsubmit="return confirm('Hapus guru ini?')">
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

