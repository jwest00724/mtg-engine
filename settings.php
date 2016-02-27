<?php
define('HEADER_TEXT', 'Settings');
require_once __DIR__ . '/includes/globals.php';
if(array_key_exists('submit', $_POST)) {
	$values = ['username', 'current', 'password', 'cpassword', 'email', 'timeout'];
	foreach($values as $what)
		$_POST[$what] = array_key_exists($what, $_POST) && isset($_POST[$what]) ? trim($_POST[$what]) : null;
	$updates = [];
	if(!empty($_POST['username'])) {
		if(($set['username_length_max'] > 0 && strlen($_POST['username']) > $set['username_length_max']) || ($set['username_length_min'] > 0 && strlen($_POST['username']) < $set['username_length_min']))
			$mtg->error('Your username must be between '.$set['username_length_min'].' and '.$set['username_length_max'].' characters');
		$db->query('SELECT `id` FROM `users` WHERE `username` = ? AND `id` <> ?');
		$db->execute([$_POST['username'], $my['id']]);
		if($db->num_rows())
			$mtg->error('That name has been taken');
		$db->query('UPDATE `users` SET `username` = ? WHERE `id` = ?');
		$db->execute([$_POST['username'], $my['id']]);
		$updates[] = 'username';
	}
	if(!empty($_POST['password']) && !empty($_POST['cpassword']) && !empty($_POST['current'])) {
		if(!password_verify($_POST['current'], $my['password']))
			$mtg->error('The password you entered as your current was incorrect');
		if(strlen($_POST['password']) < 6)
			$mtg->error('Your password requires at least 6 characters');
		if($_POST['password'] !== $_POST['cpassword'])
			$mtg->error('The passwords you entered didn\'t match');
		$db->query('UPDATE `users` SET `password` = ? WHERE `id` = ?');
		$db->execute([password_hash($_POST['username'], PASSWORD_BCRYPT), $my['id']]);
		$updates[] = 'password';
	}
	if(!empty($_POST['email'])) {
		if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
			$mtg->error('The email address you entered isn\'t valid');
		$db->query('SELECT `id` FROM `users` WHERE `email` = ? AND `id` <> ?');
		$db->execute([$_POST['email'], $my['id']]);
		if($db->num_rows())
			$mtg->error('That email has already been assigned to another account');
		$db->query('UPDATE `users` SET `email` = ? WHERE `id` = ?');
		$db->execute([$_POST['email'], $my['id']]);
		$updates[] = 'email';
	}
	if(!empty($_POST['timeout'])) {
		if(!in_array($_POST['timeout'], [300, 900, 1800, 3600, 86400, 'never']))
			$mtg->error('You didn\'t select a valid timeout');
		$users->changeSetting('logout_threshold', $_POST['timeout']);
		$updates[] = 'login timeout';
	}
	if(count($updates)) {
		$what = implode(', ', $updates);
		$mtg->success('You\'ve updated your '.$what);
	}
}
$logouts = [
	300 => '5 minutes',
	900 => '15 minutes',
	1800 => '30 minutes',
	3600 => 'An hour',
	86400 => 'A day',
	'never' => 'Never'
];
$logout = $users->getSetting('logout_threshold');
?><form action="settings.php" method="post" class="pure-form pure-form-aligned">
	<h3 class="content-subhead">Account Settings</h3>
	<div class="pure-control-group">
		<label for="username">Username<br /><div class="small">This also changes the name you use to login</div></label>
		<input type="text" name="username" placeholder="<?php echo $mtg->format($my['username']);?>" class="pure-input-1-2" />
	</div>
	<div class="pure-control-group">
		<label for="current">Current Password<span class="blue">*</span></label>
		<input type="password" name="current" placeholder="Only required if you&apos;re changing your password" class="pure-input-1-2" />
	</div>
	<div class="pure-control-group">
		<label for="password">New Password<span class="red">*</span></label>
		<input type="password" name="password" placeholder="Leave blank if you don&apos;t want to change it" class="pure-input-1-2" />
	</div>
	<div class="pure-control-group">
		<label for="confirmation">Confirm New Password<span class="red">*</span></label>
		<input type="password" name="cpassword" placeholder="Re-enter your new password" class="pure-input-1-2" />
	</div>
	<div class="pure-control-group">
		<label for="email">Email</label>
		<input type="email" name="email" placeholder="<?php echo $mtg->format($my['email']);?>" class="pure-input-1-2" />
	</div>
	<div class="pure-control-group">
		<label for="timeout">Automatic log out</label>
		<select name="timeout"><?php
		foreach($logouts as $time => $display)
			printf('<option value="%s"%s>%s</option>', $time, $time == $logout ? ' selected' : null, $display);
		?></select>
	</div>
	<div class="pure-controls">
		<button type="submit" name="submit" value="true" class="pure-button pure-button-primary"><i class="fa fa-cog"></i> Update Settings</button>
		<button type="reset" class="pure-button pure-button-secondary"><i class="fa fa-recycle"></i> Reset</button>
	</div>
	<div class=""
</form>