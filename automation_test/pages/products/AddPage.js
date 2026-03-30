class AddPage {
    constructor(page, test) {
        this.page = page;
        this.test = test;

        this.tenSuaInput = page.getByRole('textbox', { name: 'Tên sữa' })
        this.hangSuaChoose = page.getByLabel('Hãng sữa');
        this.loaiSuainput = page.getByRole('textbox', { name: 'Loại sữa' });
        this.trongLuongInput = page.getByRole('spinbutton', { name: 'Trọng lượng' });
        this.donGiaInput = page.getByRole('spinbutton', { name: 'Đơn giá' });
        this.tpddInPut = page.getByRole('textbox', { name: 'Thành phần dinh dưỡng' });
        this.loiIchInput = page.getByRole('textbox', { name: 'Lợi ích' });
        this.hinhFile = page.locator('#add-hinh');
        this.themButton = page.getByRole('button', { name: 'Thêm mới' })
    }

    async fillForm(data) {
        await this.test.step(`Nhập thông tin thêm sản phẩm: ${data.tenSua}`, async () => {
            await this.tenSuaInput.fill(data.tenSua);
            await this.hangSuaChoose.selectOption(data.hangSua);
            await this.loaiSuainput.fill(data.loaiSua);
            await this.trongLuongInput.fill(data.trongLuong);
            await this.donGiaInput.fill(data.donGia);
            await this.tpddInPut.fill(data.tpdd);
            await this.loiIchInput.fill(data.loiIch);
            if (data.hinh) {
                await this.hinhFile.setInputFiles(data.hinh);
            }
        });
    }

    async save() {
        await this.themButton.click();
    }

    async waitForSave() {
        await this.test.step('Lưu sản phẩm và chờ xác nhận', async () => {
            await Promise.all([
                this.page.waitForResponse(res =>
                    res.url().includes('/api/products')
                    && res.request().method() === 'GET'
                    && res.status() === 200
                ),
                this.save()
            ]);
        });
    }
}
module.exports = { AddPage };