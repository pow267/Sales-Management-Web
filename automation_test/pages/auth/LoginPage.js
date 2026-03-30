class LoginPage {
    constructor(page, test) {
        this.page = page;
        this.test = test;

        this.loginButton = page.locator('#loginSubmitBtn');
        this.usernameInput = page.locator('input[name="username"]');
        this.passwordInput = page.locator('input[name="password"]');
        this.loginSuccess = page.locator('#logoutForm');
        this.errorMessage = page.locator('.form-message-error');
    }

    async goto() {
        await this.test.step('Điều hướng đến trang đăng nhập', async () => {
            await this.page.goto('/login');
        });
    }

    async login(username, password) {
        await this.test.step(`Đăng nhập với tài khoản: ${username}`, async () => {
            await this.usernameInput.fill(username);
            await this.passwordInput.fill(password);
            await this.loginButton.click();
        });
    }
}
module.exports = { LoginPage };