<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pageTitle = $pageTitle ?? 'FindIT';
$assetPrefix = $assetPrefix ?? '';
$homePrefix = $homePrefix ?? '';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>
        <?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?>
        | FindIT
    </title>

    <link rel="stylesheet"
          href="<?= $assetPrefix ?>assets/css/style.css">

    <script defer
            src="<?= $assetPrefix ?>assets/js/script.js"></script>
</head>
<body>

<header class="site-header">
    <a href="<?= $homePrefix ?>index.php" class="brand">
        <span class="brand-mark">F</span>
        FindIT
    </a>

    <nav class="nav">
        <a href="<?= $homePrefix ?>index.php">Beranda</a>
        <a href="<?= $homePrefix ?>tentang.php">Tentang</a>

        <?php if (isset($_SESSION['user_id'])): ?>

            <?php if ($_SESSION['role'] === 'superadmin'): ?>
                <a href="<?= $homePrefix ?>superadmin/dashboard.php">
                    Dashboard
                </a>
            <?php else: ?>
                <a href="<?= $homePrefix ?>admin/dashboard.php">
                    Dashboard
                </a>
            <?php endif; ?>

            <a href="<?= $homePrefix ?>logout.php"
               class="nav-login">Keluar</a>

        <?php else: ?>

            <a href="<?= $homePrefix ?>login.php"
               class="nav-login">Login Admin</a>

        <?php endif; ?>
    </nav>
</header>

<main class="page-wrap">