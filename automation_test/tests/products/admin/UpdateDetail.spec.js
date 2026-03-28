const { test, expect } = require('../../../fixture/admin.fixture');
const { UpdateDetailPage } = require('../../../pages/products/UpdateDetailPage');
const { AddPage } = require('../../../pages/products/AddPage');
const { ViewPage } = require('../../../pages/products/ViewPage');
const { productData, productDetailData } = require('../../../test-data/product/product.data');
const { InvalidDetail } = require('../../../test-data/product/InvaliDetail.data');

test.describe('Kiểm tra tính năng Cập Nhật detail sản phẩm', () => {
    let updateDetail;
    let addPage;
    let viewPage;
    let testData;
    let updateData;
    test.beforeEach(async ({ adminPage }) => {
        updateDetail = new UpdateDetailPage(adminPage);
        addPage = new AddPage(adminPage);
        viewPage = new ViewPage(adminPage);
        testData = productData();
        updateData = productDetailData();
    })
    test('Detail-TC1 Chọn sản phẩm vừa thêm và sửa lại detail', async ({ adminPage }) => {
        await viewPage.clickAddButton();
        await addPage.fillForm(testData);
        await addPage.waitForSave();
        await viewPage.search(testData.tenSua);
        await viewPage.getProductCard(testData.tenSua).getByRole('link').click();
        await viewPage.clickUpdateInforButton();
        await updateDetail.fillForm(updateData);
        await updateDetail.waitForSave();
        await viewPage.search(updateData.tenSua);
        await expect(viewPage.getProductCard(updateData.tenSua)).toBeVisible();
    });
    for (let item of InvalidDetail()) {
        test(`Detail-TC2 Lưu detail khi nhập invalid case: ${item.description}`, async ({ adminPage }) => {
            await viewPage.clickAddButton();
            await addPage.fillForm(testData);
            await addPage.waitForSave();
            await viewPage.search(testData.tenSua);
            await viewPage.getProductCard(testData.tenSua).getByRole('link').click();
            await viewPage.clickUpdateInforButton();
            await updateDetail.fillForm(item.data);
            await updateDetail.save();
            await expect(adminPage.getByText(item.expectedError)).toBeVisible();
        })
    }
});