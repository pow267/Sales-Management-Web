const { productDetailInvalidData } = require('./product.data');

function InvalidDetail() {
    return [
        {
            description: 'Tên sữa bị bỏ trống',
            data: productDetailInvalidData({ tenSua: '' }),
            expectedError: 'Tên sữa không được để trống.'
        },
        {
            description: 'Trọng lượng bằng 0',
            data: productDetailInvalidData({ trongLuong: '0' }),
            expectedError: 'Trọng lượng phải lớn hơn 0.'
        },
        {
            description: 'Trọng lượng mang giá trị âm',
            data: productDetailInvalidData({ trongLuong: '-500' }),
            expectedError: 'Trọng lượng phải lớn hơn 0.'
        },
        {
            description: 'Đơn giá bằng 0',
            data: productDetailInvalidData({ donGia: '0' }),
            expectedError: 'Đơn giá phải lớn hơn 0.'
        },
        {
            description: 'Đơn giá mang giá trị âm',
            data: productDetailInvalidData({ donGia: '-1000' }),
            expectedError: 'Đơn giá phải lớn hơn 0.'
        }
    ];
}

module.exports = { InvalidDetail };
