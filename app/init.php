<?php

declare(strict_types=1);

/**
 * Minimal app bootstrap for native PHP + XAMPP
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

$GLOBALS['__config'] = require __DIR__ . '/../config/config.php';

date_default_timezone_set(config('app.timezone', 'Asia/Jakarta'));

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_name((string)config('app.session_name', 'smk_madani'));
    session_start();
}

function config(string $key, mixed $default = null): mixed
{
    $config = $GLOBALS['__config'] ?? [];
    $parts = explode('.', $key);
    $value = $config;
    foreach ($parts as $p) {
        if (!is_array($value) || !array_key_exists($p, $value)) {
            return $default;
        }
        $value = $value[$p];
    }
    return $value;
}

function base_path(): string
{
    $base = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
    return $base === '.' ? '' : $base;
}

function url_for(string $path): string
{
    $base = base_path();
    $path = '/' . ltrim($path, '/');
    return ($base ? $base : '') . $path;
}

function redirect(string $path): never
{
    header('Location: ' . url_for($path));
    exit;
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function pdo(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $db = (array)config('db', []);
    $dsn = sprintf(
        'mysql:host=%s;dbname=%s;charset=%s',
        $db['host'] ?? '127.0.0.1',
        $db['name'] ?? 'smk_madani',
        $db['charset'] ?? 'utf8mb4'
    );
    $user = (string)($db['user'] ?? 'root');
    $pass = (string)($db['pass'] ?? '');

    try {
        $pdo = new PDO(
            $dsn,
            $user,
            $pass,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
        return $pdo;
    } catch (PDOException $e) {
        // Tampilkan pesan yang lebih ramah saat koneksi DB gagal,
        // daripada fatal error dengan stack trace.
        http_response_code(500);
        if (PHP_SAPI === 'cli') {
            // Untuk CLI (seeding, dsb) tetap lempar exception asli.
            throw $e;
        }

        ?>
        <!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Database Error - SMK Madani</title>
            <style>
                body { font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background:#020617; color:#e5e7eb; display:flex; align-items:center; justify-content:center; min-height:100vh; margin:0; }
                .card { max-width:460px; padding:24px; border-radius:20px; border:1px solid #1f2937; background:rgba(15,23,42,0.9); box-shadow:16px 16px 0 0 #020617; }
                h1 { font-size:18px; margin:0 0 8px; }
                p { font-size:13px; line-height:1.5; margin:4px 0; }
                code { background:#020617; padding:2px 4px; border-radius:4px; font-size:12px; }
                ul { padding-left:18px; font-size:13px; }
            </style>
        </head>
        <body>
            <div class="card">
                <h1>Gagal konek ke database</h1>
                <p>Aplikasi tidak bisa terhubung ke MySQL.</p>
                <p style="margin-top:8px;"><strong>Cek langkah berikut:</strong></p>
                <ul>
                    <li>Pastikan service <strong>MySQL</strong> di XAMPP sudah <strong>Start</strong>.</li>
                    <li>Buka <code>phpMyAdmin</code> dan pastikan database <code>smk_madani</code> sudah dibuat (import <code>database/schema.sql</code>).</li>
                    <li>Jika port MySQL bukan bawaan (3306), sesuaikan <code>'host'</code> di <code>config/config.php</code>, misalnya <code>127.0.0.1;port=3307</code>.</li>
                    <li>Jika akun MySQL pakai password, isi field <code>'pass'</code> di <code>config/config.php</code>.</li>
                </ul>
                <p style="margin-top:8px;font-size:12px;color:#9ca3af;">
                    Detail teknis: <?= e($e->getMessage()) ?>
                </p>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
}

function flash_set(string $key, string $message): void
{
    $_SESSION['flash'][$key] = $message;
}

function flash_get(string $key): ?string
{
    $msg = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return is_string($msg) ? $msg : null;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

function csrf_verify(): void
{
    $token = $_POST['_csrf'] ?? '';
    if (!is_string($token) || !hash_equals(csrf_token(), $token)) {
        http_response_code(419);
        exit('CSRF token mismatch.');
    }
}

