<?php
require_once __DIR__ . '/includes/globals_out.php';
require_once __DIR__ . '/includes/class/class_mtg_users.php';
require_once __DIR__ . '/includes/securimage/securimage.php';
$securimage = new Securimage();
?><div class="header">
	<div class="logo"></div>
	<h1>Welcome to <?php echo $mtg->format($set['game_name']);?></h1>
	<h2 class="content-subhead">Please signup</h2>
</div>
<div class="content"><?php
	if(array_key_exists('submit', $_POST)) {
		$fields = ['username', 'password', 'cpassword', 'email'];
		foreach($fields as $what) {
			$_POST[$what] = isset($_POST[$what]) ? trim($_POST[$what]) : null;
			if(empty($_POST[$what]))
				$mtg->error('You didn\'t enter a '.str_replace('cpassword', 'password confirmation', $what));
		}
		if(($set['username_length_max'] > && strlen($_POST['username']) > $set['username_length_max'])) || ($set['username_length_min'] > 0 && strlen($_POST['username']) < $set['username_length_min']))
			$mtg->error('Your username must be between '.$set['username_length_min'].' and '.$set['username_length_max'].' characters');
		if($set['captcha_registration']) {
			$_POST['captcha_code'] = array_key_exists('captcha_code', $_POST) && ctype_digit($_POST['captcha_code']) && strlen($_POST['captcha_code']) == 6 ? $_POST['captcha_code'] : null;
			if($securimage->check($_POST['captcha_code']) == false)
				$mtg->error('You didn\'t enter a valid code');
		}
		$db->query('SELECT `id` FROM `users` WHERE `username` = ?');
		$db->execute([$_POST['username']]);
		if($db->num_rows())
			$mtg->error('That name has already been taken, please choose another');
		if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
			$mtg->error('The email you entered isn\'t valid');
		$db->query('SELECT `id` FROM `users` WHERE `email` = ?');
		$db->execute([$_POST['email']]);
		if($db->num_rows())
			$mtg->error('That email address is already in use, please choose another');
		$db->startTrans();
		$db->query('INSERT INTO `users` (`username`, `password`, `email`) VALUES (?, ?, ?)');
		$db->execute([$_POST['username'], $users->hashPass($_POST['password']), $_POST['email']]);
		$id = $db->insert_id();
		$db->query('INSERT INTO `users_equipment` (`id`) VALUES (?)');
		$db->execute([$id]);
		$db->query('INSERT INTO `users_finances` (`id`) VALUES (?)');
		$db->execute([$id]);
		$db->query('INSERT INTO `users_stats` (`id`) VALUES (?)');
		$db->execute([$id]);
		$db->query('INSERT INTO `users_ip` (`user`, `ip`) VALUES (?, ?)');
		$db->execute([$id, $mtg->_ip()]);
		$db->endTrans();
		$_SESSION['userid'] = $id;
		$mtg->success('You\'ve signed up! We\'re logging you in now <meta http-equiv="refresh" content="2; url=index.php" />', true);
	} else {
		?><form action="signup.php" method="post" class="pure-form pure-form-aligned">
			<legend>Register for a free account</legend>
			<div class="pure-control-group">
				<label for="username">Username</label>
				<input type='text' name="username" class="pure-u-1-3" required />
			</div>
			<div class="pure-control-group">
				<label for="password">Password</label>
				<input type="password" name="password" class="pure-u-1-3" required />
			</div>
			<div class="pure-control-group">
				<label for="confirmation">Confirm Password</label>
				<input type="password" name="cpassword" class="pure-u-1-3" required />
			</div>
			<div class="pure-control-group">
				<label for="email">Email</label>
				<input type="email" name="email" class="pure-u-1-3" required />
			</div>
			<div class="pure-control-group">
				<label for="dob">Date of Birth</label>
				<input type="date" name="dob" class="pure-u-1-3" placeholder="Optional" />
			</div><?php
			if($set['captcha_registration']) {
				?><div class="pure-control-group">
					<label for="image">Captcha Image</label>
					<img id="captcha" src="/includes/securimage/securimage_show.php" alt="CAPTCHA Image" />
				</div>
				<div class="pure-control-group">
					<label for="code">Captcha Code</label>
					<input type="text" name="captcha_code" size="10" maxlength="6" class="pure-u-1-3" />
					<a href="#" onclick="document.getElementById('captcha').src = '/includes/securimage/securimage_show.php?' + Math.random(); return false">[ Different Image ]</a>
				</div><?php
			}
			?><div class="pure-controls">
				<button type="submit" name="submit" value="true" class="pure-button pure-button-primary">Sign Up</button>
				<button type="reset" class="pure-button pure-button-secondary"><i class="fa fa-recycle"></i> Reset</button>
			</div>
		</form><?php
	}
?></div>