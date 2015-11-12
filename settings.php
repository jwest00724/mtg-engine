<?php
define('HEADER_TEXT', 'Settings');
require_once __DIR__ . '/includes/globals.php';
if(array_key_exists('submit', $_POST)) {
	$values = ['username', 'password', 'cpassword', 'email'];
	foreach($values as $what)
		$_POST[$what] = array_key_exists($what, $_POST) && isset($_POST[$what]) ? trim($_POST[$what]) : null;
	$updates = [];
	if(!empty($_POST['username'])) {
		$db->query('SELECT `id` FROM `users` WHERE `username` = ? AND `id` <> ?');
		$db->execute([$_POST['username'], $my['id']]);
		if($db->num_rows())
			$mtg->error('That name has been taken');
		$db->query('UPDATE `users` SET `username` = ? WHERE `id` = ?');
		$db->execute([$_POST['username'], $my['id']]);
		$updates[] = 'username';
	}
	if(!empty($_POST['password']) && !empty($_POST['cpassword'])) {
		if(strlen($_POST['password']) < 6)
			$mtg->error('Your password requires at least 6 characters');
		if($_POST['password'] !== $_POST['cpassword'])
			$mtg->error('The passwords you entered didn\'t match');
		$db->query('UPDATE `users` SET `password` = ? WHERE `id` = ?');
		$db->execute([$users->hashPass($_POST['username']), $my['id']]);
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
	if(count($updates)) {
		$what = implode(', ', $updates);
		$mtg->success('You\'ve updated your '.$what);
	}
}
?><form action="settings.php" method="post" class="pure-form pure-form-aligned">
	<legend><h3>Account Settings</h3></legend>
	<fieldset>
		<div class="pure-control-group">
			<label for="username">Username<br /><div class="small">This also changes the name you use to login</div></label>
			<input type="text" name="username" placeholder="<?php echo $mtg->format($my['username']);?>" />
		</div>
		<div class="pure-control-group">
			<label for="password">Password</label>
			<input type="password" name="password" placeholder="Leave blank if you don&apos;t want to change it" />
		</div>
		<div class="pure-control-group">
			<label for="confirmation">Confirm Password</label>
			<input type="password" name="cpassword" placeholder="Re-enter your new password (leave blank if you want to keep your current password)" />
		</div>
		<div class="pure-control-group">
			<label for="email">Email</label>
			<input type="email" name="email" placeholder="<?php echo $mtg->format($my['email']);?>" />
		</div>
	</fieldset>
	<input type="submit" name="submit" class="pure-button pure-button-primary" value="Update Settings" />
</form>