(() => {
    const root = document.documentElement;
    const toggle = document.getElementById('themeToggle');
    const icon = document.getElementById('themeIcon');
    const text = document.getElementById('themeText');
    const stored = localStorage.getItem('gatherly-theme');
    const preferred = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';

    function applyTheme(theme) {
        root.dataset.theme = theme;
        if (icon) icon.textContent = theme === 'dark' ? '☾' : '☀';
        if (text) text.textContent = theme === 'dark' ? 'Dark Mode' : 'Light Mode';
        localStorage.setItem('gatherly-theme', theme);
    }
    applyTheme(stored || preferred);

    toggle?.addEventListener('click', () => {
        applyTheme(root.dataset.theme === 'dark' ? 'light' : 'dark');
    });

    document.getElementById('mobileMenu')?.addEventListener('click', () => {
        document.getElementById('mainNav')?.classList.toggle('open');
    });

    document.querySelectorAll('[data-toast]').forEach(t => {
        setTimeout(() => {
            t.style.opacity = '0';
            t.style.transform = 'translateY(-8px)';
            setTimeout(() => t.remove(), 250);
        }, 4500);
    });

    document.querySelectorAll('form[data-validate]').forEach(form => {
        form.addEventListener('submit', e => {
            let valid = true;
            form.querySelectorAll('[required]').forEach(input => {
                input.classList.remove('invalid');
                if (!input.value.trim()) {
                    input.classList.add('invalid');
                    valid = false;
                }
            });
            if (!valid) {
                e.preventDefault();
                form.querySelector('.invalid')?.focus();
            }
        });
    });

    document.querySelectorAll('input, textarea').forEach(input => {
        input.addEventListener('input', () => input.classList.remove('invalid'));
    });
})();