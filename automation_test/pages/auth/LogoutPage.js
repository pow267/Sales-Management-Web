class LogoutPage{
    constructor(page){
        this.page = page;

        this.logoutButton = page.locator('#logoutBtn');
    }

    async goto(){
        this.page.goto('/login');
    }

    async logout(){
        this.logoutButton.click();
    }
}
module.exports = { LogoutPage };