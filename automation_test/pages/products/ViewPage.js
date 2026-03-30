const { expect } = require('../../fixture/admin.fixture');
const { query } = require('../../database-tools/db');

class ViewPage {
    constructor(page, test) {
        this.page = page;
        this.test = test;

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
        await this.test.step('Điều hướng đến trang chủ', async () => {
            await this.page.goto('/');
        });
    }

    async search(name) {
        await this.test.step(`Tìm kiếm sản phẩm: ${name}`, async () => {
            await this.page.waitForLoadState('networkidle');
            await Promise.all([
                this.page.waitForResponse(res =>
                    res.url().includes('/api/products')
                    && res.request().method() === 'GET'
                    && res.url().includes('search=')
                ),
                this.searchInput.fill(name)
            ]);
        });
    }

    async searchDB() {
        return await this.test.step('Truy vấn database để lấy tên sản phẩm', async () => {
            const res = await query('SELECT ten_sua FROM products LIMIT 1');

            if (res.length > 0) {
                return res[0].ten_sua;
            } else {
                throw new Error('Không có dữ liệu trong database để test');
            }
        });
    }

    async clickAddButton() {
        await this.test.step('Click nút "THÊM SỮA MỚI"', async () => {
            await this.addButton.click();
        });
    }

    async clickUpdateInforButton() {
        await this.test.step('Click nút "SỬA THÔNG TIN"', async () => {
            await this.page.waitForLoadState('networkidle');
            await this.updateInforButton.waitFor({ state: 'visible' });
            await this.updateInforButton.click({ force: true });
        });
    }

    async clickDeleteButton() {
        await this.test.step('Click nút "XÓA SẢN PHẨM" và xác nhận', async () => {
            await this.deleteButton.waitFor({ state: 'visible' });
            await Promise.all([
                this.page.waitForEvent('dialog').then(dialog => dialog.accept()),
                this.deleteButton.click({ force: true })
            ]);
            await this.page.waitForLoadState('networkidle');
        });
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