const { expect } = require('@playwright/test');

class InforProduct {
    constructor(page){
        this.page = page;

        this.productName = (name) => this.page.locator('.product-name a', { hasText: name }).first();
        this.inforFull = page.locator('#addToCartBtn');
    }

    async goto(){
        await this.page.goto('/');
    }

    async info(name){
        await this.productName(name).click();
        await expect(this.inforFull).toBeVisible();
    }
}

module.exports = { InforProduct };