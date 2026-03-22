exports.testData = function(){
    const t = Date.now();
    return{
        fullName: `Test User ${t}`,
        username: `user_${t}`,
        password: '123456',
        email: `test${t}@gmail.com`,
        phone: '0123456789',
        address: 'test'
    };
};