<?php
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../includes/auth.php';

require_role('admin');

$total = (int) $pdo->query(
    'SELECT COUNT(*) FROM items'
)->fetchColumn();

$hilang = (int) $pdo->query(
    "SELECT COUNT(*) FROM items WHERE status = 'hilang'"
)->fetchColumn();

$selesai = (int) $pdo->query(
    "SELECT COUNT(*) FROM items WHERE status = 'dikembalikan'"
)->fetchColumn();

$items = $pdo->query(
    'SELECT items.*, categories.nama AS nama_kategori
     FROM items
     LEFT JOIN categories
        ON items.kategori_id = categories.id
     ORDER BY items.created_at DESC'
)->fetchAll();

$pageTitle = 'Dashboard Admin';
$assetPrefix = '../';
$homePrefix = '../';

require __DIR__ . '/../includes/header.php';
?>

<div class="dashboard-head">
    <div>
        <span class="eyebrow">Panel Pengelola</span>

        <h1>
            Halo, <?= e($_SESSION['nama']) ?>!
        </h1>

        <p class="muted">
            Kelola laporan barang hilang dan temuan.
        </p>
    </div>

    <a href="barang_tambah.php" class="btn">
        + Tambah Laporan
    </a>
</div>

<?php if (isset($_GET['added'])): ?>
    <div class="alert">Laporan berhasil ditambahkan.</div>
<?php elseif (isset($_GET['updated'])): ?>
    <div class="alert">Laporan berhasil diperbarui.</div>
<?php elseif (isset($_GET['deleted'])): ?>
    <div class="alert">Laporan berhasil dihapus.</div>
<?php endif; ?>

<div class="stats">

    <div class="stat">
        <span>Total Laporan</span>
        <strong><?= $total ?></strong>
    </div>

    <div class="stat">
        <span>Masih Hilang</span>
        <strong><?= $hilang ?></strong>
    </div>

    <div class="stat">
        <span>Sudah Dikembalikan</span>
        <strong><?= $selesai ?></strong>
    </div>

</div>

<div class="section-head">
    <h2>Kelola Laporan Barang</h2>
</div>

<?php if (!$items): ?>

    <div class="empty">
        <h3>Belum ada laporan</h3>
        <p>Tambahkan laporan pertama untuk memulai.</p>

        <a href="barang_tambah.php" class="btn">
            Tambah Laporan
        </a>
    </div>

<?php else: ?>

    <div class="table-wrap">
        <table class="table">

            <thead>
                <tr>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th>Jenis</th>
                    <th>Status</th>
                    <th>Lokasi</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>

                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= e($item['judul']) ?></td>

                        <td>
                            <?= e($item['nama_kategori'] ?? 'Lainnya') ?>
                        </td>

                        <td><?= e(ucfirst($item['jenis'])) ?></td>

                        <td><?= e(ucfirst($item['status'])) ?></td>

                        <td><?= e($item['lokasi']) ?></td>

                        <td>
                            <div class="table-actions">

                                <a
                                    class="btn btn-secondary"
                                    href="barang_edit.php?id=<?= (int) $item['id'] ?>"
                                >
                                    Edit
                                </a>

                                <form
                                    class="inline-form"
                                    method="POST"
                                    action="barang_hapus.php"
                                    data-confirm="Yakin ingin menghapus laporan ini?"
                                >
                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= (int) $item['id'] ?>"
                                    >

                                    <button
                                        type="submit"
                                        class="btn btn-danger"
                                    >
                                        Hapus
                                    </button>
                                </form>

                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>

            </tbody>
        </table>
    </div>

<?php endif; ?>

<?php require __DIR__ . '/../includes/footer.php'; ?>