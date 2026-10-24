<?php
require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/includes/auth.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

$stmt = $pdo->prepare("
    SELECT items.*, categories.nama AS nama_kategori
    FROM items
    LEFT JOIN categories
        ON items.kategori_id = categories.id
    WHERE items.id = ?
");

$stmt->execute([$id ?: 0]);
$item = $stmt->fetch();

if (!$item) {
    http_response_code(404);
    $pageTitle = 'Laporan Tidak Ditemukan';
    require __DIR__ . '/includes/header.php';
    ?>

    <div class="empty">
        <h1>Laporan tidak ditemukan</h1>
        <p>Barang tersebut mungkin sudah dihapus.</p>
        <a href="index.php" class="btn">Kembali ke Beranda</a>
    </div>

    <?php
    require __DIR__ . '/includes/footer.php';
    exit;
}

$pageTitle = $item['judul'];
require __DIR__ . '/includes/header.php';
?>

<div class="actions">
    <a href="index.php" class="btn btn-secondary">
        ← Kembali
    </a>
</div>

<div class="detail-layout" style="margin-top:25px">

    <div class="item-image">
        <?php
        $fotoPath = __DIR__ . '/assets/uploads/' .
            ($item['foto'] ?? '');

        if (!empty($item['foto']) && is_file($fotoPath)):
        ?>
            <img
                src="assets/uploads/<?= e($item['foto']) ?>"
                alt="<?= e($item['judul']) ?>"
            >
        <?php else: ?>
            🔎
        <?php endif; ?>
    </div>

    <div>
        <span class="tag">
            <?= e($item['nama_kategori'] ?? 'Lainnya') ?>
        </span>

        <span class="status <?= $item['status'] === 'hilang'
            ? 'status-hilang'
            : ($item['status'] === 'dikembalikan'
                ? 'status-dikembalikan' : '') ?>">
            <?= e(ucfirst($item['status'])) ?>
        </span>

        <h1><?= e($item['judul']) ?></h1>

        <p class="muted">
            Jenis: <?= e(ucfirst($item['jenis'])) ?>
        </p>

        <div class="panel">
            <h3>Deskripsi Barang</h3>

            <p><?= nl2br(e($item['deskripsi'])) ?></p>

            <p>
                <strong>Lokasi:</strong>
                <?= e($item['lokasi']) ?>
            </p>

            <p>
                <strong>Tanggal:</strong>
                <?= e(date(
                    'd-m-Y',
                    strtotime($item['tanggal_kejadian'])
                )) ?>
            </p>

            <p>
                <strong>Kategori:</strong>
                <?= e($item['nama_kategori'] ?? 'Lainnya') ?>
            </p>

            <p>
                <strong>Kontak:</strong>
                <?= e($item['kontak']) ?>
            </p>
        </div>

        <p class="small muted">
            Pastikan kepemilikan barang sebelum menyerahkannya.
            Gunakan informasi kontak secara bertanggung jawab.
        </p>
    </div>

</div>

<?php require __DIR__ . '/includes/footer.php'; ?>