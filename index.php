<?php
require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Beranda';

$q = trim($_GET['q'] ?? '');
$jenis = $_GET['jenis'] ?? '';
$kategori = (int) ($_GET['kategori'] ?? 0);

$sql = "
    SELECT items.*, categories.nama AS nama_kategori
    FROM items
    LEFT JOIN categories
        ON items.kategori_id = categories.id
    WHERE 1=1
";

$params = [];

if ($q !== '') {
    $sql .= "
        AND (
            items.judul LIKE ?
            OR items.deskripsi LIKE ?
            OR items.lokasi LIKE ?
        )
    ";

    $keyword = "%$q%";
    $params[] = $keyword;
    $params[] = $keyword;
    $params[] = $keyword;
}

if (in_array($jenis, ['hilang', 'ditemukan'], true)) {
    $sql .= " AND items.jenis = ?";
    $params[] = $jenis;
}

if ($kategori > 0) {
    $sql .= " AND items.kategori_id = ?";
    $params[] = $kategori;
}

$sql .= " ORDER BY items.created_at DESC LIMIT 100";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$items = $stmt->fetchAll();

$categories = $pdo->query(
    "SELECT * FROM categories ORDER BY nama"
)->fetchAll();

require __DIR__ . '/includes/header.php';
?>

<section class="hero">
    <div>
        <span class="eyebrow">
            Platform Barang Hilang dan Temuan
        </span>

        <h1>
            Barang hilang?<br>
            <span style="color:var(--blue)">
                Cari di FindIT.
            </span>
        </h1>

        <p>
            Temukan informasi barang hilang dan barang temuan
            di lingkungan kampus melalui satu platform yang mudah
            digunakan.
        </p>

        <div class="actions">
            <a href="#daftar" class="btn">Cari Barang →</a>
            <a href="tentang.php" class="btn btn-secondary">
                Tentang FindIT
            </a>
        </div>
    </div>

    <div class="hero-art">
        <div class="hero-card">
            <div class="big-icon">🔎 🎒</div>
            <h3>Temukan. Hubungkan. Kembalikan.</h3>
            <p>
                Informasi yang tepat membantu barang kembali
                kepada pemiliknya.
            </p>
        </div>
    </div>
</section>

<section id="daftar">
    <div class="section-head">
        <div>
            <span class="eyebrow">Papan informasi</span>
            <h2>Daftar Barang</h2>
        </div>

        <span class="muted small">
            <?= count($items) ?> laporan ditemukan
        </span>
    </div>

    <form method="GET" class="filters">
        <input
            class="input"
            type="search"
            name="q"
            placeholder="Cari nama barang atau lokasi..."
            value="<?= e($q) ?>"
        >

        <select class="select" name="jenis">
            <option value="">Semua laporan</option>

            <option value="hilang"
                <?= $jenis === 'hilang' ? 'selected' : '' ?>>
                Barang hilang
            </option>

            <option value="ditemukan"
                <?= $jenis === 'ditemukan' ? 'selected' : '' ?>>
                Barang ditemukan
            </option>
        </select>

        <select class="select" name="kategori">
            <option value="0">Semua kategori</option>

            <?php foreach ($categories as $c): ?>
                <option
                    value="<?= (int) $c['id'] ?>"
                    <?= $kategori === (int) $c['id']
                        ? 'selected' : '' ?>
                >
                    <?= e($c['nama']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button class="btn" type="submit">Cari</button>

        <a href="index.php" class="btn btn-secondary">Reset</a>
    </form>

    <?php if (empty($items)): ?>

        <div class="empty">
            <h3>Belum ada laporan yang cocok</h3>
            <p>Coba kata kunci atau kategori yang berbeda.</p>
        </div>

    <?php else: ?>

        <div class="grid">

            <?php foreach ($items as $item): ?>

                <article class="card">

                    <div class="item-image">
                        <?php
                        $fotoPath = __DIR__ . '/assets/uploads/' .
                            ($item['foto'] ?? '');

                        if (
                            !empty($item['foto']) &&
                            is_file($fotoPath)
                        ):
                        ?>
                            <img
                                src="assets/uploads/<?= e($item['foto']) ?>"
                                alt="<?= e($item['judul']) ?>"
                            >
                        <?php else: ?>
                            🔎
                        <?php endif; ?>
                    </div>

                    <div class="card-body">

                        <span class="tag">
                            <?= e($item['nama_kategori'] ?? 'Lainnya') ?>
                        </span>

                        <span class="status <?= $item['status'] === 'hilang'
                            ? 'status-hilang'
                            : ($item['status'] === 'dikembalikan'
                                ? 'status-dikembalikan' : '') ?>">
                            <?= e(ucfirst($item['status'])) ?>
                        </span>

                        <h3><?= e($item['judul']) ?></h3>

                        <p>
                            📍 <?= e($item['lokasi']) ?>
                        </p>

                        <p>
                            📅 <?= e(date(
                                'd-m-Y',
                                strtotime($item['tanggal_kejadian'])
                            )) ?>
                        </p>

                        <p>
                            <?= e(mb_strimwidth(
                                $item['deskripsi'],
                                0,
                                100,
                                '...'
                            )) ?>
                        </p>

                        <div class="actions">
                            <a
                                href="detail.php?id=<?= (int) $item['id'] ?>"
                                class="btn btn-secondary"
                            >
                                Lihat Detail
                            </a>
                        </div>

                    </div>
                </article>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>