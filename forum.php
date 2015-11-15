<?php
define('HEADER_TEXT', 'Forums');
require_once __DIR__ . '/includes/globals.php';
if(!$set['forums_enabled'])
	$mtg->error('The forum is currently closed');
$users->checkBan('forum');
$_GET['ID'] = array_key_exists('ID', $_GET) && ctype_digit($_GET['ID']) ? $_GET['ID'] : null;
$_GET['action'] = array_key_exists('action', $_GET) && ctype_alpha($_GET['action']) ? strtolower(trim($_GET['action'])) : null;
switch($_GET['action']) {
	case 'topicview':

}