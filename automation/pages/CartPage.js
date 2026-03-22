class CartPage {
    constructor(page){
        this.page = page;

        this.cartButton = page.locator('a[href="/cart"]');
        
    }
}