class LogoutPage {
    constructor(page) {
        this.page = page;

        this.logoutButton = page.locator('#logoutBtn');
    }

    async goto() {
        await this.page.goto('/login');
    }

    async logout() {
        await this.logoutButton.click();
    }
}
module.exports = { LogoutPage };