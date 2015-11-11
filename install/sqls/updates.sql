DROP TABLE IF EXISTS `crimes`;
DROP TABLE IF EXISTS `crimegroups`;
DROP TABLE IF EXISTS `settings_game`;
RENAME TABLE `game_settings` TO `settings_game`;
UPDATE `settings_game` SET `value` = '9.0.0166' WHERE `name` = 'engine_version';