<?php
class mtg_install_header {
	static $inst = null;
	function __construct() {
		?><!DOCTYPE html>
		<html lang="en">
			<head>
				<meta charset="utf-8">
				<meta name="viewport" content="width=device-width, initial-scale=1.0">
				<meta name="description" content="A web-based GUI for installing MTG Codes v9" />
				<title>MTG Codes v9 - Installer</title>
				<link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/pure-min.css" />
				<link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/grids-responsive-min.css" />
				<!--[if lte IE 8]>
				<link rel="stylesheet" href="css/layouts/side-menu-old-ie.css" />
				<link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/grids-responsive-old-ie-min.css" />
				<![endif]-->
				<!--[if gt IE 8]><!-->
				<link rel="stylesheet" href="css/layouts/side-menu.css" />
				<!--<![endif]-->
				<link rel="stylesheet" type='text/css' href="css/message.css" />
				<link rel="stylesheet" type='text/css' href="../css/style.css" />
			</head>
			<body>
				<div id="layout">
					<a href="#menu" id="menuLink" class="menu-link"><span>&nbsp;</span></a>
					<div id="menu">
						<div class="pure-menu">
							<a class="pure-menu-heading" href="#">Menu</a>
							<ul class="pure-menu-list">
								<li class="pure-menu-item"><a href="index.php" class="pure-menu-link">Home</a></li>
								<li class="pure-menu-item"><a href="install.php" class="pure-menu-link">Install</a></li>
								<li class="pure-menu-item" class="menu-item-divided pure-menu-selected"><a href="mailto:magictallguy@hotmail.com" class="pure-menu-link">Contact Support</a></li>
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
	static public function getInstance() {
		if(self::$inst == null)
			self::$inst = new mtg_install_header();
		return self::$inst;
	}
}
$h = mtg_install_header::getInstance();