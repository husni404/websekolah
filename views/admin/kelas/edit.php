<?php

declare(strict_types=1);

require_once __DIR__ . '/../layout.php';

$pdo = pdo();
$guruList = $pdo->query('SELECT id, nama FROM guru ORDER BY nama ASC')->fetchAll();

$id = trim((string)($_GET['id'] ?? ''));
if ($id === '' || !ctype_digit($id)) {
    flash_set('error', 'ID kelas tidak valid.');
    redirect('/admin/kelas');
}

$stmt = $pdo->prepare('SELECT * FROM kelas WHERE id_kelas = ? LIMIT 1');
$stmt->execute([(int)$id]);
$kelas = $stmt->fetch();
if (!$kelas) {
    flash_set('error', 'Kelas tidak ditemukan.');
    redirect('/admin/kelas');
}

$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

if ($method === 'POST') {
    csrf_verify();
    $nama = trim((string)($_POST['nama_kelas'] ?? ''));
    $kuota = trim((string)($_POST['kuota'] ?? ''));
    $idGuru = trim((string)($_POST['id_guru'] ?? ''));

    if ($nama === '') {
        flash_set('error', 'Nama kelas wajib diisi.');
        redirect('/admin/kelas/edit?id=' . $id);
    }

    $chk = $pdo->prepare('SELECT COUNT(*) AS c FROM kelas WHERE nama_kelas = ? AND id_kelas <> ?');
    $chk->execute([$nama, (int)$id]);
    $exists = (int)($chk->fetch()['c'] ?? 0);
    if ($exists > 0) {
        flash_set('error', 'Nama kelas sudah digunakan kelas lain.');
        redirect('/admin/kelas/edit?id=' . $id);
    }

    $kuotaVal = ctype_digit($kuota) ? (int)$kuota : 36;
    if ($kuotaVal <= 0) {
        $kuotaVal = 36;
    }

    $idGuruVal = ctype_digit($idGuru) ? (int)$idGuru : null;

    $upd = $pdo->prepare('UPDATE kelas SET nama_kelas = ?, id_guru = ?, kuota = ? WHERE id_kelas = ?');
    $upd->execute([$nama, $idGuruVal, $kuotaVal, (int)$id]);

    flash_set('success', 'Data kelas berhasil diupdate.');
    redirect('/admin/kelas');
}

admin_layout_start('Edit Kelas');
?>
    <div class="flex items-start justify-between gap-4 mb-4">
        <div>
            <h1 class="text-lg font-bold mb-1">Edit Kelas</h1>
            <p class="text-sm text-slate-300">Perbarui nama, kuota, atau wali kelas.</p>
        </div>
        <a href="<?= e(url_for('/admin/kelas')) ?>" class="inline-flex items-center gap-2 rounded-2xl border border-slate-700 bg-slate-900/70 px-3 py-2 text-xs font-semibold hover:border-cyberlime hover:text-cyberlime transition">
            ← Kembali
        </a>
    </div>

    <form method="post" class="grid md:grid-cols-2 gap-4">
        <?= csrf_field() ?>
        <div>
            <label class="block text-xs text-slate-300 mb-1" for="nama_kelas">Nama Kelas</label>
            <input id="nama_kelas" name="nama_kelas" value="<?= e((string)$kelas['nama_kelas']) ?>" class="w-full rounded-2xl border border-slate-700 bg-slate-950/60 px-3 py-2 text-sm outline-none focus:border-cyberlime" />
        </div>
        <div>
            <label class="block text-xs text-slate-300 mb-1" for="kuota">Kuota (jumlah siswa)</label>
            <input id="kuota" name="kuota" type="number" min="1" max="60" value="<?= e((string)$kelas['kuota']) ?>" class="w-full rounded-2xl border border-slate-700 bg-slate-950/60 px-3 py-2 text-sm outline-none focus:border-cyberlime" />
        </div>
        <div class="md:col-span-2">
            <label class="block text-xs text-slate-300 mb-1" for="id_guru">Wali Kelas</label>
            <select id="id_guru" name="id_guru" class="w-full rounded-2xl border border-slate-700 bg-slate-950/60 px-3 py-2 text-sm outline-none focus:border-cyberlime">
                <option value="">(Opsional) pilih wali kelas</option>
                <?php foreach ($guruList as $g): ?>
                    <?php $selected = ((string)$g['id'] === (string)($kelas['id_guru'] ?? '')) ? 'selected' : ''; ?>
                    <option value="<?= e((string)$g['id']) ?>" <?= $selected ?>><?= e((string)$g['nama']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="md:col-span-2">
            <button class="inline-flex items-center justify-center gap-2 rounded-2xl bg-electric text-slate-50 px-4 py-2 text-sm font-semibold shadow-neobrutal hover:-translate-y-0.5 transition">
                Simpan Perubahan
            </button>
        </div>
    </form>
<?php
admin_layout_end();

