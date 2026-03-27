class AuthAPI {
    constructor(request) {
        this.request = request;
    }

    async login(username, password) {
        const res = await this.request.post('/api/session', {
            data: { username, password }
        });

        const body = await res.json();
        return { res, body };
    }

    async register(userData) {
        const res = await this.request.post('/api/users', {
            data: userData
        });
        const body = await res.json();
        return { res, body };
    }

    async logout(csrfToken) {
        const res = await this.request.delete('/api/session', {
            data: { csrf_token: csrfToken }
        });
        const body = await res.json();
        return { res, body };
    }
}
module.exports = { AuthAPI };
