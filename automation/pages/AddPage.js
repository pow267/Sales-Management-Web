const { expect } = require('@playwright/test');

class AddPage {
    constructor(page) {
        this.page = page;

        this.clickAdd = page.locator('a[href="?action=them&page=1"]');
        this.tenSuaInput = page.locator('input[name="ten_sua"]');
        this.chonHangSua = page.locator('select[name="ma_hang_sua"]');
        this.loaiSuaInput = page.locator('input[name="loai_sua"]');
        this.trongLuongInput = page.locator('input[name="trong_luong"]');
        this.donGiaInput = page.locator('input[name="don_gia"]');
        this.dinhDuongInput = page.locator('#dd');
        this.loiIchInput = page.locator('#li');
        this.hinhInput = page.locator('input[name="hinh"]');
        this.themButton = page.locator('button[name="btn_them"]');
        this.successAdd = page.getByText(/Thêm sản phẩm thành công!/i);
    }

    async goto(){
        await this.page.goto("/");
    }

    async add(){
        await this.clickAdd.click();
        await this.tenSuaInput.waitFor();
    }

    async fillForm(data){
        await this.tenSuaInput.fill(data.tenSua);
        await this.chonHangSua.selectOption(data.hangSua);
        await this.loaiSuaInput.fill(data.loaiSua);
        await this.trongLuongInput.fill(data.trongLuong);
        await this.donGiaInput.fill(data.donGia);
        await this.dinhDuongInput.fill(data.dinhDuong);
        await this.loiIchInput.fill(data.loiIch);
        if ( data.hinh) {
            await this.hinhInput.setInputFiles(data.hinh);
        }        
    }

    async submit(){
        await Promise.all([
            this.page.waitForURL(/id=\d+/),
            this.themButton.click()
        ]);

        await this.successAdd.waitFor();
        const url = this.page.url();
        const id = new URL(url).searchParams.get('id');
        return id;
    }

    async addProduct(data){
        await this.goto();
        await this.add();
        await this.fillForm(data);
        return await this.submit();
    }
}
module.exports = { AddPage };