<?php
define('NO_HEAD', true);
require_once __DIR__ . '/includes/globals_out.php';
require_once __DIR__ . '/includes/class/class_mtg_functions.php';
require_once __DIR__ . '/includes/class/class_mtg_users.php';
$_POST['username'] = isset($_POST['username']) ? trim($_POST['username']) : null;
$_POST['password'] = isset($_POST['password']) ? trim($_POST['password']) : null;
if(empty($_POST['username'])) {
	$_SESSION['msg'] = [
		'type' => 'error',
		'content' => 'You didn\'t enter your username'
	];
	exit(header("Location: login.php"));
}
if(empty($_POST['password'])) {
	$_SESSION['msg'] = [
		'type' => 'error',
		'content' => 'You didn\'t enter your password'
	];
	exit(header("Location: login.php"));
}
$db->query("SELECT `id`, `password`, `account_locked`, `login_attempts` FROM `users` WHERE `username` = ?");
$db->execute([$_POST['username']]);
if(!$db->num_rows()) {
	$_SESSION['msg'] = [
		'type' => 'error',
		'content' => 'An account with that name wasn\'t found'
	];
	exit(header("Location: login.php"));
}
$user = $db->fetch_row(true);
if(strtotime($user['account_locked']) >= time() && $user['login_attempts'] >= 5) {
	$_SESSION['msg'] = [
		'type' => 'error',
		'content' => 'Your account has been temporarily locked due to too many failed login attempts.<br />You can try again in '.$mtg->time_format(strtotime($user['account_locked']) - time())
	];
	exit(header('Location: login.php'));
} else if(strtotime($user['account_locked']) < time() && $user['login_attempts']) {
	$db->query('UPDATE `users` SET `account_locked` = "0000-00-00 00:00:00", `login_attempts` = 0 WHERE `id` = ?');
	$db->execute([$user['id']]);
}
if($user['password'] != $users->hashPass($_POST['password'])) {
	$_SESSION['msg'] = [
		'type' => 'error',
		'content' => 'That password was incorrect'
	];
	$db->startTrans();
	$db->query('UPDATE `users` SET `login_attempts` = `login_attempts` + 1 WHERE `id` = ?');
	$db->execute([$user['id']]);
	if($user['login_attempts'] + 1 == 5) {
		$db->query('UPDATE `users` SET `account_locked` = ? WHERE `id` = ?');
		$db->execute([date('Y-m-d H:i:s', time() + 900), $user['id']]);
	}
	$db->endTrans();
	exit(header("Location: login.php"));
}
$_SESSION['userid'] = $user['id'];
header('Location: index.php');