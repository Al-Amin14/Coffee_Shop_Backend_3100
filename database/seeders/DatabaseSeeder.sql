INSERT INTO `users` 
(`name`, `email`, `contact_number`, `email_verified_at`, `password`, `role`, `remember_token`, `created_at`, `updated_at`) 
VALUES
('Alice Johnson', 'alice@example.com', '12345678901', NOW(), '$2y$10$e0NR3lUEKjRWPaXH1PqL.ekB7Q9/sZmrbPvN0fPMAhKdW7opUy24m', 'Customer', NULL, NOW(), NOW()),
('Bob Smith', 'bob@example.com', '98765432109', NOW(), '$2y$10$e0NR3lUEKjRWPaXH1PqL.ekB7Q9/sZmrbPvN0fPMAhKdW7opUy24m', 'Admin', NULL, NOW(), NOW()),
('Carol Davis', 'carol@example.com', '11223344556', NOW(), '$2y$10$e0NR3lUEKjRWPaXH1PqL.ekB7Q9/sZmrbPvN0fPMAhKdW7opUy24m', 'Customer', NULL, NOW(), NOW());
