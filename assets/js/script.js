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
});
