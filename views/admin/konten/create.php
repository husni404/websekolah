<?php

declare(strict_types=1);

require_once __DIR__ . '/../layout.php';

$pdo = pdo();
$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

if ($method === 'POST') {
    csrf_verify();

    $judul = trim((string)($_POST['judul'] ?? ''));
    $tipe = (string)($_POST['tipe'] ?? 'info');
    $ringkasan = trim((string)($_POST['ringkasan'] ?? ''));
    $isPublished = isset($_POST['is_published']) ? 1 : 0;
    $isReel = isset($_POST['is_reel']) ? 1 : 0;

    if ($judul === '') {
        flash_set('error', 'Judul wajib diisi.');
        redirect('/admin/konten/create');
    }

    if (!in_array($tipe, ['info', 'berita', 'mading', 'event'], true)) {
        $tipe = 'info';
    }

    // Slug sederhana
    $slugBase = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $judul), '-'));
    if ($slugBase === '') {
        $slugBase = 'konten';
    }
    $slug = $slugBase;
    $i = 1;
    while (true) {
        $stmt = $pdo->prepare('SELECT COUNT(*) AS c FROM konten WHERE slug = ?');
        $stmt->execute([$slug]);
        $exists = (int)($stmt->fetch()['c'] ?? 0);
        if ($exists === 0) {
            break;
        }
        $slug = $slugBase . '-' . $i;
        $i++;
    }

    $filePath = null;
    if (!empty($_FILES['file']['name'] ?? '')) {
        $file = $_FILES['file'];
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
            $name = (string)$file['name'];
            $tmpName = (string)$file['tmp_name'];
            $size = (int)$file['size'];

            if ($size > 0 && $size <= 10 * 1024 * 1024) { // 10 MB
                $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'webp', 'mp4', 'mov'];
                if (in_array($ext, $allowed, true)) {
                    $dir = __DIR__ . '/../../../storage/konten';
                    if (!is_dir($dir)) {
                        mkdir($dir, 0775, true);
                    }
                    $targetName = 'konten_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                    $targetPath = $dir . '/' . $targetName;
                    if (move_uploaded_file($tmpName, $targetPath)) {
                        $filePath = 'storage/konten/' . $targetName;
                    }
                }
            }
        }
    }

    $ins = $pdo->prepare('INSERT INTO konten (judul, tipe, slug, ringkasan, file, is_published, is_reel) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $ins->execute([$judul, $tipe, $slug, ($ringkasan !== '' ? $ringkasan : null), $filePath, $isPublished, $isReel]);

    flash_set('success', 'Konten berhasil dibuat.');
    redirect('/admin/konten');
}

admin_layout_start('Konten Baru');
?>
    <div class="flex items-start justify-between gap-4 mb-4">
        <div>
            <h1 class="text-lg font-bold mb-1">Konten Baru</h1>
            <p class="text-sm text-slate-300">Tambah info, berita, mading, atau event untuk landing page.</p>
        </div>
        <a href="<?= e(url_for('/admin/konten')) ?>" class="inline-flex items-center gap-2 rounded-2xl border border-slate-700 bg-slate-900/70 px-3 py-2 text-xs font-semibold hover:border-cyberlime hover:text-cyberlime transition">
            ← Kembali
        </a>
    </div>

    <form method="post" enctype="multipart/form-data" class="space-y-4">
        <?= csrf_field() ?>
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs text-slate-300 mb-1" for="judul">Judul</label>
                <input id="judul" name="judul" class="w-full rounded-2xl border border-slate-700 bg-slate-950/60 px-3 py-2 text-sm outline-none focus:border-cyberlime" />
            </div>
            <div>
                <label class="block text-xs text-slate-300 mb-1" for="tipe">Tipe</label>
                <select id="tipe" name="tipe" class="w-full rounded-2xl border border-slate-700 bg-slate-950/60 px-3 py-2 text-sm outline-none focus:border-cyberlime">
                    <option value="info">Info</option>
                    <option value="berita">Berita</option>
                    <option value="mading">E-Mading</option>
                    <option value="event">Event</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-xs text-slate-300 mb-1" for="ringkasan">Ringkasan (opsional)</label>
            <textarea id="ringkasan" name="ringkasan" rows="3" class="w-full rounded-2xl border border-slate-700 bg-slate-950/60 px-3 py-2 text-sm outline-none focus:border-cyberlime" placeholder="Deskripsi singkat yang akan tampil di kartu."></textarea>
        </div>

        <div>
            <label class="block text-xs text-slate-300 mb-1" for="file">Media (opsional)</label>
            <input id="file" name="file" type="file" accept=".jpg,.jpeg,.png,.webp,.mp4,.mov" class="block w-full text-sm text-slate-200 file:mr-3 file:rounded-2xl file:border-0 file:bg-cyberlime file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-slate-900 hover:file:bg-lime-300" />
            <p class="mt-1 text-[11px] text-slate-400">Gambar/video akan disimpan di folder <code>storage/konten</code>. Maks 10 MB.</p>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
            <label class="inline-flex items-center gap-2 text-xs text-slate-200">
                <input type="checkbox" name="is_published" value="1" class="rounded border-slate-600 bg-slate-950 text-cyberlime" checked />
                <span>Published (tampil di landing)</span>
            </label>
            <label class="inline-flex items-center gap-2 text-xs text-slate-200">
                <input type="checkbox" name="is_reel" value="1" class="rounded border-slate-600 bg-slate-950 text-cyberlime" />
                <span>Tandai sebagai Reel highlight</span>
            </label>
        </div>

        <button class="inline-flex items-center justify-center gap-2 rounded-2xl bg-electric text-slate-50 px-4 py-2 text-sm font-semibold shadow-neobrutal hover:-translate-y-0.5 transition">
            Simpan
        </button>
    </form>
<?php
admin_layout_end();

