</main>

<?php $uf = user(); ?>
<footer class="site-footer">
  <div class="container">
    <div class="row g-4">
      <div class="col-lg-5">
        <div class="f-brand"><i class="bi bi-geo-alt-fill"></i>Find<span>IT</span></div>
        <p>Platform barang hilang dan temuan kampus. Satu tempat untuk melapor, mencari, dan mengembalikan barang ke pemiliknya.</p>
      </div>
      <div class="col-6 col-lg-3 offset-lg-1">
        <h4>Jelajahi</h4>
        <ul>
          <li><a href="<?= url('index.php') ?>">Beranda</a></li>
          <li><a href="<?= url('barang.php') ?>">Daftar Barang</a></li>
          <li><a href="<?= url('tentang.php') ?>">Cara Kerja</a></li>
        </ul>
      </div>
      <div class="col-6 col-lg-3">
        <h4>Akun</h4>
        <ul>
          <?php if ($uf): ?>
            <li><a href="<?= url(dashboard_path($uf['role'])) ?>">Dashboard</a></li>
            <li><a href="<?= url('logout.php') ?>">Keluar</a></li>
          <?php else: ?>
            <li><a href="<?= url('login.php') ?>">Masuk</a></li>
            <li><a href="<?= url('daftar.php') ?>">Daftar</a></li>
            <li><a href="<?= url('user/lapor.php') ?>">Buat Laporan</a></li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
    <div class="f-bottom">
      <span>&copy; <?= date('Y') ?> FindIT.</span>
    </div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= url('assets/js/script.js') ?>"></script>
</body>
</html>