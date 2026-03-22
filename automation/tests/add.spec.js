const { test, expect } = require('./fixture/fixture');
const { AddPage } = require('../pages/AddPage');
const db = require('../database/db');

test.describe('Chức năng thêm sản phẩm', () => {

    test('ADD_TC01 - Thêm sản phẩm thành công', async ({page}) => {
        const addPage = new AddPage(page);
        const name =`Sua test ${Date.now()}`;

        await addPage.addProduct({
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

        const rows = await db.query(
            `SELECT * FROM products WHERE ten_sua = $1 ORDER BY id DESC LIMIT 1`,
            [name]
        );

        expect(rows.length).toBe(1);
        expect(rows[0].trong_luong).toBe(180);
        expect(rows[0].don_gia).toBe(10000);
    });
});