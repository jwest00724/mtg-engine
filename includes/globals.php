<?php
session_start();
ob_start();
if(!defined('MTG_ENABLE'))
	define('MTG_ENABLE', true);
if(!array_key_exists('userid', $_SESSION) || !ctype_digit($_SESSION['userid']))
	exit(header('Location: login.php'));
if(!file_exists(__DIR__ . '/config.php'))
	exit(header('Location: install'));
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/class/class_mtg_db_mysqli.php';
if(!$db->tableExists('settings_game'))
	exit(header('Location: install'));
$set = [];
$db->query('SELECT `value`, `name` FROM `settings_game`');
$db->execute();
$rows = $db->fetch_row();
foreach($rows as $row)
	$set[$row['name']] = $row['value'];
error_reporting(0);
if(isset($_SESSION['userid']) && $_SESSION['userid'] == 1)
	error_reporting(E_ALL);
$db->query('SELECT `u`.*, `ue`.*, `uf`.*, `us`.* ' .
	'FROM `users` AS `u` ' .
	'LEFT JOIN `users_equipment` AS `ue` ON `u`.`id` = `ue`.`id` ' .
	'LEFT JOIN `users_finances` AS `uf` ON `u`.`id` = `uf`.`id` ' .
	'LEFT JOIN `users_stats` AS `us` ON `u`.`id` = `us`.`id` ' .
	'WHERE `u`.`id` = ?');
$db->execute([$_SESSION['userid']]);
if(!$db->num_rows()) {
	session_unset();
	session_destroy();
	exit(header('Location: login.php'));
}
$my = $db->fetch_row(true);
require_once __DIR__ . '/class/class_mtg_functions.php';
require_once __DIR__ . '/class/class_mtg_users.php';
require_once __DIR__ . '/class/class_mtg_site.php';
require_once __DIR__ . '/class/class_mtg_logs.php';
require_once __DIR__ . '/class/class_mtg_items.php';
require_once __DIR__ . '/header.php';
$h = headers::getInstance($db, $set, $my, $mtg, $users);
if(defined('MENU_STAFF') && !$my['staff_rank'])
	$mtg->error('You don\'t have access');
$my['total_stats'] = $my['strength'] + $my['agility'] + $my['guard'] + $my['labour'] + $my['iq'];