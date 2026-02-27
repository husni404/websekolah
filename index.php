<?php

declare(strict_types=1);

// Simple front controller + router

// Autoload vendor if available (PhpSpreadsheet, etc.)
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require __DIR__ . '/vendor/autoload.php';
}

require __DIR__ . '/app/init.php';
require __DIR__ . '/app/auth.php';

// Basic route detection
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
$route = '/' . ltrim(str_replace($base, '', $path), '/');
if ($route === '//') {
    $route = '/';
}

// Router
$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

switch (true) {
    case $route === '/':
        require __DIR__ . '/views/landing.php';
        break;

    // Admin entry
    case $route === '/admin':
        redirect('/admin/dashboard');
        break;

    // Admin auth
    case $route === '/admin/login':
        require __DIR__ . '/views/admin/login.php';
        break;
    case $route === '/admin/logout' && $method === 'POST':
        csrf_verify();
        auth_logout();
        flash_set('success', 'Logout berhasil.');
        redirect('/');
        break;

    // Admin pages
    case $route === '/admin/dashboard':
        require_admin();
        require __DIR__ . '/views/admin/dashboard.php';
        break;

    case $route === '/admin/sekolah':
        require_admin();
        require __DIR__ . '/views/admin/sekolah/index.php';
        break;

    // Siswa CRUD
    case $route === '/admin/siswa':
        require_admin();
        require __DIR__ . '/views/admin/siswa/index.php';
        break;
    case $route === '/admin/siswa/create':
        require_admin();
        require __DIR__ . '/views/admin/siswa/create.php';
        break;
    case $route === '/admin/siswa/edit':
        require_admin();
        require __DIR__ . '/views/admin/siswa/edit.php';
        break;
    case $route === '/admin/siswa/delete' && $method === 'POST':
        require_admin();
        require __DIR__ . '/views/admin/siswa/delete.php';
        break;

    // Guru CRUD
    case $route === '/admin/guru':
        require_admin();
        require __DIR__ . '/views/admin/guru/index.php';
        break;
    case $route === '/admin/guru/create':
        require_admin();
        require __DIR__ . '/views/admin/guru/create.php';
        break;
    case $route === '/admin/guru/edit':
        require_admin();
        require __DIR__ . '/views/admin/guru/edit.php';
        break;
    case $route === '/admin/guru/delete' && $method === 'POST':
        require_admin();
        require __DIR__ . '/views/admin/guru/delete.php';
        break;

    // Import siswa via Excel
    case $route === '/admin/import/siswa':
        require_admin();
        require __DIR__ . '/views/admin/import/siswa.php';
        break;
    case $route === '/admin/template/siswa':
        require_admin();
        require __DIR__ . '/views/admin/import/template_siswa.php';
        break;

    // Konten / E-Mading
    case $route === '/admin/konten':
        require_admin();
        require __DIR__ . '/views/admin/konten/index.php';
        break;
    case $route === '/admin/konten/create':
        require_admin();
        require __DIR__ . '/views/admin/konten/create.php';
        break;
    case $route === '/admin/konten/edit':
        require_admin();
        require __DIR__ . '/views/admin/konten/edit.php';
        break;
    case $route === '/admin/konten/delete' && $method === 'POST':
        require_admin();
        require __DIR__ . '/views/admin/konten/delete.php';
        break;

    // Kelas CRUD
    case $route === '/admin/kelas':
        require_admin();
        require __DIR__ . '/views/admin/kelas/index.php';
        break;
    case $route === '/admin/kelas/create':
        require_admin();
        require __DIR__ . '/views/admin/kelas/create.php';
        break;
    case $route === '/admin/kelas/edit':
        require_admin();
        require __DIR__ . '/views/admin/kelas/edit.php';
        break;
    case $route === '/admin/kelas/delete' && $method === 'POST':
        require_admin();
        require __DIR__ . '/views/admin/kelas/delete.php';
        break;

    // Jurusan CRUD
    case $route === '/admin/jurusan':
        require_admin();
        require __DIR__ . '/views/admin/jurusan/index.php';
        break;
    case $route === '/admin/jurusan/create':
        require_admin();
        require __DIR__ . '/views/admin/jurusan/create.php';
        break;
    case $route === '/admin/jurusan/edit':
        require_admin();
        require __DIR__ . '/views/admin/jurusan/edit.php';
        break;
    case $route === '/admin/jurusan/delete' && $method === 'POST':
        require_admin();
        require __DIR__ . '/views/admin/jurusan/delete.php';
        break;

    default:
        http_response_code(404);
        require __DIR__ . '/views/404.php';
        break;
}

