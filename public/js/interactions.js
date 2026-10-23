(() => {
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    const finePointer = window.matchMedia('(hover: hover) and (pointer: fine)');
    const surfaces = document.querySelectorAll('.hero, .admin-hero, .card, .team-card, .open-slot, .stats > .panel, .adventure-stats > .panel, .metrics-grid > .metric, .quick-link');

    surfaces.forEach((surface) => {
        surface.classList.add('interactive-surface');
        let frame = 0;
        let x = 0;
        let y = 0;

        const reset = () => {
            cancelAnimationFrame(frame);
            frame = 0;
            surface.classList.remove('is-pointer-inside');
            surface.style.removeProperty('--pointer-x');
            surface.style.removeProperty('--pointer-y');
        };

        surface.addEventListener('pointermove', (event) => {
            if (reducedMotion.matches || !finePointer.matches || event.pointerType === 'touch') return;
            const bounds = surface.getBoundingClientRect();
            x = event.clientX - bounds.left;
            y = event.clientY - bounds.top;
            if (frame) return;
            frame = requestAnimationFrame(() => {
                surface.style.setProperty('--pointer-x', `${x}px`);
                surface.style.setProperty('--pointer-y', `${y}px`);
                surface.classList.add('is-pointer-inside');
                frame = 0;
            });
        }, { passive: true });
        surface.addEventListener('pointerleave', reset);
        surface.addEventListener('pointercancel', reset);
        reducedMotion.addEventListener('change', reset);
        finePointer.addEventListener('change', reset);
    });

    const ripple = (control, clientX, clientY) => {
        if (reducedMotion.matches || control.disabled) return;
        const bounds = control.getBoundingClientRect();
        const size = Math.hypot(bounds.width, bounds.height) * 2;
        const circle = document.createElement('span');
        circle.className = 'interaction-ripple';
        circle.setAttribute('aria-hidden', 'true');
        circle.style.width = circle.style.height = `${size}px`;
        circle.style.left = `${clientX - bounds.left - size / 2}px`;
        circle.style.top = `${clientY - bounds.top - size / 2}px`;
        control.classList.add('has-ripple');
        control.querySelectorAll('.interaction-ripple').forEach((previous) => previous.remove());
        control.append(circle);
        circle.addEventListener('animationend', () => circle.remove(), { once: true });
        circle.addEventListener('animationcancel', () => circle.remove(), { once: true });
        // Also clean up when motion is disabled in the middle of an interaction.
        setTimeout(() => circle.remove(), 650);
    };

    const findControl = (target) => target.closest('a.button, button:not(.feedback-close)');
    document.addEventListener('pointerdown', (event) => {
        if (event.button !== 0) return;
        const control = findControl(event.target);
        if (control) ripple(control, event.clientX, event.clientY);
    }, { passive: true });
    document.addEventListener('click', (event) => {
        if (event.detail !== 0) return;
        const control = findControl(event.target);
        if (!control) return;
        const bounds = control.getBoundingClientRect();
        ripple(control, bounds.left + bounds.width / 2, bounds.top + bounds.height / 2);
    });
})();
