<?php

declare(strict_types=1);

require_once __DIR__ . '/../layout.php';

$pdo = pdo();

$kelas = $pdo->query('SELECT k.id_kelas, k.nama_kelas, k.kuota, k.created_at, g.nama AS wali_nama 
                      FROM kelas k 
                      LEFT JOIN guru g ON g.id = k.id_guru 
                      ORDER BY k.nama_kelas ASC')->fetchAll();

admin_layout_start('Data Kelas');
?>
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-4">
        <div>
            <h1 class="text-lg font-bold mb-1">Data Kelas</h1>
            <p class="text-sm text-slate-300">Atur nama kelas, kuota, dan wali kelas.</p>
        </div>
        <a href="<?= e(url_for('/admin/kelas/create')) ?>" class="inline-flex items-center gap-2 rounded-2xl bg-electric text-slate-50 px-3 py-2 text-xs font-semibold shadow-neobrutal hover:-translate-y-0.5 transition">
            + Tambah Kelas
        </a>
    </div>

    <div class="rounded-3xl border border-slate-800 bg-slate-950/30 overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-800 text-xs text-slate-300 flex items-center justify-between">
            <span>Total kelas: <span class="font-semibold text-slate-50"><?= e((string)count($kelas)) ?></span></span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="text-[11px] uppercase tracking-[0.2em] text-slate-400 bg-slate-950/40">
                    <tr>
                        <th class="text-left px-4 py-3">Nama Kelas</th>
                        <th class="text-left px-4 py-3">Wali Kelas</th>
                        <th class="text-left px-4 py-3">Kuota</th>
                        <th class="text-right px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-900/80">
                    <?php if (!$kelas): ?>
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-slate-300">Belum ada data kelas.</td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($kelas as $k): ?>
                        <tr class="hover:bg-slate-950/40 transition">
                            <td class="px-4 py-3 font-semibold text-slate-50"><?= e((string)$k['nama_kelas']) ?></td>
                            <td class="px-4 py-3 text-slate-200"><?= e((string)($k['wali_nama'] ?? '—')) ?></td>
                            <td class="px-4 py-3 text-slate-300 text-xs"><?= e((string)$k['kuota']) ?> siswa</td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-2">
                                    <a class="inline-flex items-center rounded-2xl border border-slate-700 px-3 py-1.5 text-xs hover:border-cyberlime hover:text-cyberlime transition"
                                       href="<?= e(url_for('/admin/kelas/edit')) ?>?id=<?= e((string)$k['id_kelas']) ?>">
                                        Edit
                                    </a>
                                    <form method="post" action="<?= e(url_for('/admin/kelas/delete')) ?>" onsubmit="return confirm('Hapus kelas ini? Siswa tetap tersimpan tetapi tidak punya kelas.')">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= e((string)$k['id_kelas']) ?>">
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

