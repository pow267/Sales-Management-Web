(function () {
    let toastTimer = null;

    function getFieldElement(form, fieldName) {
        if (!(form instanceof HTMLFormElement) || !fieldName) {
            return null;
        }

        const field = form.elements.namedItem(fieldName);

        if (!field) {
            return null;
        }

        if (typeof RadioNodeList !== 'undefined' && field instanceof RadioNodeList) {
            return field.length > 0 ? field[0] : null;
        }

        return field;
    }

    function clearFieldError(form, fieldName) {
        if (!(form instanceof HTMLFormElement) || !fieldName) {
            return;
        }

        const errorBox = form.querySelector('[data-field-error="' + fieldName + '"]');

        if (errorBox instanceof HTMLElement) {
            errorBox.textContent = '';
            errorBox.hidden = true;
        }

        const field = getFieldElement(form, fieldName);

        if (field instanceof HTMLElement) {
            field.classList.remove('input-invalid');
            field.removeAttribute('aria-invalid');
        }
    }

    function clearFormErrors(form) {
        if (!(form instanceof HTMLFormElement)) {
            return;
        }

        form.querySelectorAll('[data-field-error]').forEach((errorBox) => {
            if (!(errorBox instanceof HTMLElement)) {
                return;
            }

            errorBox.textContent = '';
            errorBox.hidden = true;
        });

        form.querySelectorAll('.input-invalid').forEach((field) => {
            if (!(field instanceof HTMLElement)) {
                return;
            }

            field.classList.remove('input-invalid');
            field.removeAttribute('aria-invalid');
        });
    }

    function renderFormErrors(form, errors) {
        if (!(form instanceof HTMLFormElement) || !errors || typeof errors !== 'object') {
            return false;
        }

        clearFormErrors(form);

        let hasErrors = false;

        Object.entries(errors).forEach(([fieldName, rawMessage]) => {
            const message = Array.isArray(rawMessage)
                ? String(rawMessage[0] || '')
                : String(rawMessage || '');

            if (!message) {
                return;
            }

            const errorBox = form.querySelector('[data-field-error="' + fieldName + '"]');
            const field = getFieldElement(form, fieldName);

            if (errorBox instanceof HTMLElement) {
                errorBox.textContent = message;
                errorBox.hidden = false;
                hasErrors = true;
            }

            if (field instanceof HTMLElement) {
                field.classList.add('input-invalid');
                field.setAttribute('aria-invalid', 'true');
                hasErrors = true;
            }
        });

        return hasErrors;
    }

    function clearMessage(target) {
        if (!(target instanceof HTMLElement)) {
            return;
        }

        target.replaceChildren();
    }

    function renderMessage(target, message, className) {
        if (!(target instanceof HTMLElement) || !message) {
            return null;
        }

        const messageBox = document.createElement('div');
        messageBox.className = className || 'form-message form-message-error';
        messageBox.textContent = message;
        target.replaceChildren(messageBox);

        return messageBox;
    }

    async function request(url, options) {
        const config = options || {};
        const headers = Object.assign(
            {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            config.headers || {}
        );

        const fetchOptions = {
            method: config.method || 'GET',
            headers,
            credentials: 'same-origin'
        };

        if (config.body !== undefined) {
            fetchOptions.body = config.body;

            if (
                !(config.body instanceof FormData) &&
                !headers['Content-Type']
            ) {
                fetchOptions.headers['Content-Type'] = 'application/json';
            }
        }

        const response = await fetch(url, fetchOptions);
        const contentType = response.headers.get('content-type') || '';
        let payload = null;

        if (contentType.includes('application/json')) {
            payload = await response.json();
        } else {
            payload = {
                success: response.ok,
                message: await response.text()
            };
        }

        if (!response.ok || payload.success === false) {
            const error = new Error(payload.message || 'Yeu cau that bai.');
            error.status = response.status;
            error.payload = payload;
            throw error;
        }

        return payload;
    }

    function ensureToast() {
        let toast = document.getElementById('toast');

        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'toast';
            toast.className = 'toast';
            document.body.appendChild(toast);
        }

        return toast;
    }

    function showToast(message, type) {
        const toast = ensureToast();

        toast.textContent = message;
        toast.style.opacity = '1';
        toast.style.display = 'block';
        toast.style.backgroundColor = type === 'error' ? '#b91c1c' : '';
        toast.style.color = '#fff';

        clearTimeout(toastTimer);
        toastTimer = window.setTimeout(() => {
            toast.style.opacity = '0';
        }, 2500);
    }

    function query(name) {
        return new URLSearchParams(window.location.search).get(name);
    }

    document.addEventListener('input', (event) => {
        const target = event.target;

        if (
            !(target instanceof HTMLInputElement) &&
            !(target instanceof HTMLSelectElement) &&
            !(target instanceof HTMLTextAreaElement)
        ) {
            return;
        }

        if (target.form && target.name) {
            clearFieldError(target.form, target.name);
        }

        if (target.form) {
            const formMessage = target.form.querySelector('[data-form-message]');

            if (formMessage instanceof HTMLElement) {
                clearMessage(formMessage);
            }
        }
    });

    document.addEventListener('change', (event) => {
        const target = event.target;

        if (
            !(target instanceof HTMLInputElement) &&
            !(target instanceof HTMLSelectElement) &&
            !(target instanceof HTMLTextAreaElement)
        ) {
            return;
        }

        if (target.form && target.name) {
            clearFieldError(target.form, target.name);
        }

        if (target.form) {
            const formMessage = target.form.querySelector('[data-form-message]');

            if (formMessage instanceof HTMLElement) {
                clearMessage(formMessage);
            }
        }
    });

    window.apiClient = {
        request,
        showToast,
        query,
        clearFieldError,
        clearFormErrors,
        renderFormErrors,
        clearMessage,
        renderMessage
    };
})();
