const { test, expect } = require ('../fixture/fixture');
const { ProductPage } = require ('../../pages/ProductPage');

test.describe('Kiểm tra trang sản phẩm', () => {

    let productPage;

    test.beforeEach( async ({page}) => {
        productPage = new ProductPage(page);

        await productPage.goto();
    });

    test('PRL_TC01 - Hiển thị danh sách sản phẩm', async () => {
        const count = await productPage.productCard.count();
        expect(count).toBeGreaterThan(0);
        await expect(productPage.productCard.first()).toBeVisible();
    });

    test('PRL_TC02 - Hiển thị các nút trong sản phẩm', async () => {

        await expect(productPage.addButton).toBeVisible();
        await expect(productPage.cartButton).toBeVisible();
        await expect(productPage.logoutButton).toBeVisible();
    });

    test('PRL_TC03 - Tìm kiếm khi không có sản phẩm được tìm thấy', async () => {
        
        await productPage.search('abc123');
        await expect(productPage.noProduct).toBeVisible();
    });

    test('PRL_TC04 - Tìm kiếm khi có sản phẩm', async () => {
        
        await productPage.search('Vinamilk');

        const cards = productPage.getProductCards();
        const count = await cards.count();

        expect(count).toBeGreaterThan(0);

        await expect(cards).toContainText(['Vinamilk']);

    });
});