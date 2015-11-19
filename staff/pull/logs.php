<?php
if(!defined('MTG_ENABLE'))
	exit;
$_GET['action'] = array_key_exists('action', $_GET) && ctype_alpha($_GET['action']) ? strtolower(trim($_GET['action'])) : null;
?><div class="content-menu">
	<a href="staff/?pull=logs&amp;action=staff"<?php echo $_GET['action'] == 'staff' ? ' class="bold"' : '';?>>Logs: Staff</a> &middot;
	<a href="staff/?pull=logs&amp;action=ips"<?php echo $_GET['action'] == 'ips' ? ' class="bold"' : '';?>>Logs: IP Access</a> &middot;
</div><?php
switch($_GET['action']) {
	case 'staff':
		if(!$users->hasAccess('staff_panel_logs_staff'))
			$mtg->error('You don\'t have access');
		staffLogs($db, $mtg, $users, $logs, $pages);
		break;
	case 'ips':
		ipLogs($db, $mtg, $users, $logs, $pages);
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

function ipLogs($db, $mtg, $users, $logs, $pages) {
	// if(!$mtg->hasAccess('staff_panel_logs_ip_access'))
	// 	$mtg->error("You don't have access");
	?><h3 class="content-subhead">Logs: IP Access</h3>
	<p>
		<form action="staff/index.php" method="get" class="pure-form pure-form-aligned">
			<input type='hidden' name='pull' value='logs' />
			<input type='hidden' name='action' value='ips' />
			<div class="pure-control-group">
				<label for="user">Player ID</label>
				<input type="text" name="user" class="pure-u-1-3" required />
			</div>
			<div class="pure-controls">
				<button type="submit" class="pure-button pure-button-primary"><i class="fa fa-magnifier"></i> Check</button>
				<button type="reset" class="pure-button pure-button-secondary"><i class="fa fa-recycle"></i> Reset</button>
			</div>
		</form>
	</p><?php
	$where = '';
	$extra = '';
	$_GET['user'] = array_key_exists('user', $_GET) && ctype_digit($_GET['user']) ? $_GET['user'] : null;
	if(!empty($_GET['user'])) {
		$db->query('SELECT `id` FROM `users` WHERE `id` = ?');
		$db->execute([$_GET['user']]);
		if(!$db->num_rows())
			$mtg->error('That player doesn\'t exist');
		$where = 'WHERE `user` = '.$_GET['user'];
		$extra = ' for '.$users->name($_GET['user']);
	}
	$db->query('SELECT CAST(GROUP_CONCAT(`ip`) AS CHAR) AS `iplist`, COUNT(`user`) AS `cnt` FROM `users_ips` '.$where.' GROUP BY `user` ORDER BY `ip`');
	$db->execute();
	$cnt = $db->fetch_row(true);
	$pages->items_total = $cnt['cnt'];
	$pages->mid_range = 3;
	$pages->paginate();
	$db->query('SELECT `id`, CAST(GROUP_CONCAT(`ip`) AS CHAR) AS `iplist`, `user` FROM `users_ips` '.$where.' GROUP BY `user` ORDER BY `user` '.$pages->limit);
	$db->execute();
	?><div class="paginate"><?php echo $pages->display_pages();?></div>
	<table width="75%" class="pure-table pure-table-striped">
		<thead>
			<tr>
				<th width="30%">User</th>
				<th width="70%">IPs</th>
			</tr>
		</thead><?php
		if(!$db->num_rows())
			echo '<tr><td colspan="2" class="center">No IPs logged</td></tr>';
		else {
			$rows = $db->fetch_row();
			foreach($rows as $row) {
				$list = '';
				$cnt = 0;
				foreach(explode(',', $row['iplist']) as $ip) {
					++$cnt;
					$list .= '<a href="staff/?pull=logs&amp;action=ipcheck&amp;IP='.$ip.'">'.$ip.'</a> &middot; ';
					if($cnt % 5 == 0)
						$list .= '<br />';
				}
				?><tr>
					<td><?php echo $users->name($row['user'], true);?></td>
					<td><?php echo substr($list, 0, -10);?></td>
				</tr><?php
			}
		}
	?></table>
	<div class="paginate"><?php echo $pages->display_pages();?></div><?php
	$logs->staff('Viewed the IP access logs'.$extra.'. (Page: '.$_GET['page'].')');
}