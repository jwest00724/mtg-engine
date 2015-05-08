<?php
if(strpos($_SERVER['REQUEST_URI'], basename(__FILE__)) !== false)
	exit;
if(!defined('MTG_ENABLE'))
	define('MTG_ENABLE', true);
$_SERVER['PHP_SELF'] = str_replace('/adz', '', $_SERVER['PHP_SELF']);
class headers {
	static $inst = null;
	static function getInstance($set) {
		if(self::$inst == null)
			self::$inst = new headers($set);
		return self::$inst;
	}
	function __construct($set) {
		global $css;
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
							<li<?php echo $_SERVER['PHP_SELF'] == '/login.php' ? " class='current_page_item'" : ''; ?>><a href='login.php' accesskey='1'>Login</a></li>
							<li<?php echo $_SERVER['PHP_SELF'] == '/signup.php' ? " class='current_page_item'" : ''; ?>><a href='signup.php' accesskey='2'>Sign Up</a></li>
							<li<?php echo $_SERVER['PHP_SELF'] == '/contact.php' ? " class='current_page_item'" : ''; ?>><a href='contact.php' accesskey='3'>Contact</a></li>
							<li<?php echo $_SERVER['PHP_SELF'] == '/tos.php' ? " class='current_page_item'" : ''; ?>><a href='tos.php' accesskey='4'>T.o.S</a></li>
						</ul>
					</div>
				</div>
			</div>
			<div id='page' class='container'><?php
	}

	function __destruct() {
		global $set;
		?>	</div>
		</div>
		<div id="copyright" class="container">
			<p>&copy; <?php echo $set['game_name']; ?>. All rights reserved. &middot; Design by <a href="http://templated.co" rel="nofollow">TEMPLATED</a>.</p>
		</div>
		</body>
		</html><?php
	}
}