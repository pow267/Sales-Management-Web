<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thông tin các sản phẩm</title>
    <link rel="stylesheet" href="/assets/css/output.css">
    <link rel="icon" href="/assets/favicon.ico">
</head>
<body>
<div class="box">

    <div class="table-title">
        THÔNG TIN CÁC SẢN PHẨM
    </div>

    <div class="top-bar" style="display:flex; justify-content:space-between; margin-bottom:15px;">
        <div>
            <a href="/cart" class="add-btn">🛒 Giỏ hàng</a>
        </div>
        <div id="topBarRight"></div>
    </div>

    <?php if (!empty($_SESSION['flash'])): ?>
        <div id="toast" class="toast">
            <?= htmlspecialchars($_SESSION['flash']); unset($_SESSION['flash']); ?>
        </div>
        <script>
            setTimeout(() => {
                const toast = document.getElementById('toast');
                if (toast) toast.style.opacity = '0';
            }, 2500);
        </script>
    <?php endif; ?>

    <div id="pageMessage"></div>

    <div class="search-section">
        <input
            type="text"
            id="searchInput"
            placeholder="Tìm kiếm sản phẩm..."
            class="search-input"
        >
    </div>

    <div class="product-grid" id="productGrid">
        <p class="empty-message">Đang tải sản phẩm...</p>
    </div>

    <div class="pagination" id="pagination"></div>

    <div class="add-btn-box" id="adminActions"></div>

    <div id="detailSection"></div>
    <div id="formSection"></div>

</div>

<script>
window.pageConfig = {
    currentPage: <?= json_encode(max(1, (int)($_GET['page'] ?? 1))) ?>,
    detailId: <?= json_encode((int)($_GET['id'] ?? 0)) ?>,
    action: <?= json_encode(trim((string)($_GET['action'] ?? ''))) ?>,
    search: <?= json_encode(trim((string)($_GET['search'] ?? ''))) ?>
};
</script>
<script src="/assets/js/api-client.js"></script>
<script src="/assets/js/products-page.js"></script>
</body>
</html>
