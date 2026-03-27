(function () {
    const state = {
        session: null,
        cart: null
    };

    const refs = {
        checkoutContent: document.getElementById('checkoutContent'),
        checkoutMessage: document.getElementById('checkoutMessage')
    };

    function escapeHtml(value) {
        return String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#39;');
    }

    function formatMoney(value) {
        return Number(value || 0).toLocaleString('vi-VN') + ' VND';
    }

    function render() {
        const cart = state.cart || { items: [], total: 0 };
        window.apiClient.clearMessage(refs.checkoutMessage);

        if (!cart.items.length) {
            window.location.href = '/cart';
            return;
        }

        const rows = cart.items.map((item) => {
            return '' +
                '<tr>' +
                    '<td>' + escapeHtml(item.ten_sua) + '</td>' +
                    '<td>' + item.quantity + '</td>' +
                    '<td>' + formatMoney(item.subtotal) + '</td>' +
                '</tr>';
        }).join('');

        refs.checkoutContent.innerHTML = '' +
            '<table class="cart-table">' +
                '<tr><th>Sản phẩm</th><th>Số lượng</th><th>Tạm tính</th></tr>' +
                rows +
            '</table>' +
            '<h3 class="cart-total">Tổng thanh toán: ' + formatMoney(cart.total) + '</h3>' +
            '<form method="POST" action="/api/orders" id="checkoutForm">' +
                '<div class="form-actions form-actions-row">' +
                    '<button type="submit" id="checkoutSubmitBtn">Xác nhận đặt hàng</button>' +
                    '<a href="/cart" class="add-btn">← Quay lại giỏ hàng</a>' +
                '</div>' +
            '</form>';
    }

    async function loadSession() {
        const response = await window.apiClient.request('/api/session');

        if (!response.data.authenticated) {
            window.location.href = '/login';
            return false;
        }

        state.session = response.data;
        return true;
    }

    async function loadCart() {
        const response = await window.apiClient.request('/api/cart');
        state.cart = response.data;
    }

    document.addEventListener('submit', async (event) => {
        const form = event.target;

        if (!(form instanceof HTMLFormElement) || form.id !== 'checkoutForm') {
            return;
        }

        event.preventDefault();

        const submitBtn = form.querySelector('#checkoutSubmitBtn');
        submitBtn.disabled = true;

        try {
            const response = await window.apiClient.request(form.action, {
                method: 'POST',
                body: JSON.stringify({
                    csrf_token: state.session.csrf_token
                })
            });

            window.location.href = response.data.redirect || '/';
        } catch (error) {
            window.apiClient.renderMessage(refs.checkoutMessage, error.message);
            submitBtn.disabled = false;
        }
    });

    async function init() {
        const ok = await loadSession();

        if (!ok) {
            return;
        }

        await loadCart();
        render();
    }

    init().catch((error) => {
        window.apiClient.renderMessage(
            refs.checkoutMessage,
            error.message || 'Không thể tải trang thanh toán.'
        );
    });
})();
