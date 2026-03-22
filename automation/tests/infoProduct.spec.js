const { test, expect } = require('./fixture/fixture');
const { InforProduct } = require('../pages/InforProduct');

test.describe('Trang thông tin chi tiết của sản phẩm', () => {

    test('INFOR_TC01 - Hiện thông tin sản phẩm đầy đủ', async ({page}) => {
        const inforProduct = new InforProduct(page);

        await inforProduct.goto();
        await inforProduct.info();
        
        await expect(inforProduct.inforFull).toBeVisible();
    });
});