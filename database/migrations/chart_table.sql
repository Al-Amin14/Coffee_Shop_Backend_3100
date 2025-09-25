//cart table
CREATE DATABASE IF NOT EXISTS user DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE user;

DROP TABLE IF EXISTS carts;

CREATE TABLE carts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    quantity INT UNSIGNED NOT NULL DEFAULT 1,
    unit_price DECIMAL(10, 2) NOT NULL,
    total_price DECIMAL(10, 2) GENERATED ALWAYS AS (quantity * unit_price) STORED,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);


ALTER TABLE carts
ADD CONSTRAINT fk_carts_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE;

ALTER TABLE carts
ADD CONSTRAINT fk_carts_product FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE;

SELECT 
    c.id AS cart_id,
    c.user_id,
    u.name AS user_name,
    c.product_id,
    p.name AS product_name,
    c.quantity,
    c.unit_price,
    c.total_price,
    c.created_at,
    c.updated_at
FROM 
    carts c
JOIN 
    users u ON c.user_id = u.id
JOIN 
    products p ON c.product_id = p.id;

SELECT 
    u.id AS user_id,
    u.name AS user_name,
    (SELECT SUM(c.total_price) 
     FROM carts c 
     WHERE c.user_id = u.id) AS total_spent
FROM 
    users u;

SELECT 
    u.id AS user_id,
    u.name AS user_name,
    c.id AS cart_id,
    p.name AS product_name,
    c.quantity,
    c.unit_price,
    c.total_price,
    (SELECT SUM(c2.total_price) 
     FROM carts c2 
     WHERE c2.user_id = u.id) AS total_spent
FROM 
    carts c
JOIN 
    users u ON c.user_id = u.id
JOIN 
    products p ON c.product_id = p.id;