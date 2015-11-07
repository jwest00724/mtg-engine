<?php
require_once(__DIR__ . '/includes/globals_out.php');
require_once(__DIR__ . '/includes/class/class_mtg_users.php');
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
}