const { Pool } = require('pg');

const pool = new Pool({
    host: process.env.DB_HOST || 'localhost',
    user: process.env.DB_USER || 'postgres',
    password: process.env.DB_PASSWORD || '123456',
    database: process.env.DB_NAME || 'ql_ban_sua',
    port: process.env.DB_PORT || 5433,
})

async function query(text, params){
    const res = await pool.query(text, params);
    return res.rows;
}

module.exports = { query };