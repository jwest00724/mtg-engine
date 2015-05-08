<?php
define('NO_HEAD', true);
require_once(__DIR__ . '/includes/globals_out.php');
require_once(__DIR__ . '/includes/class/class_mtg_users.php');
$_POST['username'] = isset($_POST['username']) ? trim($_POST['username']) : null;
$_POST['password'] = isset($_POST['password']) ? trim($_POST['password']) : null;
if(empty($_POST['username'])) {
	$_SESSION['msg'] = "You didn't enter your username";
	exit(header("Location: login.php"));
}
if(empty($_POST['password'])) {
	$_SESSION['msg'] = "You didn't enter your password";
	exit(header("Location: login.php"));
}
$db->query("SELECT id, password FROM users WHERE username = ?");
$db->execute(array($_POST['username']));
if(!$db->num_rows()) {
	$_SESSION['msg'] = "An account with that name wasn't found";
	exit(header("Location: login.php"));
}
$user = $db->fetch_row(true);
if($pass != $users->hashPass($_POST['password'])) {
	$_SESSION['msg'] = "That password is incorrect";
	exit(header("Location: login.php"));
}
$_SESSION['userid'] = $user['id'];
header("Location: index.php");