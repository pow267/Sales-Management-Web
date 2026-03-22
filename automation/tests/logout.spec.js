const { test, expect } = require ('./fixture/fixture');
const { ProductPage } = require ('../pages/ProductPage');

test.describe('Chức năng logout', () => {
    
    test('LO_TC01 - Logout thành công và quay về trang login', async ({page}) => {
        const productPage = new ProductPage(page);

        await productPage.goto();
        await productPage.logout();
        await expect(page).toHaveURL('/login');
    });
});