<?php
require '../includes/auth.php';
wajib_role('superadmin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('superadmin/dashboard.php');
csrf_check();

$id = (int)($_POST['id'] ?? 0);
// Hanya akun ber-role admin yang bisa dihapus (superadmin aman)
$a = db_one("SELECT username FROM users WHERE id = ? AND role = 'admin'", 'i', [$id]);
if ($a) {
    db_exec("DELETE FROM users WHERE id = ? AND role = 'admin'", 'i', [$id]);
    flash_set('success', 'Akun admin "' . $a['username'] . '" sudah dihapus.');
} else {
    flash_set('danger', 'Akun admin tidak ditemukan.');
}
redirect('superadmin/dashboard.php');
