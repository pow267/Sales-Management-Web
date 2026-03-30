-- ==================================================
-- DROP TABLES
-- ==================================================

DROP TABLE IF EXISTS order_items CASCADE;
DROP TABLE IF EXISTS orders CASCADE;
DROP TABLE IF EXISTS products CASCADE;
DROP TABLE IF EXISTS hang_sua CASCADE;
DROP TABLE IF EXISTS users CASCADE;

-- ==================================================
-- TABLE: users
-- ==================================================

CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    username VARCHAR(100) UNIQUE NOT NULL,
    password TEXT NOT NULL,
    email VARCHAR(255),
    phone VARCHAR(20),
    address TEXT,
    role VARCHAR(20) DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_users_username ON users(username);

-- ==================================================
-- TABLE: hang_sua
-- ==================================================

CREATE TABLE hang_sua (
    ma_hs VARCHAR(10) PRIMARY KEY,
    ten_hs VARCHAR(100) NOT NULL,
    dia_chi VARCHAR(255),
    dien_thoai VARCHAR(20),
    email VARCHAR(100) UNIQUE
);

CREATE INDEX idx_hang_sua_ten ON hang_sua(ten_hs);

-- ==================================================
-- TABLE: products
-- ==================================================

CREATE TABLE products (
    id SERIAL PRIMARY KEY,
    ten_sua VARCHAR(255) NOT NULL,
    ma_hang_sua VARCHAR(10)
        REFERENCES hang_sua(ma_hs)
        ON DELETE RESTRICT,
    loai_sua VARCHAR(100),
    trong_luong INTEGER CHECK (trong_luong > 0),
    don_gia INTEGER CHECK (don_gia > 0),
    tpdd TEXT,
    loi_ich TEXT,
    hinh VARCHAR(255) DEFAULT 'default.png',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_products_ten ON products(ten_sua);
CREATE INDEX idx_products_mahang ON products(ma_hang_sua);

-- ==================================================
-- TABLE: orders
-- ==================================================

CREATE TABLE orders (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL
        REFERENCES users(id)
        ON DELETE CASCADE,
    total_amount INTEGER NOT NULL CHECK (total_amount >= 0),
    status VARCHAR(20) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_orders_user ON orders(user_id);

-- ==================================================
-- TABLE: order_items
-- ==================================================

CREATE TABLE order_items (
    id SERIAL PRIMARY KEY,
    order_id INTEGER NOT NULL
        REFERENCES orders(id)
        ON DELETE CASCADE,
    product_id INTEGER NOT NULL
        REFERENCES products(id)
        ON DELETE RESTRICT,
    quantity INTEGER NOT NULL CHECK (quantity > 0),
    price INTEGER NOT NULL CHECK (price >= 0),
    subtotal INTEGER NOT NULL CHECK (subtotal >= 0)
);

CREATE INDEX idx_order_items_order ON order_items(order_id);
CREATE INDEX idx_order_items_product ON order_items(product_id);

-- ==================================================
-- INSERT DATA
-- ==================================================

INSERT INTO hang_sua VALUES
('AB','Abbott','Công ty nhập khẩu Việt Nam','8741258','abbott@ab.com'),
('DL','Dutch Lady','Khu công nghiệp Biên Hòa - Đồng Nai','7826451','dutchlady@dl.com'),
('DM','Dumex','Khu công nghiệp Sóng Thần Bình Dương','6258943','dumex@dm.com'),
('DS','Daisy','Khu công nghiệp Sóng Thần Bình Dương','5789321','daisy@ds.com'),
('MJ','Mead Johnson','Công ty nhập khẩu Việt Nam','8741258','meadjohn@mj.com'),
('NTF','Nutifood','Khu công nghiệp Sóng Thần Bình Dương','7895632','nutifood@ntf.com'),
('VNM','Vinamilk','123 Nguyễn Du - Quận 1 - TP.HCM','8794561','vinamilk@vnm.com');

INSERT INTO products (ten_sua, ma_hang_sua, loai_sua, trong_luong, don_gia, hinh) VALUES
('Sữa bột Abbott Grow 3','AB','Sữa bột',900,320000,'abbott_grow_3.png'),
('Sữa Cô Gái Hà Lan (Dutch Lady) Không Đường','DL','Sữa nước',180,7500,'dutch_lady_no_sugar.png'),
('Sữa bột Dumex Gold Step 2','DM','Sữa bột',800,450000,'dumex_gold_2.png'),
('Sữa Daisy Tiệt Trùng','DS','Sữa bột',400,120000,'daisy_milk.png'),
('Sữa Enfamil A+ 1','MJ','Sữa bột',900,560000,'enfamil_a1.png'),
('Sữa Nutifood Grow Plus Đỏ','NTF','Sữa bột',900,380000,'nutifood_red.png'),
('Sữa Tươi Tiệt Trùng Vinamilk ADM','VNM','Sữa nước',110,6500,'vnm_adm.png');
