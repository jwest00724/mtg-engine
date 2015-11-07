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
class headers {
	static $inst = null;
	static function getInstance($set) {
		if(self::$inst == null)
			self::$inst = new headers($set);
		return self::$inst;
	}
	function __construct($set) {
		header("Content-type: text/html;charset=UTF-8");
		?><!DOCTYPE html>
		<html lang="en">
			<head>
				<meta charset="utf-8">
				<meta name="viewport" content="width=device-width, initial-scale=1.0">
				<meta name="description" content="MTG Codes v9" />
				<title><?php echo $set['game_name'];?> - MTG Codes v9</title>
				<link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/pure-min.css" />
				<link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/grids-responsive-min.css" />
				<!--[if lte IE 8]>
				<link rel="stylesheet" href="css/layouts/side-menu-old-ie.css" />
				<link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/grids-responsive-old-ie-min.css" />
				<![endif]-->
				<!--[if gt IE 8]><!-->
				<link rel="stylesheet" href="css/layouts/side-menu.css" />
				<!--<![endif]-->
				<style type='text/css'>
					.center {
						text-align:center;
					}
				</style>
				<link rel="stylesheet" type='text/css' href="css/message.css" />
				<link rel="stylesheet" type='text/css' href="css/style.css" />
			</head>
			<body>
				<div id="layout">
					<a href="#menu" id="menuLink" class="menu-link"><span>&nbsp;</span></a>
					<div id="menu">
						<div class="pure-menu">
							<a class="pure-menu-heading" href="#">Menu</a>
							<ul class="pure-menu-list">
								<li class="pure-menu-item<?php echo $_SERVER['PHP_SELF'] == '/login.php' ? ' pure-menu-selected' : null;?>"><a href="index.php" class="pure-menu-link">Home</a></li>
								<li class="pure-menu-item<?php echo $_SERVER['PHP_SELF'] == '/tos.php' ? ' pure-menu-selected' : null;?>"><a href="tos.php" class="pure-menu-link">Terms of Service</a></li>
							</ul>
						</div>
					</div>
					<div id="main"><?php
	}

	function __destruct() {
		?>			</div>
				</div>
				<script src="js/ui.js"></script>
			</body>
		</html><?php
	}
}