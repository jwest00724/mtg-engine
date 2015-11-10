<?php
define('HEADER_TEXT', 'Hospital');
require_once(__DIR__ . '/includes/globals.php');
require_once(__DIR__ . '/includes/class/class_mtg_paginate.php');
$pages = new Paginator();
$_GET['heal'] = isset($_GET['heal']) && ctype_digit($_GET['heal']) ? $_GET['heal'] : null;
if(!empty($_GET['heal'])) {
	$db->query("SELECT hospital, level FROM users WHERE id = ?");
	$db->execute(array($_GET['heal']));
	if(!$db->num_rows())
		$mtg->error("That player doesn't exist");
	$player = $db->fetch_row(true);
	$target = $users->name($_GET['heal']);
	if(!$player['hospital'])
		$mtg->error($target." isn't in hospital");
	$cost = $player['level'] * 3;
	if($cost > $my['points'])
		$mtg->error("It costs ".$mtg->format($cost)." points to heal ".$target.". You don't have enough");
	$db->startTrans();
	$db->query("UPDATE users SET points = points - ? WHERE id = ?");
	$db->execute(array($cost, $my['id']));
	$db->query("UPDATE users SET health = health_max, hospital = 0 WHERE id = ?");
	$db->execute(array($_GET['heal']));
	$users->send_event($_GET['heal'], 'medical', $users->name($my['id'])." healed you");
	$db->endTrans();
	$mtg->success("You've healed ".$target." for ".$mtg->format($cost)." points");
}
$db->query("SELECT COUNT(id) FROM users WHERE hospital > ?");
$db->execute(array(time()));
$pages->items_total = $db->fetch_single();
$pages->mid_range = 3;
$pages->paginate();
$db->query("SELECT id, hospital, hospital_reason FROM users WHERE hospital > ? ORDER BY hospital ASC ".$pages->limit);
$db->execute(array(time()));
?><p class='paginate'><?php echo $pages->display_pages(); ?></p>
<table width='100%' class='pure-table pure-table-striped'>
	<tr>
		<th width='25%'>Patient</th>
		<th width='60%'>Reason</th>
		<th width='15%'>Actions</th>
	</tr><?php
	if(!$db->num_rows())
		echo "<tr><td colspan='3'>There are no patients</td></tr>";
	else {
		$rows = $db->fetch_row();
		foreach($rows as $row) {
			?><tr>
				<td><?php echo $users->name($row['id'], true); ?></td>
				<td><?php echo stripslashes($row['hospital_reason']); ?></td>
				<td><?php echo !$my['hospital'] ? "<a href='hospital.php?heal=".$row['id']."'>Heal</a>" : ''; ?></td>
			</tr><?php
		}
	}
?></table>
<p class='paginate'><?php echo $pages->display_pages(); ?></p>