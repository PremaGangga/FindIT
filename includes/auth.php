<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function is_logged_in(): bool
{
    return isset($_SESSION['user_id'], $_SESSION['role']);
}

function require_login(): void
{
    if (!is_logged_in()) {
        header('Location: ../login.php');
        exit;
    }
}

function require_role(string $role): void
{
    require_login();

    if ($_SESSION['role'] !== $role) {
        http_response_code(403);
        exit('403 - Anda tidak memiliki izin mengakses halaman ini.');
    }
}

function e($value): string
{
    return htmlspecialchars(
        (string) ($value ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}