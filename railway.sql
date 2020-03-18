-- phpMyAdmin SQL Dump
-- version 4.9.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Mar 18, 2020 at 03:04 PM
-- Server version: 10.4.10-MariaDB
-- PHP Version: 7.3.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `railway`
--

-- --------------------------------------------------------

--
-- Table structure for table `city`
--

DROP TABLE IF EXISTS `city`;
CREATE TABLE IF NOT EXISTS `city` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `city`
--

INSERT INTO `city` (`id`, `name`) VALUES
(1, 'A'),
(2, 'B'),
(3, 'C'),
(4, 'D'),
(5, 'E'),
(6, 'F'),
(7, 'G');

-- --------------------------------------------------------

--
-- Table structure for table `nodes`
--

DROP TABLE IF EXISTS `nodes`;
CREATE TABLE IF NOT EXISTS `nodes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nodes_ok` tinyint(1) NOT NULL,
  `schedule_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_1D3D05FCA40BC2D5` (`schedule_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nodes_city`
--

DROP TABLE IF EXISTS `nodes_city`;
CREATE TABLE IF NOT EXISTS `nodes_city` (
  `nodes_id` int(11) NOT NULL,
  `city_id` int(11) NOT NULL,
  PRIMARY KEY (`nodes_id`,`city_id`),
  KEY `IDX_19BB259EFF80F7CD` (`nodes_id`),
  KEY `IDX_19BB259E8BAC62AF` (`city_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nodes_schedule`
--

DROP TABLE IF EXISTS `nodes_schedule`;
CREATE TABLE IF NOT EXISTS `nodes_schedule` (
  `nodes_id` int(11) NOT NULL,
  `schedule_id` int(11) NOT NULL,
  PRIMARY KEY (`nodes_id`,`schedule_id`),
  KEY `IDX_3A26BFA6FF80F7CD` (`nodes_id`),
  KEY `IDX_3A26BFA6A40BC2D5` (`schedule_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `schedule`
--

DROP TABLE IF EXISTS `schedule`;
CREATE TABLE IF NOT EXISTS `schedule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `city_start_id` int(11) DEFAULT NULL,
  `city_end_id` int(11) DEFAULT NULL,
  `time_start` time NOT NULL,
  `time_end` time NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `distance` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_5A3811FBE7E581FD` (`city_start_id`),
  KEY `IDX_5A3811FB17F1C4E0` (`city_end_id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `schedule`
--

INSERT INTO `schedule` (`id`, `city_start_id`, `city_end_id`, `time_start`, `time_end`, `status`, `distance`) VALUES
(1, 1, 2, '08:00:00', '09:00:00', 'unvisited', 1),
(2, 2, 3, '12:00:00', '14:00:00', 'unvisited', 2),
(3, 1, 3, '09:00:00', '10:00:00', 'unvisited', 3),
(4, 3, 2, '13:00:00', '15:00:00', 'unvisited', 7),
(5, 2, 4, '14:00:00', '16:00:00', 'unvisited', 6),
(6, 4, 5, '12:00:00', '13:00:00', 'unvisited', 3),
(7, 4, 3, '11:00:00', '15:00:00', 'unvisited', 7),
(8, 6, 3, '16:00:00', '19:00:00', 'unvisited', 7),
(9, 4, 6, '08:00:00', '11:00:00', 'unvisited', 7),
(10, 7, 3, '06:00:00', '09:00:00', 'unvisited', 8),
(11, 1, 7, '09:00:00', '12:00:00', 'unvisited', 3),
(12, 1, 2, '04:00:00', '05:00:00', 'unvisited', 5),
(13, 2, 3, '14:00:00', '16:00:00', 'unvisited', 7),
(14, 1, 6, '09:00:00', '10:00:00', 'unvisited', 3),
(15, 3, 2, '12:00:00', '15:00:00', 'unvisited', 5),
(16, 2, 4, '06:00:00', '07:00:00', 'unvisited', 7),
(17, 4, 5, '12:00:00', '13:00:00', 'unvisited', 10),
(18, 4, 3, '08:00:00', '10:00:00', 'unvisited', 8),
(19, 6, 3, '16:00:00', '19:00:00', 'unvisited', 1),
(20, 4, 6, '08:00:00', '11:00:00', 'unvisited', 2),
(21, 7, 3, '08:00:00', '12:00:00', 'unvisited', 6),
(22, 1, 7, '05:00:00', '07:00:00', 'unvisited', 1),
(23, 6, 4, '11:00:00', '12:00:00', 'unvisited', 4),
(24, 4, 2, '13:00:00', '14:00:00', 'unvisited', 5);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `nodes`
--
ALTER TABLE `nodes`
  ADD CONSTRAINT `FK_1D3D05FCA40BC2D5` FOREIGN KEY (`schedule_id`) REFERENCES `schedule` (`id`);

--
-- Constraints for table `nodes_city`
--
ALTER TABLE `nodes_city`
  ADD CONSTRAINT `FK_19BB259E8BAC62AF` FOREIGN KEY (`city_id`) REFERENCES `city` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_19BB259EFF80F7CD` FOREIGN KEY (`nodes_id`) REFERENCES `nodes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `nodes_schedule`
--
ALTER TABLE `nodes_schedule`
  ADD CONSTRAINT `FK_3A26BFA6A40BC2D5` FOREIGN KEY (`schedule_id`) REFERENCES `schedule` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_3A26BFA6FF80F7CD` FOREIGN KEY (`nodes_id`) REFERENCES `nodes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `schedule`
--
ALTER TABLE `schedule`
  ADD CONSTRAINT `FK_5A3811FB17F1C4E0` FOREIGN KEY (`city_end_id`) REFERENCES `city` (`id`),
  ADD CONSTRAINT `FK_5A3811FBE7E581FD` FOREIGN KEY (`city_start_id`) REFERENCES `city` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
