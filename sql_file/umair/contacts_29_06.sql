-- phpMyAdmin SQL Dump
-- version 4.8.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 29, 2020 at 01:04 PM
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
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `is_primary` int(11) NOT NULL DEFAULT '1',
  `firstname` varchar(191) NOT NULL,
  `lastname` varchar(191) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phonenumber` varchar(100) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `datecreated` datetime NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `new_pass_key` varchar(32) DEFAULT NULL,
  `new_pass_key_requested` datetime DEFAULT NULL,
  `email_verified_at` datetime DEFAULT NULL,
  `email_verification_key` varchar(32) DEFAULT NULL,
  `email_verification_sent_at` datetime DEFAULT NULL,
  `last_ip` varchar(40) DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `last_password_change` datetime DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `profile_image` varchar(191) DEFAULT NULL,
  `direction` varchar(3) DEFAULT NULL,
  `invoice_emails` tinyint(1) NOT NULL DEFAULT '1',
  `estimate_emails` tinyint(1) NOT NULL DEFAULT '1',
  `credit_note_emails` tinyint(1) NOT NULL DEFAULT '1',
  `contract_emails` tinyint(1) NOT NULL DEFAULT '1',
  `task_emails` tinyint(1) NOT NULL DEFAULT '1',
  `project_emails` tinyint(1) NOT NULL DEFAULT '1',
  `ticket_emails` tinyint(1) NOT NULL DEFAULT '1',
  `area_id` int(11) NOT NULL,
  `region_id` int(11) NOT NULL,
  `subregion_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`id`, `userid`, `is_primary`, `firstname`, `lastname`, `email`, `phonenumber`, `title`, `datecreated`, `password`, `new_pass_key`, `new_pass_key_requested`, `email_verified_at`, `email_verification_key`, `email_verification_sent_at`, `last_ip`, `last_login`, `last_password_change`, `active`, `profile_image`, `direction`, `invoice_emails`, `estimate_emails`, `credit_note_emails`, `contract_emails`, `task_emails`, `project_emails`, `ticket_emails`, `area_id`, `region_id`, `subregion_id`) VALUES
(1, 1, 1, 'Surveyour', 'One', 's_1.apag@yopmail.com', '', '', '2020-05-03 16:01:01', '$2a$08$GIVVUtQdanKDIeU.RGkwyesn.oBtSON0rLs.lnSRQXjJLv4Q0qOPC', NULL, NULL, '2020-05-03 16:01:01', NULL, NULL, '::1', '2020-06-05 18:58:14', NULL, 1, NULL, '', 0, 0, 0, 0, 1, 1, 1, 0, 0, 0),
(2, 1, 0, 'Surveyor', 'Two', 's_two.apag@yopmail.com', '', '', '2020-05-03 21:11:27', '$2a$08$3nNICjdh2TakmsPxojLfRu7UEjoicwXED3n7h.xE9JOJA7E4.CV0y', NULL, NULL, '2020-05-03 21:11:27', NULL, NULL, NULL, NULL, NULL, 1, NULL, '', 0, 1, 0, 1, 1, 1, 1, 0, 0, 0),
(3, 2, 1, 'Umair', 'ansari', 'umairansari12614@gmail.com', '9891078797', NULL, '2020-06-04 12:17:10', '$2a$08$oTmLqm1Q6ckgK0VN5ti8Aerf6w2OiE65jcciUhYHrasmYOcO6ywC.', '7e35eb2f03551bda1b01b71807c59ab1', '2020-06-06 18:53:06', NULL, 'd4f5cabd32d7247fb3ddb0f32e8b0e32', NULL, '::1', '2020-06-06 18:12:25', NULL, 1, NULL, NULL, 0, 0, 0, 0, 1, 1, 1, 0, 0, 0),
(4, 3, 1, 'umair', 'ansari', 'ummuansari00@gmail.com', '9891078797', '', '2020-06-04 12:30:54', '$2a$08$z1ud.C8/pAKPLP4dQdHrke4yV/TijrcIXXPtM2zDGcDaKcVgtaJdq', '64f78265fe94d753f4ccb9d06fa99dce', '2020-06-09 19:13:26', NULL, '4231ca734ea3f49c7e48491932febd76', NULL, '::1', '2020-06-29 16:22:44', '2020-06-13 14:54:37', 1, 'a.jpg', '', 0, 0, 0, 0, 1, 1, 1, 1, 2, 2),
(5, 4, 1, 'umair', 'Ansari', 'test1@gmail.com', '9891078797', NULL, '2020-06-04 19:55:28', '$2a$08$PwOcUOEqlU4BkZw6LDhO0Og1v0ylMr4DYTgs25ddjer5.YpQ6a6gm', NULL, NULL, NULL, 'd0bd37f36048e961b2c8eea6930b7a76', NULL, '::1', '2020-06-04 19:55:42', NULL, 1, NULL, NULL, 0, 0, 0, 0, 1, 1, 1, 0, 0, 0),
(6, 5, 1, 'Umair', 'Ansari', 'testapag@gmail.com', '9891078797', NULL, '2020-06-04 20:14:32', '$2a$08$SaxrDiWoy/dbLSJDkPghTOQyBwBqMOWxNi7dDIO/m8uwKaMCFMQ.C', NULL, NULL, NULL, '1356603e47fd1857e8f7d220106d8c93', NULL, '::1', '2020-06-04 20:14:46', NULL, 1, NULL, NULL, 0, 0, 0, 0, 1, 1, 1, 0, 0, 0),
(7, 6, 1, 'Umair', 'Ansari', 'test@gmail.com', '9891078797', NULL, '2020-06-04 20:24:49', '$2a$08$4C4WBfBV8cS7MAA552KE6uueo49Zfs5uAjuoyTq1eXPGR/xJceAgi', NULL, NULL, NULL, 'f3f396e24aae1b2ae808b12c9ea1aace', NULL, '::1', '2020-06-04 20:25:29', NULL, 1, NULL, NULL, 0, 0, 0, 0, 1, 1, 1, 0, 0, 0),
(8, 7, 1, 'Umair', '', 'test12@gmail.com', '9891078797', NULL, '2020-06-04 22:14:03', '$2a$08$VhqdjIPcuM3G30Ii49p9cu20m06MzPF1.yqvq21LA/qpbywSx4fTq', NULL, NULL, NULL, '2d8dd81616e3cce4e78097458e9471a1', NULL, '::1', '2020-06-04 22:14:18', NULL, 1, NULL, NULL, 0, 0, 0, 0, 1, 1, 1, 0, 0, 0),
(9, 8, 1, 'Umair', '', 'umair@gmail.com', '9891078797', NULL, '2020-06-04 22:15:14', '$2a$08$aepICrB7mFfcTj8QrHIZ9.8ZUpzJKL3T9WBoqR6G1JbcwVnuV5lLK', 'fe576d8693c88b5708b94151faaf6385', '2020-06-06 17:10:04', NULL, '33f2d4cbfbe54fe15842a4f9ab5796ff', NULL, '::1', '2020-06-04 22:15:27', NULL, 1, NULL, NULL, 0, 0, 0, 0, 1, 1, 1, 0, 0, 0),
(10, 9, 1, 'umair', '', 'test1212@gmail.com', '9891078797', NULL, '2020-06-04 23:01:47', '$2a$08$SMMzDiR70QDKZmDGkXt0jeP6PB5GWsGUqNprmZ7dKy6tqqPtcGJCK', NULL, NULL, NULL, '7d8bbe6fdb56a3f4383e6d4463a16148', NULL, '::1', '2020-06-04 23:02:02', NULL, 1, NULL, NULL, 0, 0, 0, 0, 1, 1, 1, 0, 0, 0),
(11, 10, 1, 'umair ansari', '', 'testumair@gmail.com', '9891078797', NULL, '2020-06-04 23:07:38', '$2a$08$HpTJItqzdoXSq5yNEph4HO3yllDbtK./FBpPy2bXJflUeafBvRMT6', NULL, NULL, NULL, '4fd53ae4142f394213c3d68728a72a5a', NULL, '::1', '2020-06-04 23:07:52', NULL, 1, NULL, NULL, 0, 0, 0, 0, 1, 1, 1, 0, 0, 0),
(12, 11, 1, 'umair ansari', '', 'tes1212@gmail.com', '9891078797', NULL, '2020-06-05 10:26:45', '$2a$08$sdn47iAYvhSn2CTBFdt/gObYFSjkcRZR/AmnrYvoXRp4rM2PQVgw.', NULL, NULL, NULL, 'a218a6a0695eff3623c45f202bff0f9d', NULL, '::1', '2020-06-05 10:26:58', NULL, 1, NULL, NULL, 0, 0, 0, 0, 1, 1, 1, 0, 0, 0),
(13, 12, 1, 'umair', '', 'test1234@gmail.com', '9891078797', NULL, '2020-06-05 11:46:54', '$2a$08$ZnACj773ovGJRgsD9C2b3e3Q7h7uTQSW1HgdipdsCC7pKI1/EXzxG', NULL, NULL, NULL, '81c5c0c3b280caafb4341329946bf683', NULL, '::1', '2020-06-05 11:47:08', NULL, 1, NULL, NULL, 0, 0, 0, 0, 1, 1, 1, 0, 0, 0),
(14, 13, 1, 'umair ansari', '', 'test123@gamil.com', '9891078797', NULL, '2020-06-05 12:59:42', '$2a$08$0DrAzDF81qaNpCaMXbISueSF.xYzxdqRSIKDCEoMHVy1rj.k5duFq', NULL, NULL, NULL, '8d9fd6d06fd8b0bdbe24fbee593eed5b', NULL, '::1', '2020-06-05 12:59:58', NULL, 1, NULL, NULL, 0, 0, 0, 0, 1, 1, 1, 0, 0, 0),
(15, 14, 1, 'test1', '', 'mohammad.umair@netprophetsglobal.com', '9891078797', NULL, '2020-06-05 17:35:31', '$2a$08$EcktZFGHzrWw1Kmd9V.gwuKlDsPe241LsLLXnvAjvay6hw14gPD92', '24a461871c07ecc6d5792424c2ed2004', '2020-06-06 19:11:13', NULL, 'c1ea9e3a6cf2192ca7ae98f3430e778a', NULL, '::1', '2020-06-06 17:58:47', '2020-06-06 18:03:29', 1, NULL, NULL, 0, 0, 0, 0, 1, 1, 1, 0, 0, 0),
(16, 15, 1, 'umair', '', 'test123456@gmail.com', '9891078797', NULL, '2020-06-05 19:16:58', '$2a$08$56YolPGevky3sA6GJ4uchexAw4BMPU3ZLapI5m8vYRi5EXE3Kb50.', NULL, NULL, NULL, 'ac43f2f7d3d4a07ee13cc0f1226ba662', NULL, '::1', '2020-06-05 19:17:14', NULL, 1, NULL, NULL, 0, 0, 0, 0, 1, 1, 1, 0, 0, 0),
(17, 16, 1, 'test', '', 'test22@gmail.com', '9891078797', NULL, '2020-06-05 19:20:50', '$2a$08$5MWfjFYaCXTtjuuJ.JMTu.3ZR8PZYPcVBie4W7ZPDckvmXt5kW..2', NULL, NULL, NULL, 'da3e68bb417ad8b2ab8866f5ca7611e0', NULL, '::1', '2020-06-05 19:21:06', NULL, 1, NULL, NULL, 0, 0, 0, 0, 1, 1, 1, 0, 0, 0),
(18, 17, 1, 'Umair Ansari', '', 'umairtest@gmail.com', '9891078797', NULL, '2020-06-05 19:56:10', '$2a$08$jE08Rn0.uSs4gD2XCkOsgemoBLE9tPasIZvouXioh6RLamd6Wbm2u', NULL, NULL, NULL, '1d9f1c6353c95280a934fa0725c1ca65', NULL, '::1', '2020-06-05 19:56:28', NULL, 1, NULL, NULL, 0, 0, 0, 0, 1, 1, 1, 0, 0, 0),
(19, 18, 1, 'umair ansari', '', 'mairtest2@gmail.com', '9891078797', NULL, '2020-06-05 19:57:40', '$2a$08$xv1XAI25N9A5uTfV6dA2o.G9hSrdDedj7FqfDCPguRLG/yGg3jJ72', NULL, NULL, NULL, '52b23f2b9c4ec88e1bab5cf121755380', NULL, '::1', '2020-06-05 19:57:55', NULL, 1, NULL, NULL, 0, 0, 0, 0, 1, 1, 1, 0, 0, 0),
(20, 19, 1, 'test', '', 'testfinal@gmail.com', '9891078797', NULL, '2020-06-06 20:42:39', '$2a$08$LsRgk2N5WBN9icIGQ0rxsu4sdsocKfabrZ.Zi9PtloizUmOKLEzUe', NULL, NULL, NULL, 'd487585ce3ff939a8c7ed01962fcec39', '2020-06-06 20:42:46', '::1', '2020-06-06 20:43:11', NULL, 1, NULL, NULL, 0, 0, 0, 0, 1, 1, 1, 0, 0, 0),
(21, 20, 1, 'test', '', 'testsurv@gmail.com', '9891078797', NULL, '2020-06-10 17:05:54', '$2a$08$U9UA3HlGr0/LFaZ2l6Q1G./AJDeJ3vnBMZxBUqDKEzWd4XLbc9WPe', NULL, NULL, NULL, '466c66b7b0b1829c4fcfdbcf0295f789', '2020-06-10 17:06:00', '::1', '2020-06-10 17:06:01', NULL, 1, NULL, NULL, 0, 0, 0, 0, 1, 1, 1, 0, 0, 0),
(22, 21, 1, 'test', '', 'testfinal1@gmail.com', '9891078797', NULL, '2020-06-13 11:59:42', '$2a$08$LdPoecZvXZwNpfsH/8Fig.C3y8WFlVxqhEHwbDgC0PMo5wVKj5TuK', NULL, NULL, NULL, '9d06c401047ba402ff2b0f30781f32a8', '2020-06-13 11:59:49', '::1', '2020-06-13 11:59:49', NULL, 1, NULL, NULL, 0, 0, 0, 0, 1, 1, 1, 0, 0, 0),
(23, 22, 1, 'test', '', 'testnav@gmail.com', '9891078797', NULL, '2020-06-13 14:25:44', '$2a$08$24KEzsep7kxDixWR368ioOgC2UPqhUuz/XpeFTf/zabVA.at0Tu5e', NULL, NULL, NULL, '151e61701a26caa297a778acb9d23dde', '2020-06-13 14:25:56', '::1', '2020-06-13 14:25:56', NULL, 1, NULL, NULL, 0, 0, 0, 0, 1, 1, 1, 0, 0, 0),
(24, 23, 1, 'Test Apag', '', 'bitminix@gmail.com', '9891078797', NULL, '2020-06-13 14:30:04', '$2a$08$HY5q3dTS.2NLlpeWi56.bOjCsKAgh6YmJqtHuOCcuGT.Zwz.WSF2q', NULL, NULL, NULL, '84a8636728d95569f57b1c3af7ea838d', '2020-06-13 14:30:17', '::1', '2020-06-15 15:53:32', '2020-06-13 15:07:22', 1, NULL, NULL, 0, 0, 0, 0, 1, 1, 1, 0, 0, 0),
(25, 24, 1, 'Umair', '', 'umair23@gmail.com', '9891078797', NULL, '2020-06-23 18:13:45', '$2a$08$LQrRhTWoHGnKwuoiF89/t.DKUh/BVrWrzc.yibYCxz8tSD.B.QzdS', NULL, NULL, NULL, '856a8aa97a4149777c8d5bad4c5aa4e8', '2020-06-23 18:13:51', '::1', '2020-06-23 18:13:51', NULL, 1, NULL, NULL, 0, 0, 0, 0, 1, 1, 1, 0, 0, 0),
(26, 25, 1, 'test', '', 'testa@gmail.com', '9999999991', NULL, '2020-06-29 15:48:47', '$2a$08$UW52jv38.fHNtiv.oQakqumz2YrkiNvBWUIKJvYnhGCQV9/olCBwS', NULL, NULL, NULL, '68523d5f1118f7fde67a200bc8d418f7', '2020-06-29 15:48:56', '::1', '2020-06-29 16:24:02', NULL, 1, NULL, NULL, 0, 0, 0, 0, 1, 1, 1, 1, 3, 3);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userid` (`userid`),
  ADD KEY `firstname` (`firstname`),
  ADD KEY `lastname` (`lastname`),
  ADD KEY `email` (`email`),
  ADD KEY `is_primary` (`is_primary`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
