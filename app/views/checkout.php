<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Thanh toán</title>
<link rel="stylesheet" href="/assets/css/output.css">
</head>
<body>

<div class="box">

<div class="table-title">
THANH TOÁN
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

<div id="checkoutMessage"></div>

<div id="checkoutContent">
    <p class="text-center text-gray-500 py-10">Đang tải thông tin thanh toán...</p>
</div>

</div>

<script src="/assets/js/api-client.js"></script>
<script src="/assets/js/checkout-page.js"></script>

</body>
</html>
