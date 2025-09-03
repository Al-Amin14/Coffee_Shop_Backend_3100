CREATE DATABASE IF NOT EXISTS coffeeshop DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;


USE coffeeshop;


DROP TABLE IF EXISTS orders;

CREATE TABLE `orders` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `product_name` VARCHAR(255) NOT NULL,
    `quantity` INT UNSIGNED NOT NULL DEFAULT 1,
    `total_price` DECIMAL(10, 2) NOT NULL,
    `image_path` VARCHAR(255) DEFAULT NULL, 
    `status` ENUM('pending', 'processing', 'completed', 'cancelled') DEFAULT 'pending',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
);
