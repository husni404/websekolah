<?php

declare(strict_types=1);

require_once __DIR__ . '/../layout.php';

$pdo = pdo();
$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

if ($method === 'POST') {
    csrf_verify();

    $nama = trim((string)($_POST['nama'] ?? ''));
    $singkatan = trim((string)($_POST['singkatan'] ?? ''));
    $tagline = trim((string)($_POST['tagline'] ?? ''));
    $icon = trim((string)($_POST['icon'] ?? ''));
    $highlights = trim((string)($_POST['highlights'] ?? ''));

    if ($nama === '') {
        flash_set('error', 'Nama jurusan wajib diisi.');
        redirect('/admin/jurusan/create');
    }

    $logoPath = null;
    if (!empty($_FILES['logo']['name'] ?? '')) {
        $file = $_FILES['logo'];
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
            $name = (string)$file['name'];
            $tmpName = (string)$file['tmp_name'];
            $size = (int)$file['size'];

            if ($size > 0 && $size <= 5 * 1024 * 1024) {
                $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                $allowed = ['png', 'jpg', 'jpeg', 'webp'];
                if (in_array($ext, $allowed, true)) {
                    $dir = __DIR__ . '/../../../storage/jurusan';
                    if (!is_dir($dir)) {
                        mkdir($dir, 0775, true);
                    }
                    $targetName = 'jurusan_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                    $targetPath = $dir . '/' . $targetName;
                    if (move_uploaded_file($tmpName, $targetPath)) {
                        $logoPath = 'storage/jurusan/' . $targetName;
                    }
                }
            }
        }
    }

    $stmt = $pdo->prepare('INSERT INTO jurusan (nama, singkatan, tagline, icon, highlights, logo_path) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->execute([
        $nama,
        ($singkatan !== '' ? $singkatan : null),
        ($tagline !== '' ? $tagline : null),
        ($icon !== '' ? $icon : null),
        ($highlights !== '' ? $highlights : null),
        $logoPath,
    ]);

    flash_set('success', 'Jurusan berhasil ditambahkan.');
    redirect('/admin/jurusan');
}

admin_layout_start('Tambah Jurusan');
?>
    <div class="flex items-start justify-between gap-4 mb-4">
        <div>
            <h1 class="text-lg font-bold mb-1">Tambah Jurusan</h1>
            <p class="text-sm text-slate-300">Lengkapi informasi jurusan untuk tampil di landing.</p>
        </div>
        <a href="<?= e(url_for('/admin/jurusan')) ?>" class="inline-flex items-center gap-2 rounded-2xl border border-slate-700 bg-slate-900/70 px-3 py-2 text-xs font-semibold hover:border-cyberlime hover:text-cyberlime transition">
            ← Kembali
        </a>
    </div>

    <form method="post" enctype="multipart/form-data" class="space-y-4">
        <?= csrf_field() ?>
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs text-slate-300 mb-1" for="nama">Nama Jurusan</label>
                <input id="nama" name="nama" class="w-full rounded-2xl border border-slate-700 bg-slate-950/60 px-3 py-2 text-sm outline-none focus:border-cyberlime" placeholder="contoh: Rekayasa Perangkat Lunak" />
            </div>
            <div>
                <label class="block text-xs text-slate-300 mb-1" for="singkatan">Singkatan (opsional)</label>
                <input id="singkatan" name="singkatan" class="w-full rounded-2xl border border-slate-700 bg-slate-950/60 px-3 py-2 text-sm outline-none focus:border-cyberlime" placeholder="contoh: RPL" />
            </div>
        </div>

        <div class="grid md:grid-cols-[1.2fr_0.8fr] gap-4">
            <div>
                <label class="block text-xs text-slate-300 mb-1" for="tagline">Tagline singkat (opsional)</label>
                <input id="tagline" name="tagline" class="w-full rounded-2xl border border-slate-700 bg-slate-950/60 px-3 py-2 text-sm outline-none focus:border-cyberlime" placeholder="contoh: Tech & Software" />
            </div>
            <div>
                <label class="block text-xs text-slate-300 mb-1" for="icon">Ikon emoji (opsional)</label>
                <input id="icon" name="icon" class="w-full rounded-2xl border border-slate-700 bg-slate-950/60 px-3 py-2 text-sm outline-none focus:border-cyberlime" placeholder="contoh: 💻" />
            </div>
        </div>

        <div>
            <label class="block text-xs text-slate-300 mb-1" for="highlights">Highlight keahlian (satu per baris)</label>
            <textarea id="highlights" name="highlights" rows="4" class="w-full rounded-2xl border border-slate-700 bg-slate-950/60 px-3 py-2 text-sm outline-none focus:border-cyberlime" placeholder="contoh:
Web Dev
Mobile App
UI/UX Dasar"></textarea>
        </div>

        <div>
            <label class="block text-xs text-slate-300 mb-1" for="logo">Logo Jurusan (opsional)</label>
            <input id="logo" name="logo" type="file" accept=".png,.jpg,.jpeg,.webp" class="block w-full text-sm text-slate-200 file:mr-3 file:rounded-2xl file:border-0 file:bg-cyberlime file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-slate-900 hover:file:bg-lime-300" />
            <p class="mt-1 text-[11px] text-slate-400">Disarankan PNG background transparan. Maks 5 MB.</p>
        </div>

        <button class="inline-flex items-center justify-center gap-2 rounded-2xl bg-electric text-slate-50 px-4 py-2 text-sm font-semibold shadow-neobrutal hover:-translate-y-0.5 transition">
            Simpan
        </button>
    </form>
<?php
admin_layout_end();

