<?php
if(!defined('MTG_ENABLE'))
	exit;
$links = array(
	'index' => 'Home',
	'messages' => 'Messages [msg_count]',
	'events' => 'Notifications [ev_count]',
	'hospital' => 'Hospital [hosp_count]',
	'jail' => 'Jail [jail_count]',
	'crimes' => 'Crimes',
	'gym' => 'Gym',
	'markets' => 'Markets'
);
foreach($links as $url => $disp) {
	if(preg_match('/\[msg_count\]/', $disp)) {
		$db->query("SELECT COUNT(id) FROM users_messages WHERE `read` = 0 AND receiver = ?");
		$db->execute(array($my['id']));
		$disp = str_replace('[msg_count]', '['.$mtg->format($db->fetch_single()).']', $disp);
	}
	if(preg_match('/\[ev_count\]/', $disp)) {
		$db->query("SELECT COUNT(id) FROM users_events WHERE `read` = 0 AND user = ?");
		$db->execute(array($my['id']));
		$disp = str_replace('[ev_count]', '['.$mtg->format($db->fetch_single()).']', $disp);
	}
	if(preg_match('/\[hosp_count\]/', $disp)) {
		$db->query("SELECT COUNT(id) FROM users WHERE `hospital` > ?");
		$db->execute(array(time()));
		$disp = str_replace('[hosp_count]', '['.$mtg->format($db->fetch_single()).']', $disp);
	}
	if(preg_match('/\[jail_count\]/', $disp)) {
		$db->query("SELECT COUNT(id) FROM users WHERE `jail` > ?");
		$db->execute(array(time()));
		$disp = str_replace('[jail_count]', '['.$mtg->format($db->fetch_single()).']', $disp);
	}
	printf("<div class='button'><a href='%s.php'>%s</a></div>\n<div class='spacer'></div>\n", $url, $disp);
}
if($users->hasAccess('staff_panel_access'))
	echo "<div class='button'><a href='staff'>Staff Panel</a></div>\n<div class='spacer'></div>\n";
?><div class='button'><a href='?action=logout'>Logout</a></div>
<div class='spacer'></div>