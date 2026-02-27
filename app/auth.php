<?php

declare(strict_types=1);

require_once __DIR__ . '/init.php';

/**
 * Pastikan ada minimal satu admin.
 * Jika tabel users kosong, otomatis buat akun default:
 *   username: admin
 *   password: admin123
 */
function ensure_default_admin(): void
{
    try {
        $pdo = pdo();
        $stmt = $pdo->query('SELECT COUNT(*) AS c FROM users');
        $count = (int)($stmt->fetch()['c'] ?? 0);
        if ($count > 0) {
            return;
        }

        $username = 'admin';
        $password = 'admin123';
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $ins = $pdo->prepare('INSERT INTO users (username, password, role) VALUES (?, ?, ?)');
        $ins->execute([$username, $hash, 'admin']);
    } catch (Throwable $e) {
        // Jika gagal (misal tabel belum ada), biarkan saja tanpa mengganggu flow login.
    }
}

function auth_user(): ?array
{
    $u = $_SESSION['auth'] ?? null;
    return is_array($u) ? $u : null;
}

function auth_check(): bool
{
    return auth_user() !== null;
}

function auth_is_admin(): bool
{
    $u = auth_user();
    return $u && ($u['role'] ?? null) === 'admin';
}

function require_admin(): void
{
    if (!auth_is_admin()) {
        flash_set('error', 'Silakan login admin dulu.');
        redirect('/admin/login');
    }
}

function auth_login(string $username, string $password): bool
{
    // Otomatis buat admin default jika belum ada user sama sekali.
    ensure_default_admin();

    $stmt = pdo()->prepare('SELECT id, username, password, role FROM users WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $row = $stmt->fetch();
    if (!$row) {
        return false;
    }
    if (!password_verify($password, (string)$row['password'])) {
        return false;
    }

    $_SESSION['auth'] = [
        'id' => (int)$row['id'],
        'username' => (string)$row['username'],
        'role' => (string)$row['role'],
    ];
    session_regenerate_id(true);
    return true;
}

function auth_logout(): void
{
    unset($_SESSION['auth']);
    session_regenerate_id(true);
}

