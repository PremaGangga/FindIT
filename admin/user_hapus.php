<?php
require '../includes/auth.php';
wajib_role('admin');
$kembali = 'admin/users.php';
require '../includes/proses_hapus_user.php';