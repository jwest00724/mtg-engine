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
		$db->query("UPDATE users SET last_seen = current_timestamp WHERE id = ?");
		$db->execute(array($my['id']));
		header("Content-type: text/html;charset=UTF-8");
		?><!doctype html>
		<html lang="en">
		<head>
		<meta charset="utf-8">
		<title><?php echo $set['game_name']; ?></title>
		<meta name="author" content="Magictallguy" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<link rel="stylesheet" href="css/style.css" />
		<link rel="stylesheet" href="css/message.css" />
		</head>
		<body>
		<div class='logo'>&nbsp;</div>
		<div class='left-menu'>
		<div align='center'><?php
		if(!defined('MENU_ENABLE'))
			define('MENU_ENABLE', true);
		if(defined('MENU_STAFF'))
			require_once(DIRNAME(__DIR__) . '/staff/menu.php');
		else
			require_once(__DIR__ . '/menu.php');
		?></div>
		</div>
		<div class='right-menu'>
		<strong>Name:</strong> <?php echo $users->name($my['id'], true); ?><br />
		<strong>Money:</strong> <?php echo $set['main_currency_symbol'].$mtg->format($my['money']); ?><br />
		<strong>Level:</strong> <?php echo $mtg->format($my['level']); ?><br />
		<strong>Points:</strong> <?php echo $mtg->format($my['points']); ?><br />
		<strong>Merits:</strong> <?php echo $mtg->format($my['merits']); ?><br />
		<div class='title'>STATS</div>
		<div style='color:white;'>Bars coming soon</div>
		<span style='color:green;'>ENERGY: <?php echo round($my['energy'] / $my['energy_max'] * 100); ?>%</span><br />
		<span style='color:red;'>NERVE: <?php echo round($my['nerve'] / $my['nerve_max'] * 100); ?>%</span><br />
		<span style='color:cyan;'>HAPPY: <?php echo round($my['happy'] / $my['happy_max'] * 100); ?>%</span><br />
		<span style='color:orange;'>LIFE: <?php echo round($my['health'] / $my['health_max'] * 100); ?>%</span><br />
		<span style='color:pink;'>EXP: <?php echo $my['exp'].'/'.$users->expRequired(true); ?></span><br />
		<div class='title'>OTHER</div><br />
			<div align='center'>
				<div class='other-button'><a href='staff.php'>STAFF</a></div><br />
				<div class='other-button'><a href='lists.php?which=members'>MEMBERS</a></div><br />
				<div class='other-button'><a href='rules.php'>RULES</a></div><br />
			</div>
		</div>
		<div class='content-container'><?php
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
		global $my, $mtg, $users, $set;
		?></div>
		</body>
		</html><?php
	}
}