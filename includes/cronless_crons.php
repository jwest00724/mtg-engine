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
		`happy` = IF(`happy` + ((`happy` * 0.05) * ?) > `happy_max`, `happy_max`, `happy` + (`happy` * 0.05) * ?),
		`nerve` = IF(`nerve` + ((`nerve` * 0.15) * ?) > `nerve_max`, `nerve_max`, `nerve` + (`nerve` * 0.15) * ?),
		`power` = IF(`power` + ((`power` * 0.25) * ?) > `power_max`, `power_max`, `power` + (`power` * 0.25) * ?),
		`health` = IF(`health` + ((`health` * 0.05) * ?) > `health_max`, `health_max`, `health` + (`health` * 0.05) * ?)');
	$db->execute([$n, $n, $n, $n, $n, $n, $n, $n]);
	$db->query('UPDATE `settings_crons` SET `last` = ? WHERE `type` = "1min"');
	$db->execute([date('Y-m-d H:i:s', time() + 60)]);
	$db->endTrans();
}