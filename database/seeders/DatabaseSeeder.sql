-- INSERT INTO `users`
-- (`name`, `email`, `contact_number`, `email_verified_at`, `password`, `role`, `remember_token`, `created_at`, `updated_at`)
-- VALUES
-- ('Alice Johnson', 'alice@example.com', '12345678901', NOW(), '$2y$10$e0NR3lUEKjRWPaXH1PqL.ekB7Q9/sZmrbPvN0fPMAhKdW7opUy24m', 'Customer', NULL, NOW(), NOW()),
-- ('Bob Smith', 'bob@example.com', '98765432109', NOW(), '$2y$10$e0NR3lUEKjRWPaXH1PqL.ekB7Q9/sZmrbPvN0fPMAhKdW7opUy24m', 'Admin', NULL, NOW(), NOW()),
-- ('Carol Davis', 'carol@example.com', '11223344556', NOW(), '$2y$10$e0NR3lUEKjRWPaXH1PqL.ekB7Q9/sZmrbPvN0fPMAhKdW7opUy24m', 'Customer', NULL, NOW(), NOW());

-- INSERT INTO `products`
-- (`product_name`, `description`, `category`, `price`, `discount`, `stock_quantity`, `unit`, `is_available`, `image_path`, `created_at`, `updated_at`)
-- VALUES
-- ('Espresso Coffee', 'Strong and bold espresso coffee', 'Beverage', 120.00, 10.00, 50, 'cup', 1, 'images/espresso.jpg', NOW(), NOW()),
-- ('Cappuccino', 'Creamy cappuccino with milk froth', 'Beverage', 150.00, 15.00, 40, 'cup', 1, 'images/cappuccino.jpg', NOW(), NOW()),
-- ('Chocolate Muffin', 'Delicious chocolate muffin', 'Bakery', 80.00, 5.00, 30, 'pcs', 1, 'images/choco_muffin.jpg', NOW(), NOW()),
-- ('Blueberry Cake', 'Fresh blueberry cake slice', 'Bakery', 200.00, 20.00, 20, 'slice', 1, 'images/blueberry_cake.jpg', NOW(), NOW()),
-- ('Green Tea', 'Refreshing green tea', 'Beverage', 90.00, NULL, 60, 'cup', 1, 'images/green_tea.jpg', NOW(), NOW());

INSERT INTO
    `salary` (
        `user_id`,
        `salary`,
        `bonus`,
        `created_at`,
        `updated_at`
    )
VALUES (
        1,
        30000.00,
        2000.00,
        NOW(),
        NOW()
    ),
    (
        2,
        25000.00,
        1500.00,
        NOW(),
        NOW()
    ),
    (
        3,
        28000.00,
        1000.00,
        NOW(),
        NOW()
    ),
    (
        4,
        32000.00,
        2500.00,
        NOW(),
        NOW()
    );