
CREATE DATABASE IF NOT EXISTS coffeeshop DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE coffeeshop;

DROP TABLE IF EXISTS confirmed_orders;

CREATE TABLE `confirmed_orders` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `order_id` BIGINT UNSIGNED NOT NULL,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `payment_status` ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
    `payment_method` ENUM('cod', 'card', 'bkash', 'nagad', 'rocket') DEFAULT 'cod',
    `confirmed_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `delivery_address` TEXT NOT NULL,
    `delivery_status` ENUM('pending', 'shipped', 'delivered', 'returned') DEFAULT 'pending',
    `tracking_number` VARCHAR(100) DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Add constraints separately
-- ALTER TABLE `confirmed_orders`
--     ADD CONSTRAINT `fk_confirmed_order`
--         FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE;

-- ALTER TABLE `confirmed_orders`
--     ADD CONSTRAINT `fk_confirmed_user`
--         FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE;
