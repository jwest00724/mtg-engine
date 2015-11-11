DROP TABLE IF EXISTS `settings_game`;
CREATE TABLE IF NOT EXISTS `settings_game` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
(10, 'engine_version', '9.0.0191');