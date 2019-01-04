-- phpMyAdmin SQL Dump
-- version 4.6.6
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 04, 2019 at 10:28 PM
-- Server version: 5.6.35
-- PHP Version: 7.1.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `devSense`
--

-- --------------------------------------------------------

--
-- Table structure for table `black_list`
--

CREATE TABLE `black_list` (
  `id` int(11) NOT NULL,
  `ip` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `dateCreated` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `black_list`
--

INSERT INTO `black_list` (`id`, `ip`, `dateCreated`) VALUES
(1, '::1', '2019-01-03 13:23:55'),
(2, '::1', '2019-01-03 13:24:46'),
(3, '::1', '2019-01-03 13:31:58'),
(4, '::1', '2019-01-03 14:07:09');

-- --------------------------------------------------------

--
-- Table structure for table `friends`
--

CREATE TABLE `friends` (
  `id` int(11) NOT NULL,
  `myId` int(11) NOT NULL,
  `friendId` int(11) NOT NULL,
  `dateCreated` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `friends`
--

INSERT INTO `friends` (`id`, `myId`, `friendId`, `dateCreated`) VALUES
(22, 1, 4, '2019-01-04 22:10:17'),
(23, 1, 3, '2019-01-04 22:11:40'),
(24, 1, 5, '2019-01-04 22:13:34'),
(25, 1, 8, '2019-01-04 22:13:41'),
(26, 1, 6, '2019-01-04 22:14:04'),
(46, 3, 8, '2019-01-04 22:25:22'),
(47, 3, 7, '2019-01-04 22:25:26'),
(48, 3, 4, '2019-01-04 22:25:30'),
(49, 3, 1, '2019-01-04 22:25:34'),
(50, 3, 5, '2019-01-04 22:25:38'),
(51, 2, 3, '2019-01-04 22:26:15'),
(52, 2, 8, '2019-01-04 22:26:19'),
(53, 2, 5, '2019-01-04 22:26:23'),
(54, 2, 4, '2019-01-04 22:26:27'),
(55, 2, 6, '2019-01-04 22:26:33');

-- --------------------------------------------------------

--
-- Table structure for table `hobbies`
--

CREATE TABLE `hobbies` (
  `id` int(11) NOT NULL,
  `hobby` varchar(1120) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `hobbies`
--

INSERT INTO `hobbies` (`id`, `hobby`) VALUES
(1, 'soccerball'),
(2, 'ping pong'),
(3, 'jogging'),
(4, 'read book'),
(5, 'tell jokes');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `userName` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `birthday` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `userName`, `name`, `password`, `birthday`) VALUES
(1, 'daniel745', 'daniel', '1234', '2019-01-09 00:00:00'),
(2, 'shimi', 'shimi', '4323', '2019-01-07 00:00:00'),
(3, 'david', 'david', 'david1', '2019-01-01 00:00:00'),
(4, 'moran', 'moran', '54321', '2019-01-06 00:00:00'),
(5, 'miki', 'miki', '999999', '2019-01-19 00:00:00'),
(6, 'shoshi', 'shoshi', '11234', '2019-01-09 00:00:00'),
(7, 'kobi', 'kobi', '112345', '2019-01-05 00:00:00'),
(8, 'amit', 'amit', '888377', '2019-01-01 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `usersHobbies`
--

CREATE TABLE `usersHobbies` (
  `id` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `hobbyId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `usersHobbies`
--

INSERT INTO `usersHobbies` (`id`, `userId`, `hobbyId`) VALUES
(1, 1, 2),
(2, 1, 1),
(3, 1, 3),
(4, 2, 1),
(5, 2, 4),
(6, 3, 1),
(7, 3, 4),
(8, 1, 5);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `black_list`
--
ALTER TABLE `black_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `friends`
--
ALTER TABLE `friends`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hobbies`
--
ALTER TABLE `hobbies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `usersHobbies`
--
ALTER TABLE `usersHobbies`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `black_list`
--
ALTER TABLE `black_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `friends`
--
ALTER TABLE `friends`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;
--
-- AUTO_INCREMENT for table `hobbies`
--
ALTER TABLE `hobbies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `usersHobbies`
--
ALTER TABLE `usersHobbies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
