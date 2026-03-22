const { expect } = require('@playwright/test');

class DeletePage{
    constructor(page) {
        this.page = page;

        this.productCard = (name) => this.page.locator('.product-card', { hasText: name });
        this.deleteButton = page.getByRole('button', {name: 'XÓA SẢN PHẨM'});
        this.success = page.locator('#toast');
    }

    async goto(){
        await this.page.goto("/");
    }

    async deleteById(id){
    await this.page.goto(`/?id=${id}&page=1#chitiet`);
    await Promise.all([
        this.page.waitForEvent('dialog').then(dialog => dialog.accept()),
        this.deleteButton.click()
    ]);
}
}
module.exports = { DeletePage };