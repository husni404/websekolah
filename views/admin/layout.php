<?php

declare(strict_types=1);

require_once __DIR__ . '/../../app/auth.php';

function admin_layout_start(string $title): void
{
    $user = auth_user();
    $flashError = flash_get('error');
    $flashSuccess = flash_get('success');
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= e($title) ?> - Admin <?= e((string)config('app.name', 'SMK Madani')) ?></title>
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: { electric: '#4f46e5', cyberlime: '#a3e635' },
                        fontFamily: { jakarta: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'] },
                        boxShadow: { neobrutal: '8px 8px 0 0 rgba(15,23,42,1)' }
                    }
                }
            };
        </script>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    </head>
    <body class="min-h-screen bg-slate-950 text-slate-50 font-jakarta">
        <div class="max-w-6xl mx-auto px-4 py-6">
            <div class="flex items-center justify-between gap-4 mb-6">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-2xl bg-cyberlime flex items-center justify-center shadow-neobrutal border-2 border-slate-900">
                        <span class="text-slate-900 font-extrabold text-lg">M</span>
                    </div>
                    <div>
                        <p class="text-[11px] uppercase tracking-[0.25em] text-slate-400">Admin Panel</p>
                        <p class="font-semibold text-sm"><?= e((string)config('app.name', 'SMK Madani')) ?></p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="<?= e(url_for('/')) ?>" class="hidden sm:inline-flex items-center gap-2 rounded-2xl border border-slate-700 bg-slate-900/70 px-3 py-2 text-xs font-semibold hover:border-cyberlime hover:text-cyberlime transition">
                        <span>↩</span><span>Landing</span>
                    </a>
                    <?php if ($user): ?>
                        <span class="hidden md:inline-flex text-xs text-slate-300 px-3 py-2 rounded-2xl border border-slate-800 bg-slate-900/60">
                            <?= e($user['username'] ?? 'admin') ?> · <?= e($user['role'] ?? 'admin') ?>
                        </span>
                        <form method="post" action="<?= e(url_for('/admin/logout')) ?>">
                            <?= csrf_field() ?>
                            <button class="inline-flex items-center gap-2 rounded-2xl border-2 border-slate-100 bg-slate-50 text-slate-900 px-3 py-2 text-xs font-semibold shadow-neobrutal hover:-translate-y-0.5 transition">
                                Logout
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>

            <div class="grid md:grid-cols-[240px_1fr] gap-5">
                <aside class="rounded-3xl border border-slate-800 bg-slate-900/70 p-4 h-fit shadow-neobrutal">
                    <p class="text-[11px] uppercase tracking-[0.25em] text-slate-400 mb-3">Navigasi</p>
                    <nav class="space-y-2 text-sm">
                        <a class="block rounded-2xl px-3 py-2 border border-slate-800 hover:border-cyberlime hover:text-cyberlime transition" href="<?= e(url_for('/admin/dashboard')) ?>">Dashboard</a>
                        <a class="block rounded-2xl px-3 py-2 border border-slate-800 hover:border-cyberlime hover:text-cyberlime transition" href="<?= e(url_for('/admin/sekolah')) ?>">Identitas Sekolah</a>
                        <a class="block rounded-2xl px-3 py-2 border border-slate-800 hover:border-cyberlime hover:text-cyberlime transition" href="<?= e(url_for('/admin/siswa')) ?>">Data Siswa</a>
                        <a class="block rounded-2xl px-3 py-2 border border-slate-800 hover:border-cyberlime hover:text-cyberlime transition" href="<?= e(url_for('/admin/guru')) ?>">Data Guru</a>
                        <a class="block rounded-2xl px-3 py-2 border border-slate-800 hover:border-cyberlime hover:text-cyberlime transition" href="<?= e(url_for('/admin/kelas')) ?>">Data Kelas</a>
                        <a class="block rounded-2xl px-3 py-2 border border-slate-800 hover:border-cyberlime hover:text-cyberlime transition" href="<?= e(url_for('/admin/jurusan')) ?>">Data Jurusan</a>
                        <a class="block rounded-2xl px-3 py-2 border border-slate-800 hover:border-cyberlime hover:text-cyberlime transition" href="<?= e(url_for('/admin/konten')) ?>">Konten & E‑Mading</a>
                        <a class="block rounded-2xl px-3 py-2 border border-slate-800 hover:border-cyberlime hover:text-cyberlime transition" href="<?= e(url_for('/admin/import/siswa')) ?>">Import Excel</a>
                    </nav>
                    <div class="mt-4 pt-4 border-t border-slate-800 text-[11px] text-slate-400">
                        <p class="mb-1">UI: Neobrutal-lite</p>
                        <p>Backend: PHP 8 + PDO</p>
                    </div>
                </aside>

                <main class="rounded-3xl border border-slate-800 bg-slate-900/60 p-5 shadow-[0_0_0_1px_rgba(15,23,42,1),16px_16px_0_0_rgba(15,23,42,1)]">
                    <?php if ($flashError): ?>
                        <div class="mb-4 rounded-2xl border border-red-500/40 bg-red-500/10 px-4 py-3 text-sm text-red-200">
                            <?= e($flashError) ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($flashSuccess): ?>
                        <div class="mb-4 rounded-2xl border border-emerald-500/40 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
                            <?= e($flashSuccess) ?>
                        </div>
                    <?php endif; ?>
    <?php
}

function admin_layout_end(): void
{
    ?>
                </main>
            </div>
        </div>
    </body>
    </html>
    <?php
}

