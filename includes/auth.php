<?php
/**
 * Pengecekan login & role.
 * Pemakaian di halaman admin:
 *   require '../includes/auth.php';
 *   wajib_role('admin');
 */
require_once __DIR__ . '/../config/koneksi.php';

function user() { return $_SESSION['user'] ?? null; }

function dashboard_path($role) {
    if ($role === 'superadmin') return 'superadmin/dashboard.php';
    if ($role === 'admin')      return 'admin/dashboard.php';
    return 'user/dashboard.php';
}

function wajib_role($role) {
    $u = user();
    if (!$u) {
        flash_set('warning', 'Silakan login dulu.');
        redirect('login.php');
    }
    if ($u['role'] !== $role) {
        redirect(dashboard_path($u['role']));   // salah role -> ke dashboard miliknya
    }
}
