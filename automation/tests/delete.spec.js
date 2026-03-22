const { test, expect } = require('./fixture/fixture');
const { AddPage } = require('../pages/AddPage');
const { DeletePage } = require('../pages/DeletePage');
const db = require('../database/db');

test.describe('Chức năng xóa sản phẩm', () => {

    test('DELETE_TC01 - Xóa sản phẩm thành công', async ({page}) => {
        const deletePage = new DeletePage(page);
        const addPage = new AddPage(page);
        const name = `Test xóa sản phẩm ${Date.now()}`;

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
        await page.waitForLoadState('networkidle');

        await deletePage.deleteById(id);

        await expect(page).toHaveURL(/page=\d+#chitiet/);
        await page.reload();
        await expect(deletePage.productCard(name)).toHaveCount(0);

        const rows = await db.query(
            `SELECT * FROM products WHERE id = $1`,
            [id]
        );
        expect(rows.length).toBe(0);
    });
});