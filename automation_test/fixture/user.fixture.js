const { test: base, expect } = require('@playwright/test');
const { LoginPage } = require('../pages/auth/LoginPage');

const test = base.extend({

    userPage: async ({ page }, use) => {
        const loginPage = new LoginPage(page, base);

        await loginPage.goto();
        await loginPage.login('tester', '123');

        await expect(loginPage.loginSuccess).toBeVisible();

        await use(page);
    },
});

module.exports = { test, expect };
