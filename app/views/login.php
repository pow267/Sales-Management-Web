<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập</title>
    <link rel="stylesheet" href="/assets/css/output.css">
</head>
<body>

<div class="box auth-box">

    <div class="table-title">
        ĐĂNG NHẬP HỆ THỐNG
    </div>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="form-message form-message-error">
            <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="/login">

        <div class="form-row">
            <label>Tên đăng nhập</label>
            <input type="text" name="username" required>
        </div>

        <div class="form-row">
            <label>Mật khẩu</label>
            <input type="password" name="password" required>
        </div>

        <div class="form-actions form-actions-row">
            <button type="submit" name="btn_login">
                Đăng nhập
            </button>

            <a href="/register" class="add-btn">
                Đăng ký
            </a>
        </div>

    </form>

</div>

</body>
</html>