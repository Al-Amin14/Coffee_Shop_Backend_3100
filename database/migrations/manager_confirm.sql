CREATE DATABASE IF NOT EXISTS coffeeshop DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE coffeeshop;

DROP TABLE IF EXISTS manager_confirmed_orders;

CREATE TABLE `manager_confirmed_orders` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `order_id` BIGINT UNSIGNED NOT NULL,
    `manager_id` BIGINT UNSIGNED NOT NULL,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `confirmation_status` ENUM('approved', 'rejected') DEFAULT 'approved',
    `remarks` TEXT DEFAULT NULL,
    `confirmed_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Now add constraints separately
ALTER TABLE `manager_confirmed_orders`
ADD CONSTRAINT `fk_mgr_confirm_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

ALTER TABLE `manager_confirmed_orders`
ADD CONSTRAINT `fk_mgr_confirm_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `manager_confirmed_orders`
ADD CONSTRAINT `fk_mgr_confirm_manager` FOREIGN KEY (`manager_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;