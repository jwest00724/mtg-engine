<?php
if(strpos($_SERVER['REQUEST_URI'], basename(__FILE__)) !== false)
	exit;
if(!defined('MTG_ENABLE'))
	define('MTG_ENABLE', true);
class headers {
	static $inst = null;
	static function getInstance() {
		if(self::$inst == null)
			self::$inst = new headers();
		return self::$inst;
	}
	function __construct() {
		global $my, $set, $css;
		header("Content-type: text/html;charset=UTF-8");
		?><!DOCTYPE html>
		<html xmlns='http://www.w3.org/1999/xhtml'>
		<head>
			<base href='http://mtgtest.tk/adz/' />
			<meta name='author' content='Magictallguy' />
			<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1' />
			<title><?php echo $set['game_name']; ?></title>
			<link rel='stylesheet' href='http://yui.yahooapis.com/pure/0.6.0/pure-min.css' />
			<script type='text/javascript' src='js/jquery-2.1.3.min.js'></script><?php
			if(isset($css) && is_array($css) && count($css))
				foreach($css as $style)
					echo "<link rel='stylesheet' type='text/css' href='css/".$style.".css' />";
		?></head>
		<body><?php
	}

	function __destruct() {
		?></body>
		</html><?php
	}

	function menuarea() {
		global $my, $db, $mtg, $set;
		if(!defined('MENU_ENABLE'))
			define('MENU_ENABLE', true);
		if(defined('MENU_STAFF'))
			require_once(DIRNAME(__DIR__) . '/staff/menu.php');
		else
			require_once(__DIR__ . '/menu.php');
		include_once(__DIR__ . '/class/class_mtg_functions.php');
		if(!isset($mtg))
			$mtg = new mtg_functions;
		if($my['hospital'])
			echo "<strong>Nurse:</strong> You are currently in hospital for ".$mtg->time_format($my['hospital'] * 60).".<br />";
		if($my['jail'])
			echo "<strong>Officer:</strong> You are currently in jail for ".$mtg->time_format($my['jail'] * 60).".<br />";
	}
}