-- Crumb & Co — Database Schema
-- Import this file via phpMyAdmin (XAMPP) to set up the database.

CREATE DATABASE IF NOT EXISTS crumb_co CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE crumb_co;

-- ---------------------------------------------------------
-- Products: today's menu (cakes, pastries, cookies)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  category VARCHAR(30) NOT NULL,      -- 'cakes' | 'pastries' | 'cookies'
  tag VARCHAR(30) NOT NULL,           -- display label, e.g. 'Cakes'
  price INT NOT NULL,                 -- price in PKR
  description TEXT,
  color1 VARCHAR(7) DEFAULT '#f2c9c2',-- gradient fallback color (used if no photo)
  color2 VARCHAR(7) DEFAULT '#c65d6c',
  size VARCHAR(10) DEFAULT 'small',   -- bento grid size: small | big | wide
  image VARCHAR(255),                 -- relative path, e.g. assets/images/salted-caramel-cake.jpg
  is_available TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ---------------------------------------------------------
-- Orders: one row per checkout / confirmed order
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  customer_name VARCHAR(150) DEFAULT NULL,
  customer_phone VARCHAR(30) DEFAULT NULL,
  delivery_address VARCHAR(255) DEFAULT NULL,
  payment_method VARCHAR(30) DEFAULT 'cod',  -- cod | jazzcash | easypaisa | card
  total INT NOT NULL,
  status VARCHAR(20) DEFAULT 'pending', -- pending | confirmed | delivered
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ---------------------------------------------------------
-- Order items: each product (or custom cake) inside an order
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT DEFAULT NULL,        -- NULL for custom cakes
  product_name VARCHAR(150) NOT NULL,
  quantity INT NOT NULL DEFAULT 1,
  price INT NOT NULL,                 -- price per unit at time of order
  is_custom TINYINT(1) DEFAULT 0,
  custom_base VARCHAR(50) DEFAULT NULL,
  custom_decor VARCHAR(50) DEFAULT NULL,
  custom_message VARCHAR(100) DEFAULT NULL,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
);

-- ---------------------------------------------------------
-- Seed data: today's 9 menu items
-- ---------------------------------------------------------
INSERT INTO products (name, category, tag, price, description, color1, color2, size, image) VALUES
('Salted Caramel Layer Cake', 'cakes', 'Cakes', 2400, 'Four layers, salted caramel drip, a little too much frosting on purpose.', '#f2c9c2', '#c65d6c', 'big', 'assets/images/salted-caramel-cake.jpg'),
('Strawberry Cake', 'cakes', 'Cakes', 2200, 'Baked cheesecake layered with strawberry and a saffron drizzle.', '#e7c26b', '#9c3f4c', 'small', 'assets/images/strawberry-cake.jpg'),
('Kesar Pista Cake', 'cakes', 'Cakes', 2600, 'Saffron-soaked sponge, crushed pistachio, a hint of cardamom.', '#d9a441', '#7c8b6f', 'wide', 'assets/images/pista-cake.jpg'),
('Brown Butter Croissant', 'pastries', 'Pastries', 350, 'Laminated over three days, baked at 9am.', '#e8a6ad', '#c65d6c', 'small', 'assets/images/corrisont.jpg'),
('Cardamom Cinnamon Roll', 'pastries', 'Pastries', 420, 'Cardamom in the dough, not just the glaze.', '#e7c26b', '#c9986b', 'small', 'assets/images/Cardamom-Cinnamon-Roll.jpg'),
('Mango Cream Tart', 'pastries', 'Pastries', 480, 'Seasonal mango, light cream, a shortcrust base that stays crisp.', '#8a95a6', '#3f2e29', 'big', 'assets/images/mango-tart-cake.jpg'),
('Brown Butter Chocolate Chip', 'cookies', 'Cookies', 220, 'Crisp edge, molten center, browned butter instead of melted.', '#c9986b', '#9c3f4c', 'small', 'assets/images/Brown-Butter-Chocolate-Chip.jpg'),
('Pistachio Shortbread', 'cookies', 'Cookies', 200, 'Ground pistachio through the whole dough.', '#7c8b6f', '#3f2e29', 'small', 'assets/images/Pistachio-Shortbread.jpg'),
('Orange Cardamom Biscotti', 'cookies', 'Cookies', 180, 'Twice-baked, built for dunking, keeps for weeks.', '#e7c26b', '#c65d6c', 'small', 'assets/images/Orange-Cardamom-Biscotti.jpg');
