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

<div class="cart-navigation">
    <a href="/" class="add-btn">
        ← Tiếp tục mua hàng
    </a>
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

<div id="cartMessage"></div>

<div id="cartContent">
    <p class="text-center text-gray-500 py-10">Đang tải giỏ hàng...</p>
</div>

</div>

<script src="/assets/js/api-client.js"></script>
<script src="/assets/js/cart-page.js"></script>

</body>
</html>
