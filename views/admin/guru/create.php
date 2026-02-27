<?php

declare(strict_types=1);

require_once __DIR__ . '/../layout.php';

$pdo = pdo();
$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

if ($method === 'POST') {
    csrf_verify();
    $nip = trim((string)($_POST['nip'] ?? ''));
    $nama = trim((string)($_POST['nama'] ?? ''));
    $mapel = trim((string)($_POST['mapel'] ?? ''));
    $jabatan = trim((string)($_POST['jabatan'] ?? ''));
    $kategori = trim((string)($_POST['kategori'] ?? 'guru'));
    $tugasTambahan = trim((string)($_POST['tugas_tambahan'] ?? ''));

    if ($nip === '' || $nama === '') {
        flash_set('error', 'NIP dan Nama wajib diisi.');
        redirect('/admin/guru/create');
    }

    $stmt = $pdo->prepare('SELECT COUNT(*) AS c FROM guru WHERE nip = ?');
    $stmt->execute([$nip]);
    $exists = (int)($stmt->fetch()['c'] ?? 0);
    if ($exists > 0) {
        flash_set('error', 'Gagal: NIP sudah terdaftar.');
        redirect('/admin/guru/create');
    }

    if (!in_array($kategori, ['guru', 'staff', 'kepala'], true)) {
        $kategori = 'guru';
    }

    $fotoPath = null;
    if (!empty($_FILES['foto']['name'] ?? '')) {
        $file = $_FILES['foto'];
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
            $name = (string)$file['name'];
            $tmpName = (string)$file['tmp_name'];
            $size = (int)$file['size'];

            if ($size > 0 && $size <= 3 * 1024 * 1024) {
                $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                $allowed = ['png', 'jpg', 'jpeg', 'webp'];
                if (in_array($ext, $allowed, true)) {
                    $dir = __DIR__ . '/../../../storage/guru';
                    if (!is_dir($dir)) {
                        mkdir($dir, 0775, true);
                    }
                    $targetName = 'guru_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                    $targetPath = $dir . '/' . $targetName;
                    if (move_uploaded_file($tmpName, $targetPath)) {
                        $fotoPath = 'storage/guru/' . $targetName;
                    }
                }
            }
        }
    }

    $ins = $pdo->prepare('INSERT INTO guru (nip, nama, mapel, jabatan, kategori, tugas_tambahan, foto) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $ins->execute([
        $nip,
        $nama,
        ($mapel !== '' ? $mapel : null),
        ($jabatan !== '' ? $jabatan : null),
        $kategori,
        ($tugasTambahan !== '' ? $tugasTambahan : null),
        $fotoPath,
    ]);

    flash_set('success', 'Guru berhasil ditambahkan.');
    redirect('/admin/guru');
}

admin_layout_start('Tambah Guru');
?>
    <div class="flex items-start justify-between gap-4 mb-4">
        <div>
            <h1 class="text-lg font-bold mb-1">Tambah Guru</h1>
            <p class="text-sm text-slate-300">Masukkan data guru dan mapel yang diajarkan.</p>
        </div>
        <a href="<?= e(url_for('/admin/guru')) ?>" class="inline-flex items-center gap-2 rounded-2xl border border-slate-700 bg-slate-900/70 px-3 py-2 text-xs font-semibold hover:border-cyberlime hover:text-cyberlime transition">
            ← Kembali
        </a>
    </div>

    <form method="post" enctype="multipart/form-data" class="grid md:grid-cols-2 gap-4">
        <?= csrf_field() ?>
        <div>
            <label class="block text-xs text-slate-300 mb-1" for="nip">NIP</label>
            <input id="nip" name="nip" class="w-full rounded-2xl border border-slate-700 bg-slate-950/60 px-3 py-2 text-sm outline-none focus:border-cyberlime" placeholder="contoh: 1989123456" />
        </div>
        <div>
            <label class="block text-xs text-slate-300 mb-1" for="nama">Nama</label>
            <input id="nama" name="nama" class="w-full rounded-2xl border border-slate-700 bg-slate-950/60 px-3 py-2 text-sm outline-none focus:border-cyberlime" placeholder="Nama guru" />
        </div>
        <div>
            <label class="block text-xs text-slate-300 mb-1" for="mapel">Mapel</label>
            <input id="mapel" name="mapel" class="w-full rounded-2xl border border-slate-700 bg-slate-950/60 px-3 py-2 text-sm outline-none focus:border-cyberlime" placeholder="contoh: Matematika" />
        </div>
        <div>
            <label class="block text-xs text-slate-300 mb-1" for="jabatan">Jabatan</label>
            <input id="jabatan" name="jabatan" class="w-full rounded-2xl border border-slate-700 bg-slate-950/60 px-3 py-2 text-sm outline-none focus:border-cyberlime" placeholder="contoh: Wali Kelas, Wakasek, TU" />
        </div>
        <div>
            <label class="block text-xs text-slate-300 mb-1" for="kategori">Kategori</label>
            <select id="kategori" name="kategori" class="w-full rounded-2xl border border-slate-700 bg-slate-950/60 px-3 py-2 text-sm outline-none focus:border-cyberlime">
                <option value="guru">Guru</option>
                <option value="staff">Staff / TU</option>
                <option value="kepala">Kepala Sekolah</option>
            </select>
        </div>
        <div class="md:col-span-2">
            <label class="block text-xs text-slate-300 mb-1" for="tugas_tambahan">Tugas Tambahan (opsional)</label>
            <input id="tugas_tambahan" name="tugas_tambahan" class="w-full rounded-2xl border border-slate-700 bg-slate-950/60 px-3 py-2 text-sm outline-none focus:border-cyberlime" placeholder="contoh: Pembina OSIS, Koordinator Prakerin, dll." />
        </div>
        <div class="md:col-span-2">
            <label class="block text-xs text-slate-300 mb-1" for="foto">Foto 3x4 (disarankan)</label>
            <input id="foto" name="foto" type="file" accept=".png,.jpg,.jpeg,.webp" class="block w-full text-sm text-slate-200 file:mr-3 file:rounded-2xl file:border-0 file:bg-cyberlime file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-slate-900 hover:file:bg-lime-300" />
        </div>
        <div class="md:col-span-2">
            <button class="inline-flex items-center justify-center gap-2 rounded-2xl bg-electric text-slate-50 px-4 py-2 text-sm font-semibold shadow-neobrutal hover:-translate-y-0.5 transition">
                Simpan
            </button>
        </div>
    </form>
<?php
admin_layout_end();

