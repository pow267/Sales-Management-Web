const { test, expect } = require('../../fixture/api.fixture');
const { AuthAPI } = require('../../pages/api/AuthAPI');
const { registerData } = require('../../test-data/auth/Register.data');
const { InvalidRegister } = require('../../test-data/auth/InvalidRegister.data');
const { NegativeRegister } = require('../../test-data/auth/NegativeRegister');

test.describe('Kiểm tra Đăng ký API', () => {
    let authAPI;
    test.beforeEach(async ({ request }) => {
        authAPI = new AuthAPI(request);
    });

    test('RegisterAPI-TC1 Đăng ký thành công', async () => {
        const userData = registerData();
        const { res, body } = await authAPI.register(userData);

        expect(res.status()).toBe(201);
        expect(body.success).toBe(true);
        expect(body.message).toContain('Đăng ký thành công.');
    });

    for (const data of InvalidRegister()) {
        test(`RegisterAPI-TC2 Đăng ký thất bại với các case: ${data.name}`, async () => {
            const { res, body } = await authAPI.register(data.data);
            expect(res.status()).toBe(422);
            expect(body.success).toBe(false);
            expect(body.message).toContain('Vui lòng kiểm tra lại thông tin đăng ký.');
            expect(body.errors[data.field]).toBe(data.expectedError);
        });
    }

    test('Register-TC3 Đăng ký tên đăng nhập đã tồn tại', async () => {
        const userData = NegativeRegister()[0].data;
        const { res, body } = await authAPI.register(userData);

        expect(res.status()).toBe(409);
        expect(body.success).toBe(false);
        expect(body.message).toContain('Tên đăng nhập đã tồn tại.');
        expect(body.errors['username']).toBe('Tên đăng nhập đã tồn tại.');
    });
});