CREATE DATABASE IF NOT EXISTS user DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE user;

DROP TABLE IF EXISTS products;

CREATE TABLE `products` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `product_name` VARCHAR(100) NOT NULL,
    `description` VARCHAR(255) NULL,
    `category` VARCHAR(50) NOT NULL,
    `price` DECIMAL(10, 2) NOT NULL,
    `discount` DECIMAL(10, 2) NULL,
    `stock_quantity` INT NOT NULL,
    `unit` VARCHAR(20) NULL,
    `is_available` TINYINT(1) NOT NULL DEFAULT 1,
    `image_path` VARCHAR(255) NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL
);

ALTER TABLE `products` ADD CONSTRAINT `price` CHECK (`price` > 50);

ALTER TABLE `products`
ADD CONSTRAINT `stock_quantity` CHECK (`stock_quantity` > 1);