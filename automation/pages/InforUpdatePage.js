const { expect } = require('@playwright/test');

class InforUpdatePage{
    constructor(page){
        this.page = page;

        this.updateButton= page.getByRole('button', { name: 'Cập nhật'});
        this.success = page.locator('#toast');
        this.tenSuaInput = page.locator('input[name="ten_sua"]');
        this.chonHangSua = page.locator('select[name="ma_hang_sua"]');
        this.loaiSuaInput = page.locator('input[name="loai_sua"]');
        this.trongLuongInput = page.locator('input[name="trong_luong"]');
        this.donGiaInput = page.locator('input[name="don_gia"]');
        this.dinhDuongInput = page.locator('textarea[name="tpdd"]');
        this.loiIchInput = page.locator('textarea[name="loi_ich"]');
        this.hinhInput = page.locator('input[name="hinh"]');
        this.addCart = page.locator('#addToCartBtn');
        
    }

    async goto(){
        await this.page.goto('/');
    }

    async gotoEditById(id){
        await this.page.goto(`/?id=${id}&page=1#chitiet`);
        await expect(this.addCart).toBeVisible
        
        await this.page.getByRole('link', { name: 'SỬA THÔNG TIN' }).click();
        await expect(this.tenSuaInput).toBeVisible();
    }

    async fillForm(data){
        await expect(this.tenSuaInput).toBeVisible();

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

    async update(id, data){
        await this.gotoEditById(id);
        await this.fillForm(data)
        
        await this.updateButton.click()
        
    }
}
module.exports = { InforUpdatePage };