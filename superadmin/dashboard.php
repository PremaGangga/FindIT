<?php
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../includes/auth.php';

require_role('superadmin');

$total = (int) $pdo->query(
    'SELECT COUNT(*) FROM users'
)->fetchColumn();

$admins = (int) $pdo->query(
    "SELECT COUNT(*) FROM users WHERE role = 'admin'"
)->fetchColumn();

$items = (int) $pdo->query(
    'SELECT COUNT(*) FROM items'
)->fetchColumn();

$users = $pdo->query(
    'SELECT id, nama, username, role, created_at
     FROM users
     ORDER BY created_at DESC'
)->fetchAll();

$pageTitle = 'Dashboard Superadmin';
$assetPrefix = '../';
$homePrefix = '../';

require __DIR__ . '/../includes/header.php';
?>

<div class="dashboard-head">
    <div>
        <span class="eyebrow">Kontrol Sistem</span>
        <h1>Dashboard Superadmin</h1>

        <p class="muted">
            Selamat datang, <?= e($_SESSION['nama']) ?>.
        </p>
    </div>

    <a href="admin_tambah.php" class="btn">
        + Tambah Akun
    </a>
</div>

<?php if (isset($_GET['user_added'])): ?>
    <div class="alert">Akun berhasil ditambahkan.</div>
<?php elseif (isset($_GET['user_updated'])): ?>
    <div class="alert">Akun berhasil diperbarui.</div>
<?php elseif (isset($_GET['user_deleted'])): ?>
    <div class="alert">Akun berhasil dihapus.</div>
<?php elseif (isset($_GET['error'])): ?>
    <div class="alert alert-error">
        Akun tidak dapat dihapus.
    </div>
<?php endif; ?>

<div class="stats">

    <div class="stat">
        <span>Total Akun</span>
        <strong><?= $total ?></strong>
    </div>

    <div class="stat">
        <span>Jumlah Admin</span>
        <strong><?= $admins ?></strong>
    </div>

    <div class="stat">
        <span>Total Laporan</span>
        <strong><?= $items ?></strong>
    </div>

</div>

<div class="section-head">
    <h2>Manajemen Akun</h2>
</div>

<div class="table-wrap">
    <table class="table">

        <thead>
            <tr>
                <th>Nama</th>
                <th>Username</th>
                <th>Role</th>
                <th>Tanggal Dibuat</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>

            <?php foreach ($users as $user): ?>
                <tr>

                    <td><?= e($user['nama']) ?></td>
                    <td><?= e($user['username']) ?></td>
                    <td><?= e($user['role']) ?></td>

                    <td>
                        <?= e(date(
                            'd-m-Y',
                            strtotime($user['created_at'])
                        )) ?>
                    </td>

                    <td>
                        <div class="table-actions">

                            <a
                                href="admin_edit.php?id=<?= (int) $user['id'] ?>"
                                class="btn btn-secondary"
                            >
                                Edit
                            </a>

                            <?php if (
                                (int) $user['id'] !==
                                (int) $_SESSION['user_id']
                            ): ?>

                                <form
                                    method="POST"
                                    action="admin_hapus.php"
                                    class="inline-form"
                                    data-confirm="Yakin ingin menghapus akun ini?"
                                >
                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= (int) $user['id'] ?>"
                                    >

                                    <button
                                        type="submit"
                                        class="btn btn-danger"
                                    >
                                        Hapus
                                    </button>
                                </form>

                            <?php endif; ?>

                        </div>
                    </td>

                </tr>
            <?php endforeach; ?>

        </tbody>
    </table>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>