<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký</title>
    <link rel="stylesheet" href="/assets/css/output.css">
</head>
<body>

<div class="box register-box">

    <div class="table-title">
        ĐĂNG KÝ TÀI KHOẢN
    </div>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="form-message form-message-error">
            <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="/register">

        <div class="form-row">
            <label>Tên người dùng</label>
            <input type="text" name="full_name" required>
        </div>

        <div class="form-row">
            <label>Tên đăng nhập</label>
            <input type="text" name="username" required>
        </div>

        <div class="form-row">
            <label>Mật khẩu</label>
            <input type="password" name="password" required>
        </div>

        <div class="form-row">
            <label>Email</label>
            <input type="email" name="email" required>
        </div>

        <div class="form-row">
            <label>Số điện thoại</label>
            <input type="text" name="phone" required>
        </div>

        <div class="form-row">
            <label>Địa chỉ</label>
            <input type="text" name="address" required>
        </div>

        <div class="form-actions form-actions-row">
            <button type="submit" name="btn_register">
                Đăng ký
            </button>

            <a href="/login" class="add-btn">
                Quay lại đăng nhập
            </a>
        </div>

    </form>

</div>

</body>
</html>