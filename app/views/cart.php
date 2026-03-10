<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Giỏ hàng</title>
<link rel="stylesheet" href="/assets/css/output.css">
</head>
<body>

<div class="box">

<div class="table-title">
GIỎ HÀNG
</div>

<!-- ================= NAVIGATION ================= -->
<div class="cart-navigation">
    <a href="/" class="add-btn">
        ← Tiếp tục mua hàng
    </a>
</div>

<!-- ================= FLASH ================= -->
<?php if (!empty($_SESSION['flash'])): ?>
    <div class="toast">
        <?= $_SESSION['flash']; unset($_SESSION['flash']); ?>
    </div>
<?php endif; ?>

<?php if (empty($items)): ?>

    <p class="text-center text-gray-500 py-10">
        Giỏ hàng đang trống
    </p>

<?php else: ?>

<table class="cart-table">

<tr>
<th>Hình ảnh</th>
<th>Sản phẩm</th>
<th>Số lượng</th>
<th>Đơn giá</th>
<th>Tạm tính</th>
<th>Thao tác</th>
</tr>

<?php foreach ($items as $item): ?>

<tr>

<td>
    <?php if (!empty($item['hinh'])): ?>
        <img src="/assets/images/<?= htmlspecialchars($item['hinh']) ?>"
             alt="<?= htmlspecialchars($item['ten_sua']) ?>"
             class="cart-img">
    <?php else: ?>
        Không có ảnh
    <?php endif; ?>
</td>

<td>
    <?= htmlspecialchars($item['ten_sua']) ?>
</td>

<td>
    <?= $item['quantity'] ?>
</td>

<td>
    <?= number_format($item['don_gia'], 0, ',', '.') ?> VND
</td>

<td>
    <?= number_format($item['subtotal'], 0, ',', '.') ?> VND
</td>

<td>
    <div class="cart-actions">
        <form method="POST" action="/cart/remove">
            <input type="hidden" name="id"
                   value="<?= $item['id'] ?>">

            <input type="hidden" name="csrf_token"
                   value="<?= $_SESSION['csrf_token'] ?? '' ?>">

            <button type="submit"
                    class="add-btn"
                    onclick="return confirm('Xóa sản phẩm này khỏi giỏ?')">
                Xóa
            </button>
        </form>
    </div>
</td>

</tr>

<?php endforeach; ?>

</table>

<h3 class="cart-total">
    Tổng tiền: <?= number_format($total, 0, ',', '.') ?> VND
</h3>

<!-- ================= CHECKOUT ================= -->
<div class="mt-6 text-right">
    <a href="/checkout" class="add-btn">
        Thanh toán
    </a>
</div>

<?php endif; ?>

</div>

</body>
</html>