function NegativeRegister() {
    return [
        {
            name: 'Tên đăng nhập đã tồn tại',
            field: 'username',
            data: {
                full_name: 'a',
                field: 'full_name',
                username: 'admin',
                password: '123456',
                email: `test@gmail.com`,
                phone: '123',
                address: 'HCM'
            },
            expectedError: 'Tên đăng nhập đã tồn tại.'
        },
    ];
}
module.exports = { NegativeRegister };