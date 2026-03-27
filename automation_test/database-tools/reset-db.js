const db = require ('../database-tools/db');

async function resetDB() {
    try{
        console.log('Database đang resetting...');

        await db.query(`
            TRUNCATE TABLE
                products,
                users,
                orders
            RESTART IDENTITY CASCADE
            
            `);

            console.log('Database reset thành công');
        } catch(error){
            console.error('reset DB thất bại:', error);
        } finally{
            process.exit();
        }
}

resetDB();