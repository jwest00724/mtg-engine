-- phpMyAdmin SQL Dump
-- version 4.4.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Nov 14, 2015 at 12:27 PM
-- Server version: 5.6.26
-- PHP Version: 5.6.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mtgv9`
--

-- --------------------------------------------------------

--
-- Table structure for table `breport`
--

DROP TABLE IF EXISTS `breport`;
CREATE TABLE IF NOT EXISTS `breport` (
  `id` int(11) NOT NULL,
  `urgency` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `date_sent` varchar(255) NOT NULL,
  `description` longtext NOT NULL,
  `status` enum('Yes','No') NOT NULL DEFAULT 'No',
  `poster` int(11) NOT NULL DEFAULT '0',
  `lock_status` int(2) NOT NULL DEFAULT '0',
  `assigned` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `breport_responses`
--

DROP TABLE IF EXISTS `breport_responses`;
CREATE TABLE IF NOT EXISTS `breport_responses` (
  `id` int(11) NOT NULL,
  `bug` int(11) NOT NULL DEFAULT '0',
  `user` int(11) NOT NULL DEFAULT '0',
  `response` text NOT NULL,
  `date_sent` bigint(25) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `cities`
--

DROP TABLE IF EXISTS `cities`;
CREATE TABLE IF NOT EXISTS `cities` (
  `cityid` int(11) NOT NULL,
  `cityname` varchar(255) NOT NULL,
  `citydesc` longtext NOT NULL,
  `cityminlevel` int(11) NOT NULL DEFAULT '0',
  `time` int(11) DEFAULT '0',
  `citycountry` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `forums`
--

CREATE TABLE `forums` (
  `id` INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL DEFAULT '',
  `description` VARCHAR(255) NOT NULL DEFAULT '',
  `publicity` ENUM('all', 'upgraded', 'staff') NOT NULL DEFAULT 'all',
  `is_recycle` TINYINT(1) NOT NULL DEFAULT 0
);

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

DROP TABLE IF EXISTS `inventory`;
CREATE TABLE IF NOT EXISTS `inventory` (
  `id` int(11) NOT NULL,
  `item` int(11) NOT NULL DEFAULT '0',
  `user` int(11) NOT NULL DEFAULT '0',
  `qty` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

DROP TABLE IF EXISTS `items`;
CREATE TABLE IF NOT EXISTS `items` (
  `id` int(11) NOT NULL,
  `type` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL,
  `desc` mediumtext NOT NULL,
  `buyprice` bigint(25) NOT NULL DEFAULT '0',
  `sellprice` bigint(25) NOT NULL DEFAULT '0',
  `effect1_on` tinyint(4) NOT NULL DEFAULT '0',
  `effect1` text NOT NULL,
  `effect2_on` tinyint(4) NOT NULL DEFAULT '0',
  `effect2` text NOT NULL,
  `effect3_on` tinyint(4) NOT NULL DEFAULT '0',
  `effect3` text NOT NULL,
  `effect4_on` int(11) DEFAULT NULL,
  `effect4` text NOT NULL,
  `weapon` int(11) NOT NULL DEFAULT '0',
  `armour` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `itemtypes`
--

DROP TABLE IF EXISTS `itemtypes`;
CREATE TABLE IF NOT EXISTS `itemtypes` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `jobranks`
--

DROP TABLE IF EXISTS `jobranks`;
CREATE TABLE IF NOT EXISTS `jobranks` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `job` int(11) NOT NULL DEFAULT '0',
  `pay` int(11) NOT NULL DEFAULT '0',
  `gain_strength` int(11) NOT NULL DEFAULT '0',
  `gain_agility` int(11) NOT NULL DEFAULT '0',
  `gain_guard` int(11) NOT NULL DEFAULT '0',
  `gain_labour` int(11) NOT NULL DEFAULT '0',
  `gain_iq` int(11) NOT NULL DEFAULT '0',
  `requirement_strength` int(11) NOT NULL DEFAULT '0',
  `requirement_agility` int(11) NOT NULL DEFAULT '0',
  `requirement_guard` int(11) NOT NULL DEFAULT '0',
  `requirement_labour` int(11) NOT NULL DEFAULT '0',
  `requirement_iq` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `first_rank` int(11) NOT NULL DEFAULT '0',
  `desc` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `logs_query_errors`
--

DROP TABLE IF EXISTS `logs_query_errors`;
CREATE TABLE IF NOT EXISTS `logs_query_errors` (
  `id` int(11) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `query` text NOT NULL,
  `error` text NOT NULL,
  `fixed` tinyint(4) NOT NULL DEFAULT '0',
  `by` int(11) NOT NULL DEFAULT '0',
  `page` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `logs_staff`
--

DROP TABLE IF EXISTS `logs_staff`;
CREATE TABLE IF NOT EXISTS `logs_staff` (
  `id` int(11) NOT NULL,
  `user` int(11) NOT NULL DEFAULT '0',
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `action` text NOT NULL,
  `ip` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mail`
--

DROP TABLE IF EXISTS `mail`;
CREATE TABLE IF NOT EXISTS `mail` (
  `id` int(11) NOT NULL,
  `read` int(11) NOT NULL DEFAULT '0',
  `sender` int(11) NOT NULL DEFAULT '0',
  `receiver` int(11) NOT NULL DEFAULT '0',
  `time_sent` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `subject` varchar(255) NOT NULL,
  `message` mediumtext NOT NULL,
  `deleted` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `market_items`
--

DROP TABLE IF EXISTS `market_items`;
CREATE TABLE IF NOT EXISTS `market_items` (
  `id` int(11) NOT NULL,
  `item` int(11) NOT NULL DEFAULT '0',
  `user` int(11) NOT NULL DEFAULT '0',
  `price` bigint(25) NOT NULL DEFAULT '0',
  `qty` bigint(25) NOT NULL DEFAULT '0',
  `currency` enum('money','points') NOT NULL DEFAULT 'money',
  `time_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `market_points`
--

DROP TABLE IF EXISTS `market_points`;
CREATE TABLE IF NOT EXISTS `market_points` (
  `id` int(11) NOT NULL,
  `qty` bigint(25) NOT NULL DEFAULT '0',
  `user` int(11) NOT NULL DEFAULT '0',
  `price` bigint(25) NOT NULL DEFAULT '0',
  `time_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `preports`
--

DROP TABLE IF EXISTS `preports`;
CREATE TABLE IF NOT EXISTS `preports` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL DEFAULT '0',
  `reported` int(11) NOT NULL DEFAULT '0',
  `description` longtext NOT NULL,
  `status` enum('open','processing','handled') NOT NULL DEFAULT 'open',
  `assigned` int(11) NOT NULL DEFAULT '0',
  `date_sent` bigint(25) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `preports_responses`
--

DROP TABLE IF EXISTS `preports_responses`;
CREATE TABLE IF NOT EXISTS `preports_responses` (
  `id` int(11) NOT NULL,
  `report` int(11) NOT NULL DEFAULT '0',
  `user` int(11) NOT NULL DEFAULT '0',
  `response` text NOT NULL,
  `date_sent` bigint(25) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `settings_crons`
--

DROP TABLE IF EXISTS `settings_crons`;
CREATE TABLE IF NOT EXISTS `settings_crons` (
  `id` int(11) NOT NULL,
  `type` varchar(25) NOT NULL DEFAULT '',
  `last` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `settings_game`
--

DROP TABLE IF EXISTS `settings_game`;
CREATE TABLE IF NOT EXISTS `settings_game` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `settings_mods`
--

DROP TABLE IF EXISTS `settings_mods`;
CREATE TABLE IF NOT EXISTS `settings_mods` (
  `id` int(11) NOT NULL,
  `area` varchar(255) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `shopitems`
--

DROP TABLE IF EXISTS `shopitems`;
CREATE TABLE IF NOT EXISTS `shopitems` (
  `sitemID` int(11) NOT NULL,
  `sitemSHOP` int(11) NOT NULL DEFAULT '0',
  `sitemITEMID` int(11) NOT NULL DEFAULT '0',
  `sitemSTOCK` bigint(25) NOT NULL DEFAULT '0',
  `sitemSOLD` bigint(25) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `shops`
--

DROP TABLE IF EXISTS `shops`;
CREATE TABLE IF NOT EXISTS `shops` (
  `shopID` int(11) NOT NULL,
  `shopLOCATION` int(11) NOT NULL DEFAULT '0',
  `shopNAME` varchar(255) NOT NULL,
  `shopDESCRIPTION` mediumtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `staff_ranks`
--

DROP TABLE IF EXISTS `staff_ranks`;
CREATE TABLE IF NOT EXISTS `staff_ranks` (
  `rank_id` int(11) NOT NULL,
  `rank_name` varchar(255) NOT NULL,
  `rank_desc` varchar(255) NOT NULL,
  `rank_order` int(11) NOT NULL DEFAULT '0',
  `rank_colour` varchar(6) NOT NULL DEFAULT '000000',
  `override_all` enum('Yes','No') NOT NULL DEFAULT 'No',
  `staff_panel_access` enum('Yes','No') NOT NULL DEFAULT 'No',
  `staff_panel_tasks_manage` enum('Yes','No') NOT NULL DEFAULT 'No',
  `staff_panel_tasks_add` enum('Yes','No') NOT NULL DEFAULT 'No',
  `staff_panel_tasks_edit` enum('Yes','No') NOT NULL DEFAULT 'No',
  `staff_panel_tasks_delete` enum('Yes','No') NOT NULL DEFAULT 'No',
  `staff_panel_tasks_groups_manage` enum('Yes','No') NOT NULL DEFAULT 'No',
  `staff_panel_tasks_groups_add` enum('Yes','No') NOT NULL DEFAULT 'No',
  `staff_panel_tasks_groups_edit` enum('Yes','No') NOT NULL DEFAULT 'No',
  `staff_panel_tasks_groups_delete` enum('Yes','No') NOT NULL DEFAULT 'No',
  `staff_panel_staff_ranks_manage` enum('Yes','No') NOT NULL DEFAULT 'No',
  `staff_panel_staff_ranks_add` enum('Yes','No') NOT NULL DEFAULT 'No',
  `staff_panel_staff_ranks_edit` enum('Yes','No') NOT NULL DEFAULT 'No',
  `staff_panel_staff_ranks_delete` enum('Yes','No') NOT NULL DEFAULT 'No',
  `staff_panel_code_version_manage` enum('Yes','No') NOT NULL DEFAULT 'No',
  `staff_panel_logs_staff` enum('Yes','No') NOT NULL DEFAULT 'No'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

DROP TABLE IF EXISTS `tasks`;
CREATE TABLE IF NOT EXISTS `tasks` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `nerve` int(11) NOT NULL DEFAULT '0',
  `formula` mediumtext NOT NULL,
  `group_id` int(11) NOT NULL DEFAULT '0',
  `courses_required` varchar(255) NOT NULL DEFAULT '',
  `text_start` mediumtext NOT NULL,
  `text_success` mediumtext NOT NULL,
  `text_failure` mediumtext NOT NULL,
  `text_jail` mediumtext NOT NULL,
  `text_hospital` text NOT NULL,
  `time_jail` int(11) NOT NULL DEFAULT '0',
  `text_reason_jail` varchar(255) NOT NULL,
  `time_hospital` int(11) NOT NULL DEFAULT '0',
  `text_reason_hospital` varchar(255) NOT NULL DEFAULT '',
  `upgraded_only` tinyint(1) NOT NULL DEFAULT '0',
  `awarded_money_min` int(11) NOT NULL DEFAULT '0',
  `awarded_money_max` int(11) NOT NULL DEFAULT '0',
  `awarded_points_min` int(11) NOT NULL DEFAULT '0',
  `awarded_points_max` int(11) NOT NULL DEFAULT '0',
  `awarded_xp_min` int(11) NOT NULL DEFAULT '0',
  `awarded_xp_max` int(11) NOT NULL DEFAULT '0',
  `awarded_item` int(11) NOT NULL DEFAULT '0',
  `awarded_item_qty` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tasks_groups`
--

DROP TABLE IF EXISTS `tasks_groups`;
CREATE TABLE IF NOT EXISTS `tasks_groups` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `ordering` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` text NOT NULL,
  `email` varchar(255) NOT NULL,
  `level` int(11) NOT NULL DEFAULT '1',
  `exp` decimal(24,4) NOT NULL DEFAULT '0.0000',
  `job` int(11) NOT NULL DEFAULT '0',
  `job_rank` int(11) NOT NULL DEFAULT '0',
  `location` int(11) NOT NULL DEFAULT '0',
  `hospital` int(11) NOT NULL DEFAULT '0',
  `hospital_reason` int(11) NOT NULL DEFAULT '0',
  `jail` int(11) NOT NULL DEFAULT '0',
  `jail_reason` text NOT NULL,
  `staff_rank` bigint(25) NOT NULL DEFAULT '0',
  `last_seen` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `profile_picture` varchar(255) NOT NULL DEFAULT '',
  `status` text NOT NULL,
  `upgraded` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
  `account_locked` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
  `login_attempts` TINYINT(5) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users_bans`
--

DROP TABLE IF EXISTS `users_bans`;
CREATE TABLE IF NOT EXISTS `users_bans` (
  `id` int(11) NOT NULL,
  `user` int(11) NOT NULL DEFAULT '0',
  `time_enforced` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `time_expires` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `enforcer` int(11) NOT NULL DEFAULT '0',
  `ban_type` enum('messages','game','forum') NOT NULL DEFAULT 'game'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users_equipment`
--

DROP TABLE IF EXISTS `users_equipment`;
CREATE TABLE IF NOT EXISTS `users_equipment` (
  `id` int(11) NOT NULL,
  `equip_primary` int(11) NOT NULL DEFAULT '0',
  `equip_secondary` int(11) NOT NULL DEFAULT '0',
  `equip_armor` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users_events`
--

DROP TABLE IF EXISTS `users_events`;
CREATE TABLE IF NOT EXISTS `users_events` (
  `id` int(11) NOT NULL,
  `user` int(11) NOT NULL DEFAULT '0',
  `time_sent` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `type` varchar(255) NOT NULL DEFAULT 'Uncategorized',
  `text` text NOT NULL,
  `read` tinyint(1) NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `extra` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users_finances`
--

DROP TABLE IF EXISTS `users_finances`;
CREATE TABLE IF NOT EXISTS `users_finances` (
  `id` int(11) NOT NULL,
  `money` bigint(25) NOT NULL DEFAULT '0',
  `points` bigint(25) NOT NULL DEFAULT '0',
  `bank` bigint(25) NOT NULL DEFAULT '-1',
  `merits` bigint(25) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users_messages`
--

DROP TABLE IF EXISTS `users_messages`;
CREATE TABLE IF NOT EXISTS `users_messages` (
  `id` int(11) NOT NULL,
  `sender` int(11) NOT NULL DEFAULT '0',
  `receiver` int(11) NOT NULL DEFAULT '0',
  `subject` varchar(255) NOT NULL DEFAULT 'No subject',
  `message` text NOT NULL,
  `time_sent` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `read` tinyint(1) NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users_stats`
--

DROP TABLE IF EXISTS `users_stats`;
CREATE TABLE IF NOT EXISTS `users_stats` (
  `id` int(11) NOT NULL,
  `strength` decimal(28,4) NOT NULL DEFAULT '10.0000',
  `agility` decimal(28,4) NOT NULL DEFAULT '10.0000',
  `guard` decimal(28,4) NOT NULL DEFAULT '10.0000',
  `labour` decimal(28,4) NOT NULL DEFAULT '10.0000',
  `iq` decimal(28,4) NOT NULL DEFAULT '10.0000',
  `energy` int(11) NOT NULL DEFAULT '10',
  `energy_max` int(11) NOT NULL DEFAULT '10',
  `power` int(11) NOT NULL DEFAULT '10',
  `power_max` int(11) NOT NULL DEFAULT '10',
  `nerve` int(11) NOT NULL DEFAULT '10',
  `nerve_max` int(11) NOT NULL DEFAULT '10',
  `health` int(11) NOT NULL DEFAULT '10',
  `health_max` int(11) NOT NULL DEFAULT '10',
  `happy` int(11) NOT NULL DEFAULT '100',
  `happy_max` int(11) NOT NULL DEFAULT '100',
  `tasks_complete` bigint(25) NOT NULL DEFAULT '0',
  `tasks_failed` bigint(25) NOT NULL DEFAULT '0',
  `tasks_jailed` bigint(25) NOT NULL DEFAULT '0',
  `tasks_hospitalised` bigint(25) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `breport`
--
ALTER TABLE `breport`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `breport_responses`
--
ALTER TABLE `breport_responses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cities`
--
ALTER TABLE `cities`
  ADD PRIMARY KEY (`cityid`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `itemtypes`
--
ALTER TABLE `itemtypes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jobranks`
--
ALTER TABLE `jobranks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `logs_query_errors`
--
ALTER TABLE `logs_query_errors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `logs_staff`
--
ALTER TABLE `logs_staff`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mail`
--
ALTER TABLE `mail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `read` (`read`),
  ADD KEY `sender` (`sender`),
  ADD KEY `receiver` (`receiver`),
  ADD KEY `deleted` (`deleted`);

--
-- Indexes for table `market_items`
--
ALTER TABLE `market_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `market_points`
--
ALTER TABLE `market_points`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `preports`
--
ALTER TABLE `preports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `preports_responses`
--
ALTER TABLE `preports_responses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings_crons`
--
ALTER TABLE `settings_crons`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings_game`
--
ALTER TABLE `settings_game`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings_mods`
--
ALTER TABLE `settings_mods`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shopitems`
--
ALTER TABLE `shopitems`
  ADD PRIMARY KEY (`sitemID`),
  ADD KEY `sitemSHOP` (`sitemSHOP`);

--
-- Indexes for table `shops`
--
ALTER TABLE `shops`
  ADD PRIMARY KEY (`shopID`);

--
-- Indexes for table `staff_ranks`
--
ALTER TABLE `staff_ranks`
  ADD PRIMARY KEY (`rank_id`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tasks_groups`
--
ALTER TABLE `tasks_groups`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_2` (`ordering`),
  ADD KEY `enabled` (`enabled`),
  ADD KEY `order` (`ordering`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users_bans`
--
ALTER TABLE `users_bans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users_equipment`
--
ALTER TABLE `users_equipment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users_events`
--
ALTER TABLE `users_events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users_finances`
--
ALTER TABLE `users_finances`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users_messages`
--
ALTER TABLE `users_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users_stats`
--
ALTER TABLE `users_stats`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `breport`
--
ALTER TABLE `breport`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `breport_responses`
--
ALTER TABLE `breport_responses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `cities`
--
ALTER TABLE `cities`
  MODIFY `cityid` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `itemtypes`
--
ALTER TABLE `itemtypes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `jobranks`
--
ALTER TABLE `jobranks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `logs_query_errors`
--
ALTER TABLE `logs_query_errors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `logs_staff`
--
ALTER TABLE `logs_staff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `mail`
--
ALTER TABLE `mail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `market_items`
--
ALTER TABLE `market_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `market_points`
--
ALTER TABLE `market_points`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `preports`
--
ALTER TABLE `preports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `preports_responses`
--
ALTER TABLE `preports_responses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `settings_crons`
--
ALTER TABLE `settings_crons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `settings_game`
--
ALTER TABLE `settings_game`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `settings_mods`
--
ALTER TABLE `settings_mods`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `shopitems`
--
ALTER TABLE `shopitems`
  MODIFY `sitemID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `shops`
--
ALTER TABLE `shops`
  MODIFY `shopID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `staff_ranks`
--
ALTER TABLE `staff_ranks`
  MODIFY `rank_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `tasks_groups`
--
ALTER TABLE `tasks_groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `users_bans`
--
ALTER TABLE `users_bans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `users_equipment`
--
ALTER TABLE `users_equipment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `users_events`
--
ALTER TABLE `users_events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `users_finances`
--
ALTER TABLE `users_finances`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `users_messages`
--
ALTER TABLE `users_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `users_stats`
--
ALTER TABLE `users_stats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
