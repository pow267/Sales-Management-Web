const { test, expect } = require('../../fixture/api.fixture');
const { AuthAPI } = require('../../pages/api/AuthAPI');

test.describe('kiểm tra Logout API', () => {
    test('LogoutAPI-TC1 Đăng xuất API thành công', async ({ request }) => {
        const authAPI = new AuthAPI(request);
        const login = await authAPI.login('admin', '123');
        const csrfToken = login.body.data.csrf_token;
        const { res, body } = await authAPI.logout(csrfToken);

        expect(res.status()).toBe(200);
        expect(body.success).toBe(true);
        expect(body.message).toContain('Đăng xuất thành công');
    });
});