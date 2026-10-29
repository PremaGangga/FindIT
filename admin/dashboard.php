<?php
require '../includes/auth.php';
wajib_role('admin');

// Aksi cepat: tandai sudah dikembalikan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? '') === 'kembali') {
    csrf_check();
    db_exec("UPDATE barang SET status = 'dikembalikan' WHERE id = ?", 'i', [(int)$_POST['id']]);
    flash_set('success', 'Status barang diubah menjadi sudah dikembalikan.');
    redirect('admin/dashboard.php');
}

$q = trim($_GET['q'] ?? '');
$status = $_GET['status'] ?? '';

// Filter kondisi utama: Wajib disetujui DAN belum di-soft delete oleh admin
$where = ["b.verifikasi = 'disetujui'", "b.dihapus_admin = 0"]; 
$types = ''; 
$params = [];

if ($q !== '') { 
    $where[] = '(b.judul LIKE ? OR b.lokasi LIKE ?)'; 
    $types .= 'ss'; 
    $params[] = "%$q%"; 
    $params[] = "%$q%"; 
}

if (in_array($status, ['hilang', 'ditemukan', 'dikembalikan'], true)) { 
    $where[] = 'b.status = ?'; 
    $types .= 's'; 
    $params[] = $status; 
}

$sqlWhere = 'WHERE ' . implode(' AND ', $where);

$rows = db_all(
    "SELECT b.*, k.nama AS kategori FROM barang b LEFT JOIN kategori k ON k.id = b.kategori_id
     $sqlWhere ORDER BY b.created_at DESC, b.id DESC LIMIT 200", $types, $params
);

// Hitung statistik hanya untuk barang yang disetujui DAN belum di-soft delete
$s = ['hilang' => 0, 'ditemukan' => 0, 'dikembalikan' => 0];
foreach (db_all("SELECT status, COUNT(*) AS n FROM barang WHERE verifikasi='disetujui' AND dihapus_admin = 0 GROUP BY status") as $r) {
    $s[$r['status']] = (int)$r['n'];
}

$nPending = (int)db_one("SELECT COUNT(*) AS n FROM barang WHERE verifikasi='pending' AND dihapus_admin = 0")['n'];
$nSelesai = (int)db_one("SELECT COUNT(*) AS n FROM barang WHERE verifikasi='disetujui' AND status='dikembalikan' AND dihapus_admin = 0")['n'];

$judul = 'Dashboard admin';
require '../includes/header.php';
?>
<div class="container page-pad">
  <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
      <h1 class="page-title mb-1">Kelola barang</h1>
      <p class="text-muted mb-0">Halo, <?= e(user()['nama']) ?>. Tambah, ubah, atau hapus laporan barang.</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
      <a class="btn btn-pill btn-pill-outline" href="<?= url('admin/users.php') ?>"><i class="bi bi-people me-1"></i>Pengguna</a>
      <a class="btn btn-pill btn-pill-outline" href="<?= url('admin/verifikasi.php') ?>"><i class="bi bi-patch-check me-1"></i>Verifikasi<?= $nPending ? ' (' . $nPending . ')' : '' ?></a>
      <a class="btn btn-pill btn-pill-fill" href="<?= url('admin/barang_tambah.php') ?>"><i class="bi bi-plus-lg me-1"></i>Tambah laporan</a>
    </div>
  </div>

  <?php if ($nPending > 0): ?>
    <a class="queue-banner" href="<?= url('admin/verifikasi.php') ?>">
      <i class="bi bi-inbox"></i>
      <span><strong><?= $nPending ?> laporan baru</strong> dari pengguna menunggu verifikasimu.</span>
      <span class="ms-auto fw-bold">Periksa sekarang <i class="bi bi-arrow-right"></i></span>
    </a>
  <?php endif; ?>

  <div class="row g-3 mb-4">
    <?php if ($nSelesai > 0 && $status !== 'dikembalikan'): ?>
    <a class="queue-banner done" href="?status=dikembalikan">
      <i class="bi bi-check2-circle"></i>
      <span><strong><?= $nSelesai ?> laporan</strong> sudah dikembalikan dan tidak tampil lagi di pencarian. Hapus kalau datanya tidak diperlukan.</span>
      <span class="ms-auto fw-bold">Lihat <i class="bi bi-arrow-right"></i></span>
    </a>
  <?php endif; ?>
    <div class="col-4"><div class="stat stat-hilang"><span><?= $s['hilang'] ?></span>Hilang</div></div>
    <div class="col-4"><div class="stat stat-ditemukan"><span><?= $s['ditemukan'] ?></span>Ditemukan</div></div>
    <div class="col-4"><div class="stat stat-dikembalikan"><span><?= $s['dikembalikan'] ?></span>Dikembalikan</div></div>
  </div>

  <form class="row g-2 mb-3" method="get">
    <div class="col-md-5"><input type="search" class="form-control" name="q" value="<?= e($q) ?>" placeholder="Cari nama barang atau lokasi"></div>
    <div class="col-md-3">
      <select class="form-select" name="status" aria-label="Filter status">
        <option value="">Semua status</option>
        <?php foreach (['hilang', 'ditemukan', 'dikembalikan'] as $st): ?>
          <option value="<?= $st ?>" <?= $status === $st ? 'selected' : '' ?>><?= status_label($st) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-4 d-flex gap-2">
      <button class="btn btn-primary-ink" type="submit">Filter</button>
      <a class="btn btn-light border" href="<?= url('admin/dashboard.php') ?>">Reset</a>
    </div>
  </form>

  <div class="table-card">
    <div class="table-responsive">
      <table class="table align-middle mb-0">
        <thead><tr><th>Barang</th><th>Kategori</th><th>Lokasi</th><th>Tanggal</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
        <tbody>
        <?php if (!$rows): ?>
          <tr><td colspan="6" class="text-center text-muted py-5">Belum ada data. Klik "Tambah laporan" untuk mulai.</td></tr>
        <?php endif; ?>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td>
              <div class="d-flex align-items-center gap-3">
                <?php if ($r['foto']): ?>
                  <img class="thumb" src="<?= url('assets/uploads/' . e($r['foto'])) ?>" alt="">
                <?php else: ?>
                  <span class="thumb thumb-icon"><i class="bi <?= kategori_icon($r['kategori']) ?>"></i></span>
                <?php endif; ?>
                <a class="fw-semibold text-decoration-none text-ink" href="<?= url('detail.php?id=' . $r['id']) ?>" target="_blank"><?= e($r['judul']) ?></a>
              </div>
            </td>
            <td><?= e($r['kategori'] ?? '-') ?></td>
            <td><?= e($r['lokasi']) ?></td>
            <td class="text-nowrap"><?= tgl($r['tanggal']) ?></td>
            <td><span class="status status-inline status-<?= e($r['status']) ?>"><?= status_label($r['status']) ?></span></td>
            <td class="text-end text-nowrap">
              <?php if ($r['status'] !== 'dikembalikan'): ?>
                <form method="post" class="d-inline" data-confirm="Tandai barang ini sudah dikembalikan ke pemiliknya?">
                  <?= csrf_field() ?><input type="hidden" name="aksi" value="kembali"><input type="hidden" name="id" value="<?= $r['id'] ?>">
                  <button class="btn btn-sm btn-light border" title="Tandai dikembalikan" aria-label="Tandai dikembalikan"><i class="bi bi-check2-circle"></i></button>
                </form>
              <?php endif; ?>
              <a class="btn btn-sm btn-light border" href="<?= url('admin/barang_edit.php?id=' . $r['id']) ?>" title="Ubah" aria-label="Ubah"><i class="bi bi-pencil"></i></a>
              <form method="post" action="<?= url('admin/barang_hapus.php') ?>" class="d-inline" data-confirm="Hapus laporan &quot;<?= e($r['judul']) ?>&quot;? Tindakan ini tidak bisa dibatalkan.">
                <?= csrf_field() ?><input type="hidden" name="id" value="<?= $r['id'] ?>">
                <button class="btn btn-sm btn-outline-danger" title="Hapus" aria-label="Hapus"><i class="bi bi-trash"></i></button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php require '../includes/footer.php'; ?>