<?php

declare(strict_types=1);

require_once __DIR__ . '/../../app/auth.php';

$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

if ($method === 'POST') {
    csrf_verify();
    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        flash_set('error', 'Username dan password wajib diisi.');
        redirect('/admin/login');
    }

    if (auth_login($username, $password)) {
        flash_set('success', 'Login berhasil. Selamat datang!');
        redirect('/admin/dashboard');
    }

    flash_set('error', 'Login gagal. Cek username/password.');
    redirect('/admin/login');
}

// If already logged in
if (auth_is_admin()) {
    redirect('/admin/dashboard');
}

$error = flash_get('error');
?><!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - <?= e((string)config('app.name', 'SMK Madani')) ?></title>
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
<body class="min-h-screen bg-slate-950 text-slate-50 font-jakarta flex items-center justify-center">
    <div class="max-w-md w-full mx-4 rounded-3xl border border-slate-800 bg-slate-900/80 p-6 shadow-[0_0_0_1px_rgba(15,23,42,1),16px_16px_0_0_rgba(15,23,42,1)]">
        <p class="text-[11px] uppercase tracking-[0.25em] text-slate-400 mb-2">Admin Login</p>
        <h1 class="text-xl font-bold mb-2">Masuk Dashboard</h1>
        <p class="text-sm text-slate-300 mb-4">Kelola data siswa/guru/kelas dan import Excel.</p>

        <?php if ($error): ?>
            <div class="mb-4 rounded-2xl border border-red-500/40 bg-red-500/10 px-4 py-3 text-sm text-red-200">
                <?= e($error) ?>
            </div>
        <?php endif; ?>

        <form method="post" class="space-y-3">
            <?= csrf_field() ?>
            <div>
                <label class="block text-xs text-slate-300 mb-1" for="username">Username</label>
                <input id="username" name="username" autocomplete="username" class="w-full rounded-2xl border border-slate-700 bg-slate-950/60 px-3 py-2 text-sm outline-none focus:border-cyberlime" placeholder="admin" />
            </div>
            <div>
                <label class="block text-xs text-slate-300 mb-1" for="password">Password</label>
                <input id="password" name="password" type="password" autocomplete="current-password" class="w-full rounded-2xl border border-slate-700 bg-slate-950/60 px-3 py-2 text-sm outline-none focus:border-cyberlime" placeholder="••••••••" />
            </div>
            <button class="w-full inline-flex items-center justify-center gap-2 rounded-2xl border-2 border-slate-100 bg-slate-50 text-slate-900 px-4 py-2 text-sm font-semibold shadow-neobrutal hover:-translate-y-0.5 transition">
                Login
            </button>
        </form>

        <div class="mt-4 text-[11px] text-slate-400">
            <p class="mb-1">Akun default (jika tabel <code>users</code> masih kosong):</p>
            <p>Username: <code>admin</code> · Password: <code>admin123</code></p>
            <p class="mt-1">Untuk produksi, segera ubah password dan buat user baru yang lebih aman.</p>
        </div>

        <div class="mt-4">
            <a href="<?= e(url_for('/')) ?>" class="inline-flex items-center gap-2 rounded-2xl border border-slate-700 bg-slate-900 px-3 py-2 text-xs font-semibold hover:border-cyberlime hover:text-cyberlime transition">
                ← Kembali ke Landing
            </a>
        </div>
    </div>
</body>
</html>

