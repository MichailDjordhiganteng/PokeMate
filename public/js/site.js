(() => {
    const sidebarButton = document.querySelector('[data-toggle-sidebar]');
    const sidebar = document.getElementById('admin-sidebar');
    const backdrop = document.querySelector('[data-close-sidebar]');
    const compactSidebar = window.matchMedia('(max-width: 760px)');
    const setSidebar = (open) => {
        document.body.classList.toggle('sidebar-open', open);
        sidebarButton?.setAttribute('aria-expanded', String(open));
        if (backdrop) backdrop.hidden = !open;
        if (sidebar) sidebar.inert = compactSidebar.matches && !open;
        if (open) sidebar?.querySelector('a')?.focus();
        else sidebarButton?.focus();
    };
    sidebarButton?.addEventListener('click', () => setSidebar(!document.body.classList.contains('sidebar-open')));
    backdrop?.addEventListener('click', () => setSidebar(false));
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && document.body.classList.contains('sidebar-open')) setSidebar(false);
        if (event.key === 'Tab' && sidebar && document.body.classList.contains('sidebar-open')) {
            const controls = [...sidebar.querySelectorAll('a[href], button:not(:disabled)')];
            const first = controls[0], last = controls[controls.length - 1];
            if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last?.focus(); }
            else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first?.focus(); }
        }
        if (event.key === 'Escape') {
            const profile = document.querySelector('.profile-menu[open]');
            if (profile) { profile.open = false; profile.querySelector('summary')?.focus(); }
        }
    });
    const synchronizeSidebar = () => {
        if (!sidebar) return;
        if (!compactSidebar.matches) {
            document.body.classList.remove('sidebar-open');
            sidebarButton?.setAttribute('aria-expanded', 'false');
            if (backdrop) backdrop.hidden = true;
        }
        sidebar.inert = compactSidebar.matches && !document.body.classList.contains('sidebar-open');
    };
    compactSidebar.addEventListener('change', synchronizeSidebar);
    synchronizeSidebar();
    document.querySelector('[data-toggle-trainer-nav]')?.addEventListener('click', (event) => {
        const button = event.currentTarget;
        const open = button.getAttribute('aria-expanded') !== 'true';
        button.setAttribute('aria-expanded', String(open));
        document.getElementById('trainer-nav')?.classList.toggle('is-open', open);
    });
    document.addEventListener('click', (event) => {
        document.querySelectorAll('.profile-menu[open]').forEach((profile) => {
            if (!profile.contains(event.target)) profile.open = false;
        });
        const demo = event.target.closest('[data-fill-demo]');
        if (demo) {
            const email = document.getElementById('email');
            const password = document.getElementById('password');
            if (email) { email.value = demo.dataset.fillDemo; email.dispatchEvent(new Event('input', { bubbles: true })); }
            if (password) { password.value = 'password'; password.dispatchEvent(new Event('input', { bubbles: true })); }
        }
        const opener = event.target.closest('[data-open-dialog]');
        if (opener) document.getElementById(opener.dataset.openDialog)?.showModal();
        const closer = event.target.closest('[data-close-dialog]');
        if (closer) closer.closest('dialog')?.close();
        const passwordButton = event.target.closest('[data-toggle-password]');
        if (passwordButton) {
            const input = document.getElementById(passwordButton.dataset.togglePassword);
            if (input) {
                input.type = input.type === 'password' ? 'text' : 'password';
                passwordButton.setAttribute('aria-pressed', String(input.type === 'text'));
                passwordButton.setAttribute('aria-label', input.type === 'text' ? 'Sembunyikan password' : 'Tampilkan password');
            }
        }
        const durationButton = event.target.closest('[data-duration]');
        if (durationButton) {
            const input = document.getElementById(durationButton.dataset.durationTarget || 'duration');
            if (input) {
                input.value = durationButton.dataset.duration;
                input.dispatchEvent(new Event('change', { bubbles: true }));
            }
        }
    });
    document.querySelectorAll('[data-duration]').forEach((button) => {
        const input = document.getElementById(button.dataset.durationTarget || 'duration');
        const sync = () => button.setAttribute('aria-pressed', String(input?.value === button.dataset.duration));
        if (input) button.closest('[data-duration-picker]')?.classList.add('duration-ready');
        input?.addEventListener('change', sync);
        sync();
    });
    document.querySelectorAll('[data-file-upload]').forEach((input) => {
        const display = input.closest('.upload-control')?.querySelector('[data-file-name]');
        const update = () => { if (display) display.textContent = input.files?.[0]?.name || 'Belum ada file dipilih'; };
        input.addEventListener('change', update);
        input.form?.addEventListener('reset', () => setTimeout(update, 0));
    });
})();
