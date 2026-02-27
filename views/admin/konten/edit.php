<?php

declare(strict_types=1);

require_once __DIR__ . '/../layout.php';

$pdo = pdo();

$id = trim((string)($_GET['id'] ?? ''));
if ($id === '' || !ctype_digit($id)) {
    flash_set('error', 'ID konten tidak valid.');
    redirect('/admin/konten');
}

$stmt = $pdo->prepare('SELECT * FROM konten WHERE id = ? LIMIT 1');
$stmt->execute([(int)$id]);
$konten = $stmt->fetch();
if (!$konten) {
    flash_set('error', 'Konten tidak ditemukan.');
    redirect('/admin/konten');
}

$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

if ($method === 'POST') {
    csrf_verify();

    $judul = trim((string)($_POST['judul'] ?? ''));
    $tipe = (string)($_POST['tipe'] ?? $konten['tipe']);
    $ringkasan = trim((string)($_POST['ringkasan'] ?? ''));
    $isPublished = isset($_POST['is_published']) ? 1 : 0;
    $isReel = isset($_POST['is_reel']) ? 1 : 0;

    if ($judul === '') {
        flash_set('error', 'Judul wajib diisi.');
        redirect('/admin/konten/edit?id=' . $id);
    }

    if (!in_array($tipe, ['info', 'berita', 'mading', 'event'], true)) {
        $tipe = $konten['tipe'];
    }

    $filePath = $konten['file'] ?? null;
    if (!empty($_FILES['file']['name'] ?? '')) {
        $file = $_FILES['file'];
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
            $name = (string)$file['name'];
            $tmpName = (string)$file['tmp_name'];
            $size = (int)$file['size'];

            if ($size > 0 && $size <= 10 * 1024 * 1024) {
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

    $upd = $pdo->prepare('UPDATE konten SET judul = ?, tipe = ?, ringkasan = ?, file = ?, is_published = ?, is_reel = ? WHERE id = ?');
    $upd->execute([
        $judul,
        $tipe,
        ($ringkasan !== '' ? $ringkasan : null),
        $filePath,
        $isPublished,
        $isReel,
        (int)$id,
    ]);

    flash_set('success', 'Konten berhasil diupdate.');
    redirect('/admin/konten');
}

admin_layout_start('Edit Konten');
?>
    <div class="flex items-start justify-between gap-4 mb-4">
        <div>
            <h1 class="text-lg font-bold mb-1">Edit Konten</h1>
            <p class="text-sm text-slate-300">Perbarui info, berita, mading, atau event.</p>
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
                <input id="judul" name="judul" value="<?= e((string)$konten['judul']) ?>" class="w-full rounded-2xl border border-slate-700 bg-slate-950/60 px-3 py-2 text-sm outline-none focus:border-cyberlime" />
            </div>
            <div>
                <label class="block text-xs text-slate-300 mb-1" for="tipe">Tipe</label>
                <select id="tipe" name="tipe" class="w-full rounded-2xl border border-slate-700 bg-slate-950/60 px-3 py-2 text-sm outline-none focus:border-cyberlime">
                    <?php
                    $tipeList = [
                        'info' => 'Info',
                        'berita' => 'Berita',
                        'mading' => 'E-Mading',
                        'event' => 'Event',
                    ];
                    foreach ($tipeList as $k => $label):
                        $selected = ((string)$konten['tipe'] === $k) ? 'selected' : '';
                    ?>
                        <option value="<?= e($k) ?>" <?= $selected ?>><?= e($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-xs text-slate-300 mb-1" for="ringkasan">Ringkasan (opsional)</label>
            <textarea id="ringkasan" name="ringkasan" rows="3" class="w-full rounded-2xl border border-slate-700 bg-slate-950/60 px-3 py-2 text-sm outline-none focus:border-cyberlime"><?= e((string)($konten['ringkasan'] ?? '')) ?></textarea>
        </div>

        <div>
            <label class="block text-xs text-slate-300 mb-1" for="file">Media (opsional)</label>
            <input id="file" name="file" type="file" accept=".jpg,.jpeg,.png,.webp,.mp4,.mov" class="block w-full text-sm text-slate-200 file:mr-3 file:rounded-2xl file:border-0 file:bg-cyberlime file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-slate-900 hover:file:bg-lime-300" />
            <?php if (!empty($konten['file'])): ?>
                <p class="mt-1 text-[11px] text-slate-400">File saat ini: <code><?= e((string)$konten['file']) ?></code></p>
            <?php endif; ?>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
            <label class="inline-flex items-center gap-2 text-xs text-slate-200">
                <input type="checkbox" name="is_published" value="1" class="rounded border-slate-600 bg-slate-950 text-cyberlime" <?= ((int)$konten['is_published'] === 1) ? 'checked' : '' ?> />
                <span>Published (tampil di landing)</span>
            </label>
            <label class="inline-flex items-center gap-2 text-xs text-slate-200">
                <input type="checkbox" name="is_reel" value="1" class="rounded border-slate-600 bg-slate-950 text-cyberlime" <?= ((int)($konten['is_reel'] ?? 0) === 1) ? 'checked' : '' ?> />
                <span>Tandai sebagai Reel highlight</span>
            </label>
        </div>

        <button class="inline-flex items-center justify-center gap-2 rounded-2xl bg-electric text-slate-50 px-4 py-2 text-sm font-semibold shadow-neobrutal hover:-translate-y-0.5 transition">
            Simpan Perubahan
        </button>
    </form>
<?php
admin_layout_end();

