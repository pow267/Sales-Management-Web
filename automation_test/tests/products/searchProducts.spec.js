const { test, expect } = require('../../fixture/admin.fixture');
const { ViewPage } = require('../../pages/products/ViewPage');

test.describe('Kiểm tra chức năng tìm kiếm', () => {

    let viewPage;
    let productName;

    test.beforeEach(async ({ adminPage }) => {
        viewPage = new ViewPage(adminPage, test);
        await viewPage.goto();
    });

    test('Search-TC1 Nhập sản phẩm và tìm kiếm thành công', async ({ adminPage }) => {
        productName = await viewPage.searchDB();
        await viewPage.search(productName);
        await expect(viewPage.productName.filter({ hasText: productName }).first()).toBeVisible();
    });

    test('Search-TC2 Tìm kiếm nhận kết quả not found', async () => {
        await viewPage.search("abcsd1233");
        await expect(viewPage.loadingMessage).toBeHidden();
        await expect(viewPage.emptyMessage).toBeVisible();
    });
});