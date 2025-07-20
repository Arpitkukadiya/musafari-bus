-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 18, 2025 at 05:45 PM
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
-- Database: `booking_bus`
--

-- --------------------------------------------------------

--
-- Table structure for table `booked_seats`
--

CREATE TABLE `booked_seats` (
  `booking_id` int(11) NOT NULL,
  `seat_id` int(11) NOT NULL,
  `bus_id` int(11) NOT NULL,
  `travel_date` date NOT NULL,
  `is_booked` tinyint(1) DEFAULT 1,
  `seat_number` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booked_seats`
--

INSERT INTO `booked_seats` (`booking_id`, `seat_id`, `bus_id`, `travel_date`, `is_booked`, `seat_number`) VALUES
(152, 55, 1, '2025-02-17', 1, NULL),
(153, 56, 1, '2025-02-17', 1, NULL),
(154, 53, 1, '2025-02-18', 1, NULL),
(156, 54, 1, '2025-02-18', 1, NULL),
(157, 60, 1, '2025-02-18', 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `bus_id` int(11) NOT NULL,
  `travel_date` date NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `booking_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `seat_numbers` varchar(255) NOT NULL,
  `source` varchar(100) NOT NULL,
  `destination` varchar(100) NOT NULL,
  `arrival_time` time NOT NULL,
  `status` enum('Confirmed','Cancelled') DEFAULT 'Confirmed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `bus_id`, `travel_date`, `total_price`, `booking_date`, `seat_numbers`, `source`, `destination`, `arrival_time`, `status`) VALUES
(152, 1, 1, '2025-02-17', 200.00, '2025-02-17 12:53:48', '', 'surat', 'rajkot', '00:00:10', 'Confirmed'),
(153, 1, 1, '2025-02-17', 200.00, '2025-02-17 13:00:23', '', 'surat', 'rajkot', '00:00:10', 'Confirmed'),
(154, 1, 1, '2025-02-18', 200.00, '2025-02-17 13:19:14', '', 'surat', 'rajkot', '00:00:10', 'Confirmed'),
(156, 1, 1, '2025-02-18', 200.00, '2025-02-18 16:36:01', '', 'surat', 'rajkot', '00:00:10', 'Confirmed'),
(157, 1, 1, '2025-02-18', 200.00, '2025-02-18 16:42:42', '', 'surat', 'rajkot', '00:00:10', 'Confirmed');

-- --------------------------------------------------------

--
-- Table structure for table `buses`
--

CREATE TABLE `buses` (
  `bus_id` int(11) NOT NULL,
  `bus_name` varchar(255) NOT NULL,
  `source` varchar(255) NOT NULL,
  `destination` varchar(255) NOT NULL,
  `departure_time` time NOT NULL,
  `arrival_time` time NOT NULL,
  `seats_available` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `traveler_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `buses`
--

INSERT INTO `buses` (`bus_id`, `bus_name`, `source`, `destination`, `departure_time`, `arrival_time`, `seats_available`, `price`, `traveler_id`) VALUES
(1, 'Ram Travellers', 'surat', 'rajkot', '10:15:00', '00:00:10', 52, 200.00, 3),
(2, 'travellers', 'amreli', 'Gandhinagar ', '00:01:00', '23:58:00', 52, 200.00, 5),
(3, 'zeel travels', 'morbi', 'amreli', '08:56:00', '08:53:00', 52, 200.00, 3),
(7, 'krunal travels', 'morbi', 'gandhinagar', '09:22:00', '09:17:00', 52, 150.00, 5),
(9, 'satish', 'morbi', 'gandhinagar', '16:37:00', '17:37:00', 52, 149.00, 12);

-- --------------------------------------------------------

--
-- Table structure for table `passengers`
--

CREATE TABLE `passengers` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `age` int(11) NOT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `contact_number` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `passengers`
--

INSERT INTO `passengers` (`id`, `booking_id`, `name`, `age`, `gender`, `email`, `contact_number`) VALUES
(164, 152, 'arpit', 18, 'Male', 'meetladola94@Gmail.com', '01234567890'),
(165, 153, 'prince patel', 18, 'Male', 'meet66286@gmail.com', '0123456789'),
(166, 154, 'arpit', 18, 'Male', 'meetladola94@Gmail.com', '123111'),
(168, 156, 'user', 18, 'Male', 'user@Gmail.com', '01234567890'),
(169, 157, 'arpit', 18, 'Male', 'user@Gmail.com', '01234567890');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `cardholder_name` varchar(255) NOT NULL,
  `card_number` varchar(20) NOT NULL,
  `expiry_date` varchar(7) NOT NULL,
  `cvv` varchar(5) NOT NULL,
  `card_type` enum('Credit Card','Debit Card') NOT NULL,
  `payment_status` enum('Pending','Completed','Failed') DEFAULT 'Pending',
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `booking_id`, `cardholder_name`, `card_number`, `expiry_date`, `cvv`, `card_type`, `payment_status`, `payment_date`) VALUES
(65, 152, 'zeel', '1', '1', '1', 'Debit Card', 'Completed', '2025-02-17 12:53:48'),
(66, 153, 'malhar', '123', '123', '123', 'Debit Card', 'Completed', '2025-02-17 13:00:23'),
(67, 154, 'zeel', '123', '123', '123', 'Debit Card', 'Completed', '2025-02-17 13:19:14'),
(69, 156, 'zeel', '1', '1', '1', 'Credit Card', 'Completed', '2025-02-18 16:36:01'),
(70, 157, 'zeel', '1', '1', '1', 'Debit Card', 'Completed', '2025-02-18 16:42:42');

-- --------------------------------------------------------

--
-- Table structure for table `seats`
--

CREATE TABLE `seats` (
  `seat_id` int(11) NOT NULL,
  `bus_id` int(11) NOT NULL,
  `seat_number` varchar(10) NOT NULL,
  `row_number` int(11) NOT NULL,
  `column_number` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `seats`
--

INSERT INTO `seats` (`seat_id`, `bus_id`, `seat_number`, `row_number`, `column_number`) VALUES
(53, 1, 'A1', 1, 1),
(54, 1, 'B1', 1, 2),
(55, 1, 'C1', 1, 3),
(56, 1, 'D1', 1, 4),
(57, 1, 'E1', 1, 5),
(58, 1, 'A2', 2, 1),
(59, 1, 'B2', 2, 2),
(60, 1, 'C2', 2, 3),
(61, 1, 'D2', 2, 4),
(62, 1, 'E2', 2, 5),
(63, 1, 'A3', 3, 1),
(64, 1, 'B3', 3, 2),
(65, 1, 'C3', 3, 3),
(66, 1, 'D3', 3, 4),
(67, 1, 'E3', 3, 5),
(68, 1, 'A4', 4, 1),
(69, 1, 'B4', 4, 2),
(70, 1, 'C4', 4, 3),
(71, 1, 'D4', 4, 4),
(72, 1, 'E4', 4, 5),
(73, 1, 'A5', 5, 1),
(74, 1, 'B5', 5, 2),
(75, 1, 'C5', 5, 3),
(76, 1, 'D5', 5, 4),
(77, 1, 'E5', 5, 5),
(78, 1, 'A6', 6, 1),
(79, 1, 'B6', 6, 2),
(80, 1, 'C6', 6, 3),
(81, 1, 'D6', 6, 4),
(82, 1, 'E6', 6, 5),
(83, 1, 'A7', 7, 1),
(84, 1, 'B7', 7, 2),
(85, 1, 'C7', 7, 3),
(86, 1, 'D7', 7, 4),
(87, 1, 'E7', 7, 5),
(88, 1, 'A8', 8, 1),
(89, 1, 'B8', 8, 2),
(90, 1, 'C8', 8, 3),
(91, 1, 'D8', 8, 4),
(92, 1, 'E8', 8, 5),
(93, 1, 'A9', 9, 1),
(94, 1, 'B9', 9, 2),
(95, 1, 'C9', 9, 3),
(96, 1, 'D9', 9, 4),
(97, 1, 'E9', 9, 5),
(98, 1, 'A10', 10, 1),
(99, 1, 'B10', 10, 2),
(100, 1, 'C10', 10, 3),
(101, 1, 'D10', 10, 4),
(102, 1, 'E10', 10, 5),
(103, 1, 'A11', 11, 1),
(104, 1, 'B11', 11, 2),
(105, 2, 'A1', 1, 1),
(106, 2, 'B1', 1, 2),
(107, 2, 'C1', 1, 3),
(108, 2, 'D1', 1, 4),
(109, 2, 'E1', 1, 5),
(110, 2, 'A2', 2, 1),
(111, 2, 'B2', 2, 2),
(112, 2, 'C2', 2, 3),
(113, 2, 'D2', 2, 4),
(114, 2, 'E2', 2, 5),
(115, 2, 'A3', 3, 1),
(116, 2, 'B3', 3, 2),
(117, 2, 'C3', 3, 3),
(118, 2, 'D3', 3, 4),
(119, 2, 'E3', 3, 5),
(120, 2, 'A4', 4, 1),
(121, 2, 'B4', 4, 2),
(122, 2, 'C4', 4, 3),
(123, 2, 'D4', 4, 4),
(124, 2, 'E4', 4, 5),
(125, 2, 'A5', 5, 1),
(126, 2, 'B5', 5, 2),
(127, 2, 'C5', 5, 3),
(128, 2, 'D5', 5, 4),
(129, 2, 'E5', 5, 5),
(130, 2, 'A6', 6, 1),
(131, 2, 'B6', 6, 2),
(132, 2, 'C6', 6, 3),
(133, 2, 'D6', 6, 4),
(134, 2, 'E6', 6, 5),
(135, 2, 'A7', 7, 1),
(136, 2, 'B7', 7, 2),
(137, 2, 'C7', 7, 3),
(138, 2, 'D7', 7, 4),
(139, 2, 'E7', 7, 5),
(140, 2, 'A8', 8, 1),
(141, 2, 'B8', 8, 2),
(142, 2, 'C8', 8, 3),
(143, 2, 'D8', 8, 4),
(144, 2, 'E8', 8, 5),
(145, 2, 'A9', 9, 1),
(146, 2, 'B9', 9, 2),
(147, 2, 'C9', 9, 3),
(148, 2, 'D9', 9, 4),
(149, 2, 'E9', 9, 5),
(150, 2, 'A10', 10, 1),
(151, 2, 'B10', 10, 2),
(152, 2, 'C10', 10, 3),
(153, 2, 'D10', 10, 4),
(154, 2, 'E10', 10, 5),
(155, 2, 'A11', 11, 1),
(156, 2, 'B11', 11, 2),
(157, 3, 'A1', 1, 1),
(158, 3, 'B1', 1, 2),
(159, 3, 'C1', 1, 3),
(160, 3, 'D1', 1, 4),
(161, 3, 'E1', 1, 5),
(162, 3, 'A2', 2, 1),
(163, 3, 'B2', 2, 2),
(164, 3, 'C2', 2, 3),
(165, 3, 'D2', 2, 4),
(166, 3, 'E2', 2, 5),
(167, 3, 'A3', 3, 1),
(168, 3, 'B3', 3, 2),
(169, 3, 'C3', 3, 3),
(170, 3, 'D3', 3, 4),
(171, 3, 'E3', 3, 5),
(172, 3, 'A4', 4, 1),
(173, 3, 'B4', 4, 2),
(174, 3, 'C4', 4, 3),
(175, 3, 'D4', 4, 4),
(176, 3, 'E4', 4, 5),
(177, 3, 'A5', 5, 1),
(178, 3, 'B5', 5, 2),
(179, 3, 'C5', 5, 3),
(180, 3, 'D5', 5, 4),
(181, 3, 'E5', 5, 5),
(182, 3, 'A6', 6, 1),
(183, 3, 'B6', 6, 2),
(184, 3, 'C6', 6, 3),
(185, 3, 'D6', 6, 4),
(186, 3, 'E6', 6, 5),
(187, 3, 'A7', 7, 1),
(188, 3, 'B7', 7, 2),
(189, 3, 'C7', 7, 3),
(190, 3, 'D7', 7, 4),
(191, 3, 'E7', 7, 5),
(192, 3, 'A8', 8, 1),
(193, 3, 'B8', 8, 2),
(194, 3, 'C8', 8, 3),
(195, 3, 'D8', 8, 4),
(196, 3, 'E8', 8, 5),
(197, 3, 'A9', 9, 1),
(198, 3, 'B9', 9, 2),
(199, 3, 'C9', 9, 3),
(200, 3, 'D9', 9, 4),
(201, 3, 'E9', 9, 5),
(202, 3, 'A10', 10, 1),
(203, 3, 'B10', 10, 2),
(204, 3, 'C10', 10, 3),
(205, 3, 'D10', 10, 4),
(206, 3, 'E10', 10, 5),
(207, 3, 'A11', 11, 1),
(208, 3, 'B11', 11, 2),
(209, 9, 'A1', 1, 1),
(210, 9, 'B1', 1, 2),
(211, 9, 'C1', 1, 3),
(212, 9, 'D1', 1, 4),
(213, 9, 'E1', 1, 5),
(214, 9, 'A2', 2, 1),
(215, 9, 'B2', 2, 2),
(216, 9, 'C2', 2, 3),
(217, 9, 'D2', 2, 4),
(218, 9, 'E2', 2, 5),
(219, 9, 'A3', 3, 1),
(220, 9, 'B3', 3, 2),
(221, 9, 'C3', 3, 3),
(222, 9, 'D3', 3, 4),
(223, 9, 'E3', 3, 5),
(224, 9, 'A4', 4, 1),
(225, 9, 'B4', 4, 2),
(226, 9, 'C4', 4, 3),
(227, 9, 'D4', 4, 4),
(228, 9, 'E4', 4, 5),
(229, 9, 'A5', 5, 1),
(230, 9, 'B5', 5, 2),
(231, 9, 'C5', 5, 3),
(232, 9, 'D5', 5, 4),
(233, 9, 'E5', 5, 5),
(234, 9, 'A6', 6, 1),
(235, 9, 'B6', 6, 2),
(236, 9, 'C6', 6, 3),
(237, 9, 'D6', 6, 4),
(238, 9, 'E6', 6, 5),
(239, 9, 'A7', 7, 1),
(240, 9, 'B7', 7, 2),
(241, 9, 'C7', 7, 3),
(242, 9, 'D7', 7, 4),
(243, 9, 'E7', 7, 5),
(244, 9, 'A8', 8, 1),
(245, 9, 'B8', 8, 2),
(246, 9, 'C8', 8, 3),
(247, 9, 'D8', 8, 4),
(248, 9, 'E8', 8, 5),
(249, 9, 'A9', 9, 1),
(250, 9, 'B9', 9, 2),
(251, 9, 'C9', 9, 3),
(252, 9, 'D9', 9, 4),
(253, 9, 'E9', 9, 5),
(254, 9, 'A10', 10, 1),
(255, 9, 'B10', 10, 2),
(256, 9, 'C10', 10, 3),
(257, 9, 'D10', 10, 4),
(258, 9, 'E10', 10, 5),
(259, 9, 'A11', 11, 1),
(260, 9, 'B11', 11, 2);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `contact` varchar(15) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('customer','admin','traveler') DEFAULT 'customer',
  `is_verified` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `contact`, `password`, `role`, `is_verified`) VALUES
(1, 'user', 'user@gmail.com', '01234567890', '$2y$10$On8I0zuzFyGwKHYDlK2J/eq7cIXciHQo.JJbXsVvET1wevEwmFcim', 'customer', 0),
(2, 'admin', 'admin@gmail.com', '8220052252', '$2y$10$O6LZS8VUZfhycn3PkyB3feawjPgUhKTf5Yc.1gRO8L/rlzxEyMzhS', 'admin', 0),
(3, 'agent', 'agent@gmail.com', '1254100000', '$2y$10$ug7itSHOKx5D9/zjW/B5/eW2FdFmHAkIhcgeGClKxYr1FLNs07Hh2', 'traveler', 0),
(5, 'agent1', 'agent1@gmail.com', '8220052252', '$2y$10$IgG7Q8EZ3kZLXHiKboJeFOMAD3RQsGoR.8qhhTShFUGTcfBFO6ozu', 'traveler', 0),
(12, 'agent2', 'agent2@gmail.com', '1254100000', '$2y$10$MIOkDi0FpMC1VAj0n3wk.OGsTCm.9JJB8fdEsdIDfGbAMBL1F3QOa', 'traveler', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `booked_seats`
--
ALTER TABLE `booked_seats`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `seat_id` (`seat_id`),
  ADD KEY `bus_id` (`bus_id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `bus_id` (`bus_id`);

--
-- Indexes for table `buses`
--
ALTER TABLE `buses`
  ADD PRIMARY KEY (`bus_id`);

--
-- Indexes for table `passengers`
--
ALTER TABLE `passengers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`);

--
-- Indexes for table `seats`
--
ALTER TABLE `seats`
  ADD PRIMARY KEY (`seat_id`),
  ADD KEY `bus_id` (`bus_id`);

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
-- AUTO_INCREMENT for table `booked_seats`
--
ALTER TABLE `booked_seats`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=158;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=158;

--
-- AUTO_INCREMENT for table `buses`
--
ALTER TABLE `buses`
  MODIFY `bus_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `passengers`
--
ALTER TABLE `passengers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=170;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `seats`
--
ALTER TABLE `seats`
  MODIFY `seat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=261;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `booked_seats`
--
ALTER TABLE `booked_seats`
  ADD CONSTRAINT `booked_seats_ibfk_1` FOREIGN KEY (`seat_id`) REFERENCES `seats` (`seat_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `booked_seats_ibfk_2` FOREIGN KEY (`bus_id`) REFERENCES `buses` (`bus_id`) ON DELETE CASCADE;

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`bus_id`) REFERENCES `buses` (`bus_id`);

--
-- Constraints for table `passengers`
--
ALTER TABLE `passengers`
  ADD CONSTRAINT `passengers_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`);

--
-- Constraints for table `seats`
--
ALTER TABLE `seats`
  ADD CONSTRAINT `seats_ibfk_1` FOREIGN KEY (`bus_id`) REFERENCES `buses` (`bus_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
