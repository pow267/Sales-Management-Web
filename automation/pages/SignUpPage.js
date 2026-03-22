class SignUpPage{
    constructor(page){
        this.page = page;

        this.registerButton = page.locator('a[href="/register"]');
        this.fullNameInput = page.locator('input[name="full_name"]');
        this.usernameInput = page.locator('input[name="username"]');
        this.passwordInput = page.locator('input[name="password"]');
        this.emailInput = page.locator('input[name="email"]');
        this.phoneInput = page.locator('input[name="phone"]');
        this.addressInput = page.locator('input[name="address"]');
        this.signUpButton = page.getByRole('button', { name: 'Đăng ký'});
        this.success = page.getByRole('button', { name: 'Đăng nhập'});
    }

    async goto(){
        await this.page.goto('/');
    }

    async fillForm(data){
        if(data.fullName !== undefined)
            await this.fullNameInput.fill(data.fullName);

        if(data.username !== undefined)
            await this.usernameInput.fill(data.username);

        if(data.password !== undefined)
            await this.passwordInput.fill(data.password);

        if(data.email !== undefined)
            await this.emailInput.fill(data.email);

        if(data.phone !== undefined)
            await this.phoneInput.fill(data.phone);

        if(data.address !== undefined)
            await this.addressInput.fill(data.address);
    }

    async signUp(data){
        await this.goto();
        await this.registerButton.click();
        await this.fillForm(data);
        await this.signUpButton.click();
    }

    async requireInvalid(lacator){
        return await lacator.evaluate(el => el.validity.valueMissing);
    }

    async emailInvalid(){
        return await this.emailInput.evaluate(el => el.validity.typeMismatch);
    }
}
module.exports = { SignUpPage };