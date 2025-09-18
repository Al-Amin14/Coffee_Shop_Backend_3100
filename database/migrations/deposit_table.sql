CREATE DATABASE IF NOT EXISTS coffeeshop DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE coffeeshop;

CREATE TABLE `deposits` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `amount` DECIMAL(10, 2) NOT NULL,
    `session_id` VARCHAR(255) NOT NULL UNIQUE,
    `status` VARCHAR(255) NOT NULL DEFAULT 'Pending',
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    CONSTRAINT `deposits_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
)