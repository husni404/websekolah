<?php

declare(strict_types=1);

require_once __DIR__ . '/../layout.php';

$pdo = pdo();

$tipe = (string)($_GET['tipe'] ?? '');
$q = trim((string)($_GET['q'] ?? ''));

$where = [];
$params = [];

if ($tipe !== '' && in_array($tipe, ['info', 'berita', 'mading', 'event'], true)) {
    $where[] = 'k.tipe = ?';
    $params[] = $tipe;
}

if ($q !== '') {
    $where[] = '(k.judul LIKE ? OR k.ringkasan LIKE ?)';
    $like = '%' . $q . '%';
    $params[] = $like;
    $params[] = $like;
}

$sql = 'SELECT k.id, k.judul, k.tipe, k.slug, k.tgl_upload, k.is_published
        FROM konten k';
if ($where) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
$sql .= ' ORDER BY k.tgl_upload DESC, k.id DESC LIMIT 200';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

admin_layout_start('Konten & E‑Mading');
?>
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-4">
        <div>
            <h1 class="text-lg font-bold mb-1">Konten & E‑Mading</h1>
            <p class="text-sm text-slate-300">Kelola info, berita, mading, dan event yang tampil di landing.</p>
        </div>
        <a href="<?= e(url_for('/admin/konten/create')) ?>" class="inline-flex items-center gap-2 rounded-2xl bg-electric text-slate-50 px-3 py-2 text-xs font-semibold shadow-neobrutal hover:-translate-y-0.5 transition">
            + Konten Baru
        </a>
    </div>

    <form method="get" class="mb-4 grid md:grid-cols-[1fr_180px_auto] gap-3 items-end">
        <div>
            <label class="block text-xs text-slate-300 mb-1" for="q">Cari (judul / ringkasan)</label>
            <input id="q" name="q" value="<?= e($q) ?>" class="w-full rounded-2xl border border-slate-700 bg-slate-950/60 px-3 py-2 text-sm outline-none focus:border-cyberlime" placeholder="contoh: expo, ppdb, prestasi" />
        </div>
        <div>
            <label class="block text-xs text-slate-300 mb-1" for="tipe">Tipe Konten</label>
            <select id="tipe" name="tipe" class="w-full rounded-2xl border border-slate-700 bg-slate-950/60 px-3 py-2 text-sm outline-none focus:border-cyberlime">
                <option value="">Semua</option>
                <?php
                $tipeList = [
                    'info' => 'Info',
                    'berita' => 'Berita',
                    'mading' => 'E-Mading',
                    'event' => 'Event',
                ];
                foreach ($tipeList as $k => $label):
                    $selected = ($tipe === $k) ? 'selected' : '';
                ?>
                    <option value="<?= e($k) ?>" <?= $selected ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button class="inline-flex items-center justify-center gap-2 rounded-2xl border-2 border-slate-100 bg-slate-50 text-slate-900 px-4 py-2 text-xs font-semibold shadow-neobrutal hover:-translate-y-0.5 transition">
            Terapkan
        </button>
    </form>

    <div class="rounded-3xl border border-slate-800 bg-slate-950/30 overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-800 text-xs text-slate-300 flex items-center justify-between">
            <span>Total konten: <span class="font-semibold text-slate-50"><?= e((string)count($rows)) ?></span></span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="text-[11px] uppercase tracking-[0.2em] text-slate-400 bg-slate-950/40">
                    <tr>
                        <th class="text-left px-4 py-3">Judul</th>
                        <th class="text-left px-4 py-3">Tipe</th>
                        <th class="text-left px-4 py-3">Tanggal</th>
                        <th class="text-left px-4 py-3">Status</th>
                        <th class="text-right px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-900/80">
                    <?php if (!$rows): ?>
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-slate-300">Belum ada konten.</td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($rows as $r): ?>
                        <tr class="hover:bg-slate-950/40 transition">
                            <td class="px-4 py-3 font-semibold text-slate-50"><?= e((string)$r['judul']) ?></td>
                            <td class="px-4 py-3 text-slate-300 text-xs"><?= e((string)$r['tipe']) ?></td>
                            <td class="px-4 py-3 text-slate-300 text-xs"><?= e((string)$r['tgl_upload']) ?></td>
                            <td class="px-4 py-3 text-slate-300 text-xs">
                                <?php if ((int)$r['is_published'] === 1): ?>
                                    <span class="px-2 py-1 rounded-full bg-emerald-500/15 border border-emerald-500/40 text-emerald-200 text-[11px] font-semibold">Published</span>
                                <?php else: ?>
                                    <span class="px-2 py-1 rounded-full bg-slate-800/80 border border-slate-600 text-slate-300 text-[11px] font-semibold">Draft</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-2">
                                    <a class="inline-flex items-center rounded-2xl border border-slate-700 px-3 py-1.5 text-xs hover:border-cyberlime hover:text-cyberlime transition"
                                       href="<?= e(url_for('/admin/konten/edit')) ?>?id=<?= e((string)$r['id']) ?>">
                                        Edit
                                    </a>
                                    <form method="post" action="<?= e(url_for('/admin/konten/delete')) ?>" onsubmit="return confirm('Hapus konten ini?')">
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

