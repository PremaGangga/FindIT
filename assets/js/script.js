document.addEventListener('DOMContentLoaded', function () {
    const forms = document.querySelectorAll('[data-confirm]');

    forms.forEach(function (form) {
        form.addEventListener('submit', function (event) {
            const message =
                form.getAttribute('data-confirm') ||
                'Apakah Anda yakin ingin melanjutkan?';

            if (!confirm(message)) {
                event.preventDefault();
            }
        });
    });
});