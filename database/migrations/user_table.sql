CREATE DATABASE IF NOT EXISTS coffeeshop DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE coffeeshop;

DROP TABLE IF EXISTS users;

CREATE TABLE `users` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `contact_number` VARCHAR(11) NOT NULL,
    `email_verified_at` TIMESTAMP NULL,
    `password` VARCHAR(255) NOT NULL,
    `role` VARCHAR(255) NOT NULL DEFAULT 'Customer',
    `remember_token` VARCHAR(100) NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL
)

ALTER TABLE `users`
ADD CONSTRAINT `chk_email_format` CHECK (`email` LIKE '%@%');

ALTER TABLE `users`
ADD CONSTRAINT `chk_password_length` CHECK (CHAR_LENGTH(`password`) > 6);

ALTER TABLE `users`
ADD CONSTRAINT `chk_contact_number_length` CHECK (
    CHAR_LENGTH(`contact_number`) = 11
);