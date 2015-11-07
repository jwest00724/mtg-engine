--
-- MySQL 5.5.5
-- Mon, 06 Apr 2015 01:53:17 +0000
--
CREATE TABLE IF NOT EXISTS `market_points` (
	`id` int(11) not null auto_increment,
	`qty` bigint(25) not null default '0',
	`user` int(11) not null default '0',
	`price` bigint(25) not null default '0',
	`time_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `breport` (
	`id` int(11) not null auto_increment,
	`urgency` varchar(255) not null,
	`category` varchar(255) not null,
	`date_sent` varchar(255) not null,
	`description` longtext not null,
	`status` enum('Yes','No') not null default 'No',
	`poster` int(11) not null default '0',
	`lock_status` int(2) not null default '0',
	`assigned` int(11) not null default '0',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `breport_responses` (
	`id` int(11) not null auto_increment,
	`bug` int(11) not null default '0',
	`user` int(11) not null default '0',
	`response` text not null,
	`date_sent` bigint(25) not null default '0',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cities` (
	`cityid` int(11) not null auto_increment,
	`cityname` varchar(255) not null,
	`citydesc` longtext not null,
	`cityminlevel` int(11) not null default '0',
	`time` int(11) default '0',
	`citycountry` int(11) not null,
	PRIMARY KEY (`cityid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `crimegroups` (
	`id` int(11) not null auto_increment,
	`name` varchar(255) not null,
	`order` int(11) not null default '0',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `crimes` (
	`id` int(11) not null auto_increment,
	`name` varchar(255) not null,
	`nerve` int(11) not null default '0',
	`chance` mediumtext not null,
	`money` bigint(25) not null default '0',
	`points` int(11) not null default '0',
	`item` int(11) not null default '0',
	`crimegroup` int(11) not null default '0',
	`text_start` mediumtext not null,
	`text_success` mediumtext not null,
	`text_failure` mediumtext not null,
	`text_jail` mediumtext not null,
	`jail_time` int(11) not null default '0',
	`jail_reason` varchar(255) not null,
	`xp_awarded` int(11) not null default '0',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `events` (
	`id` int(11) not null auto_increment,
	`user` int(11) not null default '0',
	`time_sent` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`read` int(11) not null default '0',
	`text` mediumtext not null,
	`deleted` tinyint(2) not null default '0',
	PRIMARY KEY (`id`),
	KEY `user` (`user`),
	KEY `read` (`read`),
	KEY `deleted` (`deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `inventory` (
	`id` int(11) not null auto_increment,
	`item` int(11) not null default '0',
	`user` int(11) not null default '0',
	`qty` int(11) not null default '0',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `market_items` (
	`id` int(11) not null auto_increment,
	`item` int(11) not null default '0',
	`user` int(11) not null default '0',
	`price` bigint(25) not null default '0',
	`qty` bigint(25) not null default '0',
	`currency` enum('money','points') not null default 'money',
	`time_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `items` (
	`id` int(11) not null auto_increment,
	`type` int(11) not null default '0',
	`name` varchar(255) not null,
	`desc` mediumtext not null,
	`buyprice` bigint(25) not null default '0',
	`sellprice` bigint(25) not null default '0',
	`effect1_on` tinyint(4) not null default '0',
	`effect1` text not null,
	`effect2_on` tinyint(4) not null default '0',
	`effect2` text not null,
	`effect3_on` tinyint(4) not null default '0',
	`effect3` text not null,
	`effect4_on` int(11),
	`effect4` text not null,
	`weapon` int(11) not null default '0',
	`armour` int(11) not null default '0',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `itemtypes` (
	`id` int(11) not null auto_increment,
	`name` varchar(255) not null,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `jobranks` (
	`id` int(11) not null auto_increment,
	`name` varchar(255) not null,
	`job` int(11) not null default '0',
	`pay` int(11) not null default '0',
	`gain_strength` int(11) not null default '0',
	`gain_agility` int(11) not null default '0',
	`gain_guard` int(11) not null default '0',
	`gain_labour` int(11) not null default '0',
	`gain_iq` int(11) not null default '0',
	`requirement_strength` int(11) not null default '0',
	`requirement_agility` int(11) not null default '0',
	`requirement_guard` int(11) not null default '0',
	`requirement_labour` int(11) not null default '0',
	`requirement_iq` int(11) not null default '0',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `jobs` (
	`id` int(11) not null auto_increment,
	`name` varchar(255) not null,
	`first_rank` int(11) not null default '0',
	`desc` varchar(255) not null,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `logs_query_errors` (
	`id` int(11) not null auto_increment,
	`time` timestamp not null default CURRENT_TIMESTAMP,
	`query` text not null,
	`error` text not null,
	`fixed` tinyint(4) not null default '0',
	`by` int(11) not null default '0',
	`page` varchar(255) not null,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `logs_staff` (
	`id` int(11) not null auto_increment,
	`user` int(11) not null default '0',
	`time` int(11) not null default '0',
	`action` text not null,
	`ip` varchar(15) not null,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `mail` (
	`id` int(11) not null auto_increment,
	`read` int(11) not null default '0',
	`sender` int(11) not null default '0',
	`receiver` int(11) not null default '0',
	`time_sent` timestamp not null default current_timestamp,
	`subject` varchar(255) not null,
	`message` mediumtext not null,
	`deleted` int(11) not null default '0',
	PRIMARY KEY (`id`),
	KEY `read` (`read`),
	KEY `sender` (`sender`),
	KEY `receiver` (`receiver`),
	KEY `deleted` (`deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `preports` (
	`id` int(11) not null auto_increment,
	`userid` int(11) not null default '0',
	`reported` int(11) not null default '0',
	`description` longtext not null,
	`status` enum('open','processing','handled') not null default 'open',
	`assigned` int(11) not null default '0',
	`date_sent` bigint(25) not null default '0',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `preports_responses` (
	`id` int(11) not null auto_increment,
	`report` int(11) not null default '0',
	`user` int(11) not null default '0',
	`response` text not null,
	`date_sent` bigint(25) not null default '0',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `shopitems` (
	`sitemID` int(11) not null auto_increment,
	`sitemSHOP` int(11) not null default '0',
	`sitemITEMID` int(11) not null default '0',
	`sitemSTOCK` bigint(25) not null default '0',
	`sitemSOLD` bigint(25) not null default '0',
	PRIMARY KEY (`sitemID`),
	KEY `sitemSHOP` (`sitemSHOP`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `shops` (
	`shopID` int(11) not null auto_increment,
	`shopLOCATION` int(11) not null default '0',
	`shopNAME` varchar(255) not null,
	`shopDESCRIPTION` mediumtext not null,
	PRIMARY KEY (`shopID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `staff_ranks` (
	`rank_id` int(11) not null auto_increment,
	`rank_name` varchar(255) not null,
	`rank_desc` varchar(255) not null,
	`rank_order` int(11) not null default '0',
	`rank_colour` varchar(6) not null default '000000',
	`override_all` enum('Yes','No') not null default 'No',
	PRIMARY KEY (`rank_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `users` (
	`id` int(11) not null auto_increment,
	`username` varchar(255) not null,
	`password` text not null,
	`email` varchar(255) not null,
	`level` int(11) not null default '0',
	`exp` decimal(24,4) not null default '0.0000',
	`points` bigint(25) not null default '0',
	`job` int(11) not null default '0',
	`job_rank` int(11) not null default '0',
	`location` int(11) not null default '0',
	`hospital` int(11) not null default '0',
	`hospital_reason` int(11) not null default '0',
	`jail` int(11) not null default '0',
	`jail_reason` text not null,
	`staff_rank` bigint(25) not null default '0',
	`last_seen` timestamp not null default current_timestamp,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `users_equipment` (
	`id` int(11) not null auto_increment,
	`equip_primary` int(11) not null default '0',
	`equip_secondary` int(11) not null default '0',
	`equip_armor` int(11) not null default '0',
	PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `users_events` (
	`id` int(11) not null,
	`user` int(11) not null default '0',
	`type` varchar(255) not null default 'Uncategorized',
	`time_sent` timestamp not null default current_timestamp,
	`text` text not null,
	`read` tinyint(1) not null default '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `users_finances` (
	`id` int(11) not null auto_increment,
	`money` bigint(25) not null default '-1',
	`bankmoney` bigint(25) not null default '-1',
	`cybermoney` bigint(25) not null default '-1',
	PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `users_messages` (
	`id` int(11) not null,
	`sender` int(11) not null default '0',
	`receiver` int(11) not null default '0',
	`subject` varchar(255) not null default 'No subject',
	`message` text not null,
	`time_sent` timestamp not null default current_timestamp,
	`read` tinyint(1) not null default '0',
	`deleted` tinyint(1) not null default '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `users_stats` (
	`id` int(11) not null auto_increment,
	`strength` decimal(28,4) not null default '0.0000',
	`agility` decimal(28,4) not null default '0.0000',
	`guard` decimal(28,4) not null default '0.0000',
	`labour` decimal(28,4) not null default '0.0000',
	`iq` decimal(28,4) not null default '0.0000',
	`energy` bigint(20) not null default '0',
	`energy_max` bigint(20) not null default '0',
	`power` bigint(20) not null default '0',
	`power_max` bigint(20) not null default '0',
	`nerve` int(11) not null default '0',
	`nerve_max` int(11) not null default '0',
	`health` int(11) not null default '0',
	`health_max` int(11) not null default '0',
	PRIMARY KEY (`id`),
	UNIQUE KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;