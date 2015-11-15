<?php
if(!defined('MTG_ENABLE'))
	exit;
$db->query('SELECT `last` FROM `settings_crons` WHERE `type` = "1min"');
$db->execute();
if($db->num_rows()) {
	$cron = strtotime($db->fetch_single());
	$deficit = time() - $cron;
	if($deficit >= 60) {
		$n = floor($deficit / 60);
		$db->startTrans();
		$db->query('UPDATE `users_stats` SET
			`health` = LEAST(`health` + (((`health_max` * 0.25) + 2) * ?), `health_max`),
			`nerve` = LEAST(`nerve` + (((`nerve_max` * 0.33) + 2) * ?), `nerve_max`),
			`power` = LEAST(`power` + (((`power_max` * 0.4) + 2) * ?), `power_max`),
			`happy` = LEAST(`happy` + (((`happy_max` * 0.35) + 2) * ?), `happy_max`),
			`energy` = LEAST(`energy` + (((`energy_max` * 0.15) + 2) * ?), `energy_max`)');
		$db->execute([$n, $n, $n, $n, $n]);
		$db->query('UPDATE `settings_crons` SET `last` = ? WHERE `type` = "1min"');
		$db->execute([date('Y-m-d H:i:s')]);
		$time = time();
		$floor = $time - floor($time / 60) * 60;
		if($floor > 0) {
			$next = time() - $floor;
			$db->query('UPDATE `settings_crons` SET `last` = ? WHERE `type` = "1min"');
			$db->execute([date('Y-m-d H:i:s', $next)]);
		}
		$db->endTrans();
	}
} else {
	$db->query('INSERT INTO `settings_crons` (`type`, `last`) VALUES ("1min", ?)');
	$db->execute([date('Y-m-d H:i:s')]);
}
/*
	This is an automated database backup system. It'll create a .gz (gzipped) archive of both the structure and data in your game's database daily
	You MAY wish to consider moving this to a cron file
	I've stuck it here because I'm lazy..
*/
$db->query('SELECT `last` FROM `settings_crons` WHERE `type` = "1day"');
$db->execute();
if($db->num_rows()) {
	$cron = strtotime($db->fetch_single());
	$deficit = time() - $cron;
	if($deficit >= 86400) {
		$date = date('Y-m-d');
		$backupFile = DIRNAME(__DIR__) . '/sql/' . DB_NAME . '_' . $date . '.gz';
		if(file_exists($backupFile))
			unlink($backupFile);
		$command = 'mysqldump --opt -h ' . DB_HOST . ' -u ' . DB_USER . ' -p\'' . DB_PASS . '\' ' . DB_NAME . ' | gzip > ' . $backupFile;
		system($command);
		$time = time();
		$floor = $time - floor($time / 86400) * 86400;
		if($floor > 0) {
			$next = time() - $floor;
			$db->query('UPDATE `settings_crons` SET `last` = ? WHERE `type` = "1day"');
			$db->execute([date('Y-m-d H:i:s', $next)]);
		}
	}
} else {
	$db->query('INSERT INTO `settings_crons` (`type`, `last`) VALUES ("1day", ?)');
	$db->execute([date('Y-m-d H:i:s')]);
}