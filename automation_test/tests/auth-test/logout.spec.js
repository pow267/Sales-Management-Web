const { test, expect } = require('@playwright/test');
const { LogoutPage } = require('../../pages/auth/LogoutPage');
const { LoginPage } = require('../../pages/auth/LoginPage');

test.describe('Kiểm tra chức năng Logout', () => { 
    
    test('Logout-TC01 Nhấn logout để ra ngoài Login', async ({ page }) => {
        const logoutPage = new LogoutPage(page, test);
        const loginPage = new LoginPage(page, test);

        await loginPage.goto();
        await loginPage.login('admin', '123');
        await expect(loginPage.loginSuccess).toBeVisible();

        await logoutPage.logout();
        await expect(page).toHaveURL('/login');
    });
});