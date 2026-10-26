<?php
require '../includes/auth.php';
wajib_role('superadmin');

$judul    = 'Pengguna terdaftar';
$back_url = 'superadmin/dashboard.php';
$self     = 'superadmin/users.php';
$hapus_url = 'superadmin/user_hapus.php';
require '../includes/header.php';
require '../includes/daftar_user.php';
require '../includes/footer.php';