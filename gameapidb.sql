-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 15, 2022 at 12:38 AM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gameapidb`
--
CREATE DATABASE IF NOT EXISTS `gameapidb` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `gameapidb`;

-- --------------------------------------------------------

--
-- Table structure for table `author`
--

DROP TABLE IF EXISTS `author`;
CREATE TABLE `author` (
  `author_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `num_reviews` int(11) NOT NULL,
  `review_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `game`
--

DROP TABLE IF EXISTS `game`;
CREATE TABLE `game` (
  `game_id` int(11) NOT NULL,
  `title` varchar(50) NOT NULL,
  `thumbnail` varchar(50) NOT NULL,
  `short_description` text NOT NULL,
  `game_url` varchar(50) NOT NULL,
  `release_date` date NOT NULL,
  `genre` varchar(50) NOT NULL,
  `platform` varchar(50) NOT NULL,
  `publisher` varchar(50) NOT NULL,
  `developer` varchar(50) NOT NULL,
  `review_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

DROP TABLE IF EXISTS `review`;
CREATE TABLE `review` (
  `review_id` int(11) NOT NULL,
  `review_text` text NOT NULL,
  `rating` double NOT NULL,
  `game_id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `author`
--
ALTER TABLE `author`
  ADD PRIMARY KEY (`author_id`),
  ADD KEY `Fk_reviewToAuthor` (`review_id`);

--
-- Indexes for table `game`
--
ALTER TABLE `game`
  ADD PRIMARY KEY (`game_id`);

--
-- Indexes for table `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `Fk_reviewToGame` (`game_id`),
  ADD KEY `Fk_authorIdToAuthor` (`author_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `author`
--
ALTER TABLE `author`
  MODIFY `author_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `game`
--
ALTER TABLE `game`
  MODIFY `game_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `review`
--
ALTER TABLE `review`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `author`
--
ALTER TABLE `author`
  ADD CONSTRAINT `Fk_reviewToAuthor` FOREIGN KEY (`review_id`) REFERENCES `review` (`review_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `review`
--
ALTER TABLE `review`
  ADD CONSTRAINT `Fk_authorIdToAuthor` FOREIGN KEY (`author_id`) REFERENCES `author` (`author_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Fk_reviewToGame` FOREIGN KEY (`game_id`) REFERENCES `game` (`game_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
