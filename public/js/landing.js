(() => {
    const header = document.querySelector('[data-landing-header]');
    const menuButton = document.querySelector('[data-landing-menu-toggle]');
    const navigation = document.getElementById('landing-navigation');
    const mobile = window.matchMedia('(max-width: 900px)');
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

    const closeMenu = (restoreFocus = false) => {
        menuButton?.setAttribute('aria-expanded', 'false');
        menuButton?.setAttribute('aria-label', 'Buka menu navigasi');
        navigation?.classList.remove('is-open');
        if (navigation) navigation.inert = mobile.matches;
        if (restoreFocus) menuButton?.focus();
    };
    const synchronizeMenu = () => {
        closeMenu();
        if (navigation) navigation.inert = mobile.matches;
    };
    menuButton?.addEventListener('click', () => {
        if (menuButton.getAttribute('aria-expanded') === 'true') { closeMenu(); return; }
        menuButton.setAttribute('aria-expanded', 'true');
        menuButton.setAttribute('aria-label', 'Tutup menu navigasi');
        navigation?.classList.add('is-open');
        if (navigation) navigation.inert = false;
    });
    navigation?.addEventListener('click', (event) => {
        if (event.target.closest('a')) closeMenu();
    });
    document.addEventListener('click', (event) => {
        if (header && !header.contains(event.target)) closeMenu();
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && menuButton?.getAttribute('aria-expanded') === 'true') closeMenu(true);
    });
    mobile.addEventListener('change', synchronizeMenu);
    synchronizeMenu();

    const updateHeader = () => {
        header?.classList.toggle('is-scrolled', window.scrollY > 16);
        if (window.scrollY < 64) {
            document.querySelectorAll('[data-section-link]').forEach((link) => {
                link.classList.remove('is-active');
                link.removeAttribute('aria-current');
            });
        }
    };
    let headerFrame = 0;
    window.addEventListener('scroll', () => {
        if (headerFrame) return;
        headerFrame = requestAnimationFrame(() => { updateHeader(); headerFrame = 0; });
    }, { passive: true });
    updateHeader();

    const reveals = document.querySelectorAll('[data-reveal]');
    let revealObserver;
    if (!reducedMotion.matches && 'IntersectionObserver' in window) {
        revealObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                entry.target.classList.add('is-visible');
                revealObserver.unobserve(entry.target);
            });
        }, { threshold: 0.08, rootMargin: '0px 0px 20px 0px' });
        document.body.classList.add('lp-motion');
        reveals.forEach((element) => revealObserver.observe(element));
    }
    reducedMotion.addEventListener('change', () => {
        if (!reducedMotion.matches) return;
        document.body.classList.remove('lp-motion');
        revealObserver?.disconnect();
    });

    if ('IntersectionObserver' in window) {
        const sectionObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                document.querySelectorAll('[data-section-link]').forEach((link) => {
                    const active = link.dataset.sectionLink === entry.target.id;
                    link.classList.toggle('is-active', active);
                    if (active) link.setAttribute('aria-current', 'location');
                    else link.removeAttribute('aria-current');
                });
            });
        }, { rootMargin: '-10% 0px -60% 0px', threshold: 0 });
        document.querySelectorAll('#explore, #how-it-works, #about').forEach((section) => sectionObserver.observe(section));
    }

    const hero = document.querySelector('[data-hero-surface]');
    const finePointer = window.matchMedia('(hover: hover) and (pointer: fine)');
    let cursorFrame = 0;
    let cursorX = 0;
    let cursorY = 0;
    const resetCursor = () => {
        cancelAnimationFrame(cursorFrame);
        cursorFrame = 0;
        hero?.style.removeProperty('--hero-x');
        hero?.style.removeProperty('--hero-y');
    };
    hero?.addEventListener('pointermove', (event) => {
        if (!finePointer.matches || reducedMotion.matches || event.pointerType === 'touch') return;
        const bounds = hero.getBoundingClientRect();
        cursorX = event.clientX - bounds.left;
        cursorY = event.clientY - bounds.top;
        if (cursorFrame) return;
        cursorFrame = requestAnimationFrame(() => {
            hero.style.setProperty('--hero-x', `${cursorX}px`);
            hero.style.setProperty('--hero-y', `${cursorY}px`);
            cursorFrame = 0;
        });
    }, { passive: true });
    hero?.addEventListener('pointerleave', resetCursor);
    hero?.addEventListener('pointercancel', resetCursor);
    finePointer.addEventListener('change', resetCursor);
    reducedMotion.addEventListener('change', resetCursor);
})();
