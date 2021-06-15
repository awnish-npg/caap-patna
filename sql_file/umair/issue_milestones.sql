-- phpMyAdmin SQL Dump
-- version 4.8.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 11, 2020 at 07:08 PM
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
-- Table structure for table `issue_milestones`
--

CREATE TABLE `issue_milestones` (
  `id` int(11) NOT NULL,
  `issue_id` int(11) NOT NULL,
  `milestone_name` varchar(200) NOT NULL,
  `days` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `is_deleted` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `issue_milestones`
--

INSERT INTO `issue_milestones` (`id`, `issue_id`, `milestone_name`, `days`, `created_at`, `updated_at`, `is_deleted`) VALUES
(47, 36, 'work', 20, '2020-05-09 14:53:25', '2020-05-10 10:56:31', 0),
(49, 36, 'task', 100, '2020-05-09 15:25:56', '2020-05-10 09:17:49', 0),
(52, 36, 'tendering', 20, '2020-05-10 09:25:44', '2020-05-10 10:56:59', 0),
(53, 39, 'task management', 120, '2020-05-10 10:07:13', '2020-05-10 11:22:32', 0),
(69, 48, 'task management', 120, '2020-05-11 16:08:25', '0000-00-00 00:00:00', 0),
(70, 39, 'work', 20, '2020-05-11 16:56:52', '0000-00-00 00:00:00', 0),
(73, 50, 'task management', 100, '2020-05-11 16:59:20', '0000-00-00 00:00:00', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `issue_milestones`
--
ALTER TABLE `issue_milestones`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `issue_milestones`
--
ALTER TABLE `issue_milestones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
