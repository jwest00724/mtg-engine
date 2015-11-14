CREATE TABLE IF NOT EXISTS `settings_crons` (
  `id` int(11) NOT NULL,
  `type` varchar(25) NOT NULL DEFAULT '',
  `last` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
INSERT INTO `settings_crons` (`type`) VALUES ('1min');

DROP TABLE IF EXISTS `settings_game`;
CREATE TABLE IF NOT EXISTS `settings_game` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `value` mediumtext NOT NULL DEFAULT ''
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `settings_game`
--

INSERT INTO `settings_game` (`id`, `name`, `value`) VALUES
(1, 'staff_pad', 'Welcome to MTG Codes v9'),
(2, 'game_name', 'MTG Test'),
(3, 'game_description', 'Demo description here'),
(4, 'register_start_cash', '100'),
(5, 'register_promo_code', 'Your Promo Code Here'),
(6, 'register_promo_cash', '100'),
(7, 'game_owner_id', '1'),
(8, 'main_currency_symbol', '&pound;'),
(9, 'staff_notepad', 'Blah'),
(10, 'engine_version', '9.0.0232'),
(11, 'max_health_gained', '25'),
(12, 'max_energy_gained', '3'),
(13, 'max_nerve_gained', '2'),
(14, 'max_power_gained', '3'),
(15, 'level_gained', '1');