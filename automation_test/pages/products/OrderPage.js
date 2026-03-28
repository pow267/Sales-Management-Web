class OrderPage {
    constructor(page) {
        this.page = page;

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
        await this.plusButton.click();
    }

    async decreaseQuantity() {
        await this.decreaseButton.click();
    }

    async checkout() {
        await this.checkoutButton.click();
    }

    async deleteCart() {
        await this.deleteCartButton.waitFor({ state: 'visible' });
        await Promise.all([
            this.page.waitForEvent('dialog').then(dialog => dialog.accept()),
            this.deleteCartButton.click({ force: true })
        ]);
        await this.page.waitForLoadState('networkidle');
    }

    async back() {
        await this.backButton.click();
    }

    async backToCart() {
        await this.backToCartButton.click();
    }

    async order() {
        await this.orderButton.click();
    }

    async addCart() {
        await this.addCartButton.click();
    }
}
module.exports = { OrderPage };