const { test: base, expect } = require('@playwright/test');

const test = base.extend({
    adminRequest: async ({ request }, use) => {
        const loginResponse = await request.post('/api/session', {
            data: {
                username: 'admin',
                password: '123'
            }
        });

        if (!loginResponse.ok()) {
            throw new Error(`Auto-login thất bại! HTTP Status: ${loginResponse.status()}`);
        }
        const loginData = await loginResponse.json();

        request.csrfToken = loginData.data?.csrf_token;

        await use(request);
    }
});

module.exports = { test, expect };
