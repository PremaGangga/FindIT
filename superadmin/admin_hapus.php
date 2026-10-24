<?php
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../includes/auth.php';

require_role('superadmin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php');
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id || (int) $id === (int) $_SESSION['user_id']) {
    header('Location: dashboard.php?error=invalid');
    exit;
}

$stmt = $pdo->prepare(
    'SELECT role FROM users WHERE id = ?'
);

$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: dashboard.php');
    exit;
}

if ($user['role'] === 'superadmin') {
    $jumlah = (int) $pdo->query(
        "SELECT COUNT(*) FROM users WHERE role = 'superadmin'"
    )->fetchColumn();

    if ($jumlah <= 1) {
        header('Location: dashboard.php?error=last_superadmin');
        exit;
    }
}

$stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
$stmt->execute([$id]);

header('Location: dashboard.php?user_deleted=1');
exit;