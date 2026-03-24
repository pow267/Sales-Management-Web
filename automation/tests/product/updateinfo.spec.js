const { test, expect } = require('../fixture/fixture');
const { InforUpdatePage } = require('../../pages/InforUpdatePage');
const { AddPage } = require('../../pages/AddPage');
const { DeletePage } = require('../../pages/DeletePage');
const db = require('../../database/db');

test.describe('Chức năng cập nhật lại thông tin sản phẩm', () => {
        
    test('UPINFOR-TC01 - Cập nhật sản phẩm thành công', async ({page}) => {
        const inforUpdate = new InforUpdatePage(page);
        const addPage = new AddPage(page);
        const name = `Sua test ${Date.now()}`;
        const updateName = name + ' Đã update';

        const id = await addPage.addProduct({
            tenSua: name,
            hangSua: 'VNM',
            loaiSua: 'Sua tuoi',
            trongLuong: '180',
            donGia: '10000',
            dinhDuong: 'xxx',
            loiIch: 'xxx',
            hinh: 'tests/data/R.png'
        });

        await expect(addPage.successAdd).toBeVisible();
        
        
        await inforUpdate.update(id, {
                tenSua: updateName,
                hangSua: 'VNM',
                loaiSua: 'Update thành công',
                trongLuong: '180',
                donGia: '10000',
                dinhDuong: 'Update thành công',
                loiIch: 'Update thành công',
        });

        await expect(inforUpdate.success).toBeVisible();

        const rows = await db.query(
            `SELECT * FROM products WHERE ten_sua = $1 ORDER BY id DESC LIMIT 1`,
            [updateName]
        );

        expect(rows[0].loai_sua).toBe('Update thành công');
        expect(rows[0].tpdd).toBe('Update thành công');
        expect(rows[0].loi_ich).toBe('Update thành công');

        const deletePage = new DeletePage(page);

        await deletePage.deleteById(id);
        await expect(page).toHaveURL(/\/\?page=\d+$/);

    });
});
