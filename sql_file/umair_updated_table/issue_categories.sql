-- phpMyAdmin SQL Dump
-- version 4.8.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 14, 2020 at 07:23 AM
-- Server version: 10.1.33-MariaDB
-- PHP Version: 7.2.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `apag_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `issue_categories`
--

CREATE TABLE `issue_categories` (
  `id` int(11) NOT NULL,
  `issue_name` varchar(200) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `is_active` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `issue_categories`
--

INSERT INTO `issue_categories` (`id`, `issue_name`, `staff_id`, `created_at`, `updated_at`, `is_active`) VALUES
(52, 'garbage', 5, '2020-05-12 07:49:37', '2020-05-13 05:59:25', 0),
(53, 'road repairing', 1, '2020-05-12 08:05:01', '2020-05-13 05:59:46', 0),
(54, 'road repairing', 1, '2020-05-12 08:34:44', '2020-05-13 05:59:53', 0),
(55, 'garbage', 1, '2020-05-12 09:18:59', '2020-05-13 05:59:32', 0),
(56, 'Garbage Dustbins', 1, '2020-05-12 10:31:37', '2020-05-13 05:59:39', 0),
(57, 'road cleaning', 1, '2020-05-12 12:03:43', '2020-05-12 12:05:25', 0),
(58, 'test3', 1, '2020-05-13 05:49:21', '2020-05-13 06:05:15', 0),
(59, 'test 4', 1, '2020-05-13 05:50:17', '2020-05-13 05:52:55', 0),
(60, 'road repairings', 1, '2020-05-13 06:06:00', '2020-05-13 11:21:49', 0),
(61, 'garbage', 1, '2020-05-13 10:30:12', '2020-05-13 13:34:26', 0),
(62, 'road repairing', 1, '2020-05-13 11:22:32', '2020-05-13 14:03:06', 0),
(63, 'Road safety', 1, '2020-05-13 11:23:16', '2020-05-14 03:25:29', 0),
(64, 'road repairing', 1, '2020-05-13 14:02:57', '2020-05-13 14:03:15', 0),
(65, 'garbage', 1, '2020-05-13 14:03:34', '0000-00-00 00:00:00', 1),
(66, 'waste', 1, '2020-05-14 03:21:45', '0000-00-00 00:00:00', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `issue_categories`
--
ALTER TABLE `issue_categories`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `issue_categories`
--
ALTER TABLE `issue_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
