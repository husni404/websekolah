<?php
// Landing page SMK Madani - Neobrutal / Glassmorphism
?><!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMK Madani - Sekolah Future Ready</title>
    <!-- Tailwind CSS via CDN (for rapid prototyping) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        electric: '#4f46e5',
                        cyberlime: '#a3e635',
                        deepPurple: '#1e1b4b',
                    },
                    fontFamily: {
                        jakarta: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
                        inter: ['Inter', 'system-ui', 'sans-serif'],
                    },
                    boxShadow: {
                        neobrutal: '8px 8px 0 0 rgba(15,23,42,1)',
                    }
                }
            }
        };
    </script>
    <!-- Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <!-- AOS Animation -->
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css">
    <style>
        .glass-card {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
        }
        /* Theme switching (day / night) */
        body.theme-light {
            background-color: #f8fafc;
            color: #0f172a;
        }
        body.theme-light .bg-slate-900,
        body.theme-light .bg-slate-950 {
            background-color: #f9fafb !important;
        }
        body.theme-light .bg-slate-800 {
            background-color: #e5e7eb !important;
        }
        body.theme-light .border-slate-900,
        body.theme-light .border-slate-800,
        body.theme-light .border-slate-700 {
            border-color: #cbd5f5 !important;
        }
        body.theme-light .text-slate-50 {
            color: #020617 !important;
        }
        body.theme-light .text-slate-400,
        body.theme-light .text-slate-300 {
            color: #4b5563 !important;
        }
        body.theme-light .glass-card {
            background: rgba(255, 255, 255, 0.8);
        }
    </style>
</head>
<body class="bg-slate-900 text-slate-50 font-jakarta min-h-screen flex flex-col theme-dark">
    <?php
    $sekolah = [
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
    try {
        $pdoInfo = pdo();
        $stmtInfo = $pdoInfo->query('SELECT * FROM sekolah_settings WHERE id = 1 LIMIT 1');
        $rowInfo = $stmtInfo->fetch();
        if ($rowInfo) {
            $sekolah = array_merge($sekolah, $rowInfo);
        }
    } catch (Throwable $e) {
    }

    $heroReel = null;
    try {
        $pdoHero = pdo();
        $stmtHero = $pdoHero->prepare("SELECT judul, ringkasan FROM konten WHERE is_reel = 1 AND is_published = 1 ORDER BY tgl_upload DESC, id DESC LIMIT 1");
        $stmtHero->execute();
        $heroReel = $stmtHero->fetch() ?: null;
    } catch (Throwable $e) {
        $heroReel = null;
    }
    ?>
    <!-- Top navbar -->
    <header class="sticky top-0 z-30 border-b border-slate-800/80 bg-slate-900/90 backdrop-blur-xl">
        <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <?php if (!empty($sekolah['logo_path'])): ?>
                    <div class="h-9 w-9 rounded-2xl bg-slate-950 flex items-center justify-center shadow-neobrutal border-2 border-slate-900 overflow-hidden">
                        <img src="<?= e((string)$sekolah['logo_path']) ?>" alt="Logo Sekolah" class="max-h-9 max-w-9 object-contain" />
                    </div>
                <?php else: ?>
                    <div class="h-9 w-9 rounded-2xl bg-cyberlime flex items-center justify-center shadow-neobrutal border-2 border-slate-900">
                        <span class="text-slate-900 font-extrabold text-lg">M</span>
                    </div>
                <?php endif; ?>
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400"><?= e((string)($sekolah['nama'] ?? 'SMK Madani')) ?></p>
                    <p class="font-semibold text-sm"><?= e((string)($sekolah['tagline'] ?? 'Future Ready Vocational School')) ?></p>
                </div>
            </div>
            <nav class="hidden md:flex items-center gap-6 text-sm">
                <a href="#about" class="hover:text-cyberlime transition-colors">Tentang</a>
                <a href="#majors" class="hover:text-cyberlime transition-colors">Jurusan</a>
                <a href="#guru" class="hover:text-cyberlime transition-colors">Guru & Staff</a>
                <a href="#emading" class="hover:text-cyberlime transition-colors">E‑Mading</a>
                <a href="#konten" class="hover:text-cyberlime transition-colors">Event</a>
                <a href="#lokasi" class="hover:text-cyberlime transition-colors">Lokasi</a>
            </nav>
            <div class="flex items-center gap-3">
                <button id="themeToggle" class="relative inline-flex h-9 w-16 items-center rounded-full bg-slate-800/90 border border-slate-700/80 px-1">
                    <span class="absolute inset-y-0 left-1 flex items-center">
                        <span class="h-7 w-7 rounded-full bg-slate-100 text-slate-900 shadow-neobrutal flex items-center justify-center text-xs font-bold transition-transform duration-300 toggle-knob">
                            ☀
                        </span>
                    </span>
                    <span class="flex-1 text-[10px] text-center text-slate-400 ml-7">Dark</span>
                </button>
                <a href="/admin" class="hidden sm:inline-flex items-center gap-2 rounded-xl border-2 border-slate-100 bg-slate-50 text-slate-900 px-3 py-1.5 text-xs font-semibold shadow-neobrutal hover:-translate-y-0.5 hover:shadow-[10px_10px_0_0_rgba(15,23,42,1)] transition">
                    <span>Admin Login</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Hero section -->
    <main class="flex-1">
        <section class="relative overflow-hidden">
            <div class="absolute -top-32 -right-32 h-72 w-72 rounded-[3rem] bg-electric/40 blur-3xl"></div>
            <div class="absolute -bottom-24 -left-16 h-72 w-72 rounded-[3rem] bg-cyberlime/30 blur-3xl"></div>

            <div class="max-w-6xl mx-auto px-4 py-12 md:py-20 grid md:grid-cols-[1.1fr_0.9fr] gap-10 items-center">
                <div data-aos="fade-right">
                    <div class="inline-flex items-center gap-2 rounded-full border border-slate-700 bg-slate-900/70 px-3 py-1 text-[11px] mb-4">
                        <span class="inline-flex h-2 w-2 rounded-full bg-cyberlime animate-pulse"></span>
                        <span class="uppercase tracking-[0.2em] text-slate-400">Sekolah Gen Z Friendly</span>
                    </div>
                    <h1 class="text-3xl md:text-5xl font-extrabold leading-tight mb-4">
                        <del>Bukan Web Pemerintah.</del><br>
                        <p><span class="bg-cyberlime text-slate-900 px-2 rounded-lg shadow-neobrutal">Ini Portal <?= e((string)($sekolah['nama'] ?? 'SMK Madani')) ?>.</span></p>
                    </h1>
                    <p class="text-sm md:text-base text-slate-300 max-w-xl mb-6">
                        <?= e((string)($sekolah['deskripsi'] ?? 'Semua tentang SMK Madani dalam satu layar: jurusan future‑ready, e‑mading harian ala feed, sampai update event yang dikemas estetik. Dibuat supaya anak SMK nggak kabur duluan liat tampilan web.')) ?>
                    </p>
                    <div class="flex flex-wrap gap-3 mb-8">
                        <a href="#majors" class="inline-flex items-center gap-2 rounded-2xl bg-electric text-slate-50 px-4 py-2 text-sm font-semibold shadow-neobrutal hover:-translate-y-0.5 hover:shadow-[10px_10px_0_0_rgba(15,23,42,1)] transition">
                            <span>Eksplor Jurusan</span>
                            <span class="text-xs">↗</span>
                        </a>
                        <a href="#emading" class="inline-flex items-center gap-2 rounded-2xl border-2 border-slate-100 bg-slate-50 text-slate-900 px-4 py-2 text-sm font-semibold shadow-neobrutal hover:-translate-y-0.5 hover:shadow-[10px_10px_0_0_rgba(15,23,42,1)] transition">
                            <span>Lihat E‑Mading</span>
                        </a>
                    </div>
                    <div class="grid grid-cols-3 gap-4 text-xs text-slate-300">
                        <div class="rounded-2xl border border-slate-700 bg-slate-900/60 p-3 shadow-neobrutal">
                            <p class="text-[10px] uppercase tracking-[0.15em] text-slate-400 mb-1">Program Keahlian</p>
                            <p class="text-lg font-bold">5+</p>
                            <p>Siap industri kreatif dan teknologi.</p>
                        </div>
                        <div class="rounded-2xl border border-slate-700 bg-slate-900/60 p-3 shadow-neobrutal">
                            <p class="text-[10px] uppercase tracking-[0.15em] text-slate-400 mb-1">Prestasi</p>
                            <p class="text-lg font-bold">Puluhan</p>
                            <p>Lomba tingkat kota, provinsi, hingga nasional.</p>
                        </div>
                        <div class="rounded-2xl border border-slate-700 bg-slate-900/60 p-3 shadow-neobrutal">
                            <p class="text-[10px] uppercase tracking-[0.15em] text-slate-400 mb-1">Support</p>
                            <p class="text-lg font-bold">Mentor</p>
                            <p>Guru dan instruktur berpengalaman industri.</p>
                        </div>
                    </div>
                </div>
                <!-- Hero “video” mockup ala Reels/TikTok -->
                <div class="relative" data-aos="fade-left">
                    <div class="absolute -top-6 -right-6 h-16 w-16 rounded-3xl bg-cyberlime shadow-neobrutal flex items-center justify-center text-slate-900 text-xs font-bold uppercase tracking-[0.2em] rotate-3">
                        Live<br>Campus
                    </div>
                    <div class="glass-card rounded-[2rem] border border-slate-600/70 p-4 shadow-[0_0_0_1px_rgba(15,23,42,0.8),18px_18px_0_0_rgba(15,23,42,1)]">
                        <div class="flex items-center justify-between mb-3 text-xs text-slate-300">
                            <span class="inline-flex items-center gap-2">
                                <span class="h-2 w-2 rounded-full bg-red-500 animate-pulse"></span>
                                <span>Highlight Kegiatan SMK Madani</span>
                            </span>
                            <span class="text-[10px] uppercase tracking-[0.15em] bg-slate-900/60 px-2 py-0.5 rounded-full border border-slate-700">
                                Reel Mode
                            </span>
                        </div>
                        <div class="aspect-[9/16] rounded-[1.5rem] overflow-hidden bg-slate-800 border border-slate-700 relative flex items-center justify-center">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-transparent to-slate-900/50"></div>
                            <div class="absolute inset-0 bg-[radial-gradient(circle_at_10%_20%,rgba(163,230,53,0.12),transparent_60%),radial-gradient(circle_at_80%_80%,rgba(79,70,229,0.25),transparent_55%)]"></div>
                            <div class="relative z-10 flex flex-col items-center justify-center text-center px-6">
                                <span class="text-[11px] uppercase tracking-[0.25em] text-slate-300 mb-2">Campus Reel</span>
                                <?php if ($heroReel): ?>
                                    <p class="text-lg font-bold mb-2">
                                        <?= e((string)$heroReel['judul']) ?>
                                    </p>
                                    <p class="text-xs text-slate-200 mb-4">
                                        <?= e((string)($heroReel['ringkasan'] ?? 'Highlight kegiatan terbaru dari siswa dan guru.')) ?>
                                    </p>
                                <?php else: ?>
                                    <p class="text-lg font-bold mb-2">Belajar, Ngonten, Berprestasi.</p>
                                    <p class="text-xs text-slate-200 mb-4">Potongan kegiatan siswa: praktikum, project kolaborasi, sampai lomba konten kreatif.</p>
                                <?php endif; ?>
                                <a href="#emading" class="inline-flex items-center gap-2 rounded-full bg-slate-50 text-slate-900 px-4 py-1.5 text-[11px] font-semibold shadow-neobrutal">
                                    <span>▶ Play Highlight</span>
                                </a>
                            </div>
                        </div>
                        <div class="mt-3 flex items-center justify-between text-[11px] text-slate-300">
                            <span>Swipe untuk lihat jurusan & event</span>
                            <div class="flex gap-1">
                                <span class="h-1.5 w-5 rounded-full bg-slate-50"></span>
                                <span class="h-1.5 w-3 rounded-full bg-slate-500"></span>
                                <span class="h-1.5 w-2 rounded-full bg-slate-600"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Bento grid: About, Jurusan, Guru, E‑Mading, Event -->
        <section id="about" class="bg-slate-950/40 border-t border-slate-800/80">
            <div class="max-w-6xl mx-auto px-4 py-12 md:py-16">
                <div class="mb-6 flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl md:text-2xl font-bold mb-1">Apa itu <?= e((string)($sekolah['nama'] ?? 'SMK Madani')) ?>?</h2>
                        <p class="text-sm text-slate-300 max-w-lg">
                            Sekolah vokasi yang nyambung sama industri, kreatif, dan kehidupan digital anak muda masa kini—tanpa ninggalin karakter dan akhlak.
                        </p>
                    </div>
                    <div class="hidden md:flex flex-wrap gap-2 text-[11px] text-slate-300">
                        <span class="px-2 py-1 rounded-full border border-slate-700 bg-slate-900/60">#FutureReady</span>
                        <span class="px-2 py-1 rounded-full border border-slate-700 bg-slate-900/60">#TechVocational</span>
                        <span class="px-2 py-1 rounded-full border border-slate-700 bg-slate-900/60">#GenZFriendly</span>
                    </div>
                </div>

                <div class="grid md:grid-cols-4 gap-4 md:gap-5">
                    <!-- Visi Misi Card -->
                    <div class="md:col-span-2 rounded-3xl border-2 border-slate-50 bg-slate-50 text-slate-900 p-5 shadow-neobrutal" data-aos="fade-up">
                        <p class="text-[11px] uppercase tracking-[0.25em] text-slate-500 mb-2">Visi & Misi</p>
                        <p class="font-semibold mb-3">Mencetak lulusan siap kerja, siap kuliah, dan siap berkarya di ekosistem digital.</p>
                        <ul class="text-sm space-y-1.5 list-disc list-inside">
                            <li>Project based learning dengan kolaborasi industri.</li>
                            <li>Penguatan karakter madani: religius, beretika, peduli.</li>
                            <li>Literasi digital, kreatif, dan kewirausahaan.</li>
                        </ul>
                    </div>

                    <!-- Info Singkat -->
                    <div class="rounded-3xl border border-slate-700 bg-slate-900/70 p-4 flex flex-col justify-between" data-aos="fade-up" data-aos-delay="80">
                        <div>
                            <p class="text-[11px] uppercase tracking-[0.25em] text-slate-400 mb-2">Info Singkat</p>
                            <p class="text-sm text-slate-200 mb-3">
                                Terakreditasi, dengan fasilitas laboratorium, studio kreatif, dan ruang belajar yang nyaman.
                            </p>
                        </div>
                        <button class="self-start text-[11px] font-semibold px-3 py-1.5 rounded-full border border-slate-500 hover:border-cyberlime hover:text-cyberlime transition">
                            Lihat Profil Lengkap
                        </button>
                    </div>

                    <!-- Quick Stats -->
                    <div class="rounded-3xl border border-slate-700 bg-gradient-to-br from-slate-900 via-deepPurple/40 to-electric/40 p-4 text-slate-50" data-aos="fade-up" data-aos-delay="120">
                        <p class="text-[11px] uppercase tracking-[0.25em] text-slate-300 mb-2">Snapshot</p>
                        <div class="space-y-2 text-xs">
                            <div class="flex justify-between">
                                <span>Siswa aktif</span>
                                <span class="font-semibold">± 500</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Guru & Staff</span>
                                <span class="font-semibold">40+</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Kerja sama industri</span>
                                <span class="font-semibold">10+</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Guru & Staff -->
        <section id="guru" class="border-t border-slate-800/80 bg-slate-900">
            <div class="max-w-6xl mx-auto px-4 py-12 md:py-16">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-xl md:text-2xl font-bold mb-1">Guru & Staff</h2>
                        <p class="text-sm text-slate-300">Kenal lebih dekat dengan guru, kepala sekolah, dan staff yang mendampingi siswa.</p>
                    </div>
                </div>
                <?php
                $guruList = [];
                $kepala = [];
                $staff = [];
                try {
                    $pdoGuru = pdo();
                    $stmtGuru = $pdoGuru->query('SELECT nama, mapel, jabatan, kategori, tugas_tambahan, foto FROM guru ORDER BY nama ASC');
                    $allGuru = $stmtGuru->fetchAll() ?: [];
                } catch (Throwable $e) {
                    $allGuru = [];
                }

                foreach ($allGuru as $g) {
                    $kat = (string)($g['kategori'] ?? 'guru');
                    if ($kat === 'kepala') {
                        $kepala[] = $g;
                    } elseif ($kat === 'staff') {
                        $staff[] = $g;
                    } else {
                        $guruList[] = $g;
                    }
                }
                ?>
                <div class="grid md:grid-cols-3 gap-4 md:gap-5">
                    <div class="rounded-3xl border border-slate-700 bg-slate-900/70 p-4" data-aos="fade-up">
                        <p class="text-[11px] uppercase tracking-[0.25em] text-slate-400 mb-2">Kepala Sekolah</p>
                        <?php if ($kepala): ?>
                            <ul class="space-y-3 text-sm text-slate-200">
                                <?php foreach ($kepala as $k): ?>
                                    <li class="flex items-center gap-3">
                                        <div class="h-20 w-16 rounded-2xl bg-slate-900 flex items-center justify-center border border-slate-700 overflow-hidden">
                                            <?php if (!empty($k['foto'])): ?>
                                                <img src="<?= e((string)$k['foto']) ?>" alt="Foto <?= e((string)$k['nama']) ?>" class="h-full w-full object-cover" />
                                            <?php else: ?>
                                                <span class="text-xs text-slate-400">3x4</span>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <p class="font-semibold"><?= e((string)$k['nama']) ?></p>
                                        <?php if (!empty($k['jabatan'])): ?>
                                                <p class="text-xs text-slate-300"><?= e((string)$k['jabatan']) ?></p>
                                        <?php endif; ?>
                                        <?php if (!empty($k['tugas_tambahan'])): ?>
                                                <p class="text-xs text-slate-400">Tugas tambahan: <?= e((string)$k['tugas_tambahan']) ?></p>
                                        <?php endif; ?>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="text-sm text-slate-300">Data kepala sekolah belum diisi.</p>
                        <?php endif; ?>
                    </div>
                    <div class="rounded-3xl border border-slate-700 bg-slate-900/70 p-4" data-aos="fade-up" data-aos-delay="80">
                        <p class="text-[11px] uppercase tracking-[0.25em] text-slate-400 mb-2">Guru</p>
                        <?php if ($guruList): ?>
                            <ul class="space-y-3 text-sm text-slate-200 max-h-64 overflow-y-auto pr-1">
                                <?php foreach ($guruList as $g): ?>
                                    <li class="flex items-center gap-3">
                                        <div class="h-20 w-16 rounded-2xl bg-slate-900 flex items-center justify-center border border-slate-700 overflow-hidden">
                                            <?php if (!empty($g['foto'])): ?>
                                                <img src="<?= e((string)$g['foto']) ?>" alt="Foto <?= e((string)$g['nama']) ?>" class="h-full w-full object-cover" />
                                            <?php else: ?>
                                                <span class="text-xs text-slate-400">3x4</span>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <p class="font-semibold"><?= e((string)$g['nama']) ?></p>
                                            <p class="text-xs text-slate-300"><?= e((string)($g['mapel'] ?? 'Guru')) ?></p>
                                            <?php if (!empty($g['tugas_tambahan'])): ?>
                                                <p class="text-xs text-slate-400">Tugas tambahan: <?= e((string)$g['tugas_tambahan']) ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="text-sm text-slate-300">Belum ada data guru diinput dari admin.</p>
                        <?php endif; ?>
                    </div>
                    <div class="rounded-3xl border border-slate-700 bg-slate-900/70 p-4" data-aos="fade-up" data-aos-delay="160">
                        <p class="text-[11px] uppercase tracking-[0.25em] text-slate-400 mb-2">Staff / TU</p>
                        <?php if ($staff): ?>
                            <ul class="space-y-3 text-sm text-slate-200 max-h-64 overflow-y-auto pr-1">
                                <?php foreach ($staff as $s): ?>
                                    <li class="flex items-center gap-3">
                                        <div class="h-20 w-16 rounded-2xl bg-slate-900 flex items-center justify-center border border-slate-700 overflow-hidden">
                                            <?php if (!empty($s['foto'])): ?>
                                                <img src="<?= e((string)$s['foto']) ?>" alt="Foto <?= e((string)$s['nama']) ?>" class="h-full w-full object-cover" />
                                            <?php else: ?>
                                                <span class="text-xs text-slate-400">3x4</span>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <p class="font-semibold"><?= e((string)$s['nama']) ?></p>
                                            <?php if (!empty($s['jabatan'])): ?>
                                                <p class="text-xs text-slate-300"><?= e((string)$s['jabatan']) ?></p>
                                            <?php endif; ?>
                                            <?php if (!empty($s['tugas_tambahan'])): ?>
                                                <p class="text-xs text-slate-400">Tugas tambahan: <?= e((string)$s['tugas_tambahan']) ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="text-sm text-slate-300">Belum ada data staff / TU diinput dari admin.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- Majors / Jurusan Hub -->
        <section id="majors" class="border-t border-slate-800/80 bg-slate-900">
            <div class="max-w-6xl mx-auto px-4 py-12 md:py-16">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-xl md:text-2xl font-bold mb-1">Jurusan Hub</h2>
                        <p class="text-sm text-slate-300">Kenali setiap jurusan lewat ikon modern dan highlight skill yang kepakai di dunia kerja.</p>
                    </div>
                    <button class="hidden md:inline-flex items-center gap-2 rounded-full border border-slate-600 px-3 py-1.5 text-[11px] text-slate-300 hover:border-cyberlime hover:text-cyberlime transition">
                        Lihat Struktur Kurikulum
                    </button>
                </div>
                <div class="grid md:grid-cols-3 gap-4 md:gap-5">
                    <?php
                    $majors = [];
                    try {
                        $pdoMajors = pdo();
                        $stmtMajors = $pdoMajors->query('SELECT id, nama, singkatan, tagline, icon, color_from, color_to, highlights, logo_path FROM jurusan ORDER BY nama ASC');
                        $rowsMajors = $stmtMajors->fetchAll() ?: [];
                    } catch (Throwable $e) {
                        $rowsMajors = [];
                    }

                    if ($rowsMajors) {
                        $tones = [
                            'from-electric to-cyberlime',
                            'from-pink-500 to-yellow-300',
                            'from-cyan-400 to-sky-600',
                        ];
                        $i = 0;
                        foreach ($rowsMajors as $row) {
                            $color = trim((string)($row['color_from'] ?? '')) !== '' && trim((string)($row['color_to'] ?? '')) !== ''
                                ? 'from-' . trim((string)$row['color_from']) . ' to-' . trim((string)$row['color_to'])
                                : $tones[$i % count($tones)];
                            $name = (string)$row['nama'];
                            $tag = trim((string)($row['tagline'] ?? ''));
                            if ($tag === '') {
                                $tag = trim((string)($row['singkatan'] ?? ''));
                            }
                            if ($tag === '') {
                                $tag = 'Jurusan';
                            }
                            $icon = (string)($row['icon'] ?? '');
                            if ($icon === '') {
                                $icon = '🎓';
                            }
                            $skills = [];
                            $rawHighlights = (string)($row['highlights'] ?? '');
                            foreach (preg_split('/\r\n|\r|\n/', $rawHighlights) as $line) {
                                $line = trim($line);
                                if ($line !== '') {
                                    $skills[] = $line;
                                }
                            }
                            if (!$skills) {
                                $skills = ['Highlight keahlian utama jurusan', 'Keunggulan program dan praktik', 'Koneksi ke dunia kerja'];
                            }
                            $logoPath = (string)($row['logo_path'] ?? '');
                            $majors[] = [
                                'name' => $name,
                                'tag' => $tag,
                                'color' => $color,
                                'icon' => $icon,
                                'skills' => $skills,
                                'logo' => $logoPath,
                            ];
                            $i++;
                        }
                    }

                    if (!$majors) {
                        $majors = [
                            [
                                'name' => 'Rekayasa Perangkat Lunak',
                                'tag' => 'Tech & Software',
                                'color' => 'from-electric to-cyberlime',
                                'icon' => '💻',
                                'skills' => ['Web Dev', 'Mobile App', 'UI/UX Dasar'],
                            ],
                            [
                                'name' => 'Multimedia / DKV',
                                'tag' => 'Creative Content',
                                'color' => 'from-pink-500 to-yellow-300',
                                'icon' => '🎥',
                                'skills' => ['Video Editing', 'Graphic Design', 'Content Creation'],
                            ],
                            [
                                'name' => 'Teknik Komputer & Jaringan',
                                'tag' => 'Network & Infra',
                                'color' => 'from-cyan-400 to-sky-600',
                                'icon' => '🌐',
                                'skills' => ['Network Setup', 'Server Basic', 'Cybersecurity Dasar'],
                            ],
                        ];
                    }

                    $delay = 0;
                    foreach ($majors as $major):
                        $delay += 80;
                    ?>
                    <article class="rounded-3xl border border-slate-700 bg-slate-900/60 p-4 flex flex-col justify-between shadow-neobrutal" data-aos="zoom-in" data-aos-delay="<?= $delay ?>">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <span class="inline-flex items-center gap-1 text-[11px] px-2 py-1 rounded-full bg-slate-800 text-slate-300 border border-slate-600">
                                    <span class="text-base"><?= $major['icon'] ?></span>
                                    <span><?= htmlspecialchars($major['tag'], ENT_QUOTES, 'UTF-8') ?></span>
                                </span>
                                <h3 class="mt-3 text-sm font-semibold leading-snug"><?= htmlspecialchars($major['name'], ENT_QUOTES, 'UTF-8') ?></h3>
                            </div>
                            <div class="h-10 w-10 rounded-2xl bg-gradient-to-br <?= $major['color'] ?> flex items-center justify-center text-xs font-bold text-slate-900 border border-slate-900 overflow-hidden">
                                <?php if (!empty($major['logo'])): ?>
                                    <img src="<?= e((string)$major['logo']) ?>" alt="Logo <?= htmlspecialchars($major['name'], ENT_QUOTES, 'UTF-8') ?>" class="max-h-10 max-w-10 object-contain" />
                                <?php else: ?>
                                    <span>SMK</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <ul class="text-xs text-slate-300 space-y-1 mb-3">
                            <?php foreach ($major['skills'] as $skill): ?>
                                <li class="flex items-center gap-1.5">
                                    <span class="h-1.5 w-1.5 rounded-full bg-cyberlime"></span>
                                    <span><?= htmlspecialchars($skill, ENT_QUOTES, 'UTF-8') ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <button class="mt-auto inline-flex items-center justify-between gap-2 w-full text-[11px] font-semibold px-3 py-1.5 rounded-2xl border border-slate-600 hover:border-cyberlime hover:text-cyberlime transition">
                            <span>Detail Jurusan</span>
                            <span>↗</span>
                        </button>
                    </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- E-Mading & Konten -->
        <section id="emading" class="border-t border-slate-800/80 bg-slate-950/50">
            <div class="max-w-6xl mx-auto px-4 py-12 md:py-16">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-xl md:text-2xl font-bold mb-1">E‑Mading</h2>
                        <p class="text-sm text-slate-300">Feed digital berisi prestasi, karya siswa, dan pengumuman terbaru.</p>
                    </div>
                    <button class="hidden md:inline-flex items-center gap-2 rounded-full border border-slate-600 px-3 py-1.5 text-[11px] text-slate-300 hover:border-cyberlime hover:text-cyberlime transition">
                        Lihat Semua Post
                    </button>
                </div>

                <div class="grid md:grid-cols-3 gap-4 md:gap-5">
                    <?php
                    try {
                        $pdoLanding = pdo();
                        $stmtMading = $pdoLanding->prepare("SELECT judul, ringkasan, tgl_upload FROM konten WHERE tipe = 'mading' AND is_published = 1 ORDER BY tgl_upload DESC, id DESC LIMIT 3");
                        $stmtMading->execute();
                        $madingPosts = $stmtMading->fetchAll() ?: [];
                    } catch (Throwable $e) {
                        $madingPosts = [];
                    }

                    if (!$madingPosts) {
                        $madingPosts = [
                            ['judul' => 'Juara 1 Lomba Film Pendek Tingkat Kota', 'ringkasan' => 'Highlight prestasi siswa di bidang perfilman.', 'badge' => 'Prestasi'],
                            ['judul' => 'UI Design Aplikasi Keuangan Siswa versi 2.0', 'ringkasan' => 'Karya desain dari jurusan RPL & DKV.', 'badge' => 'Karya'],
                            ['judul' => 'Pendaftaran PPDB Gelombang 2 Resmi Dibuka', 'ringkasan' => 'Info resmi untuk calon peserta didik baru.', 'badge' => 'Info'],
                        ];
                    }

                    $tones = [
                        'from-emerald-400/70 to-sky-500/60',
                        'from-purple-500/80 to-pink-500/70',
                        'from-amber-300/80 to-orange-500/70',
                    ];

                    $delay = 0;
                    $i = 0;
                    foreach ($madingPosts as $post):
                        $delay += 80;
                        $tone = $tones[$i % count($tones)];
                        $badge = $post['badge'] ?? 'Mading';
                    ?>
                    <article class="rounded-3xl border border-slate-700 bg-slate-900/70 p-4 flex flex-col justify-between overflow-hidden relative" data-aos="fade-up" data-aos-delay="<?= $delay ?>">
                        <div class="absolute inset-0 bg-gradient-to-br <?= $tone ?> opacity-20 pointer-events-none"></div>
                        <div class="relative z-10">
                            <div class="flex items-center justify-between text-[11px] mb-2">
                                <span class="px-2 py-1 rounded-full bg-slate-900/80 border border-slate-600 text-slate-200">
                                    <?= e((string)$badge) ?>
                                </span>
                                <span class="px-2 py-1 rounded-full bg-slate-50 text-slate-900 border border-slate-900 text-[10px] font-semibold shadow-neobrutal">
                                    Feed
                                </span>
                            </div>
                            <h3 class="text-sm font-semibold mb-2 text-slate-50">
                                <?= e((string)$post['judul']) ?>
                            </h3>
                            <p class="text-[11px] text-slate-200 mb-3">
                                <?= e((string)($post['ringkasan'] ?? 'Konten lengkap akan tampil di halaman detail—bisa berisi foto, video, atau link dokumentasi.')) ?>
                            </p>
                        </div>
                        <button class="relative z-10 mt-auto inline-flex items-center justify-between gap-2 w-full text-[11px] font-semibold px-3 py-1.5 rounded-2xl border border-slate-600 bg-slate-900/60 hover:border-cyberlime hover:text-cyberlime transition">
                            <span>Baca Selengkapnya</span>
                            <span>→</span>
                        </button>
                    </article>
                    <?php
                        $i++;
                    endforeach;
                    ?>
                </div>
            </div>
        </section>

        <!-- Event / Info Sekolah -->
        <section id="konten" class="border-t border-slate-800/80 bg-slate-900">
            <div class="max-w-6xl mx-auto px-4 py-12 md:py-16">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                    <div>
                        <h2 class="text-xl md:text-2xl font-bold mb-1">Event & Info Sekolah</h2>
                        <p class="text-sm text-slate-300">Highlight event penting dalam layout card visual yang gampang discroll.</p>
                    </div>
                </div>

                <div class="grid md:grid-cols-[1.1fr_0.9fr] gap-5">
                    <div class="rounded-3xl border border-slate-700 bg-slate-900/70 p-4 shadow-neobrutal" data-aos="fade-right">
                        <div class="flex items-center justify-between mb-3 text-[11px] text-slate-300">
                            <span>Event Terdekat</span>
                            <span class="px-2 py-1 rounded-full border border-slate-600">Kalender</span>
                        </div>
                        <div class="space-y-3 text-xs">
                            <?php
                            try {
                                $stmtEvent = $pdoLanding->prepare("SELECT judul, ringkasan, tgl_upload FROM konten WHERE tipe = 'event' AND is_published = 1 ORDER BY tgl_upload ASC, id ASC LIMIT 3");
                                $stmtEvent->execute();
                                $events = $stmtEvent->fetchAll() ?: [];
                            } catch (Throwable $e) {
                                $events = [];
                            }

                            if (!$events) {
                                $events = [
                                    ['judul' => 'Expo Karya Siswa & Open House', 'ringkasan' => 'Pameran project akhir, demo jurusan, dan talkshow alumni.'],
                                    ['judul' => 'Workshop Content Creator', 'ringkasan' => 'Belajar bikin konten dari praktisi industri kreatif.'],
                                ];
                            }

                            $monthMap = [
                                '01' => 'Jan', '02' => 'Feb', '03' => 'Mar', '04' => 'Apr',
                                '05' => 'Mei', '06' => 'Jun', '07' => 'Jul', '08' => 'Agu',
                                '09' => 'Sep', '10' => 'Okt', '11' => 'Nov', '12' => 'Des',
                            ];

                            foreach ($events as $ev):
                                $date = isset($ev['tgl_upload']) ? substr((string)$ev['tgl_upload'], 0, 10) : date('Y-m-d');
                                [$y, $m, $d] = explode('-', $date);
                                $day = ltrim($d, '0');
                                $mon = $monthMap[$m] ?? $m;
                            ?>
                            <div class="flex items-center gap-3 rounded-2xl border border-slate-700 bg-slate-900/90 px-3 py-2">
                                <div class="h-10 w-10 rounded-2xl bg-slate-100 text-slate-900 flex flex-col items-center justify-center text-[11px] font-bold shadow-neobrutal">
                                    <span><?= e($day) ?></span>
                                    <span><?= e($mon) ?></span>
                                </div>
                                <div>
                                    <p class="font-semibold text-slate-50"><?= e((string)$ev['judul']) ?></p>
                                    <p class="text-slate-300"><?= e((string)($ev['ringkasan'] ?? '')) ?></p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="rounded-3xl border border-slate-700 bg-gradient-to-br from-slate-900 via-slate-900 to-slate-800 p-4" data-aos="fade-left">
                        <p class="text-[11px] uppercase tracking-[0.25em] text-slate-400 mb-2">Untuk Orang Tua & Publik</p>
                        <p class="text-sm text-slate-200 mb-3">
                            Semua info penting—dari jadwal penerimaan siswa baru, agenda rapat orang tua, sampai pengumuman resmi—akan muncul dalam format kartu ringkas, bukan teks panjang yang bikin pusing.
                        </p>
                        <ul class="text-xs text-slate-300 space-y-1.5 mb-4">
                            <li class="flex items-center gap-1.5">
                                <span class="h-1.5 w-1.5 rounded-full bg-cyberlime"></span>
                                <span>Notifikasi jelas: tanggal, alur, dan kontak narahubung.</span>
                            </li>
                            <li class="flex items-center gap-1.5">
                                <span class="h-1.5 w-1.5 rounded-full bg-cyberlime"></span>
                                <span>Bisa diakses dari HP tanpa zoom‑zoom kecilin layar.</span>
                            </li>
                        </ul>
                        <button class="inline-flex items-center gap-2 rounded-2xl border border-slate-100 bg-slate-50 text-slate-900 px-3 py-1.5 text-[11px] font-semibold shadow-neobrutal">
                            Lihat Pusat Informasi
                        </button>
                    </div>
                </div>
            </div>
        </section>
    </main>

        <!-- Lokasi Sekolah -->
        <section id="lokasi" class="border-t border-slate-800/80 bg-slate-950/70">
            <div class="max-w-6xl mx-auto px-4 py-12 md:py-16">
                <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h2 class="text-xl md:text-2xl font-bold mb-1">Lokasi <?= e((string)($sekolah['nama'] ?? 'SMK Madani')) ?></h2>
                        <p class="text-sm text-slate-300">Biar orang tua dan tamu nggak nyasar lagi.</p>
                    </div>
                </div>
                <div class="grid md:grid-cols-[1.1fr_0.9fr] gap-5">
                    <div class="rounded-3xl border border-slate-700 bg-slate-900/70 p-4 shadow-neobrutal">
                        <?php if (!empty($sekolah['map_embed_url']) && stripos((string)$sekolah['map_embed_url'], '<iframe') !== false): ?>
                            <div class="aspect-[16/9] rounded-2xl overflow-hidden border border-slate-800 bg-slate-900">
                                <?= $sekolah['map_embed_url'] ?>
                            </div>
                        <?php else: ?>
                            <div class="aspect-[16/9] rounded-2xl overflow-hidden border border-slate-800 bg-slate-900 flex items-center justify-center text-xs text-slate-400">
                                <p>Embed Google Maps belum diatur. Tambahkan lewat menu "Identitas Sekolah" di admin.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="rounded-3xl border border-slate-700 bg-slate-900/60 p-4 text-sm text-slate-200">
                        <p class="text-[11px] uppercase tracking-[0.25em] text-slate-400 mb-2">Alamat & Kontak</p>
                        <p class="mb-3"><?= e((string)($sekolah['alamat'] ?? 'Alamat sekolah belum diisi.')) ?></p>
                        <ul class="text-xs text-slate-300 space-y-1.5 mb-3">
                            <?php if (!empty($sekolah['telp'])): ?>
                                <li>Telepon: <span class="font-semibold"><?= e((string)$sekolah['telp']) ?></span></li>
                            <?php endif; ?>
                            <?php if (!empty($sekolah['email'])): ?>
                                <li>Email: <span class="font-semibold"><?= e((string)$sekolah['email']) ?></span></li>
                            <?php endif; ?>
                            <?php if (!empty($sekolah['website'])): ?>
                                <li>Website: <span class="font-semibold"><?= e((string)$sekolah['website']) ?></span></li>
                            <?php endif; ?>
                        </ul>
                        <p class="text-[11px] text-slate-400">Koordinat akurat bisa diambil dari Google Maps lalu di-embed ke sistem.</p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-800/80 bg-slate-950/80">
        <div class="max-w-6xl mx-auto px-4 py-5 flex flex-col md:flex-row items-center justify-between gap-3 text-[11px] text-slate-400">
            <p>© <?= date('Y') ?> <?= e((string)($sekolah['nama'] ?? 'SMK Madani')) ?>. Web dev Kaprog TJKT.</p>
            <div class="flex items-center gap-3">
                <span>For students from Mr. Hus.</span>
            </div>
        </div>
    </footer>

    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
    <script>
        AOS.init({
            once: true,
            duration: 700,
            easing: 'ease-out-cubic'
        });

        // Mode siang / malam
        const toggle = document.getElementById('themeToggle');
        const body = document.body;
        const savedTheme = localStorage.getItem('theme');
        const isDarkStart = savedTheme !== 'light'; // default dark

        function applyTheme(isDark) {
            if (isDark) {
                body.classList.add('theme-dark');
                body.classList.remove('theme-light');
            } else {
                body.classList.add('theme-light');
                body.classList.remove('theme-dark');
            }
        }

        applyTheme(isDarkStart);

        toggle?.addEventListener('click', () => {
            const nowDark = !body.classList.contains('theme-dark');
            applyTheme(nowDark);
            localStorage.setItem('theme', nowDark ? 'dark' : 'light');
        });
    </script>
</body>
</html>

