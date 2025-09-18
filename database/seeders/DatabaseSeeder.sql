INSERT INTO `users`
(`name`, `email`, `contact_number`, `email_verified_at`, `password`, `role`, `remember_token`, `created_at`, `updated_at`)
VALUES
('Alice Johnson', 'alice@example.com', '12345678901', NOW(), '$2y$10$e0NR3lUEKjRWPaXH1PqL.ekB7Q9/sZmrbPvN0fPMAhKdW7opUy24m', 'Customer', NULL, NOW(), NOW()),
('Bob Smith', 'bob@example.com', '98765432109', NOW(), '$2y$10$e0NR3lUEKjRWPaXH1PqL.ekB7Q9/sZmrbPvN0fPMAhKdW7opUy24m', 'Admin', NULL, NOW(), NOW()),
('Carol Davis', 'carol@example.com', '11223344556', NOW(), '$2y$10$e0NR3lUEKjRWPaXH1PqL.ekB7Q9/sZmrbPvN0fPMAhKdW7opUy24m', 'Customer', NULL, NOW(), NOW());

INSERT INTO `products`
(`product_name`, `description`, `category`, `price`, `discount`, `stock_quantity`, `unit`, `is_available`, `image_path`, `created_at`, `updated_at`)
VALUES
('Espresso Coffee', 'Strong and bold espresso coffee', 'Beverage', 120.00, 10.00, 50, 'cup', 1, 'images/espresso.jpg', NOW(), NOW()),
('Cappuccino', 'Creamy cappuccino with milk froth', 'Beverage', 150.00, 15.00, 40, 'cup', 1, 'images/cappuccino.jpg', NOW(), NOW()),
('Chocolate Muffin', 'Delicious chocolate muffin', 'Bakery', 80.00, 5.00, 30, 'pcs', 1, 'images/choco_muffin.jpg', NOW(), NOW()),
('Blueberry Cake', 'Fresh blueberry cake slice', 'Bakery', 200.00, 20.00, 20, 'slice', 1, 'images/blueberry_cake.jpg', NOW(), NOW()),
('Green Tea', 'Refreshing green tea', 'Beverage', 90.00, NULL, 60, 'cup', 1, 'images/green_tea.jpg', NOW(), NOW());


INSERT INTO confirmed_orders 
(order_id, user_id, payment_status, payment_method, confirmed_at, delivery_address, delivery_status, tracking_number) 
VALUES
(1, 1, 'paid', 'card', NOW(), '123 Green Road, Dhaka', 'shipped', 'TRK123456'),
(2, 2, 'pending', 'cod', NOW(), '56/B Gulshan Avenue, Dhaka', 'pending', NULL),
(3, 1, 'paid', 'bkash', NOW(), '12 Lake View, Banani, Dhaka', 'delivered', 'TRK789654'),
(4, 3, 'failed', 'nagad', NOW(), 'House 45, Uttara Sector 7, Dhaka', 'returned', 'TRK456321'),
(5, 2, 'refunded', 'rocket', NOW(), 'Flat 2B, Dhanmondi 32, Dhaka', 'pending', NULL);


INSERT INTO deposits (user_id, amount, session_id, status, created_at, updated_at) 
VALUES
(1, 500.00, 'sess_ABC123', 'Completed', NOW(), NOW()),
(2, 1200.50, 'sess_DEF456', 'Pending', NOW(), NOW()),
(3, 250.75, 'sess_GHI789', 'Failed', NOW(), NOW()),
(1, 800.00, 'sess_JKL012', 'Completed', NOW(), NOW()),
(2, 999.99, 'sess_MNO345', 'Pending', NOW(), NOW());

INSERT INTO manager_confirmed_orders 
(order_id, manager_id, user_id, confirmation_status, remarks, confirmed_at) 
VALUES
(1, 10, 1, 'approved', 'Order verified successfully.', NOW()),
(2, 11, 2, 'rejected', 'Payment issue detected.', NOW()),
(3, 10, 3, 'approved', 'Customer identity verified.', NOW()),
(4, 12, 2, 'approved', 'Special approval granted by manager.', NOW()),
(5, 11, 1, 'rejected', 'Duplicate order flagged.', NOW());
