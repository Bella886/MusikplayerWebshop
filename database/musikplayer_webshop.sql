-- Database creation for MusikplayerWebshop
CREATE DATABASE IF NOT EXISTS `musikplayer_webshop`;
USE `musikplayer_webshop`;

-- Users table
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` enum('Mr','Mrs','Ms') NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `address` varchar(255) NOT NULL,
  `postal_code` varchar(20) NOT NULL,
  `city` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL UNIQUE,
  `username` varchar(50) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Payment methods table
CREATE TABLE IF NOT EXISTS `payment_methods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `type` enum('Credit Card','PayPal','Bank Transfer') NOT NULL,
  `card_number` varchar(255) DEFAULT NULL,
  `card_holder` varchar(100) DEFAULT NULL,
  `expiry_date` varchar(10) DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Product categories table
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Products table
CREATE TABLE IF NOT EXISTS `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `rating` decimal(3,1) DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Shopping cart table
CREATE TABLE IF NOT EXISTS `cart_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(5) NOT NULL DEFAULT 1,
  `added_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Coupons table
CREATE TABLE IF NOT EXISTS `coupons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(5) NOT NULL UNIQUE,
  `value` decimal(10,2) NOT NULL,
  `expiry_date` date NOT NULL,
  `is_redeemed` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Orders table
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `payment_method_id` int(11) DEFAULT NULL,
  `coupon_id` int(11) DEFAULT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('Pending','Processing','Shipped','Delivered','Cancelled') NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`payment_method_id`) REFERENCES `payment_methods` (`id`) ON DELETE SET NULL,
  FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Order items table
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(5) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `is_visible` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Session tokens table for "remember me" functionality
CREATE TABLE IF NOT EXISTS `session_tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert admin user (password: admin123)
INSERT INTO `users` (`title`, `first_name`, `last_name`, `address`, `postal_code`, `city`, `email`, `username`, `password`, `is_admin`)
VALUES ('Mr', 'Admin', 'User', 'Admin Street 1', '12345', 'Admin City', 'admin@musikplayer.com', 'admin', '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 1);

-- Insert sample categories
INSERT INTO `categories` (`name`, `description`)
VALUES 
('Music Players', 'Portable music players for every occasion'),
('Headphones', 'Premium quality headphones for the best audio experience'),
('Accessories', 'Cables, cases and other accessories for your music devices');

-- Insert sample products
INSERT INTO `products` (`category_id`, `name`, `description`, `price`, `rating`, `image`)
VALUES 
(1, 'SoundMaster X3', 'High-resolution portable music player with 128GB storage', 299.99, 4.7, 'player_x3.jpg'),
(1, 'MeloPlay Mini', 'Ultra-compact music player with 24-hour battery life', 149.99, 4.2, 'player_mini.jpg'),
(2, 'BassBeat Pro', 'Over-ear noise cancelling headphones', 179.99, 4.8, 'headphones_pro.jpg'),
(2, 'AudioBuds Elite', 'True wireless earbuds with superior sound quality', 129.99, 4.5, 'earbuds_elite.jpg'),
(3, 'Premium Audio Cable', 'Gold-plated 3.5mm audio cable, 1.5m length', 24.99, 4.3, 'cable_premium.jpg'),
(3, 'SoundMaster X3 Case', 'Protective case for SoundMaster X3 music player', 34.99, 4.6, 'case_x3.jpg'); 