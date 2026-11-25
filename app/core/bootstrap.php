<?php
declare(strict_types=1);

session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
session_start();

header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: same-origin');

$GLOBALS['config'] = require __DIR__ . '/../../config/config.php';

function db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $c = $GLOBALS['config']['db'];
        $pdo = new PDO(
            "mysql:host={$c['host']};dbname={$c['name']};charset=utf8mb4",
            $c['user'], $c['pass'],
            [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]
        );
    }
    return $pdo;
}

/** Escape output (XSS protection). */
function e(?string $v): string
{
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

function redirect(string $route): void
{
    header('Location: index.php?r=' . $route);
    exit;
}

function flash(string $type, string $msg): void
{
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
}

/* ---------- CSRF ---------- */
function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function csrf_check(): void
{
    $sent = $_POST['csrf'] ?? '';
    if (!is_string($sent) || !hash_equals($_SESSION['csrf'] ?? '', $sent)) {
        http_response_code(419);
        exit('Invalid CSRF token.');
    }
}

/* ---------- Auth ---------- */
function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function require_login(): void
{
    if (!current_user()) {
        flash('error', 'Please log in first.');
        redirect('auth/login');
    }
}

function require_role(array $roles): void
{
    require_login();
    if (!in_array(current_user()['role'], $roles, true)) {
        http_response_code(403);
        exit('Access denied.');
    }
}

function is_staff(): bool
{
    return current_user() && in_array(current_user()['role'], ['admin', 'staff'], true);
}

function require_post(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit('Method not allowed.');
    }
    csrf_check();
}

/* ---------- Views ---------- */
function view(string $name, array $data = []): void
{
    extract($data);
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    require __DIR__ . '/../views/layout/header.php';
    require __DIR__ . "/../views/$name.php";
    require __DIR__ . '/../views/layout/footer.php';
}
