(function () {
    const state = {
        session: null,
        products: [],
        pagination: null,
        detail: null,
        brands: []
    };

    const refs = {
        topBarRight: document.getElementById('topBarRight'),
        searchInput: document.getElementById('searchInput'),
        productGrid: document.getElementById('productGrid'),
        pagination: document.getElementById('pagination'),
        adminActions: document.getElementById('adminActions'),
        detailSection: document.getElementById('detailSection'),
        formSection: document.getElementById('formSection')
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

    function buildUrl(overrides) {
        const url = new URL(window.location.href);

        Object.entries(overrides || {}).forEach(([key, value]) => {
            if (value === null || value === undefined || value === '') {
                url.searchParams.delete(key);
            } else {
                url.searchParams.set(key, String(value));
            }
        });

        return url.pathname + (url.searchParams.toString() ? '?' + url.searchParams.toString() : '');
    }

    function imageUrl(product) {
        return escapeHtml(product.image_url || '/assets/images/default.png');
    }

    function productDetailUrl(productId) {
        return buildUrl({
            id: productId,
            page: state.pagination?.page || window.pageConfig.currentPage || 1,
            action: null
        }) + '#chitiet';
    }

    function addFormHtml() {
        const csrfToken = escapeHtml(state.session?.csrf_token || '');
        const pageValue = escapeHtml(String(state.pagination?.page || 1));
        const options = state.brands.map((brand) => {
            return '<option value="' + escapeHtml(brand.ma_hs) + '">' + escapeHtml(brand.ten_hs) + '</option>';
        }).join('');

        return '' +
            '<div class="add-form">' +
                '<div class="form-title">THÊM SỮA MỚI</div>' +
                '<form method="POST" action="/api/products" enctype="multipart/form-data" id="addForm">' +
                    '<input type="hidden" name="csrf_token" value="' + csrfToken + '">' +
                    '<input type="hidden" name="page" value="' + pageValue + '">' +
                    '<div class="form-row"><label>Tên sữa</label><input type="text" name="ten_sua" required></div>' +
                    '<div class="form-row"><label>Hãng sữa</label><select name="ma_hang_sua" required>' + options + '</select></div>' +
                    '<div class="form-row"><label>Loại sữa</label><input type="text" name="loai_sua"></div>' +
                    '<div class="form-row"><label>Trọng lượng</label><input type="number" name="trong_luong" required></div>' +
                    '<div class="form-row"><label>Đơn giá</label><input type="number" name="don_gia" required></div>' +
                    '<div class="form-row"><label for="dd">Thành phần dinh dưỡng</label><textarea id="dd" name="tpdd"></textarea></div>' +
                    '<div class="form-row"><label for="li">Lợi ích</label><textarea id="li" name="loi_ich"></textarea></div>' +
                    '<div class="form-row"><label>Hình ảnh</label><input type="file" name="hinh"></div>' +
                    '<div class="form-actions"><button type="submit" id="addSubmitBtn">Thêm mới</button></div>' +
                '</form>' +
            '</div>';
    }

    function editFormHtml() {
        if (!state.detail) {
            return '';
        }

        const csrfToken = escapeHtml(state.session?.csrf_token || '');
        const pageValue = escapeHtml(String(state.pagination?.page || 1));
        const options = state.brands.map((brand) => {
            const selected = String(brand.ma_hs) === String(state.detail.ma_hang_sua) ? ' selected' : '';
            return '<option value="' + escapeHtml(brand.ma_hs) + '"' + selected + '>' + escapeHtml(brand.ten_hs) + '</option>';
        }).join('');

        return '' +
            '<div class="add-form" id="formsua">' +
                '<div class="form-title">SỬA THÔNG TIN SẢN PHẨM</div>' +
                '<form method="POST" action="/api/products/' + state.detail.id + '" enctype="multipart/form-data" id="editForm">' +
                    '<input type="hidden" name="csrf_token" value="' + csrfToken + '">' +
                    '<input type="hidden" name="page" value="' + pageValue + '">' +
                    '<input type="hidden" name="id" value="' + state.detail.id + '">' +
                    '<input type="hidden" name="hinh_cu" value="' + escapeHtml(state.detail.hinh || '') + '">' +
                    '<div class="form-row"><label>Tên sữa</label><input type="text" name="ten_sua" value="' + escapeHtml(state.detail.ten_sua) + '" required></div>' +
                    '<div class="form-row"><label>Hãng sữa</label><select name="ma_hang_sua" required>' + options + '</select></div>' +
                    '<div class="form-row"><label>Loại sữa</label><input type="text" name="loai_sua" value="' + escapeHtml(state.detail.loai_sua) + '"></div>' +
                    '<div class="form-row"><label>Trọng lượng</label><input type="number" name="trong_luong" value="' + escapeHtml(state.detail.trong_luong) + '" required></div>' +
                    '<div class="form-row"><label>Đơn giá</label><input type="number" name="don_gia" value="' + escapeHtml(state.detail.don_gia) + '" required></div>' +
                    '<div class="form-row"><label>Thành phần dinh dưỡng</label><textarea name="tpdd">' + escapeHtml(state.detail.tpdd) + '</textarea></div>' +
                    '<div class="form-row"><label>Lợi ích</label><textarea name="loi_ich">' + escapeHtml(state.detail.loi_ich) + '</textarea></div>' +
                    '<div class="form-row"><label>Hình ảnh mới</label><input type="file" name="hinh"></div>' +
                    '<div class="form-actions"><button type="submit" id="editSubmitBtn">Cập nhật</button></div>' +
                '</form>' +
            '</div>';
    }

    function renderTopBar() {
        const user = state.session?.user;

        if (!user) {
            refs.topBarRight.innerHTML = '';
            return;
        }

        refs.topBarRight.innerHTML = '' +
            '<span style="margin-right:10px;">Xin chào, ' + escapeHtml(user.username) + '</span>' +
            '<form method="POST" action="/api/auth/logout" id="logoutForm" class="inline-block">' +
                '<button type="submit" class="add-btn" id="logoutBtn">Đăng xuất</button>' +
            '</form>';
    }

    function renderProducts() {
        if (!state.products.length) {
            const emptyText = (state.pagination?.search || '').trim() !== ''
                ? 'Không có sản phẩm nào như vậy.'
                : 'Không có sản phẩm nào.';

            refs.productGrid.innerHTML = '<p class="empty-message">' + emptyText + '</p>';
            return;
        }

        refs.productGrid.innerHTML = state.products.map((product) => {
            return '' +
                '<div class="product-card">' +
                    '<div class="product-name">' +
                        '<a href="' + productDetailUrl(product.id) + '">' + escapeHtml(product.ten_sua) + '</a>' +
                    '</div>' +
                    '<div class="product-price">' +
                        escapeHtml(product.trong_luong) + ' gr - ' + formatMoney(product.don_gia) +
                    '</div>' +
                    '<div class="img-box"><img src="' + imageUrl(product) + '" alt="' + escapeHtml(product.ten_sua) + '"></div>' +
                '</div>';
        }).join('');
    }

    function renderPagination() {
        const pagination = state.pagination;

        if (!pagination || pagination.total_pages <= 1) {
            refs.pagination.innerHTML = '';
            return;
        }

        const links = [];

        if (pagination.page > 1) {
            links.push('<a href="' + buildUrl({ page: pagination.page - 1, search: pagination.search || null }) + '" class="page-btn">« Trước</a>');
        }

        for (let i = 1; i <= pagination.total_pages; i += 1) {
            links.push(
                '<a href="' + buildUrl({ page: i, search: pagination.search || null }) + '" class="page-btn ' + (i === pagination.page ? 'active' : '') + '">' + i + '</a>'
            );
        }

        if (pagination.page < pagination.total_pages) {
            links.push('<a href="' + buildUrl({ page: pagination.page + 1, search: pagination.search || null }) + '" class="page-btn">Sau »</a>');
        }

        refs.pagination.innerHTML = links.join('');
    }

    function renderAdminActions() {
        const user = state.session?.user;
        const isAdmin = user && user.role === 'admin';
        const csrfToken = escapeHtml(state.session?.csrf_token || '');
        const pageValue = escapeHtml(String(state.pagination?.page || 1));

        if (!isAdmin) {
            refs.adminActions.innerHTML = '';
            return;
        }

        let html = '<a href="' + buildUrl({ action: 'them', id: null }) + '" class="add-btn">THÊM SỮA MỚI</a>';

        if (state.detail) {
            html += '<a href="' + buildUrl({ action: 'sua', id: state.detail.id }) + '#formsua" class="add-btn">SỬA THÔNG TIN</a>';
            html += '' +
                '<form method="POST" action="/api/products/' + state.detail.id + '" class="inline-block" id="deleteProductForm">' +
                    '<input type="hidden" name="_method" value="DELETE">' +
                    '<input type="hidden" name="csrf_token" value="' + csrfToken + '">' +
                    '<input type="hidden" name="page" value="' + pageValue + '">' +
                    '<button type="submit" class="add-btn">XÓA SẢN PHẨM</button>' +
                '</form>';
        }

        refs.adminActions.innerHTML = html;
    }

    function renderDetail() {
        if (!state.detail) {
            refs.detailSection.innerHTML = '';
            return;
        }

        const isAuthenticated = Boolean(state.session?.authenticated);

        let cartHtml = '';
        if (isAuthenticated) {
            cartHtml = '' +
                '<form method="POST" action="/api/cart/items" id="addToCartForm" style="margin-top:15px;">' +
                    '<input type="hidden" name="product_id" value="' + state.detail.id + '">' +
                    '<div style="display:flex; align-items:center; gap:8px; margin-bottom:10px;">' +
                        '<button type="button" data-qty-action="decrease" style="width:35px; height:35px;">-</button>' +
                        '<input type="number" id="quantityInput" name="quantity" value="1" min="1" style="width:55px; height:35px; text-align:center;">' +
                        '<button type="button" data-qty-action="increase" style="width:35px; height:35px;">+</button>' +
                    '</div>' +
                    '<button type="submit" id="addToCartBtn" class="add-btn">THÊM VÀO GIỎ HÀNG</button>' +
                '</form>';
        }

        refs.detailSection.innerHTML = '' +
            '<div class="detail-box" id="chitiet">' +
                '<div class="form-title">CHI TIẾT SẢN PHẨM</div>' +
                '<div class="detail-content">' +
                    '<div class="detail-img"><img src="' + imageUrl(state.detail) + '" alt="' + escapeHtml(state.detail.ten_sua) + '"></div>' +
                    '<div class="detail-info">' +
                        '<p><strong>Tên sữa:</strong> ' + escapeHtml(state.detail.ten_sua) + '</p>' +
                        '<p><strong>Hãng sữa:</strong> ' + escapeHtml(state.detail.ten_hs || state.detail.ma_hang_sua) + '</p>' +
                        '<p><strong>Loại sữa:</strong> ' + escapeHtml(state.detail.loai_sua) + '</p>' +
                        '<p><strong>Thành phần dinh dưỡng:</strong><br>' + escapeHtml(state.detail.tpdd).replaceAll('\n', '<br>') + '</p>' +
                        '<p><strong>Lợi ích:</strong><br>' + escapeHtml(state.detail.loi_ich).replaceAll('\n', '<br>') + '</p>' +
                        '<p><strong>Trọng lượng:</strong> ' + escapeHtml(state.detail.trong_luong) + ' gr</p>' +
                        '<p><strong>Đơn giá:</strong> ' + formatMoney(state.detail.don_gia) + '</p>' +
                        cartHtml +
                    '</div>' +
                '</div>' +
            '</div>';
    }

    function renderFormSection() {
        const isAdmin = state.session?.user?.role === 'admin';

        if (!isAdmin) {
            refs.formSection.innerHTML = '';
            return;
        }

        if (window.pageConfig.action === 'them') {
            refs.formSection.innerHTML = addFormHtml();
            return;
        }

        if (window.pageConfig.action === 'sua' && state.detail) {
            refs.formSection.innerHTML = editFormHtml();
            return;
        }

        refs.formSection.innerHTML = '';
    }

    function syncSearchInput() {
        refs.searchInput.value = state.pagination?.search || window.pageConfig.search || '';
    }

    function updateQuantity(delta) {
        const input = document.getElementById('quantityInput');

        if (!input) {
            return;
        }

        const current = Math.max(1, parseInt(input.value || '1', 10));
        input.value = Math.max(1, current + delta);
    }

    async function loadSession() {
        const response = await window.apiClient.request('/api/auth/session');

        if (!response.data.authenticated) {
            window.location.href = '/login';
            return false;
        }

        state.session = response.data;
        return true;
    }

    async function loadProducts() {
        const search = window.pageConfig.search || '';
        const response = await window.apiClient.request(
            '/api/products?page=' + encodeURIComponent(window.pageConfig.currentPage) +
            '&search=' + encodeURIComponent(search)
        );

        state.products = response.data.items;
        state.pagination = response.data.pagination;
        window.pageConfig.currentPage = state.pagination.page;
    }

    async function loadDetail() {
        if (!window.pageConfig.detailId) {
            state.detail = null;
            return;
        }

        try {
            const response = await window.apiClient.request('/api/products/' + window.pageConfig.detailId);
            state.detail = response.data.product;
        } catch (error) {
            state.detail = null;
            window.apiClient.showToast(error.message, 'error');
        }
    }

    async function loadBrands() {
        if (state.session?.user?.role !== 'admin') {
            state.brands = [];
            return;
        }

        if (!['them', 'sua'].includes(window.pageConfig.action)) {
            state.brands = [];
            return;
        }

        const response = await window.apiClient.request('/api/brands');
        state.brands = response.data.brands || [];
    }

    function render() {
        renderTopBar();
        renderProducts();
        renderPagination();
        renderAdminActions();
        renderDetail();
        renderFormSection();
        syncSearchInput();
    }

    async function refresh() {
        await loadProducts();
        await Promise.all([loadDetail(), loadBrands()]);
        render();
    }

    function debounce(fn, delay) {
        let timer = null;

        return function debounced() {
            const args = arguments;
            clearTimeout(timer);
            timer = window.setTimeout(() => fn.apply(null, args), delay);
        };
    }

    refs.searchInput?.addEventListener('input', debounce(async (event) => {
        window.pageConfig.search = event.target.value.trim();
        window.pageConfig.currentPage = 1;
        window.pageConfig.detailId = 0;
        window.pageConfig.action = '';
        state.detail = null;

        history.replaceState({}, '', buildUrl({
            page: 1,
            search: window.pageConfig.search || null,
            id: null,
            action: null
        }));

        await loadProducts();
        render();
    }, 300));

    document.addEventListener('click', (event) => {
        const action = event.target.getAttribute('data-qty-action');

        if (action === 'increase') {
            updateQuantity(1);
        }

        if (action === 'decrease') {
            updateQuantity(-1);
        }
    });

    document.addEventListener('submit', async (event) => {
        const form = event.target;

        if (!(form instanceof HTMLFormElement)) {
            return;
        }

        if (form.id === 'logoutForm') {
            event.preventDefault();

            try {
                const response = await window.apiClient.request(form.action, {
                    method: 'POST',
                    body: JSON.stringify({
                        csrf_token: state.session.csrf_token
                    })
                });

                window.location.href = response.data.redirect || '/login';
            } catch (error) {
                window.apiClient.showToast(error.message, 'error');
            }

            return;
        }

        if (form.id === 'addToCartForm') {
            event.preventDefault();

            const quantity = Math.max(1, parseInt(form.querySelector('input[name="quantity"]').value || '1', 10));

            try {
                const response = await window.apiClient.request(form.action, {
                    method: 'POST',
                    body: JSON.stringify({
                        product_id: state.detail.id,
                        quantity,
                        csrf_token: state.session.csrf_token
                    })
                });

                window.location.href = response.data.redirect || '/cart';
            } catch (error) {
                window.apiClient.showToast(error.message, 'error');
            }

            return;
        }

        if (form.id === 'deleteProductForm') {
            event.preventDefault();

            if (!window.confirm('Bạn có chắc muốn xóa sản phẩm này?')) {
                return;
            }

            try {
                const response = await window.apiClient.request(form.action, {
                    method: 'DELETE',
                    body: JSON.stringify({
                        csrf_token: state.session.csrf_token,
                        page: state.pagination?.page || 1
                    })
                });

                window.location.href = response.data.redirect || '/';
            } catch (error) {
                window.apiClient.showToast(error.message, 'error');
            }

            return;
        }

        if (form.id === 'addForm' || form.id === 'editForm') {
            event.preventDefault();

            const submitBtn = form.querySelector('button[type="submit"]');
            const formData = new FormData(form);
            formData.set('csrf_token', state.session.csrf_token);
            formData.set('page', String(state.pagination?.page || 1));

            submitBtn.disabled = true;

            try {
                const response = await window.apiClient.request(form.action, {
                    method: 'POST',
                    body: formData
                });

                window.location.href = response.data.redirect || '/';
            } catch (error) {
                window.apiClient.showToast(error.message, 'error');
                submitBtn.disabled = false;
            }
        }
    });

    async function init() {
        const ok = await loadSession();

        if (!ok) {
            return;
        }

        await refresh();
    }

    init().catch((error) => {
        window.apiClient.showToast(error.message || 'Không thể tải dữ liệu trang.', 'error');
    });
})();
