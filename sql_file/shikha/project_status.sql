-- phpMyAdmin SQL Dump
-- version 4.9.5
-- https://www.phpmyadmin.net/
--
-- Host: 10.0.1.8
-- Generation Time: Aug 07, 2020 at 05:42 AM
-- Server version: 5.7.28
-- PHP Version: 7.2.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `apagst_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `project_status`
--

CREATE TABLE `project_status` (
  `id` int(11) NOT NULL,
  `color` varchar(7) DEFAULT NULL,
  `bg-color` varchar(7) DEFAULT NULL,
  `name` varchar(50) NOT NULL,
  `order` int(11) DEFAULT NULL,
  `filter_default` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `project_status`
--

INSERT INTO `project_status` (`id`, `color`, `bg-color`, `name`, `order`, `filter_default`) VALUES
(1, '#233ae0', '#dcdffa', 'New', 1, 1),
(2, '#e43b05', '#ffe0d6', 'In progress', 2, 1),
(3, '#096b04', '#398435', 'Closed', 3, 1),
(4, '#035703', '#12ee12', 'WIP (resolved)', 4, 1),
(5, '#d9062e', '#ffead6', 'Rejected', 5, 1),
(6, '#ff7d00', '#ffead6', 'Reopened', 6, 1),
(7, '#ff7d00', '#ffead6', 'Escalated', 7, 1),
(8, '#ff7d00', '#ffead6', 'Frozen', 8, 1),
(9, '#ff7d00', '#ffead6', 'Unassigned', 8, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `project_status`
--
ALTER TABLE `project_status`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `project_status`
--
ALTER TABLE `project_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
