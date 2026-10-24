<?php
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../includes/auth.php';

require_role('superadmin');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'admin';

    if (
        $nama === '' ||
        !preg_match('/^[a-zA-Z0-9_.-]{3,50}$/', $username) ||
        strlen($password) < 8 ||
        !in_array($role, ['admin', 'superadmin'], true)
    ) {
        $error = 'Periksa kembali data. Password minimal 8 karakter.';
    } else {
        try {
            $stmt = $pdo->prepare(
                'INSERT INTO users (nama, username, password, role)
                 VALUES (?, ?, ?, ?)'
            );

            $stmt->execute([
                $nama,
                $username,
                password_hash($password, PASSWORD_DEFAULT),
                $role
            ]);

            header('Location: dashboard.php?user_added=1');
            exit;

        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $error = 'Username sudah digunakan.';
            } else {
                $error = 'Gagal membuat akun. Silakan periksa database.';
            }
        }
    }
}

$pageTitle = 'Tambah Akun';
$assetPrefix = '../';
$homePrefix = '../';

require __DIR__ . '/../includes/header.php';
?>

<div class="section-head">
    <h1>Tambah Akun Baru</h1>

    <a href="dashboard.php" class="btn btn-secondary">
        ← Dashboard
    </a>
</div>

<?php if ($error): ?>
    <div class="alert alert-error"><?= e($error) ?></div>
<?php endif; ?>

<form class="panel" method="POST">

    <div class="form-grid">

        <div class="field">
            <label>Nama Lengkap</label>
            <input class="input" name="nama" required
                   value="<?= e($_POST['nama'] ?? '') ?>">
        </div>

        <div class="field">
            <label>Username</label>
            <input class="input" name="username" required
                   minlength="3" maxlength="50"
                   value="<?= e($_POST['username'] ?? '') ?>">
        </div>

        <div class="field">
            <label>Password (minimal 8 karakter)</label>
            <input class="input" type="password"
                   name="password" required minlength="8">
        </div>

        <div class="field">
            <label>Role Akun</label>

            <select class="select" name="role">
                <option value="admin">Admin</option>
                <option value="superadmin">Superadmin</option>
            </select>
        </div>

    </div>

    <div class="form-actions">
        <button class="btn" type="submit">Buat Akun</button>

        <a href="dashboard.php" class="btn btn-secondary">
            Batal
        </a>
    </div>

</form>

<?php require __DIR__ . '/../includes/footer.php'; ?>