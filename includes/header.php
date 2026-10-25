<?php
/**
 * Bagian atas semua halaman.
 * Sebelum include, set (opsional): $judul = 'Judul Halaman';
 */
require_once __DIR__ . '/auth.php';
$judul  = isset($judul) ? $judul . ' | FindIT' : 'FindIT - Barang Hilang & Temuan Kampus';
$u      = user();
$flash  = flash_get();
$hal    = basename($_SERVER['SCRIPT_NAME']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($judul) ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="<?= url('assets/css/style.css') ?>" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-md site-nav">
  <div class="container">
    <a class="navbar-brand brand" href="<?= url('index.php') ?>">
      <i class="bi bi-geo-alt-fill brand-pin"></i>Find<span>IT</span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu" aria-label="Buka menu">
      <i class="bi bi-list"></i>
    </button>
    <div class="collapse navbar-collapse" id="menu">
      <ul class="navbar-nav me-auto ms-md-4 gap-md-2">
        <li class="nav-item"><a class="nav-link <?= $hal === 'index.php' ? 'active' : '' ?>" href="<?= url('index.php') ?>">Beranda</a></li>
        <li class="nav-item"><a class="nav-link <?= $hal === 'barang.php' ? 'active' : '' ?>" href="<?= url('barang.php') ?>">Daftar Barang</a></li>
        <li class="nav-item"><a class="nav-link <?= $hal === 'tentang.php' ? 'active' : '' ?>" href="<?= url('tentang.php') ?>">Tentang</a></li>
      </ul>
      <div class="d-flex gap-2 mt-3 mt-md-0">
        <?php if ($u): ?>
          <a class="btn btn-pill btn-pill-outline" href="<?= url(dashboard_path($u['role'])) ?>"><i class="bi bi-speedometer2 me-1"></i>Dashboard</a>
          <a class="btn btn-pill btn-pill-fill" href="<?= url('logout.php') ?>"><i class="bi bi-box-arrow-right me-1"></i>Keluar</a>
        <?php else: ?>
          <a class="btn btn-pill btn-pill-outline" href="<?= url('login.php') ?>">Masuk</a>
          <a class="btn btn-pill btn-pill-fill" href="<?= url('daftar.php') ?>">Daftar</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>

<?php if ($flash): ?>
  <div class="container mt-3">
    <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show mb-0" role="alert">
      <?= e($flash['msg']) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
  </div>
<?php endif; ?>

<main>
