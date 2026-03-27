const { test, expect } = require('../../fixture/api.fixture');
const { AuthAPI } = require('../../pages/api/AuthAPI');
const { negativeData } = require('../../test-data/auth/NegativeLogin.data');
const { invalidData } = require('../../test-data/auth/InvalidLogin.data');

test.describe('kiểm tra Đăng nhập API', () => {
    let authAPI;
    test.beforeEach(async ({ request }) => {
        authAPI = new AuthAPI(request);
    });

    test('LoginAPI-TC1 Đăng nhập API thành công', async () => {
        const { res, body } = await authAPI.login('admin', '123');

        expect(res.status()).toBe(200);
        expect(body.success).toBe(true);
        expect(body.message).toContain('Đăng nhập thành công');
        expect(body.data).toHaveProperty('user');
        expect(body.data.user.username).toBe('admin');

    });

    for (const data of negativeData()) {
        test(`LoginAPI-TC2 Nhập sai các case: ${data.case}`, async () => {
            const { res, body } = await authAPI.login(data.username, data.password);
            expect(res.status()).toBe(401);
            expect(body.success).toBe(false);
            expect(body.message).toContain('Sai tên đăng nhập hoặc mật khẩu');
        });
    }

    for (const data of invalidData()) {
        test(`LoginAPI-TC3 Bỏ trống input các case: ${data.case}`, async () => {
            const { res, body } = await authAPI.login(data.username, data.password);
            expect(res.status()).toBe(422);
            expect(body.success).toBe(false);
            expect(body.message).toContain('Vui lòng nhập tên đăng nhập và mật khẩu.');
        });
    }
});