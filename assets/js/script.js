document.addEventListener('DOMContentLoaded', function () {

  // Konfirmasi sebelum aksi penting (hapus, tandai dikembalikan)
  // Pemakaian: <form data-confirm="Yakin hapus?"> ... </form>
  document.querySelectorAll('form[data-confirm]').forEach(function (form) {
    form.addEventListener('submit', function (event) {
      if (!confirm(form.getAttribute('data-confirm') || 'Apakah Anda yakin?')) {
        event.preventDefault();
      }
    });
  });

  // Tombol mata untuk melihat/menyembunyikan password
  // Pemakaian: <button data-toggle-password="#password">
  document.querySelectorAll('[data-toggle-password]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      const input = document.querySelector(btn.getAttribute('data-toggle-password'));
      if (!input) return;
      const tampil = input.type === 'password';
      input.type = tampil ? 'text' : 'password';
      const icon = btn.querySelector('i');
      if (icon) icon.className = tampil ? 'bi bi-eye-slash' : 'bi bi-eye';
    });
  });

  // Modal "Tolak laporan": isi id dan judul laporan yang dipilih
  document.querySelectorAll('[data-tolak-id]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      document.getElementById('tolak-id').value = btn.getAttribute('data-tolak-id');
      document.getElementById('tolak-judul').textContent = btn.getAttribute('data-tolak-judul');
      document.getElementById('catatan').value = '';
    });
  });

  // Modal "Hapus pengguna": isi id, nama, dan jumlah laporan pengguna yang dipilih
  document.querySelectorAll('[data-hapus-user-id]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      const n = parseInt(btn.getAttribute('data-hapus-user-total') || '0', 10);
      const cek = document.getElementById('hapus-laporan');
      document.getElementById('hapus-user-id').value = btn.getAttribute('data-hapus-user-id');
      document.getElementById('hapus-user-nama').textContent = btn.getAttribute('data-hapus-user-nama');
      document.getElementById('hapus-laporan-n').textContent = n;
      cek.checked = false;
      cek.disabled = (n === 0);
    });
  });

  // Bayangan navbar saat halaman di-scroll
  const nav = document.querySelector('.site-nav');
  if (nav) {
    const cek = function () { nav.classList.toggle('scrolled', window.scrollY > 8); };
    cek(); window.addEventListener('scroll', cek, { passive: true });
  }

  // Animasi muncul saat di-scroll (elemen ber-class "reveal")
  const reveals = document.querySelectorAll('.reveal');
  if ('IntersectionObserver' in window) {
    const io = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) {
        if (en.isIntersecting) { en.target.classList.add('in'); io.unobserve(en.target); }
      });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
    reveals.forEach(function (el) { io.observe(el); });
  } else {
    reveals.forEach(function (el) { el.classList.add('in'); });
  }

  // Angka di beranda menghitung naik dari 0
  // Pemakaian: <span data-count="12">12</span>
  const counters = document.querySelectorAll('[data-count]');
  if ('IntersectionObserver' in window && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    const co = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) {
        if (!en.isIntersecting) return;
        const el = en.target, target = parseInt(el.getAttribute('data-count'), 10) || 0, t0 = performance.now(), dur = 900;
        co.unobserve(el);
        (function tick(now) {
          const p = Math.min((now - t0) / dur, 1);
          el.textContent = Math.round(target * (1 - Math.pow(1 - p, 3)));
          if (p < 1) requestAnimationFrame(tick);
        })(t0);
      });
    }, { threshold: 0.6 });
    counters.forEach(function (el) { el.textContent = '0'; co.observe(el); });
  }

  // Tombol salin (tautan atau kontak)
  // Pemakaian: <button data-copy="teks yang disalin">Salin</button>
  document.querySelectorAll('[data-copy]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      const teks = btn.getAttribute('data-copy') === 'url' ? window.location.href : btn.getAttribute('data-copy');
      const label = btn.innerHTML;
      const selesai = function () {
        btn.innerHTML = '<i class="bi bi-check2 me-1"></i>Tersalin';
        setTimeout(function () { btn.innerHTML = label; }, 1800);
      };
      if (navigator.clipboard) { navigator.clipboard.writeText(teks).then(selesai); }
      else {
        const ta = document.createElement('textarea'); ta.value = teks; document.body.appendChild(ta);
        ta.select(); document.execCommand('copy'); document.body.removeChild(ta); selesai();
      }
    });
  });
});