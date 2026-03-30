const db = require('../database-tools/db');
const bcrypt = require('bcrypt');

async function seedDB() {

    try {
        console.log('Database đang seeding...');
        const count = process.argv[2] || 10;
        console.log(`Seeding ${count} products...`);

        const password = await bcrypt.hash('123', 10);
        await db.query(
            `INSERT INTO users ( full_name, username, password, role) VALUES ($1, $2, $3, $4)`,
            ['admin', 'admin', password, 'admin']
        );

        await db.query(
            `INSERT INTO users ( full_name, username, password, role) VALUES ($1, $2, $3, $4)`,
            ['Tester', 'tester', password, 'user']
        );

        for (let i = 0; i < count; i++) {
            const donGia = Math.floor(Math.random() * (500000 - 100000 + 1) + 100000); // 100.000 - 500.000
            const trongLuong = [400, 800, 900, 1500][i % 4]; // Một số trọng lượng phổ biến

            await db.query(
                `INSERT INTO products (ten_sua, ma_hang_sua, don_gia, trong_luong) VALUES ($1, $2, $3, $4)`,
                [`Seed ${Date.now()}_${i}`, 'VNM', donGia, trongLuong]
            );
        }

        console.log('Tạo tài khoản admin, user và thêm sản phẩm thành công');

    } catch (error) {
        console.log('Seed thất bại', error);
    } finally {
        process.exit();
    }
}
seedDB();