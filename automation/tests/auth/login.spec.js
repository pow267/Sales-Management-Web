const { test, expect } = require ('../fixture/fixture');
const { LoginPage } = require ('../../pages/LoginPage');


const testData = [
    {username: 'admin', password: '123', success: true},
    {username: 'admin', password: '1234', success: false},
    {username: 'admind', password: '12345', success: false},
    {username: 'admind', password: '123', success: false}
];

test.describe('Chức năng login', () => {

    let loginPage;

    test.beforeEach( async ({page}) => {
        loginPage = new LoginPage(page);
        await loginPage.goto('/login');
    });

    for (const data of testData) {

        test(`LG_TC01 - Login với tài khoản trong data ${data.username} / ${data.password}`, async ({page}) => {
    
            await loginPage.login(data.username, data.password);
            
            if (data.success) {
                await expect(page).toHaveURL('/');
            } else {
                await expect(page).toHaveURL('/login');
            }
        });
    }
});