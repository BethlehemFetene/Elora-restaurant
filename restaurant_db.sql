-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 25, 2026 at 10:48 AM
-- Server version: 8.2.0
-- PHP Version: 8.2.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `restaurant_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE `menu_items` (
  `id` int NOT NULL,
  `food_name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`id`, `food_name`, `description`, `price`, `image`) VALUES
(1, 'Burger', 'Delicious beef burger', 250.00, 'burger.jpg'),
(2, 'Pizza', 'Cheesy pizza with toppings', 500.00, 'pizza.jpg'),
(3, 'Pasta', 'Creamy chicken pasta', 350.00, 'pasta.jpg'),
(4, 'Coffee', 'Hot Ethiopian coffee', 120.00, 'coffee.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `food_name` varchar(100) NOT NULL,
  `quantity` int NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `order_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

CREATE TABLE `ratings` (
  `id` int NOT NULL,
  `customer_name` varchar(100) DEFAULT NULL,
  `food_name` varchar(100) DEFAULT NULL,
  `rating` int DEFAULT NULL,
  `review` text
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `restaurant_tables`
--

CREATE TABLE `restaurant_tables` (
  `id` int NOT NULL,
  `table_number` int NOT NULL,
  `capacity` int NOT NULL DEFAULT 2,
  `location` varchar(50) NOT NULL DEFAULT 'center',
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `restaurant_tables` (`id`, `table_number`, `capacity`, `location`, `is_active`) VALUES
(1, 1, 2, 'window', 1),
(2, 2, 2, 'window', 1),
(3, 3, 4, 'window', 1),
(4, 4, 4, 'center', 1),
(5, 5, 4, 'center', 1),
(6, 6, 4, 'center', 1),
(7, 7, 6, 'center', 1),
(8, 8, 6, 'patio', 1),
(9, 9, 6, 'patio', 1),
(10, 10, 8, 'patio', 1),
(11, 11, 8, 'private', 1),
(12, 12, 2, 'private', 1);

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `id` int NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `reservation_date` date NOT NULL,
  `reservation_time` time NOT NULL,
  `guests` int NOT NULL,
  `table_id` int DEFAULT NULL,
  `occasion` varchar(50) DEFAULT 'none',
  `special_request` text,
  `status` enum('pending','confirmed','cancelled') DEFAULT 'pending'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL,
    otp_code VARCHAR(10) NOT NULL,
    expires_at DATETIME NOT NULL,
    used TINYINT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_otp (otp_code)
);
--
-- Indexes for dumped tables
--

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `restaurant_tables`
--
ALTER TABLE `restaurant_tables`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `users` ADD COLUMN role ENUM('customer','admin') DEFAULT 'customer';
ALTER TABLE `users` ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

SELECT @rownum := @rownum + 1 AS display_id, u.* 
FROM users u, (SELECT @rownum := 0) r
ORDER BY u.id;
--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ratings`
--
ALTER TABLE `ratings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `restaurant_tables`
--
ALTER TABLE `restaurant_tables`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;
-- --------------------------------------------------------
-- Upgrades for existing databases (safe to re-run errors on duplicate column)
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `restaurant_tables` (
  `id` int NOT NULL AUTO_INCREMENT,
  `table_number` int NOT NULL,
  `capacity` int NOT NULL DEFAULT 2,
  `location` varchar(50) NOT NULL DEFAULT 'center',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `restaurant_tables` (`table_number`, `capacity`, `location`)
SELECT * FROM (
  SELECT 1, 2, 'window' UNION ALL
  SELECT 2, 2, 'window' UNION ALL
  SELECT 3, 4, 'window' UNION ALL
  SELECT 4, 4, 'center' UNION ALL
  SELECT 5, 4, 'center' UNION ALL
  SELECT 6, 4, 'center' UNION ALL
  SELECT 7, 6, 'center' UNION ALL
  SELECT 8, 6, 'patio' UNION ALL
  SELECT 9, 6, 'patio' UNION ALL
  SELECT 10, 8, 'patio' UNION ALL
  SELECT 11, 8, 'private' UNION ALL
  SELECT 12, 2, 'private'
) AS seed
WHERE NOT EXISTS (SELECT 1 FROM `restaurant_tables` LIMIT 1);

ALTER TABLE `reservations`
  ADD COLUMN `table_id` int DEFAULT NULL,
  ADD COLUMN `occasion` varchar(50) DEFAULT 'none',
  ADD COLUMN `special_request` text,
  ADD COLUMN `status` enum('pending','confirmed','cancelled') DEFAULT 'pending';

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
