<?php
$judul = 'Daftar Barang';
require 'includes/header.php';
require 'includes/kartu.php';

// ---------- Filter dari URL ----------
$q      = trim($_GET['q'] ?? '');
$kat    = (int)($_GET['kategori'] ?? 0);
$status = $_GET['status'] ?? '';
if (!in_array($status, ['hilang', 'ditemukan', 'dikembalikan'], true)) $status = '';

$where = ["b.verifikasi = 'disetujui'"]; $types = ''; $params = [];
if ($q !== '')      { $where[] = '(b.judul LIKE ? OR b.deskripsi LIKE ? OR b.lokasi LIKE ?)'; $types .= 'sss'; array_push($params, "%$q%", "%$q%", "%$q%"); }
if ($kat > 0)       { $where[] = 'b.kategori_id = ?'; $types .= 'i'; $params[] = $kat; }
if ($status !== '') { $where[] = 'b.status = ?';      $types .= 's'; $params[] = $status; }
$sqlWhere = 'WHERE ' . implode(' AND ', $where);

// ---------- Pagination ----------
$perPage = 12;
$total   = (int)db_one("SELECT COUNT(*) AS n FROM barang b $sqlWhere", $types, $params)['n'];
$pages   = max(1, (int)ceil($total / $perPage));
$page    = min(max(1, (int)($_GET['page'] ?? 1)), $pages);
$offset  = ($page - 1) * $perPage;

$items = db_all(
    "SELECT b.*, k.nama AS kategori FROM barang b
     LEFT JOIN kategori k ON k.id = b.kategori_id
     $sqlWhere ORDER BY b.created_at DESC, b.id DESC LIMIT $perPage OFFSET $offset",
    $types, $params
);
$daftarKategori = db_all('SELECT * FROM kategori ORDER BY id');

function link_halaman($p) { $qs = $_GET; $qs['page'] = $p; return '?' . http_build_query($qs); }
function link_kategori($id) { $qs = $_GET; unset($qs['page']); $qs['kategori'] = $id; return '?' . http_build_query($qs); }
$adaFilter = ($q !== '' || $kat || $status !== '');
?>

<section class="page-head">
  <div class="container">
    <h1>Daftar Barang</h1>
    <p>Cari barang hilang atau barang temuan di lingkungan kampus.</p>
  </div>
</section>

<div class="container">
  <form class="filter-panel" method="get" action="<?= url('barang.php') ?>">
    <div class="filter-row">
      <div class="input-icon">
        <i class="bi bi-search"></i>
        <input type="search" class="form-control" name="q" value="<?= e($q) ?>" placeholder="Cari nama barang atau lokasi..." aria-label="Cari barang">
      </div>
      <div class="input-icon">
        <i class="bi bi-calendar-check"></i>
        <select class="form-select" name="status" aria-label="Status laporan">
          <option value="">Semua laporan</option>
          <?php foreach (['hilang', 'ditemukan', 'dikembalikan'] as $st): ?>
            <option value="<?= $st ?>" <?= $status === $st ? 'selected' : '' ?>><?= status_label($st) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="input-icon">
        <i class="bi bi-grid"></i>
        <select class="form-select" name="kategori" aria-label="Kategori">
          <option value="0">Semua kategori</option>
          <?php foreach ($daftarKategori as $k): ?>
            <option value="<?= $k['id'] ?>" <?= $kat === (int)$k['id'] ? 'selected' : '' ?>><?= e($k['nama']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <button class="btn btn-pill btn-pill-fill" type="submit"><i class="bi bi-search me-2"></i>Cari</button>
      <a class="btn btn-pill btn-pill-outline" href="<?= url('barang.php') ?>"><i class="bi bi-arrow-repeat me-2"></i>Reset</a>
    </div>

    <div class="chip-row">
      <div class="chips">
        <a class="chip <?= $kat === 0 ? 'active' : '' ?>" href="<?= link_kategori(0) ?>"><i class="bi bi-grid"></i>Semua</a>
        <?php foreach ($daftarKategori as $k): ?>
          <a class="chip <?= $kat === (int)$k['id'] ? 'active' : '' ?>" href="<?= link_kategori($k['id']) ?>"><i class="bi <?= kategori_icon($k['nama']) ?>"></i><?= e($k['nama']) ?></a>
        <?php endforeach; ?>
      </div>
      <span class="count"><i class="bi bi-file-earmark-text me-1"></i><?= $total ?> laporan ditemukan</span>
    </div>
  </form>

  <?php if (!$items): ?>
    <div class="empty-state">
      <i class="bi bi-inbox"></i>
      <h3>Belum ada laporan yang cocok</h3>
      <p>Coba kata kunci atau kategori yang berbeda.</p>
      <?php if ($adaFilter): ?><a class="btn btn-pill btn-pill-fill" href="<?= url('barang.php') ?>">Tampilkan semua barang</a><?php endif; ?>
    </div>
  <?php else: ?>
    <div class="row g-4">
      <?php foreach ($items as $b): ?>
        <div class="col-sm-6 col-lg-4 col-xl-3"><?php kartu_barang($b); ?></div>
      <?php endforeach; ?>
    </div>

    <?php if ($pages > 1): ?>
      <nav class="mt-5" aria-label="Halaman">
        <ul class="pagination justify-content-center">
          <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>"><a class="page-link" href="<?= link_halaman($page - 1) ?>">Sebelumnya</a></li>
          <?php for ($i = 1; $i <= $pages; $i++): ?>
            <li class="page-item <?= $i === $page ? 'active' : '' ?>"><a class="page-link" href="<?= link_halaman($i) ?>"><?= $i ?></a></li>
          <?php endfor; ?>
          <li class="page-item <?= $page >= $pages ? 'disabled' : '' ?>"><a class="page-link" href="<?= link_halaman($page + 1) ?>">Berikutnya</a></li>
        </ul>
      </nav>
    <?php endif; ?>
  <?php endif; ?>

  <div class="cta-band">
    <i class="bi bi-shield-check"></i>
    <div class="flex-grow-1">
      <strong>Tidak menemukan barangmu?</strong>
      <span>Buat laporan barang hilang atau barang temuan. Admin akan memeriksanya sebelum tayang.</span>
    </div>
    <a class="btn btn-pill btn-pill-fill" href="<?= url('user/lapor.php') ?>"><i class="bi bi-plus-circle me-2"></i>Buat Laporan</a>
  </div>
</div>
<?php require 'includes/footer.php'; ?>
