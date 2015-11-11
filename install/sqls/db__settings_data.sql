-- phpMyAdmin SQL Dump
-- version 4.4.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Nov 11, 2015 at 01:02 AM
-- Server version: 5.6.26
-- PHP Version: 5.6.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `mtgv9`
--

-- --------------------------------------------------------

--
-- Table structure for table `settings_game`
--

CREATE TABLE IF NOT EXISTS `settings_game` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `settings_game`
--

INSERT INTO `settings_game` (`id`, `name`, `value`) VALUES
(1, 'staff_pad', 'Welcome to MTG Codes v9'),
(2, 'game_name', 'MTG Test'),
(3, 'game_description', ''),
(4, 'register_start_cash', '100'),
(5, 'register_promo_code', 'Your Promo Code Here'),
(6, 'register_promo_cash', '100'),
(7, 'game_owner_id', '1'),
(8, 'main_currency_symbol', '&pound;'),
(9, 'staff_notepad', 'Blah'),
(10, 'engine_version', '9.0.0126');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `settings_game`
--
ALTER TABLE `settings_game`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `settings_game`
--
ALTER TABLE `settings_game`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=11;