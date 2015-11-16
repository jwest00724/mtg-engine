--
-- MySQL 5.5.5
-- Mon, 16 Nov 2015 13:04:40 +0000
--

CREATE TABLE `settings_game` (
   `id` int(11) not null auto_increment,
   `name` varchar(255) not null,
   `value` mediumtext not null,
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=19;

INSERT INTO `settings_game` (`id`, `name`, `value`) VALUES 
('1', 'staff_pad', 'Welcome to MTG Codes v9'),
('2', 'game_name', 'MTG Engine'),
('3', 'game_description', 'Demo description here'),
('4', 'register_start_cash', '100'),
('5', 'register_promo_code', 'Your Promo Code Here'),
('6', 'register_promo_cash', '100'),
('7', 'game_owner_id', '1'),
('8', 'main_currency_symbol', '&pound;'),
('9', 'staff_notepad', 'Blah'),
('10', 'engine_version', '9.0.0314'),
('11', 'max_health_gained', '25'),
('12', 'max_energy_gained', '3'),
('13', 'max_nerve_gained', '2'),
('14', 'max_power_gained', '3'),
('15', 'level_gained', '1'),
('16', 'bank_enabled', '1'),
('17', 'bank_cost', '100000'),
('18', 'forums_enabled', '1');