<?php
if(!defined('MTG_ENABLE'))
	exit;
$db->query('SELECT `last` FROM `settings_crons` WHERE `type` = "1min"');
$db->execute();
if(!$db->num_rows())
	exit;
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