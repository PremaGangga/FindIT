<?php
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../includes/auth.php';

require_role('admin');

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

$stmt = $pdo->prepare('SELECT * FROM items WHERE id = ?');
$stmt->execute([$id ?: 0]);
$item = $stmt->fetch();

if (!$item) {
    http_response_code(404);
    exit('Laporan tidak ditemukan.');
}

$categories = $pdo->query(
    'SELECT * FROM categories ORDER BY nama'
)->fetchAll();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = trim($_POST['judul'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $kategori = (int) ($_POST['kategori_id'] ?? 0);
    $jenis = $_POST['jenis'] ?? '';
    $lokasi = trim($_POST['lokasi'] ?? '');
    $tanggal = $_POST['tanggal_kejadian'] ?? '';
    $kontak = trim($_POST['kontak'] ?? '');
    $status = $_POST['status'] ?? '';
    $foto = $item['foto'];

    if (!$judul || !$deskripsi || !$lokasi || !$tanggal || !$kontak) {
        $error = 'Semua kolom wajib diisi kecuali foto.';
    } elseif (
        !in_array($jenis, ['hilang', 'ditemukan'], true) ||
        !in_array(
            $status,
            ['hilang', 'ditemukan', 'dikembalikan'],
            true
        )
    ) {
        $error = 'Jenis atau status tidak valid.';
    } elseif (
        !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal) ||
        !checkdate(
            (int) substr($tanggal, 5, 2),
            (int) substr($tanggal, 8, 2),
            (int) substr($tanggal, 0, 4)
        )
    ) {
        $error = 'Tanggal tidak valid.';
    } else {
        if (
            isset($_FILES['foto']) &&
            $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE
        ) {
            $file = $_FILES['foto'];
            $allowed = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/webp' => 'webp'
            ];

            if (
                $file['error'] !== UPLOAD_ERR_OK ||
                $file['size'] > 2 * 1024 * 1024
            ) {
                $error = 'Upload gagal atau ukuran foto melebihi 2 MB.';
            } else {
                $mime = (new finfo(FILEINFO_MIME_TYPE))
                    ->file($file['tmp_name']);

                if (!isset($allowed[$mime])) {
                    $error = 'Foto harus JPG, PNG, atau WEBP.';
                } else {
                    $folder = __DIR__ . '/../assets/uploads/';

                    if (!is_dir($folder)) {
                        mkdir($folder, 0755, true);
                    }

                    $namaFotoBaru = bin2hex(random_bytes(16))
                        . '.' . $allowed[$mime];

                    if (move_uploaded_file(
                        $file['tmp_name'],
                        $folder . $namaFotoBaru
                    )) {
                        if (
                            $foto &&
                            is_file($folder . $foto)
                        ) {
                            unlink($folder . $foto);
                        }

                        $foto = $namaFotoBaru;
                    } else {
                        $error = 'Gagal menyimpan foto.';
                    }
                }
            }
        }

        if ($error === '') {
            $stmt = $pdo->prepare(
                'UPDATE items
                 SET judul = ?,
                     deskripsi = ?,
                     kategori_id = ?,
                     jenis = ?,
                     lokasi = ?,
                     tanggal_kejadian = ?,
                     kontak = ?,
                     foto = ?,
                     status = ?
                 WHERE id = ?'
            );

            $stmt->execute([
                $judul,
                $deskripsi,
                $kategori ?: null,
                $jenis,
                $lokasi,
                $tanggal,
                $kontak,
                $foto,
                $status,
                $id
            ]);

            header('Location: dashboard.php?updated=1');
            exit;
        }
    }

    $item = array_merge($item, [
        'judul' => $judul,
        'deskripsi' => $deskripsi,
        'kategori_id' => $kategori,
        'jenis' => $jenis,
        'lokasi' => $lokasi,
        'tanggal_kejadian' => $tanggal,
        'kontak' => $kontak,
        'status' => $status,
        'foto' => $foto
    ]);
}

$pageTitle = 'Edit Laporan';
$assetPrefix = '../';
$homePrefix = '../';

require __DIR__ . '/../includes/header.php';
?>

<div class="section-head">
    <h1>Edit Laporan Barang</h1>

    <a href="dashboard.php" class="btn btn-secondary">
        ← Dashboard
    </a>
</div>

<?php if ($error): ?>
    <div class="alert alert-error"><?= e($error) ?></div>
<?php endif; ?>

<form class="panel" method="POST" enctype="multipart/form-data">
    <div class="form-grid">

        <div class="field">
            <label>Nama Barang</label>
            <input class="input" name="judul" required
                   value="<?= e($item['judul']) ?>">
        </div>

        <div class="field">
            <label>Kategori</label>
            <select class="select" name="kategori_id">
                <option value="0">Lainnya</option>

                <?php foreach ($categories as $c): ?>
                    <option
                        value="<?= (int) $c['id'] ?>"
                        <?= (int) $item['kategori_id'] === (int) $c['id']
                            ? 'selected' : '' ?>
                    >
                        <?= e($c['nama']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="field">
            <label>Jenis</label>
            <select class="select" name="jenis">
                <option value="hilang"
                    <?= $item['jenis'] === 'hilang' ? 'selected' : '' ?>>
                    Hilang
                </option>

                <option value="ditemukan"
                    <?= $item['jenis'] === 'ditemukan' ? 'selected' : '' ?>>
                    Ditemukan
                </option>
            </select>
        </div>

        <div class="field">
            <label>Status</label>
            <select class="select" name="status">
                <?php
                $statuses = [
                    'hilang' => 'Hilang',
                    'ditemukan' => 'Ditemukan',
                    'dikembalikan' => 'Dikembalikan'
                ];

                foreach ($statuses as $value => $label):
                ?>
                    <option
                        value="<?= e($value) ?>"
                        <?= $item['status'] === $value ? 'selected' : '' ?>
                    >
                        <?= e($label) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="field">
            <label>Lokasi</label>
            <input class="input" name="lokasi" required
                   value="<?= e($item['lokasi']) ?>">
        </div>

        <div class="field">
            <label>Tanggal Kejadian</label>
            <input class="input" type="date"
                   name="tanggal_kejadian" required
                   value="<?= e($item['tanggal_kejadian']) ?>">
        </div>

        <div class="field">
            <label>Kontak</label>
            <input class="input" name="kontak" required
                   value="<?= e($item['kontak']) ?>">
        </div>

        <div class="field">
            <label>Ganti Foto (opsional)</label>
            <input class="input" type="file" name="foto"
                   accept=".jpg,.jpeg,.png,.webp">
        </div>

        <div class="field field-full">
            <label>Deskripsi</label>
            <textarea class="textarea" name="deskripsi"
                      rows="5" required><?= e($item['deskripsi']) ?></textarea>
        </div>

    </div>

    <div class="form-actions">
        <button class="btn" type="submit">Simpan Perubahan</button>
        <a href="dashboard.php" class="btn btn-secondary">Batal</a>
    </div>
</form>

<?php require __DIR__ . '/../includes/footer.php'; ?>