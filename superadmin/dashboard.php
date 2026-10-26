<?php
require '../includes/auth.php';
wajib_role('superadmin');

$admins = db_all(
    "SELECT u.*, (SELECT COUNT(*) FROM barang b WHERE b.created_by = u.id) AS jumlah_laporan
     FROM users u WHERE u.role = 'admin' ORDER BY u.created_at DESC, u.id DESC"
);
$totalBarang = (int)db_one('SELECT COUNT(*) AS n FROM barang')['n'];
$totalUser = (int)db_one("SELECT COUNT(*) AS n FROM users WHERE role='user'")['n'];

$judul = 'Dashboard superadmin';
require '../includes/header.php';
?>
<div class="container page-pad">
  <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
      <h1 class="page-title mb-1">Kelola admin</h1>
      <p class="text-muted mb-0">Halo, <?= e(user()['nama']) ?>. Atur siapa saja yang boleh mengelola laporan barang.</p>
    </div>
        <div class="d-flex gap-2 flex-wrap">
      <a class="btn btn-pill btn-pill-outline" href="<?= url('superadmin/users.php') ?>"><i class="bi bi-people me-1"></i>Lihat pengguna</a>
      <a class="btn btn-pill btn-pill-fill" href="<?= url('superadmin/admin_tambah.php') ?>"><i class="bi bi-person-plus me-1"></i>Tambah admin</a>
    </div>
  </div>

  <div class="row g-3 mb-4">
    <div class="col-4"><div class="stat stat-ink"><span><?= count($admins) ?></span>Akun admin</div></div>
    <div class="col-4"><div class="stat stat-ink"><span><?= $totalUser ?></span>Akun pengguna</div></div>
    <div class="col-4"><div class="stat stat-ink"><span><?= $totalBarang ?></span>Total laporan</div></div>
  </div>

  <div class="table-card">
    <div class="table-responsive">
      <table class="table align-middle mb-0">
        <thead><tr><th>Nama</th><th>Username</th><th>Laporan dibuat</th><th>Dibuat pada</th><th class="text-end">Aksi</th></tr></thead>
        <tbody>
        <?php if (!$admins): ?>
          <tr><td colspan="5" class="text-center text-muted py-5">Belum ada akun admin. Klik "Tambah admin" untuk membuat yang pertama.</td></tr>
        <?php endif; ?>
        <?php foreach ($admins as $a): ?>
          <tr>
            <td class="fw-semibold"><?= e($a['nama']) ?></td>
            <td><code><?= e($a['username']) ?></code></td>
            <td><?= (int)$a['jumlah_laporan'] ?></td>
            <td class="text-nowrap"><?= tgl($a['created_at']) ?></td>
            <td class="text-end text-nowrap">
              <a class="btn btn-sm btn-light border" href="<?= url('superadmin/admin_edit.php?id=' . $a['id']) ?>" title="Ubah" aria-label="Ubah"><i class="bi bi-pencil"></i></a>
              <form method="post" action="<?= url('superadmin/admin_hapus.php') ?>" class="d-inline" data-confirm="Hapus akun admin &quot;<?= e($a['username']) ?>&quot;? Laporan yang pernah dibuatnya tetap tersimpan.">
                <?= csrf_field() ?><input type="hidden" name="id" value="<?= $a['id'] ?>">
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
