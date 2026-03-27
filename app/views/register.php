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

    <form method="POST" action="/api/users" id="registerForm" novalidate>

        <div class="form-row">
            <label for="registerFullName">Tên người dùng</label>
            <div class="form-field">
                <div class="field-error" data-field-error="full_name" hidden></div>
                <input id="registerFullName" type="text" name="full_name" required>
            </div>
        </div>

        <div class="form-row">
            <label for="registerUsername">Tên đăng nhập</label>
            <div class="form-field">
                <div class="field-error" data-field-error="username" hidden></div>
                <input id="registerUsername" type="text" name="username" required>
            </div>
        </div>

        <div class="form-row">
            <label for="registerPassword">Mật khẩu</label>
            <div class="form-field">
                <div class="field-error" data-field-error="password" hidden></div>
                <input id="registerPassword" type="password" name="password" required>
            </div>
        </div>

        <div class="form-row">
            <label for="registerEmail">Email</label>
            <div class="form-field">
                <div class="field-error" data-field-error="email" hidden></div>
                <input id="registerEmail" type="email" name="email" required>
            </div>
        </div>

        <div class="form-row">
            <label for="registerPhone">Số điện thoại</label>
            <div class="form-field">
                <div class="field-error" data-field-error="phone" hidden></div>
                <input id="registerPhone" type="text" name="phone" required>
            </div>
        </div>

        <div class="form-row">
            <label for="registerAddress">Địa chỉ</label>
            <div class="form-field">
                <div class="field-error" data-field-error="address" hidden></div>
                <input id="registerAddress" type="text" name="address" required>
            </div>
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

function validateRegisterForm() {
    const errors = {};
    const fullName = registerForm.elements['full_name'].value.trim();
    const username = registerForm.elements['username'].value.trim();
    const password = registerForm.elements['password'].value.trim();
    const emailInput = registerForm.elements['email'];
    const phone = registerForm.elements['phone'].value.trim();
    const address = registerForm.elements['address'].value.trim();
    const email = emailInput.value.trim();

    if (!fullName) {
        errors.full_name = 'Vui lòng nhập tên người dùng.';
    }

    if (!username) {
        errors.username = 'Vui lòng nhập tên đăng nhập.';
    }

    if (!password) {
        errors.password = 'Vui lòng nhập mật khẩu.';
    } else if (password.length < 6) {
        errors.password = 'Mật khẩu phải có ít nhất 6 ký tự.';
    }

    if (!email) {
        errors.email = 'Vui lòng nhập email.';
    } else if (emailInput.validity.typeMismatch) {
        errors.email = 'Email không hợp lệ.';
    }

    if (!phone) {
        errors.phone = 'Vui lòng nhập số điện thoại.';
    }

    if (!address) {
        errors.address = 'Vui lòng nhập địa chỉ.';
    }

    return errors;
}

registerForm?.addEventListener('submit', async (event) => {
    event.preventDefault();

    window.apiClient.clearFormErrors(registerForm);
    registerMessage.replaceChildren();

    if (window.apiClient.renderFormErrors(registerForm, validateRegisterForm())) {
        return;
    }

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
        const hasFieldErrors = window.apiClient.renderFormErrors(
            registerForm,
            error.payload?.errors
        );

        if (!hasFieldErrors) {
            renderRegisterMessage(error.message);
        }
    } finally {
        registerSubmitBtn.disabled = false;
    }
});
</script>

</body>
</html>
