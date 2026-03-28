

function productData(overrides = {}) {
    return {
        tenSua: `AddProd Test ${Date.now()}`,
        hangSua: 'VNM',
        loaiSua: 'Sữa Tươi',
        trongLuong: '1000',
        donGia: '35000',
        tpdd: 'Năng lượng, Chất đạm, Chất béo, Hydrat cacbon, Canxi, Vitamin các loại',
        loiIch: 'Giúp xương chắc khỏe, tăng cường sức đề kháng',
        ...overrides
    };
}

function productDetailData(overrides = {}) {
    return {
        tenSua: `UpdateProd Test ${Date.now()}`,
        hangSua: 'VNM',
        loaiSua: 'Update',
        trongLuong: '1000',
        donGia: '35000',
        tpdd: 'Update',
        loiIch: 'Update',
        ...overrides
    };
}

function productDetailInvalidData(overrides = {}) {
    return {
        tenSua: `UpdateProd Test ${Date.now()}`,
        hangSua: 'VNM',
        loaiSua: 'Update',
        trongLuong: '1000',
        donGia: '35000',
        tpdd: 'Update',
        loiIch: 'Update',
        ...overrides
    };
}

module.exports = { productData, productDetailData, productDetailInvalidData };
