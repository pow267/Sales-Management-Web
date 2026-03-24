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

    <div id="authMessage">
        <?php if (!empty($_SESSION['flash'])): ?>
            <div class="form-message">
                <?= htmlspecialchars($_SESSION['flash']); unset($_SESSION['flash']); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($_SESSION['error'])): ?>
            <div class="form-message form-message-error">
                <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
    </div>

    <form method="POST" action="/api/auth/login" id="loginForm">

        <div class="form-row">
            <label>Tên đăng nhập</label>
            <input type="text" name="username" required>
        </div>

        <div class="form-row">
            <label>Mật khẩu</label>
            <input type="password" name="password" required>
        </div>

        <div class="form-actions form-actions-row">
            <button type="submit" name="btn_login" id="loginSubmitBtn">
                Đăng nhập
            </button>

            <a href="/register" class="add-btn">
                Đăng ký
            </a>
        </div>

    </form>

</div>

<script src="/assets/js/api-client.js"></script>
<script>
const loginForm = document.getElementById('loginForm');
const authMessage = document.getElementById('authMessage');
const loginSubmitBtn = document.getElementById('loginSubmitBtn');

function renderLoginMessage(message) {
    const messageBox = document.createElement('div');
    messageBox.className = 'form-message form-message-error';
    messageBox.textContent = message;
    authMessage.replaceChildren(messageBox);
}

loginForm?.addEventListener('submit', async (event) => {
    event.preventDefault();

    loginSubmitBtn.disabled = true;

    try {
        const formData = new FormData(loginForm);
        const payload = {
            username: formData.get('username'),
            password: formData.get('password')
        };

        const response = await window.apiClient.request(loginForm.action, {
            method: 'POST',
            body: JSON.stringify(payload)
        });

        window.location.href = response.data.redirect || '/';
    } catch (error) {
        renderLoginMessage(error.message);
    } finally {
        loginSubmitBtn.disabled = false;
    }
});
</script>

</body>
</html>
