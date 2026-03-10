<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thông tin các sản phẩm</title>
    <link rel="stylesheet" href="/assets/css/output.css">
</head>

<?php
$role = $_SESSION['user']['role'] ?? 'guest';
?>

<body>
<div class="box">

    <div class="table-title">
        THÔNG TIN CÁC SẢN PHẨM
    </div>

    <!-- ================= TOP BAR ================= -->
    <div class="top-bar" style="display:flex; justify-content:space-between; margin-bottom:15px;">

        <div>
            <a href="/cart" class="add-btn">
                🛒 Giỏ hàng
            </a>
        </div>

        <div>
            <?php if ($role !== 'guest'): ?>
                <span style="margin-right:10px;">
                    Xin chào, <?= htmlspecialchars($_SESSION['user']['username']) ?>
                </span>
                <a href="/logout" class="add-btn">
                    Đăng xuất
                </a>
            <?php endif; ?>
        </div>

    </div>

    <!-- ================= TOAST ================= -->
    <?php if (!empty($_SESSION['flash'])): ?>
        <div id="toast" class="toast">
            <?= $_SESSION['flash']; unset($_SESSION['flash']); ?>
        </div>
        <script>
            setTimeout(() => {
                const t = document.getElementById('toast');
                if (t) t.style.opacity = '0';
            }, 2500);
        </script>
    <?php endif; ?>

    <!-- ================= SEARCH ================= -->
    <div class="search-section">
        <input type="text"
               id="searchInput"
               placeholder="Tìm kiếm sản phẩm..."
               class="search-input">
    </div>

    <!-- ================= DANH SÁCH ================= -->
    <div class="product-grid">

    <?php if (empty($products)): ?>

        <p class="empty-message">Không có sản phẩm nào.</p>

    <?php else: ?>

        <?php foreach ($products as $row): ?>
        <div class="product-card">

            <div class="product-name">
                <a href="?id=<?= htmlspecialchars($row['id']) ?>&page=<?= $page ?>#chitiet">
                    <?= htmlspecialchars($row['ten_sua']) ?>
                </a>
            </div>

            <div class="product-price">
                <?= htmlspecialchars($row['trong_luong']) ?> gr -
                <?= number_format($row['don_gia'], 0, ',', '.') ?> VND
            </div>

            <?php if (!empty($row['hinh'])): ?>
            <div class="img-box">
                <img loading="lazy"
                     src="/assets/images/<?= htmlspecialchars($row['hinh']) ?>"
                     alt="Hình sản phẩm">
            </div>
            <?php endif; ?>

        </div>
        <?php endforeach; ?>

    <?php endif; ?>

    </div>

    <!-- ================= PAGINATION ================= -->
    <?php if (!empty($totalPages) && $totalPages > 1): ?>
    <div class="pagination">

        <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1 ?>" class="page-btn">« Trước</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?= $i ?>"
               class="page-btn <?= $i == $page ? 'active' : '' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
            <a href="?page=<?= $page + 1 ?>" class="page-btn">Sau »</a>
        <?php endif; ?>

    </div>
    <?php endif; ?>

    <!-- ================= NÚT ADMIN ================= -->
    <div class="add-btn-box">

        <?php if ($role === 'admin'): ?>
        <a href="?action=them&page=<?= $page ?>" class="add-btn">
            THÊM SỮA MỚI
        </a>
        <?php endif; ?>

        <?php if (isset($_GET['id']) && $role === 'admin'): ?>

            <a href="?action=sua&id=<?= htmlspecialchars($_GET['id']) ?>&page=<?= $page ?>#formsua"
               class="add-btn">
                SỬA THÔNG TIN
            </a>

            <form method="POST" class="inline-block">
                <input type="hidden" name="id"
                       value="<?= htmlspecialchars($_GET['id']) ?>">

                <input type="hidden" name="csrf_token"
                       value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                <button type="submit"
                        name="btn_xoa"
                        class="add-btn"
                        onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này?')">
                    XÓA SẢN PHẨM
                </button>
            </form>

        <?php endif; ?>

    </div>

    <!-- ================= CHI TIẾT ================= -->
    <?php if (!empty($chitiet)): ?>

    <div class="detail-box" id="chitiet">

        <div class="form-title">CHI TIẾT SẢN PHẨM</div>

        <div class="detail-content">

            <?php if (!empty($chitiet['hinh'])): ?>
            <div class="detail-img">
                <img loading="lazy"
                     src="/assets/images/<?= htmlspecialchars($chitiet['hinh']) ?>">
            </div>
            <?php endif; ?>

            <div class="detail-info">

                <p><strong>Tên sữa:</strong>
                    <?= htmlspecialchars($chitiet['ten_sua']) ?>
                </p>

                <p><strong>Hãng sữa:</strong>
                    <?= htmlspecialchars($chitiet['ten_hs'] ?? $chitiet['ma_hang_sua']) ?>
                </p>

                <p><strong>Loại sữa:</strong>
                    <?= htmlspecialchars($chitiet['loai_sua']) ?>
                </p>

                <p><strong>Thành phần dinh dưỡng:</strong><br>
                    <?= nl2br(htmlspecialchars($chitiet['tpdd'])) ?>
                </p>

                <p><strong>Lợi ích:</strong><br>
                    <?= nl2br(htmlspecialchars($chitiet['loi_ich'])) ?>
                </p>

                <p><strong>Trọng lượng:</strong>
                    <?= htmlspecialchars($chitiet['trong_luong']) ?> gr
                </p>

                <p><strong>Đơn giá:</strong>
                    <?= number_format($chitiet['don_gia'], 0, ',', '.') ?> VND
                </p>

        <?php if ($role !== 'guest'): ?>
            <div style="margin-top:15px;">

                <!-- Quantity -->
                <div style="display:flex; align-items:center; gap:8px; margin-bottom:10px;">

                    <button type="button"
                            onclick="decreaseQty()"
                            style="width:35px; height:35px;">
                        −
                    </button>

                    <input type="number"
                        id="quantityInput"
                        value="1"
                        min="1"
                        style="width:55px; height:35px; text-align:center;">

                    <button type="button"
                            onclick="increaseQty()"
                            style="width:35px; height:35px;">
                        +
                    </button>

                </div>

                <!-- Add button -->
                <a id="addToCartBtn"
                href="/cart/add?id=<?= $chitiet['id'] ?>&qty=1"
                class="add-btn">
                    THÊM VÀO GIỎ HÀNG
                </a>

            </div>
        <?php endif; ?>

            </div>

        </div>

    </div>

    <?php endif; ?>

    <!-- ================= FORM THÊM ================= -->
    <?php if (isset($_GET['action']) && $_GET['action'] === 'them' && $role === 'admin'): ?>

    <div class="add-form">

        <div class="form-title">THÊM SỮA MỚI</div>

        <form method="POST" enctype="multipart/form-data">

            <input type="hidden" name="csrf_token"
                   value="<?= $_SESSION['csrf_token'] ?? '' ?>">

            <div class="form-row">
                <label>Tên sữa</label>
                <input type="text" name="ten_sua" required>
            </div>

            <div class="form-row">
                <label>Hãng sữa</label>
                <select name="ma_hang_sua" required>
                    <?php foreach ($hangSua as $hang): ?>
                        <option value="<?= htmlspecialchars($hang['ma_hs']) ?>">
                            <?= htmlspecialchars($hang['ten_hs']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-row">
                <label>Loại sữa</label>
                <input type="text" name="loai_sua">
            </div>

            <div class="form-row">
                <label>Trọng lượng</label>
                <input type="number" name="trong_luong" required>
            </div>

            <div class="form-row">
                <label>Đơn giá</label>
                <input type="number" name="don_gia" required>
            </div>

            <div class="form-row">
                <label>Thành phần dinh dưỡng</label>
                <textarea name="tpdd"></textarea>
            </div>

            <div class="form-row">
                <label>Lợi ích</label>
                <textarea name="loi_ich"></textarea>
            </div>

            <div class="form-row">
                <label>Hình ảnh</label>
                <input type="file" name="hinh">
            </div>

            <div class="form-actions">
                <button type="submit" name="btn_them">
                    Thêm mới
                </button>
            </div>

        </form>

    </div>

    <?php endif; ?>

    <!-- ================= FORM SỬA ================= -->
    <?php if (isset($_GET['action']) && $_GET['action'] === 'sua' && !empty($chitiet) && $role === 'admin'): ?>

    <div class="add-form" id="formsua">

        <div class="form-title">SỬA THÔNG TIN SẢN PHẨM</div>

        <form method="POST" enctype="multipart/form-data">

            <input type="hidden" name="csrf_token"
                   value="<?= $_SESSION['csrf_token'] ?? '' ?>">

            <input type="hidden" name="id"
                   value="<?= htmlspecialchars($chitiet['id']) ?>">

            <input type="hidden" name="hinh_cu"
                   value="<?= htmlspecialchars($chitiet['hinh'] ?? '') ?>">

            <div class="form-row">
                <label>Tên sữa</label>
                <input type="text" name="ten_sua"
                       value="<?= htmlspecialchars($chitiet['ten_sua']) ?>" required>
            </div>

            <div class="form-row">
                <label>Hãng sữa</label>
                <select name="ma_hang_sua" required>
                    <?php foreach ($hangSua as $hang): ?>
                        <option value="<?= htmlspecialchars($hang['ma_hs']) ?>"
                            <?= ($hang['ma_hs'] == $chitiet['ma_hang_sua']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($hang['ten_hs']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-row">
                <label>Loại sữa</label>
                <input type="text" name="loai_sua"
                       value="<?= htmlspecialchars($chitiet['loai_sua']) ?>">
            </div>

            <div class="form-row">
                <label>Trọng lượng</label>
                <input type="number" name="trong_luong"
                       value="<?= htmlspecialchars($chitiet['trong_luong']) ?>" required>
            </div>

            <div class="form-row">
                <label>Đơn giá</label>
                <input type="number" name="don_gia"
                       value="<?= htmlspecialchars($chitiet['don_gia']) ?>" required>
            </div>

            <div class="form-row">
                <label>Thành phần dinh dưỡng</label>
                <textarea name="tpdd"><?= htmlspecialchars($chitiet['tpdd']) ?></textarea>
            </div>

            <div class="form-row">
                <label>Lợi ích</label>
                <textarea name="loi_ich"><?= htmlspecialchars($chitiet['loi_ich']) ?></textarea>
            </div>

            <div class="form-row">
                <label>Hình ảnh mới</label>
                <input type="file" name="hinh">
            </div>

            <div class="form-actions">
                <button type="submit" name="btn_capnhat">
                    Cập nhật
                </button>
            </div>

        </form>

    </div>

    <?php endif; ?>

</div>

<script>
document.getElementById('searchInput').addEventListener('keyup', function() {
    let value = this.value.toLowerCase();
    let cards = document.querySelectorAll('.product-card');
    cards.forEach(card => {
        let name = card.querySelector('.product-name').innerText.toLowerCase();
        card.style.display = name.includes(value) ? 'flex' : 'none';
    });
});
</script>

<script>
function increaseQty() {
    const input = document.getElementById('quantityInput');
    input.value = parseInt(input.value) + 1;
    updateCartLink();
}

function decreaseQty() {
    const input = document.getElementById('quantityInput');
    if (parseInt(input.value) > 1) {
        input.value = parseInt(input.value) - 1;
    }
    updateCartLink();
}

function updateCartLink() {
    const qty = document.getElementById('quantityInput').value;
    const btn = document.getElementById('addToCartBtn');
    btn.href = "/cart/add?id=<?= $chitiet['id'] ?>&qty=" + qty;
}

document.getElementById('quantityInput')?.addEventListener('change', updateCartLink);
</script>
</body>
</html>