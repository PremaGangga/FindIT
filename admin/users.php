<?php
require '../includes/auth.php';
wajib_role('admin');

$judul    = 'Pengguna terdaftar';
$back_url = 'admin/dashboard.php';
$self     = 'admin/users.php';
$hapus_url = 'admin/user_hapus.php';
require '../includes/header.php';
require '../includes/daftar_user.php';
require '../includes/footer.php';