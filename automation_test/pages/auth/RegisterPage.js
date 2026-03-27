class RegisterPage{
    constructor(page){
        this.page = page;

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

    async goto(){
        await this.page.goto('/login');
        await this.registerButton.click();
    }

    async fill(data){
        const { full_name, username, password, email, phone, address } = data;
        await this.fullNameInput.fill(full_name);
        await this.usernameInput.fill(username);
        await this.passwordInput.fill(password);
        await this.emailInput.fill(email);
        await this.phoneInput.fill(phone);
        await this.addressInput.fill(address);
    }

    async submit(){
        await this.registerSubmit.click();
    }

    async backLogin(){
        await this.backToLogin.click();
    }

    getError(field){
        return this.page.locator(`[data-field-error="${field}"]`);
    }
}
module.exports ={ RegisterPage };