<?php
namespace MTG;
/*DON'T BE A DICK PUBLIC LICENSE

Everyone is permitted to copy and distribute verbatim or modified copies of this license document, and changing it is allowed as long as the name is changed.

    DON'T BE A DICK PUBLIC LICENSE TERMS AND CONDITIONS FOR COPYING, DISTRIBUTION AND MODIFICATION

    Do whatever you like with the original work, just don't be a dick.

    Being a dick includes - but is not limited to - the following instances:

    1a. Outright copyright infringement - Don't just copy this and change the name.
    1b. Selling the unmodified original with no work done what-so-ever, that's REALLY being a dick.
    1c. Modifying the original work to contain hidden harmful content. That would make you a PROPER dick.

    If you become rich through modifications, related works/services, or supporting the original work, share the love. Only a dick would make loads off this work and not buy the original works creator(s) a pint.

    Code is provided with no warranty. Using somebody else's code and bitching when it goes wrong makes you a DONKEY dick. Fix the problem yourself. A non-dick would submit the fix back.
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
		header('Content-type: text/html;charset=UTF-8');
		?><!DOCTYPE html>
		<html lang="en">
			<head>
				<base href="http://engine.magictallguy.tk/" />
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