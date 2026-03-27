const db = require('../database-tools/db');
const bcrypt = require('bcrypt');

async function seedDB(){
    
    try{
        console.log('Database đang seeding...');
        const count = process.argv[2] || 10;
        console.log(`Seeding ${count} products...`);

        const password = await bcrypt.hash('123', 10);
        await db.query(
            `INSERT INTO users ( full_name, username, password, role) VALUES ($1, $2, $3, $4)`,
            ['admin', 'admin', password,'admin']
        );

        for( let i = 0; i < count; i++){
            await db.query(
                `INSERT INTO products ( ten_sua, ma_hang_sua) VALUES ( $1, $2)`,
                [ `Seed ${Date.now()}_${i}`, 'VNM']
            );
        }

        console.log('Tạo tài khoản admin và thêm sản phẩm thành công');

    } catch(error){
        console.log('Seed thất bại', error);
    } finally{
        process.exit();
    }
}
seedDB();