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

    <div id="registerMessage">
        <?php if (!empty($_SESSION['error'])): ?>
            <div class="form-message form-message-error">
                <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
    </div>

    <form method="POST" action="/api/auth/register" id="registerForm">

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
            <button type="submit" name="btn_register" id="registerSubmitBtn">
                Đăng ký
            </button>

            <a href="/login" class="add-btn">
                Quay lại đăng nhập
            </a>
        </div>

    </form>

</div>

<script src="/assets/js/api-client.js"></script>
<script>
const registerForm = document.getElementById('registerForm');
const registerMessage = document.getElementById('registerMessage');
const registerSubmitBtn = document.getElementById('registerSubmitBtn');

function renderRegisterMessage(message) {
    const messageBox = document.createElement('div');
    messageBox.className = 'form-message form-message-error';
    messageBox.textContent = message;
    registerMessage.replaceChildren(messageBox);
}

registerForm?.addEventListener('submit', async (event) => {
    event.preventDefault();

    registerSubmitBtn.disabled = true;

    try {
        const formData = new FormData(registerForm);
        const payload = Object.fromEntries(formData.entries());

        const response = await window.apiClient.request(registerForm.action, {
            method: 'POST',
            body: JSON.stringify(payload)
        });

        window.location.href = response.data.redirect || '/login';
    } catch (error) {
        renderRegisterMessage(error.message);
    } finally {
        registerSubmitBtn.disabled = false;
    }
});
</script>

</body>
</html>
