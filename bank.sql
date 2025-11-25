-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 25, 2025 at 01:31 AM
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
-- Database: `bank`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `id` int(11) NOT NULL,
  `holder_name` varchar(100) NOT NULL,
  `holder_id` int(11) NOT NULL,
  `balance` decimal(10,2) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`id`, `holder_name`, `holder_id`, `balance`) VALUES
(1, 'Vincent Bejbom', 1, 29711.00),
(2, 'Vincent Bejbom', 1, 30201.00),
(3, 'Vinne Bejbom', 2, 278.00),
(4, 'Vincent Bejbom', 1, 50.00);

-- --------------------------------------------------------

--
-- Table structure for table `account_holders`
--

CREATE TABLE `account_holders` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `account_holders`
--

INSERT INTO `account_holders` (`id`, `name`, `password`) VALUES
(1, 'Vincent Bejbom', 'password'),
(2, 'Vinne Bejbom', 'testPassword');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `type` enum('deposit','withdraw','transfer') NOT NULL,
  `from_account_id` int(11) DEFAULT NULL,
  `to_account_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `time_stamp` datetime NOT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `type`, `from_account_id`, `to_account_id`, `amount`, `time_stamp`, `description`) VALUES
(1, 'deposit', NULL, NULL, 50.00, '0000-00-00 00:00:00', ''),
(3, 'deposit', NULL, NULL, 50.00, '0000-00-00 00:00:00', ''),
(4, 'deposit', NULL, NULL, 50.00, '2025-10-27 18:17:48', ''),
(5, 'deposit', NULL, NULL, 78.00, '2025-10-27 18:21:25', ''),
(6, 'deposit', NULL, NULL, 50.00, '2025-10-27 19:17:41', ''),
(7, 'deposit', NULL, 1, 50.00, '2025-10-27 19:26:08', ''),
(8, '', 1, NULL, 50.00, '2025-10-27 19:35:22', ''),
(9, '', 1, NULL, 1.00, '2025-10-27 19:56:55', ''),
(10, 'transfer', 1, NULL, 1.00, '2025-10-27 19:57:23', ''),
(11, '', 1, NULL, 1.00, '2025-10-27 20:06:10', ''),
(12, 'withdraw', 1, NULL, 1.00, '2025-10-27 20:08:13', ''),
(13, 'deposit', NULL, 1, 40000.00, '2025-10-27 20:08:36', 'Testing'),
(14, 'deposit', NULL, 1, 1.00, '2025-10-27 20:14:26', ''),
(15, 'transfer', 1, 2, 50.00, '2025-10-27 20:23:45', 'Tesst1231'),
(16, 'transfer', 1, 3, 50.00, '2025-10-27 20:24:40', ''),
(20, 'transfer', 1, 3, 78.00, '2025-10-27 20:34:26', ''),
(21, 'withdraw', 1, NULL, 50.00, '2025-10-27 20:40:39', ''),
(22, 'deposit', NULL, 2, 40000.00, '2025-10-28 17:22:35', ''),
(23, 'withdraw', 2, NULL, 10000.00, '2025-10-28 17:22:41', ''),
(24, 'deposit', NULL, 2, 150.55, '2025-10-28 17:22:53', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `holder_id` (`holder_id`);

--
-- Indexes for table `account_holders`
--
ALTER TABLE `account_holders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `password` (`password`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `from_account_id` (`from_account_id`),
  ADD KEY `to_account_id` (`to_account_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `account_holders`
--
ALTER TABLE `account_holders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accounts`
--
ALTER TABLE `accounts`
  ADD CONSTRAINT `accounts_ibfk_1` FOREIGN KEY (`holder_id`) REFERENCES `account_holders` (`id`);

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`from_account_id`) REFERENCES `accounts` (`id`),
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`from_account_id`) REFERENCES `accounts` (`id`),
  ADD CONSTRAINT `transactions_ibfk_3` FOREIGN KEY (`to_account_id`) REFERENCES `accounts` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
