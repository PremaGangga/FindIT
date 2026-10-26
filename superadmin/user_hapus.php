<?php
require '../includes/auth.php';
wajib_role('superadmin');
$kembali = 'superadmin/users.php';
require '../includes/proses_hapus_user.php';