<?php
define('HEADER_TEXT', 'Notifications and Events');
require_once __DIR__ . '/includes/globals.php';
require_once __DIR__ . '/includes/class/class_mtg_paginate.php';
$pages = new Paginator();
$_GET['ID'] = isset($_GET['ID']) && ctype_digit($_GET['ID']) ? $_GET['ID'] : null;
$_GET['action'] = isset($_GET['action']) && ctype_alpha($_GET['action']) ? strtolower(trim($_GET['action'])) : null;
switch($_GET['action']) {
	case 'delete':
		if(empty($_GET['ID']))
			$mtg->error("You didn't select a valid event");
		$db->query("SELECT `user` FROM `users_events` WHERE `id` = ?");
		$db->execute([$_GET['ID']]);
		if(!$db->num_rows())
			$mtg->error("That event doesn't exist");
		if($db->fetch_single() != $my['id'])
			$mtg->error("That's not your event");
		$db->query("UPDATE `users_events` SET `deleted` = 1 WHERE `id` = ?");
		$db->execute([$_GET['ID']]);
		$mtg->success("Your event has been deleted");
		break;
	default:
		$db->query("SELECT COUNT(`id`) FROM `users_events` WHERE `user` = ?");
		$db->execute([$my['id']]);
		$pages->items_total = $db->fetch_single();
		$pages->mid_range = 3;
		$pages->paginate();
		$db->query("SELECT `id`, `text`, `type`, `time_sent` FROM `users_events` WHERE `user` = ? ORDER BY `time_sent` DESC ".$pages->limit);
		$db->execute([$my['id']]);
		?><p class='paginate'><?php echo $pages->display_pages(); ?></p>
		<table width='100%' class='pure-table pure-table-striped'>
			<tr>
				<th width='25%'>Info</th>
				<th width='65%'>Event</th>
				<th width='10%'>Actions</th>
			</tr><?php
			if(!$db->num_rows())
				echo "<tr><td colspan='3'>You have no events</td></tr>";
			else {
				$rows = $db->fetch_row();
				foreach($rows as $row) {
					?><tr>
						<td>
							<strong>Received:</strong> <?php echo date('H:i:s d/m/Y', strtotime($row['time_sent'])); ?><br />
							<strong>Category:</strong> <?php echo ucfirst($row['type']); ?>
						</td>
						<td><?php echo stripslashes($row['text']); ?></td>
						<td><a href='events.php?action=delete&amp;ID=<?php echo $row['id']; ?>'>Delete</a></td>
					</tr><?php
				}
			}
		?></table>
		<p class='paginate'><?php echo $pages->display_pages(); ?></p><?php
		break;
}