class OrderPage {
    constructor(page, test) {
        this.page = page;
        this.test = test;

        this.addCartButton = page.getByRole('button', { name: 'THÊM VÀO GIỎ HÀNG' });
        this.decreaseButton = page.getByRole('button', { name: '-' });
        this.plusButton = page.getByRole('button', { name: '+' })
        this.numberProd = page.getByRole('spinbutton', { name: 'Số lượng' });
        this.checkoutButton = page.getByRole('link', { name: 'Thanh toán' });
        this.deleteCartButton = page.getByRole('button', { name: 'Xóa' });
        this.nullCartMessage = page.getByText('Giỏ hàng đang trống');
        this.backButton = page.getByRole('link', { name: '← Tiếp tục mua hàng' });
        this.backToCartButton = page.getByRole('link', { name: '← Quay lại giỏ hàng' });
        this.orderButton = page.getByRole('button', { name: 'Xác nhận đặt hàng' });
        this.orderSuccessMessage = page.getByText('Đặt hàng thành công!');
        this.quantityCell = (qty) => this.page.getByRole('cell', { name: qty.toString(), exact: true })
    }

    async increaseQuantity() {
        await this.test.step('Tăng số lượng sản phẩm', async () => {
            await this.plusButton.click();
        });
    }

    async decreaseQuantity() {
        await this.test.step('Giảm số lượng sản phẩm', async () => {
            await this.decreaseButton.click();
        });
    }

    async checkout() {
        await this.test.step('Tiến hành thanh toán', async () => {
            await this.checkoutButton.click();
        });
    }

    async deleteCart() {
        await this.test.step('Xóa giỏ hàng', async () => {
            await this.deleteCartButton.waitFor({ state: 'visible' });
            await Promise.all([
                this.page.waitForEvent('dialog').then(dialog => dialog.accept()),
                this.deleteCartButton.click({ force: true })
            ]);
            await this.page.waitForLoadState('networkidle');
        });
    }

    async back() {
        await this.test.step('Quay lại trang sản phẩm', async () => {
            await this.backButton.click();
        });
    }

    async backToCart() {
        await this.test.step('Quay lại giỏ hàng', async () => {
            await this.backToCartButton.click();
        });
    }

    async order() {
        await this.test.step('Xác nhận đặt hàng', async () => {
            await this.orderButton.click();
        });
    }

    async addCart() {
        await this.test.step('Thêm vào giỏ hàng', async () => {
            await this.addCartButton.click();
        });
    }
}
module.exports = { OrderPage };