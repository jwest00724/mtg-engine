<?php
require_once(__DIR__ . '/includes/globals_out.php');
require_once(__DIR__ . '/includes/class/class_mtg_users.php');
?><div class="header">
	<div class="logo"></div>
	<h1>Welcome to <?php echo $mtg->format($set['game_name']);?></h1>
	<h2 class="content-subhead">Please signup</h2>
</div>
<div class="content"><?php
	if(isset($_SESSION['msg'])) {
		$mtg->error($_SESSION['msg'], false);
		unset($_SESSION['msg']);
	}
	if(array_key_exists('submit', $_POST)) {
		$fields = ['username', 'password', 'cpassword', 'email'];
		foreach($fields as $what) {
			$_POST[$what] = isset($_POST[$what]) ? trim($_POST[$what]) : null;
			if(empty($_POST[$what]))
				$mtg->error("You didn't enter a ".str_replace('cpassword', 'password confirmation', $what));
		}
		$db->query("SELECT `id` FROM `users` WHERE `username` = ?");
		$db->execute([$_POST['username']]);
		if($db->num_rows())
			$mtg->error("That name has already been taken, please choose another");
		if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
			$mtg->error("The email you entered isn't valid");
		$db->query("SELECT `id` FROM `users` WHERE `email` = ?");
		$db->execute([$_POST['email']]);
		if($db->num_rows())
			$mtg->error("That email address is already in use, please choose another");
		$db->startTrans();
		$db->query("INSERT INTO `users` (`username`, `password`, `email`) VALUES (?, ?, ?)");
		$db->execute([$_POST['username'], $users->hashPass($_POST['password']), $_POST['email']]);
		$id = $db->insert_id();
		$db->query("INSERT INTO `users_equipment` (`id`) VALUES (?)");
		$db->execute([$id]);
		$db->query("INSERT INTO `users_finances` (`id`) VALUES (?)");
		$db->execute([$id]);
		$db->query("INSERT INTO `users_stats` (`id`) VALUES (?)");
		$db->execute([$id]);
		$db->endTrans();
		$_SESSION['userid'] = $id;
		$mtg->success("You've signed up! We're logging you in now <meta http-equiv='refresh' content='2; url=index.php' />", true);
	} else {
		?><form action="signup.php" method="post" class="pure-form pure-form-aligned">
			<fieldset>
				<legend>Register for a free account</legend>
				<div class="pure-control-group">
					<label for="username">Username</label>
					<input type='text' name="username" required />
				</div>
				<div class="pure-control-group">
					<label for="password">Password</label>
					<input type="password" name="password" required />
				</div>
				<div class="pure-control-group">
					<label for="confirmation">Confirm Password</label>
					<input type="password" name="cpassword" required />
				</div>
				<div class="pure-control-group">
					<label for="email">Email</label>
					<input type="email" name="email" required />
				</div>
				<div class="pure-control-group">
					<label for="dob">Date of Birth</label>
					<input type="date" name="dob" placeholder="Optional" />
				</div>
				<div class="pure-controls">
					<button type="submit" name="submit" class="pure-button pure-button-primary">Sign Up</button>
				</div>
			</fieldset>
		</form><?php
	}
?></div>