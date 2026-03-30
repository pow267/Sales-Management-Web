class LogoutPage {
    constructor(page, test) {
        this.page = page;
        this.test = test;

        this.logoutButton = page.locator('#logoutBtn');
    }

    async goto() {
        await this.test.step('Điều hướng đến trang đăng nhập', async () => {
            await this.page.goto('/login');
        });
    }

    async logout() {
        await this.test.step('Nhấn nút đăng xuất', async () => {
            await this.logoutButton.click();
        });
    }
}
module.exports = { LogoutPage };