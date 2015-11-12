<?php
session_start();
ob_start();
if(!defined('MTG_ENABLE'))
	define('MTG_ENABLE', true);
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
error_reporting(E_ALL);
require_once __DIR__ . '/class/class_mtg_functions.php';
if(!isset($mtg))
	$mtg = MTG\mtg_functions::getInstance();
if(!defined('NO_HEAD')) {
	require_once __DIR__ . '/header_out.php';
	$h = MTG\headers::getInstance($set);
}