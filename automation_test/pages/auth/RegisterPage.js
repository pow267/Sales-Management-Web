class RegisterPage{
    constructor(page, test) {
        this.page = page;
        this.test = test;

        this.registerButton = page.getByRole('link', { name: 'Đăng ký' });
        this.fullNameInput = page.locator('#registerFullName');
        this.usernameInput = page.locator('#registerUsername');
        this.passwordInput = page.locator('#registerPassword');
        this.emailInput = page.locator('#registerEmail');
        this.phoneInput = page.locator('#registerPhone');
        this.addressInput = page.locator('#registerAddress');
        this.registerSubmit = page.locator('#registerSubmitBtn');
        this.successMessage = page.locator('.form-message');
        this.backToLogin = page.locator('a[href="/login"]');

    }

    async goto() {
        await this.test.step('Điều hướng đến trang đăng ký', async () => {
            await this.page.goto('/login');
            await this.registerButton.click();
        });
    }

    async fill(data) {
        await this.test.step(`Nhập thông tin đăng ký cho: ${data.username}`, async () => {
            const { full_name, username, password, email, phone, address } = data;
            await this.fullNameInput.fill(full_name);
            await this.usernameInput.fill(username);
            await this.passwordInput.fill(password);
            await this.emailInput.fill(email);
            await this.phoneInput.fill(phone);
            await this.addressInput.fill(address);
        });
    }

    async submit() {
        await this.test.step('Nhấn nút "Đăng ký"', async () => {
            await this.registerSubmit.click();
        });
    }

    async backLogin() {
        await this.test.step('Nhấn nút "Quay lại trang Đăng nhập"', async () => {
            await this.backToLogin.click();
        });
    }

    getError(field) {
        return this.page.locator(`[data-field-error="${field}"]`);
    }
}
module.exports ={ RegisterPage };