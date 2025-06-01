INSERT INTO users (id, name, email, password, role, profile_pic, contact_number, address) VALUES
(1, 'Geykson', 'geykson@gmail.com', '$2y$10$QNSYFeBiSe0/5Xc5zr8W..EWoXfSStyN93..WAgu4wAwRSrx.T.p2', 'admin', '683bd48066075_monkey-man-with-thumb-up_1368-7198.avif', '', ''),
(2, 'Seller Geyk', 'seller@gmail.com', '$2y$10$1F.2fmJ01tQc3dIrfp1.kO5ELrdyaor0eBQjl12suSJWlzTAFhOQW', 'seller', '683bd04d9f77c_man-with-beard-avatar-character-isolated-icon-free-vector.jpg', '09123456', 'Test Address'),
(3, 'Test Customer', 'customer@gmail.com', '$2y$10$TJIwJ.6UuqrJPusnXkq4Je55ZerLD4IEpgUZGal5BKVq3nI0L20y.', 'customer', '683bd3cb235d2_images.jpg', '09123452', 'wew');

INSERT INTO products (id, seller_id, name, description, price, stock, image) VALUES
(2, 2, 'Test 001', 'Sample product', 25.00, 94, NULL),
(3, 2, 'soap', 'wew', 25.00, 6, '683bd01655b73_pink-soap-bar-transparent-background_1094163-56733.avif');

-- Add similar insert queries for orders and notifications
