<?php
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../includes/auth.php';

require_role('admin');

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
    $foto = null;

    if (
        $judul === '' ||
        $deskripsi === '' ||
        $lokasi === '' ||
        $tanggal === '' ||
        $kontak === ''
    ) {
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
        $error = 'Tanggal kejadian tidak valid.';
    } else {
        if (
            isset($_FILES['foto']) &&
            $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE
        ) {
            $allowed = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/webp' => 'webp'
            ];

            $file = $_FILES['foto'];

            if ($file['error'] !== UPLOAD_ERR_OK) {
                $error = 'Upload foto gagal.';
            } elseif ($file['size'] > 2 * 1024 * 1024) {
                $error = 'Ukuran foto maksimal 2 MB.';
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

                    $foto = bin2hex(random_bytes(16))
                        . '.' . $allowed[$mime];

                    if (!move_uploaded_file(
                        $file['tmp_name'],
                        $folder . $foto
                    )) {
                        $error = 'Gagal menyimpan foto.';
                        $foto = null;
                    }
                }
            }
        }

        if ($error === '') {
            $stmt = $pdo->prepare(
                'INSERT INTO items (
                    judul, deskripsi, kategori_id, jenis,
                    lokasi, tanggal_kejadian, kontak,
                    foto, status, created_by
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
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
                $_SESSION['user_id']
            ]);

            header('Location: dashboard.php?added=1');
            exit;
        }
    }
}

$pageTitle = 'Tambah Laporan';
$assetPrefix = '../';
$homePrefix = '../';

require __DIR__ . '/../includes/header.php';
?>

<div class="section-head">
    <h1>Tambah Laporan Barang</h1>

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
            <label>Nama Barang *</label>
            <input class="input" name="judul" required
                   value="<?= e($_POST['judul'] ?? '') ?>">
        </div>

        <div class="field">
            <label>Kategori</label>

            <select class="select" name="kategori_id">
                <?php foreach ($categories as $c): ?>
                    <option value="<?= (int) $c['id'] ?>">
                        <?= e($c['nama']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="field">
            <label>Jenis Laporan</label>

            <select class="select" name="jenis">
                <option value="hilang">Barang Hilang</option>
                <option value="ditemukan">Barang Ditemukan</option>
            </select>
        </div>

        <div class="field">
            <label>Status</label>

            <select class="select" name="status">
                <option value="hilang">Hilang</option>
                <option value="ditemukan">Ditemukan</option>
                <option value="dikembalikan">Dikembalikan</option>
            </select>
        </div>

        <div class="field">
            <label>Lokasi Kejadian *</label>
            <input class="input" name="lokasi" required
                   value="<?= e($_POST['lokasi'] ?? '') ?>">
        </div>

        <div class="field">
            <label>Tanggal Kejadian *</label>
            <input class="input" type="date"
                   name="tanggal_kejadian" required
                   value="<?= e($_POST['tanggal_kejadian'] ?? date('Y-m-d')) ?>">
        </div>

        <div class="field">
            <label>Kontak *</label>
            <input class="input" name="kontak" required
                   placeholder="Email atau nomor kontak"
                   value="<?= e($_POST['kontak'] ?? '') ?>">
        </div>

        <div class="field">
            <label>Foto (maks. 2 MB)</label>
            <input class="input" type="file" name="foto"
                   accept=".jpg,.jpeg,.png,.webp">
        </div>

        <div class="field field-full">
            <label>Deskripsi Barang *</label>
            <textarea class="textarea" name="deskripsi"
                      rows="5" required><?= e($_POST['deskripsi'] ?? '') ?></textarea>
        </div>

    </div>

    <div class="form-actions">
        <button class="btn" type="submit">Simpan Laporan</button>

        <a href="dashboard.php" class="btn btn-secondary">
            Batal
        </a>
    </div>

</form>

<?php require __DIR__ . '/../includes/footer.php'; ?>