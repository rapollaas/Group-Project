// Small progressive enhancements for navigation and forms.
document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.querySelector('.menu-toggle');
    const nav = document.querySelector('.main-nav');

    if (toggle && nav) {
        toggle.addEventListener('click', () => {
            const isOpen = nav.classList.toggle('is-open');
            toggle.setAttribute('aria-expanded', String(isOpen));
        });
    }

    document.querySelectorAll('form[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (!window.confirm(form.dataset.confirm)) {
                event.preventDefault();
            }
        });
    });

    const progress = document.querySelector('[data-progress]');
    const progressValue = document.querySelector('#progress-value');
    if (progress && progressValue) {
        progress.addEventListener('input', () => {
            progressValue.textContent = `${progress.value}%`;
        });
    }
});
