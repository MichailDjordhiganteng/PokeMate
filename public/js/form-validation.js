(() => {
    if (!window.PokePartner?.notify || !document.getElementById('notification-template-warning')) return;

    document.querySelectorAll('form[data-validate]').forEach((form, formIndex) => {
        // Keep HTML constraints as a no-JavaScript fallback; use themed feedback when JS is ready.
        form.noValidate = true;
        const fields = [...form.elements].filter((field) => field.willValidate);
        const feedbackKey = `validation-${form.id || formIndex}`;
        const marked = new Map();
        let notification = null;

        const messageFor = (field) => {
            const label = field.dataset.validationLabel || field.labels?.[0]?.textContent.trim() || 'Kolom ini';
            const blankText = field.required && typeof field.value === 'string'
                && !['checkbox', 'radio', 'file'].includes(field.type) && !field.value.trim();
            if (field.validity.valueMissing || blankText) {
                return field.dataset.requiredMessage || `${label} wajib diisi.`;
            }
            if (field.validity.valid) return '';
            if (field.dataset.invalidMessage) return field.dataset.invalidMessage;
            if (field.validity.typeMismatch) return `${label} belum sesuai format yang diminta.`;
            if (field.validity.tooLong) return `${label} maksimal ${field.maxLength} karakter.`;
            if (field.validity.rangeUnderflow) return `${label} tidak boleh kurang dari ${field.min}.`;
            if (field.validity.rangeOverflow) return `${label} tidak boleh lebih dari ${field.max}.`;
            return `Periksa kembali ${label.toLowerCase()}.`;
        };

        const clearField = (field) => {
            const state = marked.get(field);
            if (!state) return;
            state.error.remove();
            if (state.description === null) field.removeAttribute('aria-describedby');
            else field.setAttribute('aria-describedby', state.description);
            if (state.invalid === null) field.removeAttribute('aria-invalid');
            else field.setAttribute('aria-invalid', state.invalid);
            field.closest('label.check')?.classList.remove('field-invalid');
            marked.delete(field);
        };

        const markField = (field, message) => {
            if (!message) { clearField(field); return; }
            let state = marked.get(field);
            if (!state) {
                const error = document.createElement('p');
                error.className = 'field-error';
                error.id = `${feedbackKey}-${fields.indexOf(field)}-error`;
                state = { error, description: field.getAttribute('aria-describedby'), invalid: field.getAttribute('aria-invalid') };
                marked.set(field, state);
                (field.closest('label.check') || field).insertAdjacentElement('afterend', error);
                field.setAttribute('aria-describedby', [state.description, error.id].filter(Boolean).join(' '));
            }
            state.error.textContent = message;
            field.setAttribute('aria-invalid', 'true');
            field.closest('label.check')?.classList.add('field-invalid');
        };

        fields.forEach((field) => {
            const update = () => {
                if (marked.has(field)) markField(field, messageFor(field));
                if (fields.every((candidate) => !messageFor(candidate))) notification?.remove();
            };
            field.addEventListener('input', update);
            field.addEventListener('change', update);
        });

        form.addEventListener('submit', (event) => {
            const errors = fields.map((field) => ({ field, message: messageFor(field) })).filter(({ message }) => message);
            fields.forEach((field) => markField(field, messageFor(field)));
            if (!errors.length) { notification?.remove(); return; }
            event.preventDefault();
            notification = window.PokePartner.notify({
                type: 'warning', title: form.dataset.validationTitle || 'Periksa kembali data Anda',
                details: errors.map(({ message }) => message), duration: 0, key: feedbackKey,
            });
            errors[0].field.focus();
        });
        form.addEventListener('reset', () => {
            fields.forEach(clearField);
            notification?.remove();
        });
    });
})();
