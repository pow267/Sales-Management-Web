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
        formSection: document.getElementById('formSection'),
        pageMessage: document.getElementById('pageMessage')
    };

    function clearPageMessage() {
        window.apiClient.clearMessage(refs.pageMessage);
    }

    function renderPageMessage(message) {
        window.apiClient.renderMessage(refs.pageMessage, message);
    }

    function clearFormMessage(form) {
        if (!(form instanceof HTMLFormElement)) {
            return;
        }

        const container = form.querySelector('[data-form-message]');

        if (container instanceof HTMLElement) {
            window.apiClient.clearMessage(container);
        }
    }

    function renderFormMessage(form, message) {
        if (!(form instanceof HTMLFormElement)) {
            return;
        }

        const container = form.querySelector('[data-form-message]');

        if (container instanceof HTMLElement) {
            window.apiClient.renderMessage(container, message);
        }
    }

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

    function formRowHtml(label, fieldName, controlHtml, labelFor) {
        const forAttribute = labelFor ? ' for="' + escapeHtml(labelFor) + '"' : '';

        return '' +
            '<div class="form-row">' +
                '<label' + forAttribute + '>' + label + '</label>' +
                '<div class="form-field">' +
                    '<div class="field-error" data-field-error="' + escapeHtml(fieldName) + '" hidden></div>' +
                    controlHtml +
                '</div>' +
            '</div>';
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
                    '<div data-form-message></div>' +
                    '<input type="hidden" name="csrf_token" value="' + csrfToken + '">' +
                    '<input type="hidden" name="page" value="' + pageValue + '">' +
                    formRowHtml(
                        'Tên sữa',
                        'ten_sua',
                        '<input id="add-ten-sua" type="text" name="ten_sua" required>',
                        'add-ten-sua'
                    ) +
                    formRowHtml(
                        'Hãng sữa',
                        'ma_hang_sua',
                        '<select id="add-ma-hang-sua" name="ma_hang_sua" required>' + options + '</select>',
                        'add-ma-hang-sua'
                    ) +
                    formRowHtml(
                        'Loại sữa',
                        'loai_sua',
                        '<input id="add-loai-sua" type="text" name="loai_sua">',
                        'add-loai-sua'
                    ) +
                    formRowHtml(
                        'Trọng lượng',
                        'trong_luong',
                        '<input id="add-trong-luong" type="number" name="trong_luong" required>',
                        'add-trong-luong'
                    ) +
                    formRowHtml(
                        'Đơn giá',
                        'don_gia',
                        '<input id="add-don-gia" type="number" name="don_gia" required>',
                        'add-don-gia'
                    ) +
                    formRowHtml(
                        'Thành phần dinh dưỡng',
                        'tpdd',
                        '<textarea id="add-tpdd" name="tpdd"></textarea>',
                        'add-tpdd'
                    ) +
                    formRowHtml(
                        'Lợi ích',
                        'loi_ich',
                        '<textarea id="add-loi-ich" name="loi_ich"></textarea>',
                        'add-loi-ich'
                    ) +
                    formRowHtml(
                        'Hình ảnh',
                        'hinh',
                        '<input id="add-hinh" type="file" name="hinh" accept=".png,.jpg,.jpeg,image/png,image/jpeg">',
                        'add-hinh'
                    ) +
                    '<div class="form-actions"><button type="button" id="addSubmitBtn">Thêm mới</button></div>' +
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
                    '<div data-form-message></div>' +
                    '<input type="hidden" name="_method" value="PATCH">' +
                    '<input type="hidden" name="csrf_token" value="' + csrfToken + '">' +
                    '<input type="hidden" name="page" value="' + pageValue + '">' +
                    '<input type="hidden" name="id" value="' + state.detail.id + '">' +
                    '<input type="hidden" name="hinh_cu" value="' + escapeHtml(state.detail.hinh || '') + '">' +
                    formRowHtml(
                        'Tên sữa',
                        'ten_sua',
                        '<input id="edit-ten-sua" type="text" name="ten_sua" value="' + escapeHtml(state.detail.ten_sua) + '" required>',
                        'edit-ten-sua'
                    ) +
                    formRowHtml(
                        'Hãng sữa',
                        'ma_hang_sua',
                        '<select id="edit-ma-hang-sua" name="ma_hang_sua" required>' + options + '</select>',
                        'edit-ma-hang-sua'
                    ) +
                    formRowHtml(
                        'Loại sữa',
                        'loai_sua',
                        '<input id="edit-loai-sua" type="text" name="loai_sua" value="' + escapeHtml(state.detail.loai_sua) + '">',
                        'edit-loai-sua'
                    ) +
                    formRowHtml(
                        'Trọng lượng',
                        'trong_luong',
                        '<input id="edit-trong-luong" type="number" name="trong_luong" value="' + escapeHtml(state.detail.trong_luong) + '" required>',
                        'edit-trong-luong'
                    ) +
                    formRowHtml(
                        'Đơn giá',
                        'don_gia',
                        '<input id="edit-don-gia" type="number" name="don_gia" value="' + escapeHtml(state.detail.don_gia) + '" required>',
                        'edit-don-gia'
                    ) +
                    formRowHtml(
                        'Thành phần dinh dưỡng',
                        'tpdd',
                        '<textarea id="edit-tpdd" name="tpdd">' + escapeHtml(state.detail.tpdd) + '</textarea>',
                        'edit-tpdd'
                    ) +
                    formRowHtml(
                        'Lợi ích',
                        'loi_ich',
                        '<textarea id="edit-loi-ich" name="loi_ich">' + escapeHtml(state.detail.loi_ich) + '</textarea>',
                        'edit-loi-ich'
                    ) +
                    formRowHtml(
                        'Hình ảnh mới',
                        'hinh',
                        '<input id="edit-hinh" type="file" name="hinh" accept=".png,.jpg,.jpeg,image/png,image/jpeg">',
                        'edit-hinh'
                    ) +
                    '<div class="form-actions"><button type="button" id="editSubmitBtn">Cập nhật</button></div>' +
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
            '<form method="POST" action="/api/session" id="logoutForm" class="inline-block">' +
                '<input type="hidden" name="_method" value="DELETE">' +
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
                    '<button type="button" class="add-btn">XÓA SẢN PHẨM</button>' +
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
                    '<div data-form-message></div>' +
                    '<input type="hidden" name="product_id" value="' + state.detail.id + '">' +
                    '<div style="max-width:220px;">' +
                        '<label for="quantityInput" style="display:block; margin-bottom:8px; font-weight:600;">Số lượng</label>' +
                        '<div class="field-error" data-field-error="quantity" hidden></div>' +
                        '<div style="display:flex; align-items:center; gap:8px; margin-bottom:10px;">' +
                            '<button type="button" data-qty-action="decrease" style="width:35px; height:35px;">-</button>' +
                            '<input type="number" id="quantityInput" name="quantity" value="1" min="0" style="width:55px; height:35px; text-align:center;">' +
                            '<button type="button" data-qty-action="increase" style="width:35px; height:35px;">+</button>' +
                        '</div>' +
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
                        '<p><strong>Đơn giá:</strong> ' + escapeHtml(state.detail.trong_luong) + ' gr</p>' +
                        '<p><strong>Trọng lượng:</strong> ' + formatMoney(state.detail.don_gia) + '</p>' +
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

        const current = Math.max(0, parseInt(input.value || '1', 10));
        input.value = Math.max(0, current + delta);

        if (input.form) {
            window.apiClient.clearFieldError(input.form, 'quantity');
        }
    }

    function validateProductForm(form) {
        const errors = {};
        const tenSua = String(form.elements['ten_sua']?.value || '').trim();
        const maHangSua = String(form.elements['ma_hang_sua']?.value || '').trim();
        const trongLuongValue = String(form.elements['trong_luong']?.value || '').trim();
        const donGiaValue = String(form.elements['don_gia']?.value || '').trim();
        const trongLuong = Number(trongLuongValue);
        const donGia = Number(donGiaValue);
        const imageInput = form.elements['hinh'];

        if (!tenSua) {
            errors.ten_sua = 'Tên sữa không được để trống.';
        }

        if (!maHangSua) {
            errors.ma_hang_sua = 'Hãng sữa không được để trống.';
        }

        if (!trongLuongValue || !Number.isFinite(trongLuong) || trongLuong <= 0) {
            errors.trong_luong = 'Trọng lượng phải lớn hơn 0.';
        }

        if (!donGiaValue || !Number.isFinite(donGia) || donGia <= 0) {
            errors.don_gia = 'Đơn giá phải lớn hơn 0.';
        }

        if (
            imageInput instanceof HTMLInputElement &&
            imageInput.files &&
            imageInput.files.length > 0
        ) {
            const file = imageInput.files[0];
            const acceptedTypes = ['image/png', 'image/jpeg'];

            if (file.type && !acceptedTypes.includes(file.type)) {
                errors.hinh = 'Chỉ chấp nhận ảnh PNG hoặc JPEG.';
            }
        }

        return errors;
    }

    function validateAddToCartForm(form) {
        const rawQuantity = String(form.elements['quantity']?.value || '').trim();
        const quantity = Number(rawQuantity);

        if (!rawQuantity || !Number.isInteger(quantity) || quantity < 0) {
            return {
                quantity: 'Số lượng phải lớn hơn hoặc bằng 0.'
            };
        }

        return {};
    }

    async function submitProductForm(form) {
        const submitBtn = form.querySelector('button');

        if (!(submitBtn instanceof HTMLButtonElement)) {
            return;
        }

        clearFormMessage(form);

        if (window.apiClient.renderFormErrors(form, validateProductForm(form))) {
            return;
        }

        const formData = new FormData(form);
        const methodOverride = String(formData.get('_method') || '').toUpperCase();
        const method = ['PUT', 'PATCH'].includes(methodOverride) ? methodOverride : 'POST';
        formData.set('csrf_token', state.session.csrf_token);
        formData.set('page', String(state.pagination?.page || 1));

        if (method !== 'POST') {
            formData.delete('_method');
        }

        submitBtn.disabled = true;

        try {
            const response = await window.apiClient.request(form.action, {
                method,
                body: formData
            });

            window.location.href = response.data.redirect || '/';
        } catch (error) {
            const hasFieldErrors = window.apiClient.renderFormErrors(
                form,
                error.payload?.errors
            );

            if (!hasFieldErrors) {
                renderFormMessage(form, error.message);
            }

            submitBtn.disabled = false;
        }
    }

    async function deleteProduct(form) {
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
            renderPageMessage(error.message);
        }
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
            renderPageMessage(error.message);
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
        clearPageMessage();
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
        const target = event.target;

        if (!(target instanceof HTMLElement)) {
            return;
        }

        const action = target.getAttribute('data-qty-action');

        if (action === 'increase') {
            updateQuantity(1);
            return;
        }

        if (action === 'decrease') {
            updateQuantity(-1);
            return;
        }

        const productSubmitBtn = target.closest('#addSubmitBtn, #editSubmitBtn');

        if (productSubmitBtn instanceof HTMLButtonElement) {
            const form = productSubmitBtn.form;

            if (form instanceof HTMLFormElement) {
                submitProductForm(form);
            }

            return;
        }

        const deleteBtn = target.closest('#deleteProductForm button');

        if (deleteBtn instanceof HTMLButtonElement) {
            const form = deleteBtn.form;

            if (form instanceof HTMLFormElement) {
                deleteProduct(form);
            }
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
                    method: 'DELETE',
                    body: JSON.stringify({
                        csrf_token: state.session.csrf_token
                    })
                });

                window.location.href = response.data.redirect || '/login';
            } catch (error) {
                renderPageMessage(error.message);
            }

            return;
        }

        if (form.id === 'addToCartForm') {
            event.preventDefault();

            if (window.apiClient.renderFormErrors(form, validateAddToCartForm(form))) {
                return;
            }

            const quantity = Math.max(
                1,
                parseInt(form.querySelector('input[name="quantity"]').value || '1', 10)
            );

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
                const hasFieldErrors = window.apiClient.renderFormErrors(
                    form,
                    error.payload?.errors
                );

                if (!hasFieldErrors) {
                    renderFormMessage(form, error.message);
                }
            }

            return;
        }

        if (form.id === 'deleteProductForm') {
            event.preventDefault();
            await deleteProduct(form);

            return;
        }

        if (form.id === 'addForm' || form.id === 'editForm') {
            event.preventDefault();
            await submitProductForm(form);
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
        renderPageMessage(error.message || 'Không thể tải dữ liệu trang.');
    });
})();
