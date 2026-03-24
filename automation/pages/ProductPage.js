class ProductPage {
    constructor(page){
        this.page = page;
        this.productCard = page.locator('.product-card');
        this.searchInput = page.locator('#searchInput');
        this.addButton = page.locator('a[href="/?action=them"]');
        this.logoutButton = page.locator('#logoutBtn');
        this.cartButton = page.locator('a[href="/cart"]');
        this.noProduct = page.getByText('Không có sản phẩm nào như vậy.');
        this.productName = (name) => this.productCard.filter({ hasText: name });
        this.waitProduct = page.getByText('Đang tải sản phẩm...');
    }

    async goto() {
        await this.page.goto('/');
    }

    async search(keyword){
        await this.searchInput.fill(keyword);
        await this.searchInput.press('Enter');
    }

    async logout() {
        await this.logoutButton.click();
    }

    getProductCards() {
        return this.page.locator('.product-card');
    }
}

module.exports = { ProductPage };