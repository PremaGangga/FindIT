<?php
require '../includes/auth.php';
wajib_role('admin');

$tab = ($_GET['tab'] ?? 'pending') === 'ditolak' ? 'ditolak' : 'pending';
$rows = db_all(
    "SELECT b.*, k.nama AS kategori, u.nama AS pelapor, u.username
     FROM barang b LEFT JOIN kategori k ON k.id = b.kategori_id LEFT JOIN users u ON u.id = b.created_by
     WHERE b.verifikasi = ? ORDER BY b.created_at ASC, b.id ASC", 's', [$tab]
);
$nPending = (int)db_one("SELECT COUNT(*) AS n FROM barang WHERE verifikasi='pending'")['n'];
$nDitolak = (int)db_one("SELECT COUNT(*) AS n FROM barang WHERE verifikasi='ditolak'")['n'];

$judul = 'Verifikasi laporan';
require '../includes/header.php';
?>
<div class="container page-pad">
  <a class="back-link" href="<?= url('admin/dashboard.php') ?>"><i class="bi bi-arrow-left me-1"></i>Kembali ke dashboard</a>
  <h1 class="page-title mt-2 mb-1">Verifikasi laporan</h1>
  <p class="text-muted mb-4">Periksa laporan dari pengguna. Hanya laporan yang disetujui yang tampil ke publik.</p>

  <ul class="nav nav-pills tabs-pill mb-3">
    <li class="nav-item"><a class="nav-link <?= $tab === 'pending' ? 'active' : '' ?>" href="?tab=pending">Menunggu <span class="badge-n"><?= $nPending ?></span></a></li>
    <li class="nav-item"><a class="nav-link <?= $tab === 'ditolak' ? 'active' : '' ?>" href="?tab=ditolak">Ditolak <span class="badge-n"><?= $nDitolak ?></span></a></li>
  </ul>

  <div class="table-card">
    <div class="table-responsive">
      <table class="table align-middle mb-0">
        <thead><tr><th>Barang</th><th>Pelapor</th><th>Jenis</th><th>Lokasi dan tanggal</th><th>Kontak</th><th class="text-end">Aksi</th></tr></thead>
        <tbody>
        <?php if (!$rows): ?>
          <tr><td colspan="6" class="text-center text-muted py-5"><?= $tab === 'pending' ? 'Tidak ada laporan yang menunggu. Semua sudah diperiksa.' : 'Belum ada laporan yang ditolak.' ?></td></tr>
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
                  <a class="fw-semibold text-decoration-none text-ink" href="<?= url('detail.php?id=' . $r['id']) ?>" target="_blank"><?= e($r['judul']) ?></a>
                  <div class="small text-muted"><?= e($r['kategori'] ?? '-') ?></div>
                  <?php if ($tab === 'ditolak' && $r['catatan_admin']): ?><div class="small text-danger">Alasan: <?= e($r['catatan_admin']) ?></div><?php endif; ?>
                </div>
              </div>
            </td>
            <td><?= $r['pelapor'] ? e($r['pelapor']) . '<div class="small text-muted">@' . e($r['username']) . '</div>' : '<span class="text-muted">-</span>' ?></td>
            <td><span class="status status-inline status-<?= e($r['status']) ?>"><?= status_label($r['status']) ?></span></td>
            <td><?= e($r['lokasi']) ?><div class="small text-muted"><?= tgl($r['tanggal']) ?></div></td>
            <td><?= e($r['kontak'] ?: '-') ?></td>
            <td class="text-end text-nowrap">
              <form method="post" action="<?= url('admin/verifikasi_aksi.php') ?>" class="d-inline" data-confirm="Setujui dan tayangkan laporan ini ke publik?">
                <?= csrf_field() ?><input type="hidden" name="aksi" value="setujui"><input type="hidden" name="id" value="<?= $r['id'] ?>">
                <button class="btn btn-sm btn-success"><i class="bi bi-check-lg me-1"></i>Setujui</button>
              </form>
              <?php if ($tab === 'pending'): ?>
                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalTolak"
                        data-tolak-id="<?= $r['id'] ?>" data-tolak-judul="<?= e($r['judul']) ?>"><i class="bi bi-x-lg me-1"></i>Tolak</button>
              <?php endif; ?>
              <a class="btn btn-sm btn-light border" href="<?= url('admin/barang_edit.php?id=' . $r['id']) ?>" title="Periksa dan ubah" aria-label="Ubah"><i class="bi bi-pencil"></i></a>
              <form method="post" action="<?= url('admin/barang_hapus.php') ?>" class="d-inline" data-confirm="Hapus laporan &quot;<?= e($r['judul']) ?>&quot; secara permanen?">
                <?= csrf_field() ?><input type="hidden" name="id" value="<?= $r['id'] ?>">
                <button class="btn btn-sm btn-light border text-danger" title="Hapus" aria-label="Hapus"><i class="bi bi-trash"></i></button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal alasan penolakan (dipakai bersama untuk semua baris) -->
<div class="modal fade" id="modalTolak" tabindex="-1" aria-labelledby="judulModalTolak" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form class="modal-content" method="post" action="<?= url('admin/verifikasi_aksi.php') ?>">
      <?= csrf_field() ?>
      <input type="hidden" name="aksi" value="tolak">
      <input type="hidden" name="id" id="tolak-id" value="">
      <div class="modal-header">
        <h2 class="modal-title h5" id="judulModalTolak">Tolak laporan</h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body">
        <p class="mb-3">Laporan: <strong id="tolak-judul"></strong></p>
        <label class="form-label" for="catatan">Alasan penolakan</label>
        <textarea class="form-control" id="catatan" name="catatan" rows="3" maxlength="255" required placeholder="Contoh: Foto kurang jelas, mohon lengkapi ciri-ciri barang."></textarea>
        <div class="form-text">Alasan ini akan terlihat oleh pelapor di dashboard-nya.</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-danger">Tolak laporan</button>
      </div>
    </form>
  </div>
</div>
<?php require '../includes/footer.php'; ?>
