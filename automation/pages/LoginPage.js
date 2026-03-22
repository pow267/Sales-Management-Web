class LoginPage {
    constructor(page){
        this.page = page;

        this.usernameInput = page.locator('input[name="username"]');
        this.passwordInput = page.locator('input[name="password"]');
        this.error = page.locator('.form-message-error')
        this.buttonLogin = page.getByRole('button', { name: 'Đăng nhập'});
        this.successLogin = page.getByText('Xin chào');
        
    }

    async goto(){
        await this.page.goto('/login');
    }

    async login(username, password){
        await this.usernameInput.fill(username);
        await this.passwordInput.fill(password);
        await this.buttonLogin.click();
    }
}

module.exports = { LoginPage };