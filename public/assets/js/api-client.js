(function () {
    let toastTimer = null;

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

    window.apiClient = {
        request,
        showToast,
        query
    };
})();
