-- phpMyAdmin SQL Dump
-- version 4.8.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 12, 2020 at 08:06 PM
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
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL,
  `name` mediumtext,
  `description` text,
  `priority` int(11) DEFAULT NULL,
  `dateadded` datetime NOT NULL,
  `startdate` date NOT NULL,
  `duedate` date DEFAULT NULL,
  `reminderone_date` date NOT NULL,
  `remindertwo_date` date NOT NULL,
  `datefinished` datetime DEFAULT NULL,
  `reminderone_days` int(11) NOT NULL,
  `remindertwo_days` int(11) NOT NULL,
  `task_days` int(11) NOT NULL,
  `is_closed` int(11) NOT NULL,
  `addedfrom` int(11) NOT NULL,
  `is_added_from_contact` tinyint(1) NOT NULL DEFAULT '0',
  `status` int(11) NOT NULL DEFAULT '0',
  `recurring_type` varchar(10) DEFAULT NULL,
  `repeat_every` int(11) DEFAULT NULL,
  `recurring` int(11) NOT NULL DEFAULT '0',
  `is_recurring_from` int(11) DEFAULT NULL,
  `cycles` int(11) NOT NULL DEFAULT '0',
  `total_cycles` int(11) NOT NULL DEFAULT '0',
  `custom_recurring` tinyint(1) NOT NULL DEFAULT '0',
  `last_recurring_date` date DEFAULT NULL,
  `rel_id` int(11) DEFAULT NULL,
  `rel_type` varchar(30) DEFAULT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT '0',
  `billable` tinyint(1) NOT NULL DEFAULT '0',
  `billed` tinyint(1) NOT NULL DEFAULT '0',
  `invoice_id` int(11) NOT NULL DEFAULT '0',
  `hourly_rate` decimal(15,2) NOT NULL DEFAULT '0.00',
  `milestone` int(11) DEFAULT '0',
  `kanban_order` int(11) NOT NULL DEFAULT '0',
  `milestone_order` int(11) NOT NULL DEFAULT '0',
  `visible_to_client` tinyint(1) NOT NULL DEFAULT '0',
  `deadline_notified` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `name`, `description`, `priority`, `dateadded`, `startdate`, `duedate`, `reminderone_date`, `remindertwo_date`, `datefinished`, `reminderone_days`, `remindertwo_days`, `task_days`, `is_closed`, `addedfrom`, `is_added_from_contact`, `status`, `recurring_type`, `repeat_every`, `recurring`, `is_recurring_from`, `cycles`, `total_cycles`, `custom_recurring`, `last_recurring_date`, `rel_id`, `rel_type`, `is_public`, `billable`, `billed`, `invoice_id`, `hourly_rate`, `milestone`, `kanban_order`, `milestone_order`, `visible_to_client`, `deadline_notified`) VALUES
(193, 'Project Closure', NULL, NULL, '2020-06-12 23:34:55', '2020-06-12', '2020-12-29', '2020-06-12', '2020-06-12', NULL, 0, 0, 200, 0, 3, 0, 0, NULL, NULL, 0, NULL, 0, 0, 0, NULL, 83, 'project', 0, 0, 0, 0, '0.00', 0, 0, 0, 1, 0),
(194, 'test', NULL, NULL, '2020-06-12 23:34:55', '2020-06-12', '2020-09-20', '2020-08-01', '2020-08-31', NULL, 50, 80, 100, 0, 3, 0, 0, NULL, NULL, 0, NULL, 0, 0, 0, NULL, 83, 'project', 0, 0, 0, 0, '0.00', 0, 0, 0, 1, 0),
(195, 'testing 1234', NULL, NULL, '2020-06-12 23:34:55', '2020-09-21', '2020-12-29', '2020-11-09', '2020-12-09', NULL, 50, 80, 100, 0, 3, 0, 0, NULL, NULL, 0, NULL, 0, 0, 0, NULL, 83, 'project', 0, 0, 0, 0, '0.00', 0, 0, 0, 1, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rel_id` (`rel_id`),
  ADD KEY `rel_type` (`rel_type`),
  ADD KEY `milestone` (`milestone`),
  ADD KEY `kanban_order` (`kanban_order`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=196;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
