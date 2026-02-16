-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 14, 2026 at 05:05 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `56food`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `description`, `ip_address`, `created_at`) VALUES
(1, 2, 'VIEW_ORDERS', 'User viewed their orders', '127.0.0.1', '2026-02-06 14:08:10'),
(2, 2, 'VIEW_ORDERS', 'User viewed their orders', '127.0.0.1', '2026-02-06 14:08:17'),
(3, 2, 'CANCEL_ORDER', 'Cancelled order ID #4', '127.0.0.1', '2026-02-06 14:08:17'),
(4, 2, 'VIEW_ORDERS', 'User viewed their orders', '127.0.0.1', '2026-02-06 14:08:32'),
(5, 2, 'LOGOUT', 'User logged out of the system', '127.0.0.1', '2026-02-06 15:20:59'),
(6, 2, 'LOGIN', 'User logged into the system', '127.0.0.1', '2026-02-06 15:21:02'),
(7, 2, 'LOGOUT', 'User logged out of the system', '127.0.0.1', '2026-02-06 15:29:39'),
(8, 2, 'LOGIN', 'User logged into the system', '127.0.0.1', '2026-02-06 15:29:41'),
(9, 2, 'LOGOUT', 'User logged out of the system', '127.0.0.1', '2026-02-06 15:29:52'),
(10, 1, 'LOGIN', 'User logged into the system', '127.0.0.1', '2026-02-10 05:45:18'),
(11, 1, 'VIEW_ORDERS', 'User viewed their orders', '127.0.0.1', '2026-02-10 05:51:41'),
(12, 1, 'VIEW_ORDERS', 'User viewed their orders', '127.0.0.1', '2026-02-10 05:51:44'),
(13, 1, 'CANCEL_ORDER', 'Cancelled order ID #2', '127.0.0.1', '2026-02-10 05:51:44'),
(14, 1, 'LOGOUT', 'User logged out of the system', '127.0.0.1', '2026-02-10 05:52:46'),
(15, 2, 'LOGIN', 'User logged into the system', '127.0.0.1', '2026-02-10 12:41:45'),
(16, 2, 'LOGOUT', 'User logged out of the system', '127.0.0.1', '2026-02-10 12:44:19'),
(17, 2, 'LOGIN', 'User logged into the system', '127.0.0.1', '2026-02-11 09:35:13'),
(18, 2, 'LOGOUT', 'User logged out of the system', '127.0.0.1', '2026-02-11 09:36:49'),
(19, 2, 'LOGIN', 'User logged into the system', '127.0.0.1', '2026-02-11 09:54:36'),
(20, 2, 'LOGOUT', 'User logged out of the system', '127.0.0.1', '2026-02-11 10:25:26'),
(21, 2, 'LOGIN', 'User logged into the system', '127.0.0.1', '2026-02-11 10:27:36'),
(22, 2, 'LOGOUT', 'User logged out of the system', '127.0.0.1', '2026-02-11 10:28:59'),
(23, 2, 'LOGIN', 'User logged into the system', '127.0.0.1', '2026-02-11 10:29:19'),
(24, 2, 'LOGOUT', 'User logged out of the system', '127.0.0.1', '2026-02-11 10:29:56'),
(25, 2, 'LOGIN', 'User logged into the system', '127.0.0.1', '2026-02-11 10:30:10'),
(26, 2, 'LOGOUT', 'User logged out of the system', '127.0.0.1', '2026-02-11 10:31:54'),
(27, 2, 'LOGIN', 'User logged into the system', '127.0.0.1', '2026-02-11 10:34:34'),
(28, 2, 'LOGOUT', 'User logged out of the system', '127.0.0.1', '2026-02-11 10:35:03'),
(29, 2, 'LOGIN', 'User logged into the system', '127.0.0.1', '2026-02-13 17:08:28'),
(30, 2, 'LOGOUT', 'User logged out of the system', '127.0.0.1', '2026-02-13 17:08:30'),
(31, 1, 'LOGIN', 'User logged into the system', '127.0.0.1', '2026-02-13 17:08:44'),
(32, 1, 'VIEW_ORDERS', 'User viewed their orders', '127.0.0.1', '2026-02-13 17:16:06'),
(33, 1, 'LOGOUT', 'User logged out of the system', '127.0.0.1', '2026-02-13 17:16:16'),
(34, 2, 'LOGIN', 'User logged into the system', '127.0.0.1', '2026-02-13 17:16:20'),
(35, 2, 'LOGOUT', 'User logged out of the system', '127.0.0.1', '2026-02-13 17:16:31'),
(36, 1, 'VIEW_ORDERS', 'User viewed their orders', '127.0.0.1', '2026-02-13 18:14:37'),
(37, 1, 'LOGOUT', 'User logged out of the system', '127.0.0.1', '2026-02-13 18:24:48'),
(38, 1, 'LOGOUT', 'User logged out of the system', '127.0.0.1', '2026-02-13 18:31:01'),
(39, 2, 'LOGOUT', 'User logged out of the system', '127.0.0.1', '2026-02-13 18:35:12'),
(40, 4, 'LOGOUT', 'User logged out of the system', '127.0.0.1', '2026-02-13 18:35:53'),
(41, 4, 'LOGOUT', 'User logged out of the system', '127.0.0.1', '2026-02-13 18:36:23'),
(42, 4, 'LOGOUT', 'User logged out of the system', '127.0.0.1', '2026-02-13 18:38:04'),
(43, 5, 'VIEW_ORDERS', 'User viewed their orders', '127.0.0.1', '2026-02-13 18:39:19'),
(44, 5, 'LOGOUT', 'User logged out of the system', '127.0.0.1', '2026-02-13 18:39:24'),
(45, 4, 'LOGOUT', 'User logged out of the system', '127.0.0.1', '2026-02-13 19:10:51'),
(46, 4, 'LOGOUT', 'User logged out of the system', '127.0.0.1', '2026-02-13 19:49:59'),
(47, 1, 'LOGOUT', 'User logged out of the system', '127.0.0.1', '2026-02-13 19:50:23'),
(48, 4, 'LOGOUT', 'User logged out of the system', '127.0.0.1', '2026-02-13 19:50:55'),
(49, 4, 'LOGOUT', 'User logged out of the system', '127.0.0.1', '2026-02-13 19:51:27'),
(50, 1, 'LOGOUT', 'User logged out of the system', '127.0.0.1', '2026-02-13 19:52:29'),
(51, 4, 'LOGOUT', 'User logged out of the system', '127.0.0.1', '2026-02-14 11:12:22'),
(52, 1, 'VIEW_ORDERS', 'User viewed their orders', '127.0.0.1', '2026-02-14 11:22:45'),
(53, 1, 'VIEW_ORDERS', 'User viewed their orders', '127.0.0.1', '2026-02-14 11:22:48'),
(54, 1, 'LOGOUT', 'User logged out of the system', '127.0.0.1', '2026-02-14 11:22:58'),
(55, 4, 'LOGOUT', 'User logged out of the system', '127.0.0.1', '2026-02-14 11:24:41'),
(56, 7, 'VIEW_ORDERS', 'User viewed their orders', '127.0.0.1', '2026-02-14 11:25:33'),
(57, 7, 'VIEW_ORDERS', 'User viewed their orders', '127.0.0.1', '2026-02-14 11:50:38'),
(58, 7, 'VIEW_ORDERS', 'User viewed their orders', '127.0.0.1', '2026-02-14 11:51:11'),
(59, 7, 'LOGOUT', 'User logged out of the system', '127.0.0.1', '2026-02-14 11:52:06'),
(60, 4, 'LOGOUT', 'User logged out of the system', '127.0.0.1', '2026-02-14 11:55:04'),
(61, 7, 'LOGOUT', 'User logged out of the system', '127.0.0.1', '2026-02-14 12:05:56');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `menu_id`, `quantity`, `created_at`) VALUES
(11, 2, 1, 2, '2026-02-06 14:08:39');

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE `menu` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` enum('available','unavailable') DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`id`, `name`, `description`, `price`, `image`, `status`, `created_at`) VALUES
(1, 'kisinia', 'kula msosi', 2900.00, 'food_69834e6e92cdc2.14637536.jpg', 'available', '2026-02-04 13:49:34'),
(2, 'kitimoto', 'mtu mmoja', 15000.00, 'food_69835acf0cc8c4.92239070.jpg', 'available', '2026-02-04 14:42:23'),
(3, 'Mandi Kuku', 'MIxer Samaki', 18000.00, 'food_698b280ed4d0b8.31739272.jpg', 'available', '2026-02-10 12:43:58'),
(4, 'Pizza', 'Wonderful Pizza', 20000.00, 'food_698c59110111f5.12375843.jpg', 'available', '2026-02-11 10:25:21'),
(5, 'Nyama Choma', 'Mixer Juice', 12000.00, 'food_698c59e812dc34.90095180.jpg', 'available', '2026-02-11 10:28:56'),
(7, 'Biriani Kuku', 'Msosi mtamu', 12000.00, 'food_698c5a978108d9.25362908.jpg', 'available', '2026-02-11 10:31:51'),
(8, 'Samaki', 'Kitu chamoto', 6000.00, 'food_698c5b54462410.49618049.jpg', 'available', '2026-02-11 10:35:00'),
(45, 'nazi', 'uuuu', 400.00, 'food_698f809cca77d7.05990936.jpg', 'available', '2026-02-13 19:50:52');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','delivered','cancelled') DEFAULT 'pending',
  `order_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `status`, `order_date`) VALUES
(1, 2, 29000.00, 'confirmed', '2026-02-04 14:16:15'),
(2, 1, 145000.00, 'cancelled', '2026-02-04 14:39:24'),
(3, 2, 60000.00, 'confirmed', '2026-02-04 15:18:21'),
(4, 2, 30000.00, 'confirmed', '2026-02-06 13:52:39'),
(5, 5, 108000.00, 'confirmed', '2026-02-13 18:39:12'),
(6, 1, 12000.00, 'confirmed', '2026-02-14 11:22:16'),
(7, 7, 400.00, 'pending', '2026-02-14 11:25:53'),
(8, 7, 400.00, 'confirmed', '2026-02-14 11:50:32'),
(9, 7, 6000.00, 'cancelled', '2026-02-14 11:51:09');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `menu_id`, `quantity`, `price`) VALUES
(1, 1, 1, 1, 29000.00),
(2, 2, 1, 5, 29000.00),
(3, 3, 2, 4, 15000.00),
(4, 4, 2, 2, 15000.00),
(5, 5, 3, 6, 18000.00),
(6, 6, 7, 1, 12000.00),
(7, 7, 45, 1, 400.00),
(8, 8, 45, 1, 400.00),
(9, 9, 8, 1, 6000.00);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('cash','mobile','card') DEFAULT 'cash',
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','completed','failed') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `order_id`, `amount`, `payment_method`, `payment_date`, `status`) VALUES
(1, 1, 29000.00, 'cash', '2026-02-04 14:16:15', 'completed'),
(2, 2, 145000.00, 'mobile', '2026-02-04 14:39:24', 'pending'),
(3, 3, 60000.00, 'card', '2026-02-04 15:18:21', 'completed'),
(4, 4, 30000.00, 'cash', '2026-02-06 13:52:39', 'pending'),
(5, 5, 108000.00, 'cash', '2026-02-13 18:39:12', 'pending'),
(6, 6, 12000.00, 'mobile', '2026-02-14 11:22:16', 'pending'),
(7, 7, 400.00, 'card', '2026-02-14 11:25:53', 'pending'),
(8, 8, 400.00, 'cash', '2026-02-14 11:50:32', 'pending'),
(9, 9, 6000.00, 'cash', '2026-02-14 11:51:09', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','customer') DEFAULT 'customer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `password`, `role`, `created_at`) VALUES
(1, 'Baraka Adam Kapolesya', 'bkapolesya23@gmail.com', '+255628901233', '$2y$10$oSoHp0foB1GwalTj1qwT4efUr.l80X71Dt3.rpXNruwWipqczomBu', 'customer', '2026-02-04 10:31:04'),
(2, 'shedy wilson', 'wilson@gmail.com', NULL, '$2y$10$q9Grs/t8LJ3t.lUXRQ2RxOKA4QO2JNOzJXJKsDpek2fnoDkEfEkTa', 'admin', '2026-02-04 12:43:25'),
(4, 'Funky Adam', 'funky@gmail.com', NULL, '$2y$10$oHK6G2yxe1fSWjOkzsRFz.5hpgQdAvEA8CxS/Uzz7aJpJPQemSX6m', 'admin', '2026-02-13 18:35:39'),
(5, 'amos simbo', 'amossimbo@gmail.com', NULL, '$2y$10$bEZNtPq3T0PamS46aZDyW.1REB6BUcX5shSaUcE9p6Vr7OmbNTZpe', 'customer', '2026-02-13 18:38:26'),
(7, 'Peter Mussa', 'peter@gmail.com', NULL, '$2y$10$Jw9qBSHSYMCV/ocQABiP...y46rWvapJAKUHyj9uoEoWxDm./7WSq', 'customer', '2026-02-14 11:25:14');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`menu_id`),
  ADD KEY `menu_id` (`menu_id`);

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `menu_id` (`menu_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
