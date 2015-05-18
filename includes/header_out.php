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
	static function getInstance($set) {
		if(self::$inst == null)
			self::$inst = new headers($set);
		return self::$inst;
	}
	function __construct($set) {
		global $css;
		header("Content-type: text/html;charset=UTF-8");
		?><!doctype html>
		<!--[if lt IE 7 ]> <html lang="en" class="no-js ie6"> <![endif]-->
		<!--[if IE 7 ]>    <html lang="en" class="no-js ie7"> <![endif]-->
		<!--[if IE 8 ]>    <html lang="en" class="no-js ie8"> <![endif]-->
		<!--[if IE 9 ]>    <html lang="en" class="no-js ie9"> <![endif]-->
		<!--[if (gt IE 9)|!(IE)]><!--> <html lang="en" class="no-js"> <!--<![endif]-->
		<head>
			<base href='http://mtgtest.tk/adz/' />
			<meta charset="utf-8">
			<title><?php echo $set['game_name']; ?></title>
			<meta name="author" content="Magictallguy" />
			<meta name="viewport" content="width=device-width, initial-scale=1.0" />
			<link rel="stylesheet" href="css/style.css">
			<!-- superfish style include -->
			<link rel="stylesheet" href="js/superfish/css/superfish.css">
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
		<body id="page_login">
		<div class="page_login">
		<div class="page_login_tile">
		<!-- header -->
		<header>
		<!-- .logo -->
		<h1 class="logo"> <a href="index.php"><img src="images/logo.png" alt="<?php echo $set['game_name']; ?>" title="<?php echo $set['game_name']; ?>" /></a></h1>
		<!-- /.logo -->
		</header>
		<!-- EOF header -->
		<div id="main">
		<div class="login_block"><?php
	}

	function __destruct() {
		global $set;
		?><a href="login.php" class="forgot">Sign in</a> &middot; <a href="signup.php" class="forgot">Sign up</a> &middot; <a href="contact.php" class="forgot">Contact</a> &middot; <a href="tos.php" class="forgot">TOS</a>
		</div>
		<!-- end of #container -->
		<!-- footer -->
		<footer>
		<div class="foot">
		<div class="footer">
		<div class="copy">Copyright &copy; <?php echo date('Y'); ?> Magictallguy. All rights reserved.</div>
		</div>
		</div>
		</footer>
		<!-- EOF footer -->
		</div>
		</div>
		<script type="text/javascript" src="js/jquery.validate.pack.js"></script>
		<!--[if lt IE 9  ]> <script src="js/cycle/jquery.cycle.all.min_ie.js"></script>     <![endif]-->
		<!--[if  IE 9 | !(IE)]><!-->  <script src="js/cycle/jquery.cycle.all.min.js" type="text/javascript"></script>  <!--<![endif]-->
		<!-- super fish js include -->
		<script type="text/javascript" src="js/superfish/js/superfish.js"></script>
		<script type="text/javascript" src="js/superfish/js/hoverIntent.js"></script>
		<script src="js/jquery.checkbox.js"></script>
		<!-- scripts concatenated and minified via ant build script-->
		<script src="js/script.js"></script>
		<!-- end concatenated and minified scripts   <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.js"></script>
		-->
		</body>
		</html><?php
	}
}