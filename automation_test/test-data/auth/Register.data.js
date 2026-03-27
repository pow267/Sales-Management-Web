function registerData(){
    return{
        full_name: `Test User ${Date.now()}`,
        username: `test${Date.now()}`,
        password: '123456',
        email: `test${Date.now()}@gmail.com`,
        phone: '123',
        address: 'HCM'
    }
}

module.exports = { registerData };