class DeletePage{
    constructor(page) {
        this.page = page;

        this.productCard = (name) => this.page.locator('.product-card', { hasText: name });
        this.deleteButton = page.getByRole('button', {name: 'XÓA SẢN PHẨM'});
        this.success = page.locator('#toast');
    }

    async goto(){
        await this.page.goto("/");
        await this.page.waitForLoadState('networkidle');
    }

    async deleteById(id){
        await this.page.goto(`/?id=${id}&page=1#chitiet`);
        await this.page.waitForLoadState('networkidle');
        await this.deleteButton.waitFor();

        await Promise.all([
            this.page.waitForEvent('dialog').then(dialog => dialog.accept()),
            this.page.waitForURL(/\/\?page=\d+$/),
            this.deleteButton.click()
        ]);

        await this.page.waitForLoadState('networkidle');
    }
}
module.exports = { DeletePage };
