const { test, expect } = require('@playwright/test');
const { RegisterPage } = require('../../pages/auth/RegisterPage');
const { registerData } = require('../../test-data/auth/Register.data');
const { InvalidRegister } = require('../../test-data/auth/InvalidRegister.data');
const { NegativeRegister } = require('../../test-data/auth/NegativeRegister');

test.describe('Kiểm tra chức năng đăng ký', () => {

    let registerPage;
    test.beforeEach(async ({ page }) => {
        registerPage = new RegisterPage(page, test);
        await registerPage.goto();
    });

    test('Register-TC1 Đăng ký thành công', async () => {
        const data = registerData();
        await registerPage.fill(data);
        await registerPage.submit();
        await expect(registerPage.successMessage).toHaveText('Đăng ký thành công. Vui lòng đăng nhập.');
    });

    InvalidRegister().forEach( ({ name, field, data, expectedError}) => {
        test(`Register-TC2 Input invalid: ${name} `, async () => { 
            await registerPage.fill(data);
            await registerPage.submit();
            await expect(registerPage.getError(field)).toHaveText(expectedError);
        });
    });

    NegativeRegister().forEach( ({ name, field, data, expectedError}) => {
        test(`Register-TC3 Tên đăng nhập đã tồn tại khi đăng ký: ${name} `, async () => {
                await registerPage.fill(data);
                await registerPage.submit();
                await expect(registerPage.getError(field)).toHaveText(expectedError);
        });
    });

    test('Register-TC4 Nhấn nút quay lại trang Login', async ({page}) => { 
        await registerPage.backLogin();
        await expect(page).toHaveURL('/login');
    });
});