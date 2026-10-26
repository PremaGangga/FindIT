<?php
/**
 * Tabel pengguna terdaftar (hanya baca). Dipakai admin dan superadmin.
 * Butuh: $back_url (path dashboard), $self (path halaman ini).
 * Password TIDAK pernah dibaca atau ditampilkan di sini.
 */
$q = trim($_GET['q'] ?? '');
$where = "WHERE u.role = 'user'"; $types = ''; $params = [];
if ($q !== '') { $where .= ' AND (u.nama LIKE ? OR u.username LIKE ?)'; $types = 'ss'; $params = ["%$q%", "%$q%"]; }

$rows = db_all(
    "SELECT u.id, u.nama, u.username, u.created_at,
            COUNT(b.id) AS total,
            COALESCE(SUM(b.verifikasi = 'disetujui'), 0) AS tayang,
            COALESCE(SUM(b.verifikasi = 'pending'), 0)   AS menunggu,
            COALESCE(SUM(b.verifikasi = 'ditolak'), 0)   AS ditolak
     FROM users u LEFT JOIN barang b ON b.created_by = u.id
     $where
     GROUP BY u.id, u.nama, u.username, u.created_at
     ORDER BY u.created_at DESC, u.id DESC LIMIT 300", $types, $params
);
$totalUser = (int)db_one("SELECT COUNT(*) AS n FROM users WHERE role = 'user'")['n'];
$baru7     = (int)db_one("SELECT COUNT(*) AS n FROM users WHERE role = 'user' AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")['n'];
$aktif     = (int)db_one("SELECT COUNT(DISTINCT b.created_by) AS n FROM barang b JOIN users u ON u.id = b.created_by WHERE u.role = 'user'")['n'];

function inisial($nama) {
    $c = function_exists('mb_substr') ? mb_substr($nama, 0, 1) : substr($nama, 0, 1);
    return function_exists('mb_strtoupper') ? mb_strtoupper($c) : strtoupper($c);
}
?>
<div class="container page-pad">
  <a class="back-link" href="<?= url($back_url) ?>"><i class="bi bi-arrow-left me-1"></i>Kembali ke dashboard</a>
  <h1 class="page-title mt-2 mb-1">Pengguna terdaftar</h1>
  <p class="text-muted mb-4">Daftar akun yang mendaftar lewat website, beserta jumlah laporan yang pernah mereka buat.</p>

  <div class="row g-3 mb-4">
    <div class="col-4"><div class="stat stat-ink"><span><?= $totalUser ?></span>Total pengguna</div></div>
    <div class="col-4"><div class="stat stat-ink"><span><?= $baru7 ?></span>Baru 7 hari terakhir</div></div>
    <div class="col-4"><div class="stat stat-ink"><span><?= $aktif ?></span>Pernah melapor</div></div>
  </div>

  <form class="row g-2 mb-3" method="get" action="<?= url($self) ?>">
    <div class="col-md-6"><input type="search" class="form-control" name="q" value="<?= e($q) ?>" placeholder="Cari nama atau username" aria-label="Cari pengguna"></div>
    <div class="col-md-6 d-flex gap-2">
      <button class="btn btn-primary-ink" type="submit">Cari</button>
      <a class="btn btn-light border" href="<?= url($self) ?>">Reset</a>
    </div>
  </form>

  <div class="table-card">
    <div class="table-responsive">
      <table class="table align-middle mb-0">
        <thead><tr><th>#</th><th>Pengguna</th><th>Terdaftar</th><th>Total laporan</th><th>Rincian</th><th class="text-end">Aksi</th></tr></thead>
        <tbody>
        <?php if (!$rows): ?>
          <tr><td colspan="6" class="text-center text-muted py-5"><?= $q !== '' ? 'Tidak ada pengguna yang cocok dengan pencarian.' : 'Belum ada pengguna yang mendaftar.' ?></td></tr>
        <?php endif; ?>
        <?php $no = 0; foreach ($rows as $r): $no++; ?>
          <tr>
            <td class="text-muted"><?= $no ?></td>
            <td>
              <div class="d-flex align-items-center gap-3">
                <span class="avatar"><?= e(inisial($r['nama'])) ?></span>
                <div><div class="fw-semibold text-ink"><?= e($r['nama']) ?></div><div class="small text-muted">@<?= e($r['username']) ?></div></div>
              </div>
            </td>
            <td class="text-nowrap"><?= tgl($r['created_at']) ?><div class="small text-muted"><?= date('H:i', strtotime($r['created_at'])) ?></div></td>
            <td class="fw-semibold"><?= (int)$r['total'] ?></td>
            <td>
              <?php if ((int)$r['total'] === 0): ?>
                <span class="text-muted">Belum ada laporan</span>
              <?php else: ?>
                <div class="d-flex flex-wrap gap-1">
                  <?php if ($r['tayang']):   ?><span class="status status-inline status-disetujui"><?= (int)$r['tayang'] ?> tayang</span><?php endif; ?>
                  <?php if ($r['menunggu']): ?><span class="status status-inline status-pending"><?= (int)$r['menunggu'] ?> menunggu</span><?php endif; ?>
                  <?php if ($r['ditolak']):  ?><span class="status status-inline status-ditolak"><?= (int)$r['ditolak'] ?> ditolak</span><?php endif; ?>
                </div>
                            <?php endif; ?>
            </td>
            <td class="text-end">
              <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalHapusUser"
                      data-hapus-user-id="<?= $r['id'] ?>" data-hapus-user-nama="<?= e($r['nama']) ?>" data-hapus-user-total="<?= (int)$r['total'] ?>"
                      title="Hapus pengguna" aria-label="Hapus pengguna"><i class="bi bi-trash"></i></button>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
    <p class="text-muted small mt-3"><i class="bi bi-info-circle me-1"></i>Password pengguna tersimpan terenkripsi dan tidak bisa dilihat siapa pun.</p>
</div>

<!-- Modal konfirmasi hapus pengguna (dipakai bersama untuk semua baris) -->
<div class="modal fade" id="modalHapusUser" tabindex="-1" aria-labelledby="judulModalHapusUser" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form class="modal-content" method="post" action="<?= url($hapus_url) ?>">
      <?= csrf_field() ?>
      <input type="hidden" name="id" id="hapus-user-id" value="">
      <div class="modal-header">
        <h2 class="modal-title h5" id="judulModalHapusUser">Hapus pengguna</h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body">
        <p>Akun <strong id="hapus-user-nama"></strong> akan dihapus permanen dan tidak bisa masuk lagi.</p>
        <div class="form-check hapus-opsi">
          <input class="form-check-input" type="checkbox" name="hapus_laporan" value="1" id="hapus-laporan">
          <label class="form-check-label" for="hapus-laporan">Hapus juga <strong><span id="hapus-laporan-n">0</span> laporan</strong> milik pengguna ini</label>
        </div>
        <div class="form-text mt-2">Kalau tidak dicentang, laporannya tetap ada (yang sudah tayang tetap tampil) tetapi tanpa pemilik. Centang untuk menghapus laporan spam.</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-danger"><i class="bi bi-trash me-1"></i>Hapus pengguna</button>
      </div>
    </form>
  </div>
</div>