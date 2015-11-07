--
-- MySQL 5.5.5
-- Mon, 06 Apr 2015 02:01:21 +0000
--
DROP TABLE IF EXISTS `game_settings`;
CREATE TABLE IF NOT EXISTS `game_settings` (
   `id` int(11) not null auto_increment,
   `name` varchar(255) not null,
   `value` mediumtext not null,
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

INSERT INTO `game_settings` (`name`, `value`) VALUES
('staff_pad', 'Welcome to MTG Codes v9'),
('game_name', 'MTG Codes v9'),
('game_description', ''),
('register_start_cash', '100'),
('register_promo_code', 'Your Promo Code Here'),
('register_promo_cash', '100'),
('main_currency_symbol', '&pound;'),
('game_owner_id', '1');