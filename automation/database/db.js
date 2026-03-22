const { Pool } = require('pg');

const pool = new Pool({
    host: 'localhost',
    user: 'postgres',
    password: '123456',
    database: 'ql_ban_sua',
    port: 5433,
});

async function query(text, params){
    const res = await pool.query(text, params);
    return res.rows;
}
module.exports = { query};