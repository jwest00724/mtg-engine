<?php
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
	static function getInstance($db, $set, $my, $mtg, $users) {
		if(self::$inst == null)
			self::$inst = new headers($db, $set, $my, $mtg, $users);
		return self::$inst;
	}
	function __construct($db, $set, $my, $mtg, $users) {
		$db->query('UPDATE `users` SET `last_seen` = current_timestamp WHERE `id` = ?');
		$db->execute([$my['id']]);
		header("Content-type: text/html;charset=UTF-8");
		?><!DOCTYPE html>
		<html lang="en">
			<head><?php echo "\n\t\t\t\t";
				if(preg_match('/localhost/i', $_SERVER['HTTP_HOST']))
					echo '<base href="http://localhost/mtg-engine/">'."\n\t\t\t\t";
				?><meta charset="utf-8">
				<meta name="viewport" content="width=device-width, initial-scale=1.0">
				<meta name="description" content="MTG Codes v9" />
				<meta name="author" content="Magictallguy" />
				<title><?php echo $mtg->format($set['game_name']);?> - MTG Codes v9</title>
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
				<link rel="stylesheet" type="text/css" href="css/message.css" />
				<link rel="stylesheet" type="text/css" href="css/style.css" />
				<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css" />
			</head>
		<body>
			<div id="layout">
				<a href="#menu" id="menuLink" class="menu-link"><span>&nbsp;</span></a>
				<div id="menu">
					<div class="userinfo">
						<ul class="pure-menu-list">
							<li class="pure-menu-item"><?php echo $users->name($my['id'], true);?></li>
							<li class="pure-menu-item"><strong>Money:</strong> <?php echo $set['main_currency_symbol'].$mtg->format($my['money']);?></li>
							<li class="pure-menu-item"><strong>Level:</strong> <?php echo $mtg->format($my['level']);?></li>
							<li class="pure-menu-item"><strong>Points:</strong> <?php echo $mtg->format($my['points']);?></li>
							<li class="pure-menu-item"><strong>Merits:</strong> <?php echo $mtg->format($my['merits']);?></li>
						</ul><hr />
						<ul class="pure-menu-list">
							<li class="pure-menu-item">ENERGY: <?php echo round($my['energy'] / $my['energy_max'] * 100);?>%</li>
							<li class="pure-menu-item">NERVE: <?php echo round($my['nerve'] / $my['nerve_max'] * 100);?>%</li>
							<li class="pure-menu-item">HAPPY: <?php echo round($my['happy'] / $my['happy_max'] * 100);?>%</li>
							<li class="pure-menu-item">LIFE: <?php echo round($my['health'] / $my['health_max'] * 100);?>%</li>
							<li class="pure-menu-item">EXP: <?php echo $mtg->format($my['exp'], 2).'/'.$users->expRequired(true);?></li>
						</ul>
					</div>
					<div class="pure-menu"><?php
					if(!defined('MENU_ENABLE'))
						define('MENU_ENABLE', true);
					require_once defined('MENU_STAFF') ? DIRNAME(__DIR__) . '/staff/menu.php' : __DIR__ . '/menu.php';
					?></div>
				</div>
				<div id="main">
					<div class="header">
						<div class="logo">&nbsp;</div><?php
						if(defined('HEADER_TEXT'))
							echo '<h3>',HEADER_TEXT,'</h3>';
					?></div>
					<div class="content"><?php
						if(array_key_exists('action', $_GET) && $_GET['action'] == 'logout') {
							session_unset();
							session_destroy();
							session_start();
							$_SESSION['msg'] = [
								'type' => 'success',
								'content' => 'You\'ve logged out. Come back soon!'
							];
							exit(header('Location: index.php'));
						}
						if($my['hospital'])
							echo '<strong>Nurse:</strong> You\'re currently in hospital for '.$mtg->time_format($my['hospital'] * 60).'.<br />';
						if($my['jail'])
							echo '<strong>Officer:</strong> You\'re currently in jail for '.$mtg->time_format($my['jail'] * 60).'.<br />';
	}
	function __destruct() {
		if(!isset($mtg)) {
			require_once __DIR__ . '/class/class_mtg_functions.php';
			$mtg = MTG\mtg_functions::getInstance();
		}
		$year = date('Y');
		?>			</div>
					<div class="footer">
						Running MTG Codes <?php echo $mtg->codeVersion('installed');?><br />
						Copyright &copy;2015<?php echo $year > 2015 ? ' - '.$year : '';?>, Magictallguy.
					</div>
				</div>
			</div>
			<script src="js/ui.js"></script>
			</body>
		</html><?php
	}
}