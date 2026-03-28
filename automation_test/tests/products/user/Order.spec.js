const { test, expect } = require('../../../fixture/user.fixture');
const { OrderPage } = require('../../../pages/products/OrderPage');
const { ViewPage } = require('../../../pages/products/ViewPage');

test.describe('Kiểm tra chức năng đặt hàng', () => {

    let orderPage;
    let viewPage;
    test.beforeEach(async ({ userPage }) => {
        orderPage = new OrderPage(userPage);
        viewPage = new ViewPage(userPage);
    });

    test('Order-TC1 User đặt hàng thành công', async () => {
        await viewPage.search('Seed');
        await viewPage.getProductCard('Seed').first().getByRole('link').click();
        await orderPage.addCart()
        await orderPage.checkout();
        await orderPage.order();
        await expect(orderPage.orderSuccessMessage).toBeVisible();
    });

    test('Order-TC2 User đổi ý không muốn mua nữa', async ({ userPage }) => {
        await viewPage.search('Seed');
        await viewPage.getProductCard('Seed').first().getByRole('link').click();
        await orderPage.addCart()
        await orderPage.checkout();
        await orderPage.backToCart();
        await orderPage.deleteCart();
        await expect(orderPage.nullCartMessage).toBeVisible();

        await orderPage.back();
        await expect(userPage).toHaveURL('/');
    });

    test('Order-TC3 User tăng số lượng hàng lên 1 và thêm vào giỏ hàng', async () => {
        await viewPage.search('Seed');
        await viewPage.getProductCard('Seed').first().getByRole('link').click();
        await orderPage.increaseQuantity();
        await expect(orderPage.numberProd).not.toHaveValue('1');

        await orderPage.addCart();
        await expect(orderPage.quantityCell(2)).toBeVisible();
    });

    test('Order-TC4 User giảm số lượng hàng xuống 1 và thêm vào giỏ hàng', async () => {
        await viewPage.search('Seed');
        await viewPage.getProductCard('Seed').first().getByRole('link').click();
        await orderPage.decreaseQuantity();
        await expect(orderPage.numberProd).toHaveValue('1');
        await orderPage.addCart();
        await expect(orderPage.quantityCell(1)).toBeVisible();
    });
});