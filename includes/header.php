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
	static function getInstance($db, $set, $my, $mtg, $users) {
		if(self::$inst == null)
			self::$inst = new headers($db, $set, $my, $mtg, $users);
		return self::$inst;
	}
	function __construct($db, $set, $my, $mtg, $users) {
		$db->query("UPDATE users SET last_seen = current_timestamp WHERE id = ?");
		$db->execute(array($my['id']));
		header("Content-type: text/html;charset=UTF-8");
		?><!doctype html>
		<!--[if lt IE 7 ]> <html lang="en" class="no-js ie6"> <![endif]-->
		<!--[if IE 7 ]>    <html lang="en" class="no-js ie7"> <![endif]-->
		<!--[if IE 8 ]>    <html lang="en" class="no-js ie8"> <![endif]-->
		<!--[if IE 9 ]>    <html lang="en" class="no-js ie9"> <![endif]-->
		<!--[if (gt IE 9)|!(IE)]><!--> <html lang="en" class="no-js"> <!--<![endif]-->
		<head>
		<base href="http://mtgtest.tk/adz/" />
		<meta charset="utf-8">
		<title><?php echo $set['game_name']; ?></title>
		<meta name="author" content="Magictallguy" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<link rel="stylesheet" href="css/style.css" />
		<!-- superfish style include -->
		<link rel="stylesheet" href="js/superfish/css/superfish.css" />
		<script src="js/libs/modernizr-1.6.min.js"></script>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.js"></script>
		<script>!window.jQuery && document.write(unescape('%3Cscript src="js/libs/jquery-1.4.2.min.js"%3E%3C/script%3E'))</script>
		<script type="text/javascript" src="js/libs/cufon-yui.js"></script>
		<script type="text/javascript" src="js/font/Myriad_Pro_400.font.js"></script>
		<script type="text/javascript" src="js/font/Myriad_Pro_600.font.js"></script>
		<script type="text/javascript" src="js/font/Myriad_Pro_700.font.js"></script>
		<script type="text/javascript" src="js/font/Myriad_Pro_300.font.js"></script>
		<script type="text/javascript" src="js/cufon-replace.js"></script>
		</head>
		<body>
		<div id="container">
		<!-- header -->
		<header>
		<!-- .logo -->
		<div class="logo"> <a href="index.php"><img src="images/logo.png" alt="<?php echo $set['game_name']; ?>" title="<?php echo $set['game_name']; ?>" /></a></div>
		<!-- /.logo -->
		<nav>
			<ul class="sf-menu">
				<li<?php echo $_SERVER['PHP_SELF'] == '/profile.php' ? " class='current_page_item'" : ''; ?>><?php echo $users->name($my['id']); ?></li>
				<li<?php echo $_SERVER['PHP_SELF'] == '/settings.php' ? " class='current_page_item'" : ''; ?>><a href='settings.php' accesskey='2'>Settings</a></li>
				<li<?php echo $_SERVER['QUERY_STRING'] == 'action=logout' ? " class='current_page_item'" : ''; ?>><a href='?action=logout' accesskey='3'>Logout</a></li>
			</ul>
		</nav>
		</header>
		 <!-- EOF header -->
		 <!-- slider -->
		 <div class="row_top_tile_sub">
			<div class="row_top_sub">
				<div class="container">
					<h1 class="page_title"><?php echo $set['game_name']; ?></h1>
				</div>
			</div>
		 </div>
		 <!-- EOF slider -->
		<div id="main" class="shape">
			<div class="container_12"><?php
	}

	function menuarea($my, $mtg, $users, $db) {
		if(!defined('MENU_ENABLE'))
			define('MENU_ENABLE', true);
		if(defined('MENU_STAFF'))
			require_once(DIRNAME(__DIR__) . '/staff/menu.php');
		else
			require_once(__DIR__ . '/menu.php');
		?><div class="grid_8 pad3">
			<p><?php
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
		?>			</p>
				</div>
			</div>
		</div>
		<!-- footer -->
		<footer>
			<div class="foot">
				<div class="footer">
					<div class="copy">Copyright &copy; 2011 Forceful Theme. All rights reserved.</div>
					<div class="bottom_menu">
						<ul>
							<li><a href="index.php">Home</a></li>
							<li><a href="contact.php">Contact</a></li>
							<li><a href="?action=logout">Logout</a></li>
						</ul>
					</div>
				</div>
			</div>
		</footer>
		<!-- EOF footer -->
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.js"></script>
		<script type="text/javascript" src="js/jquery.validate.pack.js"></script>
		<!--[if lt IE 9  ]> <script src="js/cycle/jquery.cycle.all.min_ie.js"></script>     <![endif]-->
		<!--[if  IE 9 | !(IE)]><!-->  <script src="js/cycle/jquery.cycle.all.min.js" type="text/javascript"></script>  <!--<![endif]-->
		<!-- super fish js include -->
		<script type="text/javascript" src="js/superfish/js/superfish.js"></script>
		<script type="text/javascript" src="js/superfish/js/hoverIntent.js"></script>
		<!-- scripts concatenated and minified via ant build script-->
		<script src="js/script.js"></script>
		<!-- end concatenated and minified scripts-->
		</body>
		</html><?php
	}
}