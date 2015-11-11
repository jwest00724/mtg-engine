<?php
define('HEADER_TEXT', 'Jail');
require_once __DIR__ . '/includes/globals.php';
require_once __DIR__ . '/includes/class/class_mtg_paginate.php';
$pages = new Paginator();
$_GET['ID'] = isset($_GET['ID']) && ctype_digit($_GET['ID']) ? $_GET['ID'] : null;
if(!empty($_GET['ID'])) {
	$_GET['method'] = isset($_GET['method']) && in_array($_GET['method'], ['bust', 'bail']) ? $_GET['method'] : null;
	if(empty($_GET['method']))
		$mtg->error("You didn't select a valid method");
	$db->query('SELECT `jail`, `level` FROM `users` WHERE `id` = ?');
	$db->execute([$_GET['ID']]);
	if(!$db->num_rows())
		$mtg->error("That player doesn't exist");
	$player = $db->fetch_row(true);
	$target = $users->name($_GET['ID']);
	if(!$player['jail'])
		$mtg->error($target.' isn\'t in jail');
	if($_GET['method'] == 'bail') {
		$cost = $player['level'] * 5;
		if($cost > $my['points'])
			$mtg->error('It costs '.$mtg->format($cost).' point'.$mtg->s($cost).' to heal '.$target.'. You don\'t have enough');
		$db->startTrans();
		$db->query("UPDATE `users_finances` SET `points` = `points` - ? WHERE `id` = ?");
		$db->execute([$cost, $my['id']]);
		$db->query("UPDATE `users` SET `jail` = 0 WHERE `id` = ?");
		$db->execute([$_GET['ID']]);
		$users->send_event($_GET['ID'], 'law', '{id} bailed you from jail', $my['id']);
		$db->endTrans();
		$mtg->success('You\'ve posted '.$target.'\'s bail, costing you '.$mtg->format($cost).' point'.$mtg->s($cost));
	} else {
		$chance = mt_rand(0, 100);
		if($chance >= 50) {
			$exp = mt_rand($my['level'], $my['level'] + 50);
			$edb->startTrans();
			$db->query("UPDATE `users` SET `jail` = 0 WHERE `id` = ?");
			$db->execute([$_GET['ID']]);
			$db->query("UPDATE `users` SET `exp` = `exp` + ? WHERE `id` = ?");
			$db->execute([$exp, $my['id']]);
			$users->send_event($_GET['ID'], 'law', '{id} busted you from jail', $my['id']);
			$db->endTrans();
			$mtg->success('You\'ve busted '.$target.' from jail and gained '.$mtg->format($exp).' experience point'.$mtg->s($exp));
		} else if($chance >= 51 && $chance <= 90)
			$mtg->info('You\'ve failed to bust '.$target.' from jail, but you managed to escape before the officers caught you');
		else {
			$jailed = time() + mt_rand(60, 600);
			$db->query("UPDATE `users` SET `jail` = ?, `jail_reason` = ? WHERE `id` = ?");
			$db->execute([$jailed, 'Failed to bust '.$target, $my['id']]);
			$mtg->warning('Whilst attempting to bust '.$target.', the officers patrolling the cell block apprehended you. You\'ve been sent to jail for '.$mtg->time_format($jailed - time()));
		}
	}
}
$db->query("SELECT COUNT(`id`) FROM `users` WHERE `jail` > ?");
$db->execute([time()]);
$pages->items_total = $db->fetch_single();
$pages->mid_range = 3;
$pages->paginate();
$db->query("SELECT `id`, `jail`, `jail_reason` FROM `users` WHERE `jail` > ? ORDER BY `jail` ASC ".$pages->limit);
$db->execute([time()]);
?><p class="paginate"><?php echo $pages->display_pages(); ?></p>
<table width="100%" class="pure-table pure-table-striped">
	<tr>
		<th width="25%">Inmate</th>
		<th width="60%">Reason</th>
		<th width="15%">Actions</th>
	</tr><?php
	if(!$db->num_rows())
		echo '<tr><td colspan="3" class="center">There are no inmates</td></tr>';
	else {
		$rows = $db->fetch_row();
		foreach($rows as $row) {
			?><tr>
				<td><?php echo $users->name($row['id'], true);?></td>
				<td><?php echo stripslashes($row['jail_reason']);?></td>
				<td><?php echo !$my['jail'] ? '<a href="jail.php?method=bust&amp;ID='.$row['id'].'">Bust</a> &middot; <a href="jail.php?method=bail&amp;ID='.$row['id'].'">Bail</a>' : '';?></td>
			</tr><?php
		}
	}
?></table>
<p class="paginate"><?php echo $pages->display_pages(); ?></p>