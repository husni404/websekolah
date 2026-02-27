<?php

declare(strict_types=1);

require_once __DIR__ . '/../layout.php';

$pdo = pdo();

try {
    $stmt = $pdo->query('SELECT id, nama, singkatan, tagline, logo_path, created_at FROM jurusan ORDER BY nama ASC');
    $jurusan = $stmt->fetchAll() ?: [];
} catch (Throwable $e) {
    $jurusan = [];
}

admin_layout_start('Data Jurusan');
?>
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-4">
        <div>
            <h1 class="text-lg font-bold mb-1">Data Jurusan</h1>
            <p class="text-sm text-slate-300">Atur daftar jurusan yang tampil di landing.</p>
        </div>
        <a href="<?= e(url_for('/admin/jurusan/create')) ?>" class="inline-flex items-center gap-2 rounded-2xl bg-electric text-slate-50 px-3 py-2 text-xs font-semibold shadow-neobrutal hover:-translate-y-0.5 transition">
            + Tambah Jurusan
        </a>
    </div>

    <div class="rounded-3xl border border-slate-800 bg-slate-950/30 overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-800 text-xs text-slate-300 flex items-center justify-between">
            <span>Total jurusan: <span class="font-semibold text-slate-50"><?= e((string)count($jurusan)) ?></span></span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="text-[11px] uppercase tracking-[0.2em] text-slate-400 bg-slate-950/40">
                    <tr>
                        <th class="text-left px-4 py-3">Jurusan</th>
                        <th class="text-left px-4 py-3">Singkatan</th>
                        <th class="text-left px-4 py-3">Tagline</th>
                        <th class="text-right px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-900/80">
                    <?php if (!$jurusan): ?>
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-slate-300">Belum ada data jurusan.</td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($jurusan as $j): ?>
                        <tr class="hover:bg-slate-950/40 transition">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <?php if (!empty($j['logo_path'])): ?>
                                        <div class="h-8 w-8 rounded-2xl bg-slate-900 flex items-center justify-center border border-slate-700 overflow-hidden">
                                            <img src="<?= e((string)$j['logo_path']) ?>" alt="Logo <?= e((string)$j['nama']) ?>" class="max-h-8 max-w-8 object-contain" />
                                        </div>
                                    <?php endif; ?>
                                    <span class="font-semibold text-slate-50"><?= e((string)$j['nama']) ?></span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-slate-300 text-xs"><?= e((string)($j['singkatan'] ?? '')) ?></td>
                            <td class="px-4 py-3 text-slate-300 text-xs"><?= e((string)($j['tagline'] ?? '')) ?></td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-2">
                                    <a class="inline-flex items-center rounded-2xl border border-slate-700 px-3 py-1.5 text-xs hover:border-cyberlime hover:text-cyberlime transition"
                                       href="<?= e(url_for('/admin/jurusan/edit')) ?>?id=<?= e((string)$j['id']) ?>">
                                        Edit
                                    </a>
                                    <form method="post" action="<?= e(url_for('/admin/jurusan/delete')) ?>" onsubmit="return confirm('Hapus jurusan ini dari daftar?')">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= e((string)$j['id']) ?>">
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
