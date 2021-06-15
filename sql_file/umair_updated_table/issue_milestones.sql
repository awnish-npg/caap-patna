-- phpMyAdmin SQL Dump
-- version 4.8.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 14, 2020 at 07:24 AM
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
  `reminder_one` int(11) NOT NULL,
  `reminder_two` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `is_active` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `issue_milestones`
--

INSERT INTO `issue_milestones` (`id`, `issue_id`, `milestone_name`, `days`, `reminder_one`, `reminder_two`, `created_at`, `updated_at`, `is_active`) VALUES
(78, 52, 'task management', 10, 0, 0, '2020-05-12 07:49:37', '2020-05-13 05:59:25', 0),
(79, 53, 'task management', 100, 0, 0, '2020-05-12 08:05:02', '2020-05-13 05:59:47', 0),
(80, 54, 'task management', 100, 0, 0, '2020-05-12 08:34:44', '2020-05-13 05:59:53', 0),
(81, 54, 'work', 20, 0, 0, '2020-05-12 08:59:16', '2020-05-13 05:59:53', 0),
(82, 55, 'task management', 100, 0, 0, '2020-05-12 09:18:59', '2020-05-13 05:59:32', 0),
(83, 55, 'task management', 100, 0, 0, '2020-05-12 10:26:00', '2020-05-13 05:59:32', 0),
(84, 55, 'work', 100, 0, 0, '2020-05-12 10:29:36', '2020-05-13 05:59:32', 0),
(85, 56, 'task management', 100, 0, 0, '2020-05-12 10:31:37', '2020-05-13 05:59:39', 0),
(86, 56, 'tendering', 10, 0, 0, '2020-05-12 10:31:37', '2020-05-13 05:59:39', 0),
(87, 56, 'work', 100, 0, 0, '2020-05-12 10:31:37', '2020-05-13 05:59:39', 0),
(88, 56, 'feasibility studies', 10, 0, 0, '2020-05-12 10:34:48', '2020-05-13 05:59:39', 0),
(89, 56, 'task management', 100, 0, 0, '2020-05-12 11:54:50', '2020-05-13 05:59:39', 0),
(90, 57, 'work', 100, 0, 0, '2020-05-12 12:03:43', '2020-05-12 12:20:29', 1),
(91, 57, 'task management', 100, 0, 0, '2020-05-12 12:05:25', '2020-05-12 12:06:56', 1),
(92, 57, 'tendering', 100, 0, 0, '2020-05-12 12:20:33', '2020-05-13 05:11:11', 1),
(93, 57, 'task management', 200, 0, 0, '2020-05-12 19:36:16', '2020-05-12 19:36:25', 1),
(94, 58, 'mile1', 100, 0, 0, '2020-05-13 05:49:21', '2020-05-13 06:05:15', 0),
(95, 58, 'mile2', 200, 0, 0, '2020-05-13 05:49:21', '2020-05-13 06:05:15', 0),
(96, 59, 'mile1', 100, 0, 0, '2020-05-13 05:50:17', '2020-05-13 05:52:55', 0),
(97, 59, 'mile2', 100, 0, 0, '2020-05-13 05:50:17', '2020-05-13 05:52:55', 0),
(98, 60, 'task management', 100, 20, 30, '2020-05-13 06:06:00', '2020-05-13 06:24:16', 0),
(99, 60, 'feasibility studies', 100, 20, 10, '2020-05-13 06:06:00', '2020-05-13 06:51:41', 0),
(100, 60, 'mile2', 100, 10, 20, '2020-05-13 06:33:36', '2020-05-13 06:35:34', 0),
(101, 60, 'tendering', 200, 20, 30, '2020-05-13 06:51:45', '2020-05-13 07:00:08', 0),
(102, 60, 'task management', 100, 20, 30, '2020-05-13 07:00:24', '2020-05-13 11:21:49', 0),
(103, 60, 'work', 100, 20, 30, '2020-05-13 09:40:58', '2020-05-13 11:21:49', 0),
(104, 61, 'work', 100, 20, 30, '2020-05-13 10:30:12', '2020-05-13 13:34:26', 0),
(105, 61, 'tendering', 100, 10, 20, '2020-05-13 11:14:47', '2020-05-13 11:15:02', 0),
(106, 62, 'task management', 100, 10, 20, '2020-05-13 11:22:32', '2020-05-13 14:03:06', 0),
(107, 63, 'work', 100, 10, 20, '2020-05-13 11:23:16', '2020-05-14 03:25:29', 0),
(108, 63, 'feasibility studies', 100, 20, 30, '2020-05-13 11:23:16', '2020-05-14 03:25:29', 0),
(109, 64, 'task management', 200, 10, 20, '2020-05-13 14:02:57', '2020-05-13 14:03:15', 0),
(110, 64, 'feasibility studies', 100, 10, 20, '2020-05-13 14:02:57', '2020-05-13 14:03:15', 0),
(111, 65, 'task management', 100, 10, 20, '2020-05-13 14:03:34', '2020-05-14 05:01:43', 0),
(112, 66, 'task management', 100, 20, 30, '2020-05-14 03:21:46', '0000-00-00 00:00:00', 1),
(113, 65, 'feasibility study', 60, 10, 30, '2020-05-14 05:01:49', '0000-00-00 00:00:00', 1),
(114, 65, 'budget', 100, 10, 20, '2020-05-14 05:02:11', '2020-05-14 05:08:36', 0);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=115;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
