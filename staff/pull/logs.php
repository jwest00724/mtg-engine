<?php
if(!defined('MTG_ENABLE'))
	exit;
$_GET['action'] = array_key_exists('action', $_GET) && ctype_alpha($_GET['action']) ? strtolower(trim($_GET['action'])) : null;
?><div class="content-menu">
	<a href="staff/?pull=logs&amp;action=logs"<?php echo $_GET['action'] == 'logs' ? ' class="bold"' : '';?>>Logs: Staff</a>
</div><?php
switch($_GET['action']) {
	case 'staff':
		if(!$users->hasAccess('staff_panel_logs_staff'))
			$mtg->error('You don\'t have access');
		staffLogs($db, $mtg, $users, $logs, $pages);
		break;
	default:
		$mtg->info('Please choose an action above');
		break;
}
function staffLogs($db, $mtg, $users, $logs, $pages) {
	?><h3 class="content-subhead">Logs: Staff</h3><?php
	$db->query('SELECT COUNT(`id`) FROM `logs_staff`');
	$db->execute();
	$pages->items_total = $db->fetch_single();
	$pages->mid_range = 3;
	$pages->paginate();
	$db->query('SELECT `user`, `action`, `time` FROM `logs_staff` ORDER BY `time` DESC '.$pages->limit);
	$db->execute();
	?><div class="paginate"><?php echo $pages->display_pages();?></div>
	<table width="100%" class="pure-table pure-table-striped">
		<thead>
			<tr>
				<th width="25%">Player</th>
				<th width="25%">Time</th>
				<th width="50%">Action</th>
			</tr>
		</thead><?php
	if(!$db->num_rows())
		echo '<tr><td colspan="3" class="center">There are no staff logs</td></tr>';
	else {
		$rows = $db->fetch_row();
		foreach($rows as $row) {
			?><tr>
				<td><?php echo $users->name($row['user'], true);?></td>
				<td><?php echo date('H:i:s d/m/Y', strtotime($row['time']));?></td>
				<td><?php echo $mtg->format($row['action']);?></td>
			</tr><?php
		}
	}
	?></table>
	<div class="paginate"><?php echo $pages->display_pages();?></div><?php
	$logs->staff('Viewed the staff logs (Page '.$_GET['page'].')');
}