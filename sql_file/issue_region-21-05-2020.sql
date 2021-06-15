-- phpMyAdmin SQL Dump
-- version 4.8.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 21, 2020 at 12:38 PM
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
-- Database: `a-pag_db_19_05`
--

-- --------------------------------------------------------

--
-- Table structure for table `issue_region`
--

CREATE TABLE `issue_region` (
  `id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `area_id` int(11) NOT NULL,
  `issue_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `issue_region`
--

INSERT INTO `issue_region` (`id`, `staff_id`, `area_id`, `issue_id`) VALUES
(1, 18, 1, 65),
(2, 18, 1, 57),
(4, 18, 1, 69),
(6, 18, 1, 60),
(7, 18, 1, 67),
(8, 15, 7, 65),
(9, 18, 1, 66),
(10, 15, 7, 57),
(11, 18, 1, 68),
(12, 18, 1, 72),
(13, 18, 1, 73),
(14, 15, 7, 74),
(15, 18, 1, 59),
(16, 18, 1, 70),
(17, 15, 7, 60),
(18, 18, 1, 80),
(19, 18, 1, 81),
(20, 18, 1, 83);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `issue_region`
--
ALTER TABLE `issue_region`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `issue_region`
--
ALTER TABLE `issue_region`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
