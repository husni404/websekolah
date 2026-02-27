<?php
// Sementara: placeholder sebelum dashboard admin full diimplementasi
?><!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - SMK Madani</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        electric: '#4f46e5',
                        cyberlime: '#a3e635',
                    },
                    fontFamily: {
                        jakarta: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
                    }
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
        <p class="text-[11px] uppercase tracking-[0.25em] text-slate-400 mb-2">Admin Area</p>
        <h1 class="text-xl font-bold mb-2">Dashboard SMK Madani</h1>
        <p class="text-sm text-slate-300 mb-4">
            Halaman ini nanti berisi statistik real‑time, manajemen data siswa/guru/kelas, dan fitur import Excel. Untuk sekarang masih placeholder.
        </p>
        <a href="/" class="inline-flex items-center gap-2 rounded-2xl border-2 border-slate-100 bg-slate-50 text-slate-900 px-4 py-2 text-xs font-semibold shadow-[8px_8px_0_0_rgba(15,23,42,1)] hover:-translate-y-0.5 hover:shadow-[10px_10px_0_0_rgba(15,23,42,1)] transition">
            ← Kembali ke Landing
        </a>
    </div>
</body>
</html>

