<?php
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../includes/auth.php';

require_role('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php');
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header('Location: dashboard.php');
    exit;
}

$stmt = $pdo->prepare('SELECT foto FROM items WHERE id = ?');
$stmt->execute([$id]);
$item = $stmt->fetch();

if ($item) {
    $delete = $pdo->prepare('DELETE FROM items WHERE id = ?');
    $delete->execute([$id]);

    if (!empty($item['foto'])) {
        $path = __DIR__ . '/../assets/uploads/' . $item['foto'];

        if (is_file($path)) {
            unlink($path);
        }
    }
}

header('Location: dashboard.php?deleted=1');
exit;