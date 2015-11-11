DROP TABLE IF EXISTS `crimes`;
DROP TABLE IF EXISTS `crimegroups`;
DROP TABLE IF EXISTS `settings_game`;
RENAME TABLE `game_settings` TO `settings_game`;
ALTER TABLE `staff_ranks` CHANGE `staff_panel_staff_ranks_view` `staff_panel_staff_ranks_manage` ENUM('Yes','No') NOT NULL DEFAULT 'No';
UPDATE `settings_game` SET `value` = '9.0.0182' WHERE `name` = 'engine_version';