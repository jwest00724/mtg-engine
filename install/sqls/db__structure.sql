--
-- MySQL 5.5.5
-- Fri, 20 Nov 2015 21:06:01 +0000
--

CREATE TABLE `breport` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE `breport_responses` (
   `id` int(11) not null auto_increment,
   `bug` int(11) not null default '0',
   `user` int(11) not null default '0',
   `response` text not null,
   `date_sent` bigint(25) not null default '0',
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE `cities` (
   `cityid` int(11) not null auto_increment,
   `cityname` varchar(255) not null,
   `citydesc` longtext not null,
   `cityminlevel` int(11) not null default '0',
   `time` int(11) default '0',
   `citycountry` int(11) not null,
   PRIMARY KEY (`cityid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE `forums` (
   `id` int(11) not null auto_increment,
   `name` varchar(255) CHARSET latin1 not null,
   `description` varchar(255) CHARSET latin1 not null,
   `publicity` enum('all','upgraded','staff') CHARSET latin1 not null default 'all',
   `is_recycle` tinyint(1) not null default '0',
   `latest_post_id` int(11) not null default '0',
   `latest_topic_id` int(11) not null default '0',
   `latest_post_user` int(11) not null default '0',
   `latest_post_time` timestamp not null default '0000-00-00 00:00:00',
   PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=7;


CREATE TABLE `forums_posts` (
   `id` int(11) not null auto_increment,
   `parent_topic` int(11) not null default '0',
   `user` int(11) not null default '0',
   `content` text CHARSET latin1 not null,
   `posted` timestamp not null default CURRENT_TIMESTAMP,
   `edit_times` int(11) not null default '0',
   `edit_user` int(11) not null default '0',
   `edit_date` timestamp not null default '0000-00-00 00:00:00',
   `deleted` tinyint(1) not null default '0',
   PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=2775;


CREATE TABLE `forums_subscriptions` (
   `id` int(11) not null auto_increment,
   `user` int(11) not null default '0',
   `topic` int(11) not null default '0',
   `date_subscribed` timestamp not null default CURRENT_TIMESTAMP,
   PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1349;


CREATE TABLE `forums_topics` (
   `id` int(11) not null auto_increment,
   `name` varchar(255) CHARSET latin1 not null,
   `description` varchar(255) CHARSET latin1 not null,
   `creation_time` timestamp not null default CURRENT_TIMESTAMP,
   `parent_board` int(11) not null default '0',
   `pinned` tinyint(1) not null default '0',
   `locked` tinyint(1) not null default '0',
   `creator` int(11) not null default '0',
   `latest_post_id` int(11) not null default '0',
   `latest_post_user` int(11) not null default '0',
   `latest_post_time` timestamp not null default '0000-00-00 00:00:00',
   PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=2291;


CREATE TABLE `inventory` (
   `id` int(11) not null auto_increment,
   `item` int(11) not null default '0',
   `user` int(11) not null default '0',
   `qty` int(11) not null default '0',
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE `items` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE `itemtypes` (
   `id` int(11) not null auto_increment,
   `name` varchar(255) not null,
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE `jobranks` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE `jobs` (
   `id` int(11) not null auto_increment,
   `name` varchar(255) not null,
   `first_rank` int(11) not null default '0',
   `desc` varchar(255) not null,
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE `logs_query_errors` (
   `id` int(11) not null auto_increment,
   `time` timestamp not null default CURRENT_TIMESTAMP,
   `query` text not null,
   `error` text not null,
   `fixed` tinyint(4) not null default '0',
   `by` int(11) not null default '0',
   `page` varchar(255) not null,
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE `logs_staff` (
   `id` int(11) not null auto_increment,
   `user` int(11) not null default '0',
   `time` timestamp not null default CURRENT_TIMESTAMP,
   `action` text not null,
   `ip` varchar(15) not null,
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=68;


CREATE TABLE `market_items` (
   `id` int(11) not null auto_increment,
   `item` int(11) not null default '0',
   `user` int(11) not null default '0',
   `price` bigint(25) not null default '0',
   `qty` bigint(25) not null default '0',
   `currency` enum('money','points') not null default 'money',
   `time_added` timestamp not null default CURRENT_TIMESTAMP,
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE `market_points` (
   `id` int(11) not null auto_increment,
   `qty` bigint(25) not null default '0',
   `user` int(11) not null default '0',
   `price` bigint(25) not null default '0',
   `time_added` timestamp not null default CURRENT_TIMESTAMP,
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE `preports` (
   `id` int(11) not null auto_increment,
   `userid` int(11) not null default '0',
   `reported` int(11) not null default '0',
   `description` longtext not null,
   `status` enum('open','processing','handled') not null default 'open',
   `assigned` int(11) not null default '0',
   `date_sent` bigint(25) not null default '0',
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE `preports_responses` (
   `id` int(11) not null auto_increment,
   `report` int(11) not null default '0',
   `user` int(11) not null default '0',
   `response` text not null,
   `date_sent` bigint(25) not null default '0',
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE `settings_crons` (
   `id` int(11) not null auto_increment,
   `type` varchar(25) not null,
   `last` timestamp not null default CURRENT_TIMESTAMP,
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=3;


CREATE TABLE `settings_game` (
   `id` int(11) not null auto_increment,
   `name` varchar(255) not null,
   `value` mediumtext not null,
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=25;


CREATE TABLE `settings_mods` (
   `id` int(11) not null auto_increment,
   `area` varchar(255) not null,
   `status` tinyint(1) not null default '0',
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE `shopitems` (
   `sitemID` int(11) not null auto_increment,
   `sitemSHOP` int(11) not null default '0',
   `sitemITEMID` int(11) not null default '0',
   `sitemSTOCK` bigint(25) not null default '0',
   `sitemSOLD` bigint(25) not null default '0',
   PRIMARY KEY (`sitemID`),
   KEY `sitemSHOP` (`sitemSHOP`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE `shops` (
   `shopID` int(11) not null auto_increment,
   `shopLOCATION` int(11) not null default '0',
   `shopNAME` varchar(255) not null,
   `shopDESCRIPTION` mediumtext not null,
   PRIMARY KEY (`shopID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE `staff_ranks` (
   `rank_id` int(11) not null auto_increment,
   `rank_name` varchar(255) not null,
   `rank_desc` varchar(255) not null,
   `rank_order` int(11) not null default '0',
   `rank_colour` varchar(6) not null default '000000',
   `override_all` enum('Yes','No') not null default 'No',
   `staff_panel_access` enum('Yes','No') not null default 'No',
   `staff_panel_tasks_manage` enum('Yes','No') not null default 'No',
   `staff_panel_tasks_add` enum('Yes','No') not null default 'No',
   `staff_panel_tasks_edit` enum('Yes','No') not null default 'No',
   `staff_panel_tasks_delete` enum('Yes','No') not null default 'No',
   `staff_panel_tasks_groups_manage` enum('Yes','No') not null default 'No',
   `staff_panel_tasks_groups_add` enum('Yes','No') not null default 'No',
   `staff_panel_tasks_groups_edit` enum('Yes','No') not null default 'No',
   `staff_panel_tasks_groups_delete` enum('Yes','No') not null default 'No',
   `staff_panel_staff_ranks_manage` enum('Yes','No') not null default 'No',
   `staff_panel_staff_ranks_add` enum('Yes','No') not null default 'No',
   `staff_panel_staff_ranks_edit` enum('Yes','No') not null default 'No',
   `staff_panel_staff_ranks_delete` enum('Yes','No') not null default 'No',
   `staff_panel_code_version_manage` enum('Yes','No') not null default 'No',
   `staff_panel_logs_staff` enum('Yes','No') not null default 'No',
   `staff_panel_forum_board_add` enum('Yes','No') not null default 'No',
   `staff_panel_forum_board_edit` enum('Yes','No') not null default 'No',
   `staff_panel_forum_board_delete` enum('Yes','No') not null default 'No',
   `forum_post_edit` enum('Yes','No') not null default 'No',
   `forum_post_delete` enum('Yes','No') not null default 'No',
   `forum_post_locked` enum('Yes','No') not null default 'No',
   `forum_topic_lock` enum('Yes','No') not null default 'No',
   `forum_topic_pin` enum('Yes','No') not null default 'No',
   `forum_topic_delete` enum('Yes','No') not null default 'No',
   `forum_topic_move` enum('Yes','No') not null default 'No',
   PRIMARY KEY (`rank_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=2;


CREATE TABLE `tasks` (
   `id` int(11) not null auto_increment,
   `name` varchar(255) not null,
   `nerve` int(11) not null default '0',
   `formula` mediumtext not null,
   `group_id` int(11) not null default '0',
   `courses_required` varchar(255) not null,
   `text_start` mediumtext not null,
   `text_success` mediumtext not null,
   `text_failure` mediumtext not null,
   `text_jail` mediumtext not null,
   `text_hospital` text not null,
   `time_jail` int(11) not null default '0',
   `text_reason_jail` varchar(255) not null,
   `time_hospital` int(11) not null default '0',
   `text_reason_hospital` varchar(255) not null,
   `upgraded_only` tinyint(1) not null default '0',
   `awarded_money_min` int(11) not null default '0',
   `awarded_money_max` int(11) not null default '0',
   `awarded_points_min` int(11) not null default '0',
   `awarded_points_max` int(11) not null default '0',
   `awarded_xp_min` int(11) not null default '0',
   `awarded_xp_max` int(11) not null default '0',
   `awarded_item` int(11) not null default '0',
   `awarded_item_qty` int(11) not null default '0',
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=3;


CREATE TABLE `tasks_groups` (
   `id` int(11) not null auto_increment,
   `name` varchar(255) not null,
   `enabled` tinyint(1) not null default '1',
   `ordering` int(11) not null default '0',
   PRIMARY KEY (`id`),
   UNIQUE KEY (`ordering`),
   KEY `enabled` (`enabled`),
   KEY `order` (`ordering`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=2;


CREATE TABLE `users` (
   `id` int(11) not null auto_increment,
   `username` varchar(255) not null,
   `password` text not null,
   `email` varchar(255) not null,
   `level` int(11) not null default '1',
   `exp` decimal(24,4) not null default '0.0000',
   `job` int(11) not null default '0',
   `job_rank` int(11) not null default '0',
   `location` int(11) not null default '0',
   `hospital` int(11) not null default '0',
   `hospital_reason` int(11) not null default '0',
   `jail` int(11) not null default '0',
   `jail_reason` text not null,
   `staff_rank` bigint(25) not null default '0',
   `last_seen` timestamp not null default CURRENT_TIMESTAMP,
   `profile_picture` varchar(255) not null,
   `status` text not null,
   `upgraded` timestamp not null default '0000-00-00 00:00:00',
   `account_locked` timestamp not null default CURRENT_TIMESTAMP,
   `login_attempts` int(11) not null default '0',
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=3331;


CREATE TABLE `users_bans` (
   `id` int(11) not null auto_increment,
   `user` int(11) not null default '0',
   `time_enforced` timestamp not null default CURRENT_TIMESTAMP,
   `time_expires` timestamp not null default '0000-00-00 00:00:00',
   `enforcer` int(11) not null default '0',
   `ban_type` enum('messages','game','forum') not null default 'game',
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE `users_equipment` (
   `id` int(11) not null auto_increment,
   `primary` int(11) not null default '0',
   `secondary` int(11) not null default '0',
   `armour` int(11) not null default '0',
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=3331;


CREATE TABLE `users_events` (
   `id` int(11) not null auto_increment,
   `user` int(11) not null default '0',
   `time_sent` timestamp not null default CURRENT_TIMESTAMP,
   `type` varchar(255) not null default 'Uncategorized',
   `text` text not null,
   `read` tinyint(1) not null default '0',
   `deleted` tinyint(1) not null default '0',
   `extra` int(11) not null default '0',
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=127;


CREATE TABLE `users_finances` (
   `id` int(11) not null auto_increment,
   `money` bigint(25) not null default '0',
   `points` bigint(25) not null default '0',
   `bank` bigint(25) not null default '-1',
   `merits` bigint(25) not null default '0',
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=3331;


CREATE TABLE `users_ips` (
   `id` int(11) not null auto_increment,
   `user` int(11) not null default '0',
   `ip` varchar(255) CHARSET latin1 not null default '0.0.0.0',
   PRIMARY KEY (`id`),
   UNIQUE KEY (`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=140;


CREATE TABLE `users_messages` (
   `id` int(11) not null auto_increment,
   `sender` int(11) not null default '0',
   `receiver` int(11) not null default '0',
   `subject` varchar(255) not null default 'No subject',
   `message` text not null,
   `time_sent` timestamp not null default CURRENT_TIMESTAMP,
   `read` tinyint(1) not null default '0',
   `deleted` tinyint(1) not null default '0',
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=6889;


CREATE TABLE `users_reset` (
   `id` int(11) not null auto_increment,
   `user` int(11) not null default '0',
   `requested` timestamp not null default CURRENT_TIMESTAMP,
   `email` varchar(255) not null,
   `code` varchar(32) not null,
   `ip` varchar(255) not null,
   PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=5;


CREATE TABLE `users_settings` (
   `id` int(11) not null auto_increment,
   `logout_threshold` enum('300','900','1800','3600','86400','never') CHARSET latin1 not null default '900',
   PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=3328;


CREATE TABLE `users_stats` (
   `id` int(11) not null auto_increment,
   `strength` decimal(28,4) not null default '10.0000',
   `agility` decimal(28,4) not null default '10.0000',
   `guard` decimal(28,4) not null default '10.0000',
   `labour` decimal(28,4) not null default '10.0000',
   `iq` decimal(28,4) not null default '10.0000',
   `energy` int(11) not null default '10',
   `energy_max` int(11) not null default '10',
   `power` int(11) not null default '10',
   `power_max` int(11) not null default '10',
   `nerve` int(11) not null default '10',
   `nerve_max` int(11) not null default '10',
   `health` int(11) not null default '10',
   `health_max` int(11) not null default '10',
   `happy` int(11) not null default '100',
   `happy_max` int(11) not null default '100',
   `tasks_complete` bigint(25) not null default '0',
   `tasks_failed` bigint(25) not null default '0',
   `tasks_jailed` bigint(25) not null default '0',
   `tasks_hospitalised` bigint(25) not null default '0',
   PRIMARY KEY (`id`),
   UNIQUE KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=3331;


CREATE TABLE `webchat_lines` (
   `id` int(10) unsigned not null auto_increment,
   `author` varchar(16) not null,
   `gravatar` varchar(32) not null,
   `text` varchar(255) not null,
   `ts` timestamp not null default CURRENT_TIMESTAMP,
   PRIMARY KEY (`id`),
   KEY `ts` (`ts`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE `webchat_users` (
   `id` int(10) unsigned not null auto_increment,
   `name` varchar(16) not null,
   `gravatar` varchar(32) not null,
   `last_activity` timestamp not null default CURRENT_TIMESTAMP,
   PRIMARY KEY (`id`),
   UNIQUE KEY (`name`),
   KEY `last_activity` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;