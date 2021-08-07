-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Aug 02, 2021 at 12:16 PM
-- Server version: 5.7.31
-- PHP Version: 7.3.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `prostate_cancer`
--

-- --------------------------------------------------------

--
-- Table structure for table `diagnose`
--

DROP TABLE IF EXISTS `diagnose`;
CREATE TABLE IF NOT EXISTS `diagnose` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `diagnosis_hash` varchar(40) DEFAULT NULL,
  `userid` int(11) NOT NULL,
  `psa` double(5,2) NOT NULL,
  `activity` double(5,2) NOT NULL,
  `pbv` double(5,2) NOT NULL,
  `ethnicity` varchar(20) NOT NULL,
  `gleason_score` int(3) NOT NULL,
  `ipss` int(3) NOT NULL,
  `fpsa` double(5,2) NOT NULL,
  `tnms` enum('t1a','t1b','t2a','t2b','t3a','t3b','t3c','t4a','nil') DEFAULT NULL,
  `l123f` varchar(20) DEFAULT NULL,
  `l123ftxt` text,
  `heredity` enum('yes','no') NOT NULL,
  `user_request` tinyint(1) NOT NULL DEFAULT '0',
  `user_request_reply` tinyint(1) NOT NULL DEFAULT '0',
  `viewed_user_request` tinyint(1) NOT NULL DEFAULT '0',
  `viewed_report` tinyint(1) NOT NULL DEFAULT '0',
  `request_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `request_reply_time` datetime DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=95 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `diagnose`
--

INSERT INTO `diagnose` (`id`, `diagnosis_hash`, `userid`, `psa`, `activity`, `pbv`, `ethnicity`, `gleason_score`, `ipss`, `fpsa`, `tnms`, `l123f`, `l123ftxt`, `heredity`, `user_request`, `user_request_reply`, `viewed_user_request`, `viewed_report`, `request_time`, `request_reply_time`, `status`) VALUES
(82, '8793d7a5ffd1e8232d9bcd7403960b515cc7769e', 5, 12.23, 12.34, 3.44, '34.22', 3, 2, 2.34, 't3b', NULL, 'njknj{{_}}jnjknk{{_}}jnkk{{_}}eat well', 'yes', 1, 1, 1, 1, '2021-07-22 09:24:47', NULL, 0),
(85, 'c1dfd96eea8cc2b62785275bca38ac261256e278', 5, 12.23, 12.34, 3.44, '34.22', 3, 2, 2.34, 'nil', NULL, NULL, 'yes', 0, 0, 1, 0, '2021-07-22 09:30:20', NULL, 0),
(92, '6d8b890f00b13c3ed08553a31800f32a414ba457', 5, 12.23, 12.34, 3.44, '34.22', 3, 2, 2.34, 'nil', NULL, NULL, 'yes', 0, 0, 0, 0, '2021-07-22 09:40:26', NULL, 0),
(93, '62055daf70716a9462befac11666144b4b683e6a', 5, 12.23, 12.34, 3.44, '34.22', 3, 2, 2.34, 't4a', NULL, 'very great{{_}}drink excess water{{_}}eat well{{_}}exercsise twicw ad da', 'yes', 1, 1, 1, 1, '2021-07-22 09:40:40', NULL, 0),
(94, '15726e3d86de408818fde48bd70a92ee9ace419c', 5, 12.23, 12.34, 3.44, '34.22', 3, 2, 2.34, 't2a', NULL, 'Take it in {{_}}Second intake{{_}}Third film instated{{_}}lastly inputed', 'yes', 1, 1, 1, 1, '2021-07-22 09:42:29', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `usertype` tinyint(1) NOT NULL DEFAULT '0',
  `surname` varchar(40) NOT NULL,
  `firstname` varchar(40) NOT NULL,
  `username` varchar(40) NOT NULL,
  `password` varchar(40) NOT NULL,
  `address` varchar(50) NOT NULL,
  `phone` varchar(11) NOT NULL,
  `occupation` varchar(40) DEFAULT NULL,
  `nok` varchar(40) DEFAULT NULL,
  `relationship_nok` varchar(40) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `marital_status` enum('single','married','divorced','widowed') NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `usertype`, `surname`, `firstname`, `username`, `password`, `address`, `phone`, `occupation`, `nok`, `relationship_nok`, `dob`, `marital_status`) VALUES
(1, 0, 'Udoh', 'Joseph', 'jossy_boy123@yahoo.com', '93b337b2d5176f76eadeb699c067591f90ce5d2f', '12 itu Road', '3546557', 'Teacher', 'BRIAN', 'Father', '2000-09-03', 'single'),
(2, 0, 'Udoh', 'Joseph', 'jossy_boy12ds3@yahoo.com', 'jossyboy', 'Joseph', '93455', 'Programmer', 'BEN', 'Brother', '2000-09-03', 'single'),
(3, 1, 'Nkanga', 'Nsikan', 'nsikan@prostatedoctor.com', '7940ef8250bca869e0ea3586b2ffd3c1ef04273a', 'Joseph', '070887345', 'Teacher', 'BRIAN', 'Father', '2000-09-03', 'divorced'),
(4, 0, 'Nkanga', 'Nsikan', 'jorefsss', '9d8bf187331a254c32f5286dff3af1f6bb7fd20f', 'Nsikan', '07087410660', 'Student', 'Mfon Nkanga', 'Brother', '2000-09-03', 'single'),
(5, 0, 'Udoh', 'Joseph', 'joref', 'joref', '12, Adderej kjkj', '08026811396', 'Db', NULL, NULL, '2000-09-03', 'single');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
