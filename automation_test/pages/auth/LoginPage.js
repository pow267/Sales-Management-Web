class LoginPage {
    constructor(page){
        this.page = page;

        this.loginButton = page.locator('#loginSubmitBtn');
        this.usernameInput = page.locator('input[name="username"]');
        this.passwordInput = page.locator('input[name="password"]');
        this.loginSuccess = page.locator('#logoutForm');
        this.errorMessage = page.locator('.form-message-error');
    }

    async goto(){
        await this.page.goto('/login');
    }

    async login(username, password){
        await this.usernameInput.fill(username);
        await this.passwordInput.fill(password);
        await this.loginButton.click();
        await this.page.waitForLoadState('networkidle');
    }
}
module.exports = { LoginPage };