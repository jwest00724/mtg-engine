<?php
require_once __DIR__ . '/includes/globals_out.php';
require_once __DIR__ . '/includes/class/class_mtg_functions.php';
require_once __DIR__ . '/includes/class/class_mtg_users.php';
?><div class="header">
	<div class="logo"></div>
	<h1>Welcome to <?php echo $mtg->format($set['game_name']);?></h1>
	<h2 class="content-subhead">Password Reset</h2>
</div>
<div class="content"><?php
$_GET['code'] = array_key_exists('code', $_GET) && ctype_alnum($_GET['code']) ? strtolower(trim($_GET['code'])) : null;
if(!empty($_GET['code'])) {
	$db->query('SELECT * FROM `users_reset` WHERE `code` = ?');
	$db->execute([$_GET['code']]);
	if(!$db->num_rows())
		$mtg->error('That reset couldn\'t be found');
	$row = $db->fetch_row(true);
	if(time() < strtotime($row['requested']) + 3600) {
		$db->query('DELETE FROM `users_reset` WHERE `id` = ?');
		$db->execute([$row['id']]);
		$mtg->error('That code has expired. Please remember that all codes are only valid for an hour');
	}
	if(!array_key_exists('submit', $_POST)) {
		?><form action="reset.php?code=<?php echo $_GET['code'];?>" method="post" class="pure-form pure-form-aligned">
			<div class="pure-control-group">
				<label for="password">Please enter your new password</label>
				<input type="password" name="password" class="pure-u-1-3" required />
			</div>
			<div class="pure-control-group">
				<label for="cpassword">Enter it again, just to be sure</label>
				<input type="password" name="cpassword" class="pure-u-1-3" required />
			</div>
			<div class="pure-controls">
				<button type="submit" name="submit" value="true" class="pure-button pure-button-primary"><i class="fa fa-cog"></i> Reset my password</button>
				<button type="reset" class="pure-button pure-button-secondary"><i class="fa fa-recycle"></i> Reset</button>
			</div>
		</form><?php
	} else {
		$_POST['password'] = array_key_exists('password', $_POST) && isset($_POST['password']) ? trim($_POST['password']) : null;
		if(empty($_POST['password']))
			$mtg->error('You didn\'t enter a valid password');
		$_POST['cpassword'] = array_key_exists('cpassword', $_POST) && isset($_POST['cpassword']) ? trim($_POST['cpassword']) : null;
		if(empty($_POST['cpassword']))
			$mtg->error('You didn\'t enter a valid password confirmation');
		if($_POST['password'] !== $_POST['cpassword'])
			$mtg->error('The passwords you entered didn\'t match');
		$db->startTrans();
		$db->query('UPDATE `users` SET `password` = ? WHERE `id` = ?');
		$db->execute([$users->hashPass($_POST['password']), $row['user']]);
		$db->query('DELETE FROM `users_reset` WHERE `user` = ?');
		$db->execute([$row['user']]);
		$db->endTrans();
		$_SESSION['msg'] = [
			'type' => 'success',
			'content' => 'Your password has been updated, please login'
		];
		exit(header('Location: login.php'));
	}
} else {
	if(!array_key_exists('submit', $_POST)) {
		?><form action="reset.php" method="post" class="pure-form pure-form-aligned">
			<div class="pure-control-group">
				<label for="email">Enter the email address you used to sign up</label>
				<input type="email" name="email" class="pure-u-1-3" required />
			</div>
			<div class="pure-controls">
				<button type="submit" name="submit" value="true" class="pure-button pure-button-primary"><i class="fa fa-cog"></i> Send the reset email</button>
				<button type="reset" class="pure-button pure-button-secondary"><i class="fa fa-recycle"></i> Reset</button>
			</div>
		</form><?php
	} else {
		$_POST['email'] = array_key_exists('email', $_POST) && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) ? $_POST['email'] : null;
		if(empty($_POST['email']))
			$mtg->error('You didn\'t enter a valid email address');
		$db->query('SELECT `requested` FROM `users_reset` WHERE `email` = ?');
		$db->execute([$_POST['email']]);
		if($db->num_rows()) {
			if(time() < strtotime($db->fetch_single()) + 3600) {
				$db->query('DELETE FROM `users_reset` WHERE `email` = ?');
				$db->execute([$_POST['email']]);
			} else
				$mtg->error('A password reset email has already been sent to you. Please remember to check your spam box too!');
		}
		$db->query('SELECT `id` FROM `users` WHERE `email` = ?');
		$db->execute([$_POST['email']]);
		if(!$db->num_rows())
			$mtg->error('An account with that email address couldn\'t be found');
		$id = $db->fetch_single();
		$code = md5(microtime(true));
		$db->query('INSERT INTO `users_reset` (`user`, `code`, `email`, `ip`) VALUES (?, ?, ?, ?)');
		$db->execute([$id, $code, $_POST['email'], $mtg->_ip()]);
		$message = 'Hello from '.$mtg->format($set['game_name'])."\n\n" .
			'A password reset has been requested from this email address. If that wasn\'t you, simply ignore this message. No changes have been made'."\n" .
			'However, if it was you, just click this link: http://'.$_SERVER['HTTP_HOST'].'/reset.php?code='.$code."\n\n" .
			'Regards, '."\n" .
			'The '.$mtg->format($set['game_name']).' Administration';
		if(@mail($_POST['email'], $mtg->format($set['game_name']).': Password Reset', $message, 'From: support@'.$_SERVER['HTTP_HOST']))
			$mtg->success('An email has been sent to you. Some providers can be a little slow, so please be patient - remember to check your spam box too!<br />Your reset code is only valid for 1 hour');
		else {
			$users->send_event($set['game_owner_id'], 'Administration', 'Password reset email couldn\'t be sent - check pending resets for '.$users->name($id, true));
			$mtg->error('A password reset email couldn\'t be sent. Not to worry, the administration has been informed. You should receive a response to the email address you provided within 48 hours (normally a lot sooner!)');
		}
	}
}