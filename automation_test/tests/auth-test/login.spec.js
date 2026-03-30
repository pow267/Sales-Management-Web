const { test, expect } = require('@playwright/test');
const { LoginPage } = require('../../pages/auth/LoginPage');
const { negativeData } = require('../../test-data/auth/NegativeLogin.data');
const { invalidData } = require('../../test-data/auth/InvalidLogin.data');

test.describe('Kiểm tra chức năng Login', () => {

    let loginPage;
    test.beforeEach(async ({ page }) => {
        loginPage = new LoginPage(page, test);
        await loginPage.goto();
    });

    test('Auth-TC01 Đăng nhập thành công', async () => {
        await loginPage.login('admin', '123');
        await expect(loginPage.loginSuccess).toBeVisible();
    });

    for (const data of negativeData()) {
        test(`Auth-TC02 Nhập sai negative case: ${data.case}`, async () => {
            await loginPage.login(data.username, data.password);
            await expect(loginPage.errorMessage).toHaveText('Sai tên đăng nhập hoặc mật khẩu.');
        });
    }

    for (const data of invalidData()) {
        test(`Auth-TC03 nhập giá trị invalid case: ${data.case}`, async () => {
            await loginPage.login(data.username, data.password);
            await expect(loginPage.errorMessage).toHaveText('Vui lòng nhập tên đăng nhập và mật khẩu.');
        });
    }
});