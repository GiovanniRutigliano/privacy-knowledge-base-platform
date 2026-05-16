<?php

declare(strict_types=1);

namespace App\Includes;

/**
 * Initializes runtime session strictly enforcing secure runtime boundaries.
 */
function require_secure_session(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        // Enforce hardened cookie transmission flags to prevent interception and fixation attacks
        ini_set('session.cookie_httponly', '1');
        ini_set('session.use_only_cookies', '1');
        ini_set('session.cookie_samesite', 'Lax');
        session_start();

        // Strict Cache-Control boundaries to prevent browser bfcache from exposing stale sensitive snapshots on back-navigation
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
    }
}

/**
 * Validates active authorization status against target RBAC parameters.
 * Redirects unauthenticated flows automatically.
 *
 * @param string $requiredRole Expected authorization boundary ('admin' or 'security manager')
 */
function require_role(string $requiredRole): void
{
    require_secure_session();

    if (!isset($_SESSION['role']) || $_SESSION['role'] !== $requiredRole) {
        header("Location: /login.php");
        exit();
    }
}

/**
 * Generates an active cryptographically secure token payload for state verification.
 */
function get_csrf_token(): string
{
    require_secure_session();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Evaluates transmitted form tokens against active state.
 */
function verify_csrf_token(?string $token): bool
{
    require_secure_session();
    return !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], (string)$token);
}