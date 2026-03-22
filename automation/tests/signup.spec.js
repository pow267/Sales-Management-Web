const { test, expect } = require('./fixture/fixture_guest');
const { SignUpPage } = require('../pages/SignUpPage');
const { LoginPage } = require('../pages/LoginPage');
const { testData } = require('../tests/data/testData');

const db = require('../database/db');

test.describe('Kiểm tra chức năng đăng ký', () => {

    test("SNU-TC01 Đăng ký tài khoản thành công", async ({page}) => {
        const signUp = new SignUpPage(page);
        const loginPage = new LoginPage(page);
        const data = testData();

        await signUp.signUp(data);
        await expect(signUp.success).toBeVisible();

        const rows = await db.query(
            `SELECT * FROM users WHERE username = $1 ORDER BY id DESC LIMIT 1`,
            [data.username]
        );

        expect(rows.length).toBe(1);

        await loginPage.goto();
        await loginPage.login(data.username, '123456');
        await expect(loginPage.successLogin).toBeVisible();
    });


    const cases = [
        { field: 'fullName', locator: 'fullNameInput'},
        { field: 'username', locator: 'usernameInput'},
        { field: 'password', locator: 'passwordInput'},
        { field: 'email', locator: 'emailInput'},
        { field: 'phone', locator: 'phoneInput'},
        { field: 'address', locator: 'addressInput'},
        
    ];

    for ( const c of cases ) {
        test(`SNU-TC02 Thiếu dữ liệu ${c.field}`, async ({ page }) => {
            const signUp = new SignUpPage(page);
            const data = testData();

            data[c.field] = '';

            await signUp.signUp(data);

            const invalid = await signUp.requireInvalid(signUp[c.locator]);
            expect(invalid).toBe(true);
        });
    }

    test('SNU-TC03 Đăng ký thất bại vì viết sai định dạng ', async ({page}) => {
        const signUp = new SignUpPage(page);
        const data = testData();

        data.email = 'abc';

        await signUp.signUp(data);

        const invalid = await signUp.emailInvalid();
        expect(invalid).toBe(true);

    });
});
