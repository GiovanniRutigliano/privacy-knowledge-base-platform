<?php

declare(strict_types=1);

namespace App\Includes;

function require_secure_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    $https = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    session_set_cookie_params([
        'path' => '/',
        'secure' => $https,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();

    header('Cache-Control: no-store');
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('Referrer-Policy: strict-origin-when-cross-origin');
}

function require_role(string $requiredRole): void
{
    require_secure_session();
    if (!isset($_SESSION['role'])) {
        header('Location: /login.php', true, 303);
        exit;
    }
    if ($_SESSION['role'] !== $requiredRole) {
        http_response_code(403);
        exit('You do not have permission to access this page.');
    }
}

function get_csrf_token(): string
{
    require_secure_session();
    return $_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));
}

function verify_csrf_token(mixed $token): bool
{
    require_secure_session();
    return is_string($token)
        && isset($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

function require_csrf_token(mixed $token): void
{
    if (!verify_csrf_token($token)) {
        http_response_code(403);
        exit('Your session could not be verified. Go back and try again.');
    }
}

function end_session(): void
{
    require_secure_session();
    $_SESSION = [];
    $options = session_get_cookie_params();
    setcookie(session_name(), '', [
        'expires' => time() - 3600,
        'path' => $options['path'],
        'domain' => $options['domain'],
        'secure' => $options['secure'],
        'httponly' => $options['httponly'],
        'samesite' => $options['samesite'],
    ]);
    session_destroy();
}
