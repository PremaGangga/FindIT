<?php
require '../includes/auth.php';
wajib_role('user');
$uid = user()['id'];

$rows = db_all(
    "SELECT b.*, k.nama AS kategori FROM barang b LEFT JOIN kategori k ON k.id = b.kategori_id
     WHERE b.created_by = ? ORDER BY b.created_at DESC, b.id DESC", 'i', [$uid]
);
$s = ['pending' => 0, 'disetujui' => 0, 'ditolak' => 0];
foreach ($rows as $r) $s[$r['verifikasi']]++;

$judul = 'Dashboard saya';
require '../includes/header.php';
?>
<div class="container page-pad">
  <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
      <h1 class="page-title mb-1">Laporan saya</h1>
      <p class="text-muted mb-0">Halo, <?= e(user()['nama']) ?>. Laporanmu diperiksa admin sebelum tampil di daftar barang.</p>
    </div>
    <a class="btn btn-pill btn-pill-fill" href="<?= url('user/lapor.php') ?>"><i class="bi bi-plus-circle me-2"></i>Buat laporan</a>
  </div>

  <div class="row g-3 mb-4">
    <div class="col-4"><div class="stat stat-pending"><span><?= $s['pending'] ?></span>Menunggu</div></div>
    <div class="col-4"><div class="stat stat-ditemukan"><span><?= $s['disetujui'] ?></span>Tayang</div></div>
    <div class="col-4"><div class="stat stat-hilang"><span><?= $s['ditolak'] ?></span>Ditolak</div></div>
  </div>

  <div class="table-card">
    <div class="table-responsive">
      <table class="table align-middle mb-0">
        <thead><tr><th>Barang</th><th>Jenis</th><th>Tanggal</th><th>Verifikasi</th><th class="text-end">Aksi</th></tr></thead>
        <tbody>
        <?php if (!$rows): ?>
          <tr><td colspan="5" class="text-center text-muted py-5">Kamu belum membuat laporan. Klik "Buat laporan" untuk memulai.</td></tr>
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
                <div>
                  <a class="fw-semibold text-decoration-none text-ink" href="<?= url('detail.php?id=' . $r['id']) ?>"><?= e($r['judul']) ?></a>
                  <?php if ($r['verifikasi'] === 'ditolak' && $r['catatan_admin']): ?>
                    <div class="small text-danger mt-1"><i class="bi bi-chat-left-text me-1"></i>Catatan admin: <?= e($r['catatan_admin']) ?></div>
                  <?php endif; ?>
                </div>
              </div>
            </td>
            <td><span class="status status-inline status-<?= e($r['status']) ?>"><?= status_label($r['status']) ?></span></td>
            <td class="text-nowrap"><?= tgl($r['tanggal']) ?></td>
            <td><span class="status status-inline status-<?= e($r['verifikasi']) ?>"><?= verifikasi_label($r['verifikasi']) ?></span></td>
            <td class="text-end text-nowrap">
              <?php if ($r['verifikasi'] !== 'disetujui'): ?>
                <a class="btn btn-sm btn-light border" href="<?= url('user/edit.php?id=' . $r['id']) ?>" title="<?= $r['verifikasi'] === 'ditolak' ? 'Perbaiki dan kirim ulang' : 'Ubah' ?>" aria-label="Ubah"><i class="bi bi-pencil"></i></a>
                <form method="post" action="<?= url('user/hapus.php') ?>" class="d-inline" data-confirm="Hapus laporan &quot;<?= e($r['judul']) ?>&quot;?">
                  <?= csrf_field() ?><input type="hidden" name="id" value="<?= $r['id'] ?>">
                  <button class="btn btn-sm btn-outline-danger" title="Hapus" aria-label="Hapus"><i class="bi bi-trash"></i></button>
                </form>
                            <?php else: ?>
                <?php if ($r['status'] !== 'dikembalikan'): ?>
                  <form method="post" action="<?= url('user/selesai.php') ?>" class="d-inline" data-confirm="Tandai &quot;<?= e($r['judul']) ?>&quot; sudah dikembalikan? Laporan akan hilang dari daftar pencarian.">
                    <?= csrf_field() ?><input type="hidden" name="id" value="<?= $r['id'] ?>">
                    <button class="btn btn-sm btn-success"><i class="bi bi-check2-circle me-1"></i>Sudah dikembalikan</button>
                  </form>
                <?php endif; ?>
                <a class="btn btn-sm btn-light border" href="<?= url('detail.php?id=' . $r['id']) ?>" title="Lihat" aria-label="Lihat"><i class="bi bi-eye"></i></a>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
  <p class="text-muted small mt-3"><i class="bi bi-info-circle me-1"></i>Laporan yang sudah tayang tidak bisa diubah sendiri. Kalau barangnya sudah kembali, klik <strong>Sudah dikembalikan</strong>: laporan hilang dari daftar pencarian dan admin akan merapikan datanya.</p>
</div>
<?php require '../includes/footer.php'; ?>
