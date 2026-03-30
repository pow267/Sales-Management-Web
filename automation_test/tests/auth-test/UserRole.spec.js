const { test, expect } = require('../../fixture/user.fixture');
const { ViewPage } = require('../../pages/products/ViewPage');


test.describe('Kiểm tra Role của User', () => {
    let viewPage;
    test.beforeEach(async ({ userPage }) => {
        viewPage = new ViewPage(userPage, test);
    });

    test('Role-TC1 User không được phép thấy nút Thêm sản phẩm', async () => {
        await expect(viewPage.addButton).toBeHidden();
    });

    test('Role-TC2 User click vào sản phẩm thì không được phép thấy Sửa/Xóa', async () => {
        await viewPage.search('Seed');
        await viewPage.getProductCard('Seed').first().getByRole('link').click();
        await expect(viewPage.updateInforButton).toBeHidden();
        await expect(viewPage.deleteButton).toBeHidden();
    });

    test('Role-TC3 User truy cập trực tiếp link chức năng của Admin phải bị chặn', async ({ userPage }) => {
        await userPage.goto('/?page=1&id=8&action=sua#formsua');

        await expect(viewPage.addButton).toBeHidden();
        await expect(viewPage.updateInforButton).toBeHidden();
        await expect(viewPage.deleteButton).toBeHidden();
    });
});
