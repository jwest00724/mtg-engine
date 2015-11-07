<?php
if(!defined('MTG_ENABLE'))
	exit("Direct access not permitted");
$_GET['action'] = isset($_GET['action']) && ctype_alpha($_GET['action']) ? strtolower(trim($_GET['action'])) : null;
switch($_GET['action']) {
	case 'settings':
		gameSettings($db, $my, $mtg, $set);
		break;
	default:
		index($db, $my, $mtg, $set);
		break;
}
function index($db, $my, $mtg, $set) {
	if(array_key_exists('submit', $_POST)) {
		$_POST['text'] = isset($_POST['text']) ? trim($_POST['text']) : null;
		$db->query("UPDATE `game_settings` SET `value` = ? WHERE `name` = 'staff_notepad'");
		$db->execute([$_POST['text']]);
		$set['staff_notepad'] = $_POST['text'];
	}
	// $version = fopen('http://magictallguy.tk/vers/?v=9.1.1631', 'r');
	// if(!$version)
	// 	$vers = 'Unable to get update information';
	// else
	// 	while(!feof($version))
	// 		$vers = fgets($version, 1024);
	$vers = '<span style="color:green;">Currently being worked on</span>';
	$db->query("SELECT VERSION()");
	$db->execute();
	?><p><table width='100%'>
		<tr>
			<th width='25%'>Code Version</th>
			<td width='75%'><?php echo $vers; ?></td>
		</tr>
		<tr>
			<th>PHP Version</th>
			<td><?php echo phpversion(); ?></td>
		</tr>
		<tr>
			<th>MySQL Version</th>
			<td><?php echo $db->fetch_single(); ?></td>
		</tr>
	</table></p><?php
	if(array_key_exists('text', $_POST))
		echo $mtg->success("Staff Notepad updated");
	?><form method='post' class='cmxform'>
		<div class='height_area'><textarea name='text' rows='10' cols='40'><?php echo stripslashes($set['staff_notepad']); ?></textarea></div>
		<input type='submit' name='submit' value='Update Staff Notepad' />
	</form><?php
}
function gameSettings($db, $my, $mtg, $set) {
	?><h2 class="styleh3 p2">Game Settings</h2><?php
	if(array_key_exists('submit', $_POST)) {
		$strs = ['game_name', 'game_description', 'register_promo_code', 'main_currency_symbol'];
		foreach($strs as $what)
			$_POST[$what] = isset($_POST[$what]) && is_string($_POST[$what]) ? trim($_POST[$what]) : null;
		$nums = ['register_start_cash', 'register_promo_cash', 'game_owner_id'];
		foreach($nums as $what)
			$_POST[$what] = isset($_POST[$what]) && ctype_digit(str_replace(',', '', $_POST[$what])) ? str_replace(',', '', $_POST[$what]) : 0;
		$posted = array_merge($strs, $nums);
		foreach($posted as $what) {
			$db->query("UPDATE game_settings SET value = ? WHERE name = ?");
			$db->execute([$_POST[$what], $posted]);
		}
		$mtg->success("You've updated the game's settings");
	}
	?><form action='?action=settings' method='post' class='pure-form pure-form-aligned'>
		<table width='100%'>
			<tr>
				<thead><th colspan='2'>Basic Settings</th></thead>
			</tr>
			<tr>
				<th width='25%'>Name</th>
				<td width='75%'><input type='text' name='game_name' value='<?php echo $mtg->format($set['game_name']); ?>' /></td>
			</tr>
			<tr>
				<th>Description</th>
				<td><textarea name='game_description' rows='10' cols='40'><?php echo $mtg->format($set['game_description']); ?></textarea></td>
			</tr>
			<tr>
				<th>Game Owner's ID</th>
				<td><input type='text' name='game_owner_id' value='<?php echo $mtg->format($set['game_owner_id']); ?>' /></td>
			</tr>
			<tr>
				<thead><th colspan='2'>Registration</th></thead>
			</tr>
			<tr>
				<th>Start Cash</th>
				<td><input type='text' name='register_start_cash' value='<?php echo $mtg->format($set['register_start_cash']); ?>' /></td>
			</tr>
			<tr>
				<th>Promo Code</th>
				<td><input type='text' name='register_promo_code' value='<?php echo $mtg->format($set['register_promo_code']); ?>' /></td>
			</tr>
			<tr>
				<th>Promo Cash</th>
				<td><input type='text' name='register_promo_cash' value='<?php echo $mtg->format($set['register_promo_cash']); ?>' /></td>
			</tr>
			<tr>
				<td colspan='2' class='center'><input type='submit' name='submit' value='Update Settings' /></td>
			</tr>
		</table>
	</form><?php
}