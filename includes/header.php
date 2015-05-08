<?php
/*Copyright (c) 2015 Orsokuma

Permission is hereby granted, free of charge, to any person obtaining a
copy of this software and associated documentation files (the "Software"),
to deal in the Software without restriction, including without limitation
the rights to use, copy, modify, merge, publish, distribute, sublicense,
and/or sell copies of the Software, and to permit persons to whom the
Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
DEALINGS IN THE SOFTWARE.
*/
if(strpos($_SERVER['REQUEST_URI'], basename(__FILE__)) !== false)
	exit;
if(!defined('MTG_ENABLE'))
	define('MTG_ENABLE', true);
$_SERVER['PHP_SELF'] = str_replace('/adz', '', $_SERVER['PHP_SELF']);
class headers {
	static $inst = null;
	static function getInstance($set, $my) {
		if(self::$inst == null)
			self::$inst = new headers($set, $my);
		return self::$inst;
	}
	function __construct($set, $my) {
		global $css, $mtg;
		header("Content-type: text/html;charset=UTF-8");
		?><!DOCTYPE html>
		<html xmlns='http://www.w3.org/1999/xhtml'>
		<head>
			<base href='http://mtgtest.tk/adz/' />
			<meta name='author' content='Magictallguy' />
			<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1' />
			<title><?php echo $set['game_name']; ?></title>
			<link href='http://fonts.googleapis.com/css?family=Varela' rel='stylesheet' />
			<link href='css/default.css' rel='stylesheet' type='text/css' media='all' />
			<link href='css/fonts.css' rel='stylesheet' type='text/css' media='all' />
			<!--[if IE 6]><link href='default_ie6.css' rel='stylesheet' type='text/css' /><![endif]-->
			<link rel='stylesheet' href='http://yui.yahooapis.com/pure/0.6.0/pure-min.css' />
			<link rel="stylesheet" type='text/css' href="css/message.css" />
			<script type='text/javascript' src='js/jquery_2.1.4_min.css'></script><?php
			if(isset($css) && is_array($css) && count($css))
				foreach($css as $style)
					echo "<link rel='stylesheet' type='text/css' href='css/".$style.".css' />";
		?></head>
		<body>
		<div id='wrapper'>
			<div id='header-wrapper'>
				<div id='header' class='container'>
					<div id='logo'>
						<h1><a href='index.php'><?php echo $set['game_name']; ?></a></h1>
					</div>
					<div id='menu'>
						<ul>
							<li<?php echo $_SERVER['PHP_SELF'] == '/profile.php' ? " class='current_page_item'" : ''; ?>><?php echo $mtg->username($my['id']); ?></li>
							<li<?php echo $_SERVER['PHP_SELF'] == '/settings.php' ? " class='current_page_item'" : ''; ?>><a href='settings.php' accesskey='2'>Settings</a></li>
							<li<?php echo $_SERVER['QUERY_STRING'] == 'action=logout' ? " class='current_page_item'" : ''; ?>><a href='?action=logout' accesskey='3'>Logout</a></li>
						</ul>
					</div>
				</div>
			</div>
			<div id='two-column'>
				<div class='tbox1'>
					<div class='box'>
						<p><?php
	}

	function menuarea() {
		global $my, $mtg, $users;
		if(!defined('MENU_ENABLE'))
			define('MENU_ENABLE', true);
		if(defined('MENU_STAFF'))
			require_once(DIRNAME(__DIR__) . '/staff/menu.php');
		else
			require_once(__DIR__ . '/menu.php');
		?>			</p>
				</div>
			</div>
			<div id='page' class='container'><?php
		if(array_key_exists('action', $_GET) && $_GET['action'] == 'logout') {
			session_unset();
			session_destroy();
			$mtg->success("You've logged out. Come back soon!", true);
		}
		if($my['hospital'])
			echo "<strong>Nurse:</strong> You're currently in hospital for ".$mtg->time_format($my['hospital'] * 60).".<br />";
		if($my['jail'])
			echo "<strong>Officer:</strong> You're currently in jail for ".$mtg->time_format($my['jail'] * 60).".<br />";
	}

	function __destruct() {
		global $set;
		?>		</div>
			</div>
		</div>
		<div id="copyright" class="container">
			<p>&copy; <?php echo $set['game_name']; ?>. All rights reserved. &middot; Design by <a href="http://templated.co" rel="nofollow">TEMPLATED</a>.</p>
		</div>
		</body>
		</html><?php
	}
}