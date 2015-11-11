<?php
if(!defined('MTG_ENABLE'))
	exit;
?><ul class="pure-menu-list"><?php
$links = array(
	'index.php' => 'Home',
	'messages.php' => 'Messages [msg_count]',
	'events.php' => 'Notifications [ev_count]',
	'hospital.php' => 'Hospital [hosp_count]',
	'jail.php' => 'Jail [jail_count]',
	'tasks.php' => 'Tasks',
	'gym.php' => 'Gym',
	'markets.php' => 'Markets',
	'list.php?action=players' => 'Player List',
	'list.php?action=online' => 'Online List'
);
foreach($links as $url => $disp) {
	if(preg_match('/\[msg_count\]/', $disp)) {
		$db->query("SELECT COUNT(`id`) FROM `users_messages` WHERE `read` = 0 AND `receiver` = ?");
		$db->execute([$my['id']]);
		$disp = str_replace('[msg_count]', '['.$mtg->format($db->fetch_single()).']', $disp);
	}
	if(preg_match('/\[ev_count\]/', $disp)) {
		$db->query("SELECT COUNT(`id`) FROM `users_events` WHERE `read` = 0 AND `user` = ?");
		$db->execute([$my['id']]);
		$disp = str_replace('[ev_count]', '['.$mtg->format($db->fetch_single()).']', $disp);
	}
	if(preg_match('/\[hosp_count\]/', $disp)) {
		$db->query("SELECT COUNT(`id`) FROM `users` WHERE `hospital` > ?");
		$db->execute([time()]);
		$disp = str_replace('[hosp_count]', '['.$mtg->format($db->fetch_single()).']', $disp);
	}
	if(preg_match('/\[jail_count\]/', $disp)) {
		$db->query("SELECT COUNT(`id`) FROM `users` WHERE `jail` > ?");
		$db->execute([time()]);
		$disp = str_replace('[jail_count]', '['.$mtg->format($db->fetch_single()).']', $disp);
	}
	printf('<li class="pure-menu-item"><a href="%s" class="pure-menu-link%s">%s</a></li>'."\n", $url, $_SERVER['PHP_SELF'] == '/'.$url ? ' pure-menu-selected' : null, $disp);
}
if($users->hasAccess('staff_panel_access'))
	echo '<li class="pure-menu-item menu-item-divided"><a href="staff" class="pure-menu-link">Staff Panel</a></li>'."\n";
?><li class="pure-menu-item menu-item-divided"><a href="settings.php" class="pure-menu-link">Settings</a></li>
<li class="pure-menu-item menu-item-divided"><a href="?action=logout" class="pure-menu-link">Logout</a></li>
</ul>