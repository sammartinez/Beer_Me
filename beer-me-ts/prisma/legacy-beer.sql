-- phpMyAdmin SQL Dump
-- version 4.4.11
-- http://www.phpmyadmin.net
--
-- Host: localhost:8080
-- Generation Time: Sep 04, 2015 at 12:29 AM
-- Server version: 5.6.26
-- PHP Version: 5.6.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `beer`
--
CREATE DATABASE IF NOT EXISTS `beer` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `beer`;

-- --------------------------------------------------------

--
-- Table structure for table `bars`
--

CREATE TABLE IF NOT EXISTS `bars` (
  `name` varchar(50) DEFAULT NULL,
  `address` varchar(50) DEFAULT NULL,
  `phone` varchar(25) DEFAULT NULL,
  `website` varchar(50) DEFAULT NULL,
  `id` bigint(20) unsigned NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bars`
--

INSERT INTO `bars` (`name`, `address`, `phone`, `website`, `id`) VALUES
('Side Street', '123 ABC St.', '123-456-7890', 'http://www.sidestreetpdx.com', 1),
('Test Bar', '312 CBA Ave.', '444-444-4444', 'bar.com', 2),
('Target', '123 Wall St. ', '345-890-7845', 'www.target.com', 3),
('Apex', '987 Heresville', '999-999-9999', 'www.apexpdx.com', 4),
('Binks', 'Alberta St', '999-999-9999', 'www.binks.com', 5);

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE IF NOT EXISTS `items` (
  `description` varchar(50) DEFAULT NULL,
  `cost` decimal(8,2) DEFAULT NULL,
  `id` bigint(20) unsigned NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`description`, `cost`, `id`) VALUES
('Domestic', '3.00', 1),
('Import', '5.00', 2),
('Domestic', '5.00', 4),
('Import', '10.00', 5),
('Special', '1.00', 6),
('International', '20.00', 7);

-- --------------------------------------------------------

--
-- Table structure for table `menus`
--

CREATE TABLE IF NOT EXISTS `menus` (
  `bar_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `id` bigint(20) unsigned NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `menus`
--

INSERT INTO `menus` (`bar_id`, `item_id`, `id`) VALUES
(1, 1, 1),
(1, 2, 2),
(1, 3, 3),
(2, 4, 4),
(2, 5, 5),
(2, 6, 6),
(4, 7, 7),
(4, 0, 8),
(1, 0, 9);

-- --------------------------------------------------------

--
-- Table structure for table `patrons`
--

CREATE TABLE IF NOT EXISTS `patrons` (
  `name` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `id` bigint(20) unsigned NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `patrons`
--

INSERT INTO `patrons` (`name`, `email`, `id`) VALUES
('Casey', 'heitz.casey@gmail.com', 1),
('Casey', 'casey@email.com', 2),
('Sam', 'sammartinez19@gmail.com', 3),
('Kyle', 'kyle@email.com', 4);

-- --------------------------------------------------------

--
-- Table structure for table `preferbars`
--

CREATE TABLE IF NOT EXISTS `preferbars` (
  `bar_id` int(11) DEFAULT NULL,
  `patron_id` int(11) DEFAULT NULL,
  `id` bigint(20) unsigned NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `preferbars`
--

INSERT INTO `preferbars` (`bar_id`, `patron_id`, `id`) VALUES
(1, 2, 7),
(2, 2, 9),
(1, 3, 12),
(2, 3, 14),
(3, 3, 15),
(4, 3, 16),
(2, 1, 31),
(3, 1, 33),
(5, 4, 34),
(1, 1, 35);

-- --------------------------------------------------------

--
-- Table structure for table `tokens`
--

CREATE TABLE IF NOT EXISTS `tokens` (
  `patron_id` int(11) DEFAULT NULL,
  `menu_id` int(11) DEFAULT NULL,
  `sender_id` int(11) DEFAULT NULL,
  `id` bigint(20) unsigned NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tokens`
--

INSERT INTO `tokens` (`patron_id`, `menu_id`, `sender_id`, `id`) VALUES
(3, 6, 1, 26),
(3, 7, 4, 28),
(3, 1, 1, 32),
(3, 1, 1, 33),
(3, 1, 1, 34),
(3, 1, 1, 35),
(1, 1, 3, 37),
(3, 1, 1, 39),
(3, 1, 1, 40),
(3, 1, 1, 41),
(3, 7, 4, 42),
(3, 6, 4, 43),
(3, 6, 4, 44),
(3, 6, 4, 45),
(3, 6, 4, 46),
(3, 6, 4, 47),
(3, 6, 4, 48),
(3, 6, 4, 49),
(3, 6, 4, 50),
(3, 6, 4, 51),
(3, 7, 4, 52),
(1, 2, 1, 53);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bars`
--
ALTER TABLE `bars`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `menus`
--
ALTER TABLE `menus`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `patrons`
--
ALTER TABLE `patrons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `preferbars`
--
ALTER TABLE `preferbars`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `tokens`
--
ALTER TABLE `tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bars`
--
ALTER TABLE `bars`
  MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `menus`
--
ALTER TABLE `menus`
  MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `patrons`
--
ALTER TABLE `patrons`
  MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `preferbars`
--
ALTER TABLE `preferbars`
  MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=36;
--
-- AUTO_INCREMENT for table `tokens`
--
ALTER TABLE `tokens`
  MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=54;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
