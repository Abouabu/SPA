-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 11, 2025 at 08:03 PM
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
-- Database: `beauty_book`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `sub_service_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `booking_date` date NOT NULL,
  `booking_time` time NOT NULL DEFAULT '09:00:00',
  `status` enum('pending','completed') NOT NULL DEFAULT 'pending',
  `time` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `sub_service_id`, `service_id`, `price`, `booking_date`, `booking_time`, `status`, `time`, `created_at`) VALUES
(1, 2, 20, 2, 1500.00, '2025-04-14', '09:00:00', 'completed', '2025-04-13 08:55:16', '2025-04-13 08:59:43'),
(2, 4, 45, 4, 7000.00, '2025-04-14', '09:00:00', 'pending', '2025-04-13 09:30:20', '2025-04-13 09:30:20');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `image_url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `name`, `description`, `image_url`) VALUES
(1, 'Haircuts', 'Professional haircuts for all hair types', 'images/haircut.jpg'),
(2, 'Manicures', 'Relaxing and stylish manicures', 'images/manicure.jpg'),
(3, 'Facials', 'Rejuvenating facial treatments', 'images/facial.jpg'),
(4, 'Massages', 'Relaxing massages to relieve stress', 'images/massage.jpg'),
(5, 'Plaiting', 'Different hairstyles', 'images/plaiting.jpg'),
(6, 'Makeup', 'Professional makeup for any occasion', 'images/makeup.jpg'),
(7, 'Beauty Products', 'We also sell Beauty products and services', 'images/products.jpg'),
(8, 'Pedicures', 'Pampering pedicures for your feet', 'images/pedicure.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `sub_services`
--

CREATE TABLE `sub_services` (
  `id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sub_services`
--

INSERT INTO `sub_services` (`id`, `service_id`, `name`, `price`) VALUES
(1, 1, 'Buzz cut', 800.00),
(2, 1, 'Fade cut', 500.00),
(3, 1, 'Undercut', 600.00),
(4, 1, 'Layered cut', 500.00),
(5, 1, 'Bob cut', 700.00),
(6, 1, 'Crew cut', 500.00),
(7, 1, 'Mohawk', 500.00),
(8, 3, 'Deep cleansing', 2500.00),
(9, 3, 'Hydrating', 2000.00),
(10, 3, 'Anti-aging', 3500.00),
(11, 3, 'Brightening', 1500.00),
(12, 3, 'Acne treatment', 4500.00),
(13, 3, 'Oxygen', 4500.00),
(14, 3, 'Microdermabrasion', 3000.00),
(15, 2, 'French Manicure', 1500.00),
(16, 2, 'Gel Manicure', 1000.00),
(17, 2, 'Acrylic Manicure', 2500.00),
(18, 2, 'Dip Powder Nails', 1500.00),
(19, 2, 'Ombre Manicure', 800.00),
(20, 2, 'Paraffin Wax Treatment', 1500.00),
(21, 2, 'Classic Manicure', 1000.00),
(22, 8, 'French Pedicure', 1800.00),
(23, 8, 'Gel Pedicure', 1200.00),
(24, 8, 'Spa Pedicure', 2000.00),
(25, 8, 'Medical Pedicure', 2500.00),
(26, 8, 'Paraffin Pedicure', 1800.00),
(27, 8, 'Classic Pedicure', 1200.00),
(28, 5, 'Box braids', 1000.00),
(29, 5, 'Cornrows', 1000.00),
(30, 5, 'Knotless braids', 1500.00),
(31, 5, 'Goddess braids', 2000.00),
(32, 5, 'Fulani and lemonade braids', 1800.00),
(33, 5, 'Passion twist', 2000.00),
(34, 5, 'Invisible locs', 1800.00),
(35, 6, 'Natural', 1000.00),
(36, 6, 'Glam', 2500.00),
(37, 6, 'Bridal', 6500.00),
(38, 6, 'Matte finish', 2500.00),
(39, 6, 'Dewy', 1800.00),
(40, 6, 'Smokey eye', 3000.00),
(41, 6, 'Cut crease', 3500.00),
(42, 4, 'Swedish', 4000.00),
(43, 4, 'Deep tissue', 6000.00),
(44, 4, 'Hot stone', 7500.00),
(45, 4, 'Aromatherapy', 7000.00),
(46, 4, 'Thai', 6500.00),
(47, 4, 'Reflexology', 8500.00),
(48, 4, 'Prenatal', 5000.00),
(49, 7, 'Foundation', 1000.00),
(50, 7, 'Lipstick and lipgloss', 500.00),
(51, 7, 'Setting spray', 1500.00),
(52, 7, 'Skin care serum', 2500.00),
(53, 7, 'Nail polish', 150.00),
(54, 7, 'Nail gel', 450.00),
(55, 7, 'Eye lashes', 200.00),
(56, 7, 'Stick-on nails and glue', 700.00),
(57, 7, 'Make-up kit', 6500.00);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `role` enum('admin','client') DEFAULT 'client',
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `phone`, `role`, `password`, `created_at`) VALUES
(2, 'massy_b', 'massy@spa.com', '0700224455', 'client', '$2y$10$2n2wsGFG69kNp.fTFz1PIefn9coIrbK63wQdzJ06O5iwo1X9xN6xK', '2025-04-13 08:59:26'),
(3, 'admin', 'admin@spa.com', '0110000891', 'admin', '$2y$10$JTYdw53P7xnJz5KlBbOnAeowHDivA31rU3qhw2/xejqfQ5gTD5sWq', '2025-04-13 08:59:26'),
(4, 'example', 'example@spa.com', '0123456789', 'client', '$2y$10$w.9NPV/0FjduQ5DGPvNznuuhaOCg9giNoJaK6qfBXB5fkwYVan1TO', '2025-04-13 09:29:53');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `sub_service_id` (`sub_service_id`),
  ADD KEY `service_id` (`service_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sub_services`
--
ALTER TABLE `sub_services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_id` (`service_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `sub_services`
--
ALTER TABLE `sub_services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`sub_service_id`) REFERENCES `sub_services` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sub_services`
--
ALTER TABLE `sub_services`
  ADD CONSTRAINT `sub_services_ibfk_1` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
