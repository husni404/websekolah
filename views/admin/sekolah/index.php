<?php

declare(strict_types=1);

require_once __DIR__ . '/../layout.php';

$pdo = pdo();

// Pastikan tabel sekolah_settings ada (auto-create jika belum)
try {
    $pdo->query('SELECT 1 FROM sekolah_settings LIMIT 1');
} catch (Throwable $e) {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS sekolah_settings (
          id TINYINT UNSIGNED NOT NULL PRIMARY KEY DEFAULT 1,
          nama VARCHAR(150) NOT NULL,
          tagline VARCHAR(255) NULL,
          deskripsi TEXT NULL,
          alamat TEXT NULL,
          telp VARCHAR(50) NULL,
          email VARCHAR(100) NULL,
          website VARCHAR(150) NULL,
          logo_path VARCHAR(255) NULL,
          map_embed_url TEXT NULL,
          created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
          updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB;
    ");
    $pdo->exec("
        INSERT IGNORE INTO sekolah_settings (id, nama, tagline)
        VALUES (1, 'SMK Madani', 'Future Ready Vocational School');
    ");
}

$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

if ($method === 'POST') {
    csrf_verify();

    $nama = trim((string)($_POST['nama'] ?? ''));
    $tagline = trim((string)($_POST['tagline'] ?? ''));
    $deskripsi = trim((string)($_POST['deskripsi'] ?? ''));
    $alamat = trim((string)($_POST['alamat'] ?? ''));
    $telp = trim((string)($_POST['telp'] ?? ''));
    $email = trim((string)($_POST['email'] ?? ''));
    $website = trim((string)($_POST['website'] ?? ''));
    $mapEmbed = trim((string)($_POST['map_embed_url'] ?? ''));

    if ($nama === '') {
        flash_set('error', 'Nama sekolah wajib diisi.');
        redirect('/admin/sekolah');
    }

    // Handle upload logo (opsional)
    $logoPath = null;
    $stmtCurrent = $pdo->prepare('SELECT logo_path FROM sekolah_settings WHERE id = 1');
    $stmtCurrent->execute();
    $current = $stmtCurrent->fetch() ?: [];
    $currentLogo = $current['logo_path'] ?? null;

    if (!empty($_FILES['logo']['name'] ?? '')) {
        $file = $_FILES['logo'];
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
            $name = (string)$file['name'];
            $tmpName = (string)$file['tmp_name'];
            $size = (int)$file['size'];

            if ($size > 0 && $size <= 5 * 1024 * 1024) { // 5 MB
                $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                $allowed = ['png', 'jpg', 'jpeg', 'webp', 'svg'];
                if (in_array($ext, $allowed, true)) {
                    $dir = __DIR__ . '/../../../storage/sekolah';
                    if (!is_dir($dir)) {
                        mkdir($dir, 0775, true);
                    }
                    $targetName = 'logo_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                    $targetPath = $dir . '/' . $targetName;
                    if (move_uploaded_file($tmpName, $targetPath)) {
                        $logoPath = 'storage/sekolah/' . $targetName;
                    }
                }
            }
        }
    }

    if ($logoPath === null) {
        $logoPath = $currentLogo;
    }

    $stmt = $pdo->prepare('
        INSERT INTO sekolah_settings (id, nama, tagline, deskripsi, alamat, telp, email, website, logo_path, map_embed_url)
        VALUES (1, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            nama = VALUES(nama),
            tagline = VALUES(tagline),
            deskripsi = VALUES(deskripsi),
            alamat = VALUES(alamat),
            telp = VALUES(telp),
            email = VALUES(email),
            website = VALUES(website),
            logo_path = VALUES(logo_path),
            map_embed_url = VALUES(map_embed_url)
    ');
    $stmt->execute([
        $nama,
        ($tagline !== '' ? $tagline : null),
        ($deskripsi !== '' ? $deskripsi : null),
        ($alamat !== '' ? $alamat : null),
        ($telp !== '' ? $telp : null),
        ($email !== '' ? $email : null),
        ($website !== '' ? $website : null),
        $logoPath,
        ($mapEmbed !== '' ? $mapEmbed : null),
    ]);

    flash_set('success', 'Identitas sekolah berhasil disimpan.');
    redirect('/admin/sekolah');
}

$stmt = $pdo->prepare('SELECT * FROM sekolah_settings WHERE id = 1 LIMIT 1');
$stmt->execute();
$sekolah = $stmt->fetch() ?: [
    'nama' => 'SMK Madani',
    'tagline' => 'Future Ready Vocational School',
    'deskripsi' => null,
    'alamat' => null,
    'telp' => null,
    'email' => null,
    'website' => null,
    'logo_path' => null,
    'map_embed_url' => null,
];

admin_layout_start('Identitas Sekolah');
?>
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-4">
        <div>
            <h1 class="text-lg font-bold mb-1">Identitas Sekolah</h1>
            <p class="text-sm text-slate-300">Edit nama, tagline, deskripsi, logo, dan lokasi SMK Madani.</p>
        </div>
    </div>

    <form method="post" enctype="multipart/form-data" class="grid md:grid-cols-[1.4fr_1fr] gap-6">
        <?= csrf_field() ?>
        <div class="space-y-4">
            <div>
                <label class="block text-xs text-slate-300 mb-1" for="nama">Nama Sekolah</label>
                <input id="nama" name="nama" value="<?= e((string)$sekolah['nama']) ?>" class="w-full rounded-2xl border border-slate-700 bg-slate-950/60 px-3 py-2 text-sm outline-none focus:border-cyberlime" />
            </div>
            <div>
                <label class="block text-xs text-slate-300 mb-1" for="tagline">Tagline</label>
                <input id="tagline" name="tagline" value="<?= e((string)($sekolah['tagline'] ?? '')) ?>" class="w-full rounded-2xl border border-slate-700 bg-slate-950/60 px-3 py-2 text-sm outline-none focus:border-cyberlime" placeholder="contoh: Future Ready Vocational School" />
            </div>
            <div>
                <label class="block text-xs text-slate-300 mb-1" for="deskripsi">Deskripsi Singkat</label>
                <textarea id="deskripsi" name="deskripsi" rows="3" class="w-full rounded-2xl border border-slate-700 bg-slate-950/60 px-3 py-2 text-sm outline-none focus:border-cyberlime" placeholder="Deskripsi singkat sekolah untuk ditampilkan di landing."><?= e((string)($sekolah['deskripsi'] ?? '')) ?></textarea>
            </div>
            <div>
                <label class="block text-xs text-slate-300 mb-1" for="alamat">Alamat</label>
                <textarea id="alamat" name="alamat" rows="2" class="w-full rounded-2xl border border-slate-700 bg-slate-950/60 px-3 py-2 text-sm outline-none focus:border-cyberlime"><?= e((string)($sekolah['alamat'] ?? '')) ?></textarea>
            </div>
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs text-slate-300 mb-1" for="telp">Telepon</label>
                    <input id="telp" name="telp" value="<?= e((string)($sekolah['telp'] ?? '')) ?>" class="w-full rounded-2xl border border-slate-700 bg-slate-950/60 px-3 py-2 text-sm outline-none focus:border-cyberlime" />
                </div>
                <div>
                    <label class="block text-xs text-slate-300 mb-1" for="email">Email</label>
                    <input id="email" name="email" type="email" value="<?= e((string)($sekolah['email'] ?? '')) ?>" class="w-full rounded-2xl border border-slate-700 bg-slate-950/60 px-3 py-2 text-sm outline-none focus:border-cyberlime" />
                </div>
            </div>
            <div>
                <label class="block text-xs text-slate-300 mb-1" for="website">Website (opsional)</label>
                <input id="website" name="website" value="<?= e((string)($sekolah['website'] ?? '')) ?>" class="w-full rounded-2xl border border-slate-700 bg-slate-950/60 px-3 py-2 text-sm outline-none focus:border-cyberlime" placeholder="contoh: https://smkmadanicianjur.sch.id" />
            </div>
        </div>

        <div class="space-y-4">
            <div class="rounded-3xl border border-slate-700 bg-slate-950/50 p-4">
                <p class="text-[11px] uppercase tracking-[0.25em] text-slate-400 mb-2">Logo Sekolah</p>
                <?php if (!empty($sekolah['logo_path'])): ?>
                    <div class="mb-3 flex items-center gap-3">
                        <div class="h-12 w-12 rounded-2xl bg-slate-900 flex items-center justify-center border border-slate-700 overflow-hidden">
                            <img src="<?= e((string)$sekolah['logo_path']) ?>" alt="Logo Sekolah" class="max-h-12 max-w-12 object-contain" />
                        </div>
                        <div class="text-[11px] text-slate-300">
                            <p>Logo saat ini digunakan di header landing & admin.</p>
                        </div>
                    </div>
                <?php else: ?>
                    <p class="text-[11px] text-slate-400 mb-3">Belum ada logo yang diupload. Akan memakai logo default huruf "M".</p>
                <?php endif; ?>

                <label class="block text-xs text-slate-300 mb-1" for="logo">Upload Logo Baru</label>
                <input id="logo" name="logo" type="file" accept=".png,.jpg,.jpeg,.webp,.svg" class="block w-full text-sm text-slate-200 file:mr-3 file:rounded-2xl file:border-0 file:bg-cyberlime file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-slate-900 hover:file:bg-lime-300" />
                <p class="mt-1 text-[11px] text-slate-400">Disarankan PNG / SVG dengan background transparan. Maks 5 MB.</p>
            </div>

            <div class="rounded-3xl border border-slate-700 bg-slate-950/50 p-4">
                <p class="text-[11px] uppercase tracking-[0.25em] text-slate-400 mb-2">Lokasi (Google Maps Embed)</p>
                <p class="text-[11px] text-slate-300 mb-2">
                    Tempel kode <em>embed</em> Google Maps (iframe) atau URL embed di sini. Akan ditampilkan di section "Lokasi" landing.
                </p>
                <textarea id="map_embed_url" name="map_embed_url" rows="3" class="w-full rounded-2xl border border-slate-700 bg-slate-950/60 px-3 py-2 text-xs outline-none focus:border-cyberlime" placeholder="contoh: &lt;iframe src='https://www.google.com/maps/embed?...' ...&gt;&lt;/iframe&gt;"><?= e((string)($sekolah['map_embed_url'] ?? '')) ?></textarea>
            </div>

            <div>
                <button class="inline-flex items-center justify-center gap-2 rounded-2xl bg-electric text-slate-50 px-4 py-2 text-sm font-semibold shadow-neobrutal hover:-translate-y-0.5 transition">
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </form>
<?php
admin_layout_end();

