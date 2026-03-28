const { productData } = require('./product.data');

function InvalidProduct() {
    return [
        {
            description: 'Tên sữa bị bỏ trống',
            data: productData({ tenSua: '' }),
            expectedError: 'Tên sữa không được để trống.'
        },
        {
            description: 'Trọng lượng bằng 0',
            data: productData({ trongLuong: '0' }),
            expectedError: 'Trọng lượng phải lớn hơn 0.'
        },
        {
            description: 'Trọng lượng mang giá trị âm',
            data: productData({ trongLuong: '-500' }),
            expectedError: 'Trọng lượng phải lớn hơn 0.'
        },
        {
            description: 'Đơn giá bằng 0',
            data: productData({ donGia: '0' }),
            expectedError: 'Đơn giá phải lớn hơn 0.'
        },
        {
            description: 'Đơn giá mang giá trị âm',
            data: productData({ donGia: '-1000' }),
            expectedError: 'Đơn giá phải lớn hơn 0.'
        }
    ];
}

module.exports = { InvalidProduct };
