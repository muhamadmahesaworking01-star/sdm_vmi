(function () {
    'use strict';

    const ready = () => document.body.classList.add('is-ready');
    const loader = document.getElementById('page-loader');
    const progress = document.getElementById('route-progress');
    const showProgress = () => { if (progress) progress.style.width = '82%'; };

    window.addEventListener('load', () => {
        ready();
        if (loader) { loader.classList.add('is-hidden'); setTimeout(() => loader.remove(), 450); }
        if (progress) progress.style.width = '100%';
    });
    if (document.readyState === 'complete') ready();

    document.querySelectorAll('.js-reveal, .card, .list-group-item, .table-responsive').forEach((el, i) => {
        el.classList.add('js-reveal');
        el.style.transitionDelay = `${Math.min(i * 35, 280)}ms`;
    });
    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver(entries => entries.forEach(entry => {
            if (entry.isIntersecting) { entry.target.classList.add('is-visible'); observer.unobserve(entry.target); }
        }), { threshold: .08 });
        document.querySelectorAll('.js-reveal').forEach(el => observer.observe(el));
    } else document.querySelectorAll('.js-reveal').forEach(el => el.classList.add('is-visible'));

    document.addEventListener('click', e => {
        const target = e.target.closest('button, .btn, .menu-item a, .submenu-item a, .back-dashboard-btn');
        if (!target || target.disabled) return;
        const href = target.getAttribute('href');
        if (href && href !== '#' && !href.startsWith('#') && target.target !== '_blank') showProgress();
    });

    document.querySelectorAll('form').forEach(form => form.addEventListener('submit', e => {
        if (e.defaultPrevented || form.dataset.noLoading !== undefined) return;
        const button = form.querySelector('button[type="submit"], button:not([type]), input[type="submit"]');
        if (!button) return;
        button.disabled = true; button.classList.add('is-loading');
        const label = button.querySelector('.button-label') || button;
        if (!button.dataset.originalLabel) button.dataset.originalLabel = label.innerHTML;
        label.innerHTML = '<span class="inline-spinner" aria-hidden="true"></span>Memproses...';
        showProgress();
    }));

    document.querySelectorAll('a[href]').forEach(link => link.addEventListener('click', e => {
        if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey || link.target === '_blank' || link.getAttribute('href').startsWith('#')) return;
        showProgress();
    }));

    const sidebar = document.querySelector('.sidebar');
    if (sidebar) {
        const button = document.createElement('button'); button.className = 'mobile-menu-button'; button.type = 'button'; button.setAttribute('aria-label', 'Buka menu'); button.innerHTML = '<i class="bi bi-list" aria-hidden="true"></i>';
        const backdrop = document.createElement('div'); backdrop.className = 'sidebar-backdrop'; document.body.append(button, backdrop);
        const toggle = () => { sidebar.classList.toggle('mobile-open'); backdrop.classList.toggle('is-visible'); button.innerHTML = sidebar.classList.contains('mobile-open') ? '<i class="bi bi-x-lg" aria-hidden="true"></i>' : '<i class="bi bi-list" aria-hidden="true"></i>'; };
        button.addEventListener('click', toggle); backdrop.addEventListener('click', toggle);
        sidebar.querySelectorAll('a').forEach(a => a.addEventListener('click', () => { if (window.innerWidth <= 768 && sidebar.classList.contains('mobile-open')) toggle(); }));
    }
})();