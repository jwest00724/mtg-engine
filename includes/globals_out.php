<?php
session_start();
ob_start();
if(!defined('MTG_ENABLE'))
	define('MTG_ENABLE', true);
if(!file_exists(__DIR__ . '/includes/config.php'))
	exit(header("Location: install"));
require_once(__DIR__ . '/config.php');
require_once(__DIR__ . '/class/class_mtg_db_mysqli.php');
if($db->tableExists('game_settings'))
	exit(header("Location: install"));
$set   = array();
$db->query("SELECT value, name FROM game_settings");
$db->execute();
$row = $db->fetch_row();
foreach($row as $r)
	$set[$r['name']] = $r['value'];
error_reporting(0);
require_once(__DIR__ . '/class/class_mtg_functions.php');
if(!isset($mtg))
	$mtg = new mtg_functions;