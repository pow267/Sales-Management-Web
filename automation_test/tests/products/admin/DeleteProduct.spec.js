const { test, expect } = require('../../../fixture/admin.fixture');
const { ViewPage } = require('../../../pages/products/ViewPage');
const { AddPage } = require('../../../pages/products/AddPage');
const { productData } = require('../../../test-data/product/product.data');

test.describe('Kiểm tra chức năng Xóa sản phẩm', () => {
    let viewPage;
    let addPage;
    let testData;
    test.beforeEach(async ({ adminPage }) => {
        viewPage = new ViewPage(adminPage, test);
        addPage = new AddPage(adminPage, test);
        testData = productData();
    });
    test('Delete-TC1 Thêm một sản phẩm mới và xóa nó đi', async () => {
        await viewPage.clickAddButton();
        await addPage.fillForm(testData);
        await addPage.waitForSave();
        await viewPage.search(testData.tenSua);
        await expect(viewPage.getProductCard(testData.tenSua)).toBeVisible();
        await viewPage.getProductCard(testData.tenSua).getByRole('link').click();
        await viewPage.clickDeleteButton();
        await viewPage.search(testData.tenSua);
        await expect(viewPage.getProductCard(testData.tenSua)).toBeHidden();
    });
});