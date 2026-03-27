function InvalidRegister() {
    return [
        {
            name: 'Tên người dùng để trống',
            field: 'full_name',
            data: {
                full_name: '',
                username: `test${Date.now()}`,
                password: '123456',
                email: `test${Date.now()}@gmail.com`,
                phone: '123',
                address: 'HCM'
            },
            expectedError: 'Vui lòng nhập tên người dùng.'
        },

        {
            name: 'Tên đăng nhập để trống',
            field: 'username',
            data: {
                full_name: 'Test User',
                username: '',
                password: '123456',
                email: `test${Date.now()}@gmail.com`,
                phone: '123',
                address: 'HCM'
            },
            expectedError: 'Vui lòng nhập tên đăng nhập.'
        },

        {
            name: 'Mật khẩu để trống',
            field: 'password',
            data: {
                full_name: 'Test User',
                username: `test${Date.now()}`,
                password: '',
                email: `test${Date.now()}@gmail.com`,
                phone: '123',
                address: 'HCM'
            },
            expectedError: 'Vui lòng nhập mật khẩu.'
        },

        {
            name: 'Mật khẩu có 5 ký tự.',
            field: 'password',
            data: {
                full_name: 'Test User',
                username: `test${Date.now()}`,
                password: '12345',
                email: `test${Date.now()}@gmail.com`,
                phone: '123',
                address: 'HCM'
            },
            expectedError: 'Mật khẩu phải có ít nhất 6 ký tự.'
        },

        {
            name: 'Email để trống',
            field: 'email',
            data: {
                full_name: 'Test User',
                username: `test${Date.now()}`,
                password: '123456',
                email: '',
                phone: '123',
                address: 'HCM'
            },
            expectedError: 'Vui lòng nhập email.'
        },

        {
            name: 'Email sai định dạng',
            field: 'email',
            data: {
                full_name: 'Test User',
                username: `test${Date.now()}`,
                password: '123456',
                email: `test${Date.now()}@gmaiaacom`,
                phone: '123',
                address: 'HCM'
            },
            expectedError: 'Email không hợp lệ.'
        },

        {
            name: 'Số điện thoại để trống',
            field: 'phone',
            data: {
                full_name: 'Test User',
                username: `test${Date.now()}`,
                password: '123456',
                email: `test${Date.now()}@gmail.com`,
                phone: '',
                address: 'HCM'
            },
            expectedError: 'Vui lòng nhập số điện thoại.'
        },

        {
            name: 'Địa chỉ để trống',
            field: 'address',
            data: {
                full_name: 'Test User',
                username: `test${Date.now()}`,
                password: '123456',
                email: `test${Date.now()}@gmail.com`,
                phone: '123',
                address: ''
            },
            expectedError: 'Vui lòng nhập địa chỉ.'
        },
    ];
}

module.exports = { InvalidRegister };