(function () {
    const state = {
        session: null,
        cart: null
    };

    const refs = {
        cartContent: document.getElementById('cartContent'),
        cartMessage: document.getElementById('cartMessage')
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
        window.apiClient.clearMessage(refs.cartMessage);

        if (!cart.items.length) {
            refs.cartContent.innerHTML = '<p class="text-center text-gray-500 py-10">Giỏ hàng đang trống</p>';
            return;
        }

        const rows = cart.items.map((item) => {
            return '' +
                '<tr>' +
                    '<td><img src="' + escapeHtml(item.image_url || '/assets/images/default.png') + '" alt="' + escapeHtml(item.ten_sua) + '" class="cart-img"></td>' +
                    '<td>' + escapeHtml(item.ten_sua) + '</td>' +
                    '<td>' + item.quantity + '</td>' +
                    '<td>' + formatMoney(item.don_gia) + '</td>' +
                    '<td>' + formatMoney(item.subtotal) + '</td>' +
                    '<td>' +
                        '<div class="cart-actions">' +
                            '<form method="POST" action="/api/cart/items/' + item.id + '" class="remove-cart-form">' +
                                '<button type="submit" class="add-btn">Xóa</button>' +
                            '</form>' +
                        '</div>' +
                    '</td>' +
                '</tr>';
        }).join('');

        refs.cartContent.innerHTML = '' +
            '<table class="cart-table">' +
                '<tr><th>Hình ảnh</th><th>Sản phẩm</th><th>Số lượng</th><th>Đơn giá</th><th>Tạm tính</th><th>Thao tác</th></tr>' +
                rows +
            '</table>' +
            '<h3 class="cart-total">Tổng tiền: ' + formatMoney(cart.total) + '</h3>' +
            '<div class="mt-6 text-right"><a href="/checkout" class="add-btn">Thanh toán</a></div>';
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

        if (!(form instanceof HTMLFormElement) || !form.classList.contains('remove-cart-form')) {
            return;
        }

        event.preventDefault();

        if (!window.confirm('Xóa sản phẩm này khỏi giỏ?')) {
            return;
        }

        try {
            await window.apiClient.request(form.action, {
                method: 'DELETE',
                body: JSON.stringify({
                    csrf_token: state.session.csrf_token
                })
            });

            await loadCart();
            render();
        } catch (error) {
            window.apiClient.renderMessage(refs.cartMessage, error.message);
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
            refs.cartMessage,
            error.message || 'Không thể tải giỏ hàng.'
        );
    });
})();
