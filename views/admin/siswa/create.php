<?php

declare(strict_types=1);

require_once __DIR__ . '/../layout.php';

$pdo = pdo();
$kelasList = $pdo->query('SELECT id_kelas, nama_kelas FROM kelas ORDER BY nama_kelas ASC')->fetchAll();

$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

if ($method === 'POST') {
    csrf_verify();
    $nisn = trim((string)($_POST['nisn'] ?? ''));
    $nama = trim((string)($_POST['nama'] ?? ''));
    $idKelas = trim((string)($_POST['id_kelas'] ?? ''));
    $alamat = trim((string)($_POST['alamat'] ?? ''));

    if ($nisn === '' || $nama === '') {
        flash_set('error', 'NISN dan Nama wajib diisi.');
        redirect('/admin/siswa/create');
    }

    // Unique NISN
    $stmt = $pdo->prepare('SELECT COUNT(*) AS c FROM siswa WHERE nisn = ?');
    $stmt->execute([$nisn]);
    $exists = (int)($stmt->fetch()['c'] ?? 0);
    if ($exists > 0) {
        flash_set('error', 'Gagal: NISN sudah ada di database.');
        redirect('/admin/siswa/create');
    }

    $idKelasValue = (ctype_digit($idKelas) ? (int)$idKelas : null);

    $ins = $pdo->prepare('INSERT INTO siswa (nisn, nama, id_kelas, alamat) VALUES (?, ?, ?, ?)');
    $ins->execute([$nisn, $nama, $idKelasValue, ($alamat !== '' ? $alamat : null)]);

    flash_set('success', 'Siswa berhasil ditambahkan.');
    redirect('/admin/siswa');
}

admin_layout_start('Tambah Siswa');
?>
    <div class="flex items-start justify-between gap-4 mb-4">
        <div>
            <h1 class="text-lg font-bold mb-1">Tambah Siswa</h1>
            <p class="text-sm text-slate-300">Input manual (untuk data sedikit). Untuk data banyak, pakai Import Excel.</p>
        </div>
        <a href="<?= e(url_for('/admin/siswa')) ?>" class="inline-flex items-center gap-2 rounded-2xl border border-slate-700 bg-slate-900/70 px-3 py-2 text-xs font-semibold hover:border-cyberlime hover:text-cyberlime transition">
            ← Kembali
        </a>
    </div>

    <form method="post" class="grid md:grid-cols-2 gap-4">
        <?= csrf_field() ?>
        <div class="md:col-span-1">
            <label class="block text-xs text-slate-300 mb-1" for="nisn">NISN</label>
            <input id="nisn" name="nisn" class="w-full rounded-2xl border border-slate-700 bg-slate-950/60 px-3 py-2 text-sm outline-none focus:border-cyberlime" placeholder="contoh: 0099123456" />
        </div>
        <div class="md:col-span-1">
            <label class="block text-xs text-slate-300 mb-1" for="nama">Nama</label>
            <input id="nama" name="nama" class="w-full rounded-2xl border border-slate-700 bg-slate-950/60 px-3 py-2 text-sm outline-none focus:border-cyberlime" placeholder="Nama lengkap" />
        </div>
        <div class="md:col-span-1">
            <label class="block text-xs text-slate-300 mb-1" for="id_kelas">Kelas</label>
            <select id="id_kelas" name="id_kelas" class="w-full rounded-2xl border border-slate-700 bg-slate-950/60 px-3 py-2 text-sm outline-none focus:border-cyberlime">
                <option value="">(Opsional) pilih kelas</option>
                <?php foreach ($kelasList as $k): ?>
                    <option value="<?= e((string)$k['id_kelas']) ?>"><?= e((string)$k['nama_kelas']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="md:col-span-2">
            <label class="block text-xs text-slate-300 mb-1" for="alamat">Alamat</label>
            <textarea id="alamat" name="alamat" rows="3" class="w-full rounded-2xl border border-slate-700 bg-slate-950/60 px-3 py-2 text-sm outline-none focus:border-cyberlime" placeholder="Alamat (opsional)"></textarea>
        </div>
        <div class="md:col-span-2 flex items-center gap-2">
            <button class="inline-flex items-center justify-center gap-2 rounded-2xl bg-electric text-slate-50 px-4 py-2 text-sm font-semibold shadow-neobrutal hover:-translate-y-0.5 transition">
                Simpan
            </button>
            <a class="inline-flex items-center justify-center gap-2 rounded-2xl border border-slate-700 bg-slate-900/70 px-4 py-2 text-sm font-semibold hover:border-cyberlime hover:text-cyberlime transition" href="<?= e(url_for('/admin/import/siswa')) ?>">
                Import Excel ↗
            </a>
        </div>
    </form>
<?php
admin_layout_end();

