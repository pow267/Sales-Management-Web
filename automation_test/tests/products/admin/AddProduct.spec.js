const { test, expect } = require('../../../fixture/admin.fixture');
const { AddPage } = require('../../../pages/products/AddPage');
const { ViewPage } = require('../../../pages/products/ViewPage');
const { productData } = require('../../../test-data/product/product.data');
const { InvalidProduct } = require('../../../test-data/product/InvalidProduct.data')


test.describe('Kiểm tra chức năng Thêm sản phẩm', () => {

    let addPage;
    let viewPage;
    test.beforeEach(async ({ adminPage }) => {
        addPage = new AddPage(adminPage, test);
        viewPage = new ViewPage(adminPage, test);
        await viewPage.clickAddButton();
    });

    test('AddProd-TC1 Thêm sản phẩm thành công', async ({ adminPage }) => {

        const testData = productData();
        await addPage.fillForm(testData);
        await addPage.waitForSave();
        await viewPage.search(testData.tenSua);

        await expect(viewPage.getProductCard(testData.tenSua)).toBeVisible();
    });

    for (let item of InvalidProduct()) {
        test(`AddProd-TC2 Thêm sản phẩm thất bại với invalid case: ${item.description}`, async () => {
            await addPage.fillForm(item.data);
            await addPage.save();
            await expect(addPage.page.getByText(item.expectedError)).toBeVisible();
        });
    }
});