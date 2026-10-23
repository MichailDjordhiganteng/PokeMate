(() => {
    const dialog = document.getElementById('confirm-dialog');
    const title = document.getElementById('confirm-title');
    const description = document.getElementById('confirm-description');
    const approveButton = document.getElementById('confirm-submit');
    const approvedForms = new WeakSet();
    let pending = null;

    document.addEventListener('click', (event) => {
        const dismiss = event.target.closest('[data-dismiss-notification]');
        if (dismiss) dismiss.closest('[data-notification]')?.remove();
    });

    const initializeNotification = (notification) => {
        const duration = Number(notification.dataset.duration);
        const progress = notification.querySelector('[data-notification-progress]');
        // Validation messages stay visible until explicitly dismissed.
        if (!Number.isFinite(duration) || duration <= 0 || !progress) return;

        const countdown = progress.animate([
            { transform: 'scaleX(1)' }, { transform: 'scaleX(0)' },
        ], { duration, fill: 'forwards', easing: 'linear' });
        countdown.onfinish = () => notification.remove();

        const synchronize = () => {
            const pause = document.hidden || notification.matches(':hover')
                || notification.contains(document.activeElement) || dialog?.open;
            if (pause) countdown.pause();
            else if (countdown.playState !== 'finished') countdown.play();
        };
        notification.addEventListener('pointerenter', synchronize);
        notification.addEventListener('pointerleave', synchronize);
        notification.addEventListener('focusin', synchronize);
        notification.addEventListener('focusout', () => queueMicrotask(synchronize));
        document.addEventListener('visibilitychange', synchronize);
        const observer = dialog ? new MutationObserver(synchronize) : null;
        observer?.observe(dialog, { attributes: true, attributeFilter: ['open'] });
        // Release observers and animation state when dismissed manually or by the timer.
        const removal = new MutationObserver(() => {
            if (notification.isConnected) return;
            countdown.cancel();
            observer?.disconnect();
            document.removeEventListener('visibilitychange', synchronize);
            removal.disconnect();
        });
        removal.observe(notification.parentElement, { childList: true });
        synchronize();
    };

    document.querySelectorAll('[data-notification]').forEach(initializeNotification);

    const notify = ({ type = 'info', title, message, details = [], duration, key } = {}) => {
        const variant = ['success', 'error', 'warning', 'info'].includes(type) ? type : 'info';
        const template = document.getElementById(`notification-template-${variant}`);
        if (!template) return null;
        const activeDialog = document.querySelector('dialog[open]');
        let stack = activeDialog ? activeDialog.querySelector('.notification-stack')
            : document.querySelector('main > .notification-stack, body > .notification-stack');
        if (!stack) {
            stack = document.createElement('div');
            stack.className = 'notification-stack';
            stack.setAttribute('aria-label', 'Notifikasi');
            (activeDialog || document.body).append(stack);
        }
        if (key) {
            stack.querySelectorAll('[data-notification]').forEach((existing) => {
                if (existing.dataset.notificationKey === key) existing.remove();
            });
        }
        const notification = template.content.firstElementChild.cloneNode(true);
        const content = notification.querySelector('.feedback-content');
        if (title) content.querySelector('strong').textContent = title;
        if (message) {
            const paragraph = document.createElement('p');
            paragraph.textContent = message;
            content.append(paragraph);
        }
        if (details.length) {
            const list = document.createElement('ul');
            details.forEach((detail) => {
                const item = document.createElement('li');
                item.textContent = detail;
                list.append(item);
            });
            content.append(list);
        }
        if (duration !== undefined) notification.dataset.duration = String(duration);
        if (key) notification.dataset.notificationKey = key;
        stack.append(notification);
        initializeNotification(notification);
        return notification;
    };
    window.PokePartner = Object.assign(window.PokePartner || {}, { notify });

    if (!dialog) return;

    document.addEventListener('submit', (event) => {
        const form = event.target;
        if (!(form instanceof HTMLFormElement) || !form.hasAttribute('data-confirm')) return;
        if (approvedForms.has(form)) {
            approvedForms.delete(form);
            return;
        }

        event.preventDefault();
        if (dialog.open) return;
        pending = { form, submitter: event.submitter };
        title.textContent = form.dataset.confirm;
        description.textContent = form.dataset.confirmDescription || 'Pastikan data yang dipilih sudah benar sebelum melanjutkan.';
        approveButton.disabled = false;
        approveButton.textContent = 'Ya, Hapus';
        dialog.showModal();
    });

    dialog.querySelectorAll('[data-cancel-confirm]').forEach((button) => {
        button.addEventListener('click', () => dialog.close());
    });

    dialog.addEventListener('click', (event) => {
        if (event.target !== dialog) return;
        const bounds = dialog.getBoundingClientRect();
        if (event.clientX < bounds.left || event.clientX > bounds.right || event.clientY < bounds.top || event.clientY > bounds.bottom) {
            dialog.close();
        }
    });

    dialog.addEventListener('close', () => { pending = null; });

    approveButton.addEventListener('click', () => {
        if (!pending || approveButton.disabled) return;
        const { form, submitter } = pending;
        approveButton.disabled = true;
        approveButton.textContent = 'Menghapus…';
        pending = null;
        dialog.close();
        approvedForms.add(form);
        // Preserve CSRF, method spoofing, validation, and the original submit button.
        if (submitter && submitter.form === form) form.requestSubmit(submitter);
        else form.requestSubmit();
        approvedForms.delete(form);
    });
})();
