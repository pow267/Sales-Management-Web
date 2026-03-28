const { expect } = require('../../fixture/admin.fixture');
const { query } = require('../../database-tools/db');

class ViewPage {
    constructor(page) {
        this.page = page;

        this.searchInput = page.locator('#searchInput');
        this.cartButton = page.getByRole('link', { name: '🛒 Giỏ hàng' });
        this.addButton = page.getByRole('link', { name: 'THÊM SỮA MỚI' });
        this.updateInforButton = page.getByRole('link', { name: 'SỬA THÔNG TIN' });
        this.deleteButton = page.getByRole('button', { name: 'XÓA SẢN PHẨM' });
        this.addCartButton = page.getByRole('button', { name: 'THÊM VÀO GIỎ HÀNG' });
        this.emptyMessage = page.getByText('Không có sản phẩm nào như vậy.');
        this.loadingMessage = page.getByText('Đang tải sản phẩm...');
        this.productName = page.locator('.product-card');
    }

    async goto() {
        await this.page.goto('/');
    }

    async search(name) {
        await this.page.waitForLoadState('networkidle');
        await Promise.all([
            this.page.waitForResponse(res =>
                res.url().includes('/api/products')
                && res.request().method() === 'GET'
                && res.url().includes('search=')
            ),
            this.searchInput.fill(name)
        ]);
    }

    async searchDB() {
        const res = await query('SELECT ten_sua FROM products LIMIT 1');

        if (res.length > 0) {
            return res[0].ten_sua;
        } else {
            throw new Error('Không có dữ liệu trong database để test');
        }
    }

    async clickAddButton() {
        await this.addButton.click();
    }

    async clickUpdateInforButton() {
        await this.page.waitForLoadState('networkidle');
        await this.updateInforButton.waitFor({ state: 'visible' });
        await this.updateInforButton.click({ force: true });
    }

    async clickDeleteButton() {
        await this.deleteButton.waitFor({ state: 'visible' });
        await Promise.all([
            this.page.waitForEvent('dialog').then(dialog => dialog.accept()),
            this.deleteButton.click({ force: true })
        ]);
        await this.page.waitForLoadState('networkidle');
    }
    async clickAddCartButton() {
        await this.addCartButton.click();
    }

    async clickCartButton() {
        await this.cartButton.click();
    }

    getProductCard(name) {
        return this.page.locator('.product-card').filter({ hasText: name });
    }
}

module.exports = { ViewPage };