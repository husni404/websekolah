<?php
require_once __DIR__ . '/../app/init.php';
?><!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Halaman tidak ditemukan</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-950 text-slate-50 flex items-center justify-center">
    <div class="max-w-md w-full mx-4 rounded-3xl border border-slate-800 bg-slate-900/80 p-6 shadow-[0_0_0_1px_rgba(15,23,42,1),16px_16px_0_0_rgba(15,23,42,1)]">
        <p class="text-[11px] uppercase tracking-[0.25em] text-slate-400 mb-2">404</p>
        <h1 class="text-xl font-bold mb-2">Halaman nggak ketemu</h1>
        <p class="text-sm text-slate-300 mb-4">Coba balik ke home atau dashboard.</p>
        <div class="flex gap-2">
            <a href="<?= e(url_for('/')) ?>" class="inline-flex items-center gap-2 rounded-2xl border-2 border-slate-100 bg-slate-50 text-slate-900 px-4 py-2 text-xs font-semibold shadow-[8px_8px_0_0_rgba(15,23,42,1)] hover:-translate-y-0.5 transition">
                Home
            </a>
            <a href="<?= e(url_for('/admin/dashboard')) ?>" class="inline-flex items-center gap-2 rounded-2xl border border-slate-700 bg-slate-900 text-slate-50 px-4 py-2 text-xs font-semibold hover:border-cyberlime hover:text-cyberlime transition">
                Admin
            </a>
        </div>
    </div>
</body>
</html>

