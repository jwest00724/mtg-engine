<?php
if(!defined('MTG_ENABLE'))
	exit;
if(!$users->hasAccess('override_all'))
	$mtg->error("You don't have access");
$fields = [];
$db->query('SHOW COLUMNS FROM `staff_ranks` WHERE `Type` = "enum(\'Yes\',\'No\')"');
$db->execute();
$rows = $db->fetch_row();
foreach($rows as $row)
	$fields[] = $row['Field'];
$_GET['ID'] = array_key_exists('ID', $_GET) && ctype_digit($_GET['ID']) ? $_GET['ID'] : null;
$_GET['action'] = array_key_exists('action', $_GET) && ctype_alpha($_GET['action']) ? strtolower(trim($_GET['action'])) : null;
switch($_GET['action']) {
	case 'add':
		if(!$users->hasAccess('staff_panel_staff_ranks_add'))
			$mtg->error('You don\'t have access');
		$users->updateStatus("Adding a new staff rank");
		addRank($db, $mtg, $logs);
		break;
	case 'edit':
		if(!$users->hasAccess('staff_panel_staff_ranks_edit'))
			$mtg->error('You don\'t have access');
		$users->updateStatus("Editing a staff rank");
		editRank($db, $mtg, $logs);
		break;
	case 'del':
		if(!$users->hasAccess('staff_panel_staff_ranks_delete'))
			$mtg->error('You don\'t have access');
		$users->updateStatus("Deleting a staff rank");
		deleteRank($db, $mtg, $logs);
		break;
	case 'view':
		if(!$users->hasAccess('staff_panel_staff_ranks_manage'))
			$mtg->error('You don\'t have access');
		$users->updateStatus("Viewing the details of a rank");
		viewRank($db, $mtg, $logs);
		break;
	case 'set':
		if(!$users->hasAccess('staff_panel_staff_ranks_manage'))
			$mtg->error('You don\'t have access');
		$users->updateStatus("Setting a player's staff rank");
		setStaffRank($db, $mtg, $logs);
		break;
	case 'currentstaff':
		if(!$users->hasAccess('staff_panel_staff_ranks_manage'))
			$mtg->error('You don\'t have access');
		$users->updateStatus("Managing the Staff List");
		manageStaff($db, $mtg, $logs);
		break;
	default:
		listRanks($db, $mtg);
		break;
}

function listRanks($db, $mtg) {
	global $fields;
	?><h4><a href="staff/?pull=ranks&amp;action=add">Add Rank</a></h4>
	<table class="table" width="100%">
		<tr>
			<th width="40%">Rank</th>
			<th width="20%">Order</th>
			<th width="40%">Actions</th>
		</tr><?php
		$db->query("SELECT `rank_id`, `rank_name`, `rank_desc`, `rank_colour`, `rank_order` FROM `staff_ranks` ORDER BY `rank_order` ASC");
		$db->execute();
		if(!$db->num_rows())
			echo '<tr><td colspan="3" class="center">There are no staff ranks</td></tr>';
		else {
			$rows = $db->fetch_row();
			foreach($rows as $row) {
				?><tr>
					<td><span style="color:#<?php echo $row['rank_colour'];?>;"><?php echo $mtg->format($row['rank_name']);?></span><br /><span class="small"><strong>Description:</strong> <?php echo $row['rank_desc'] ? $mtg->format($row['rank_desc']) : 'None';?></span></td>
					<td><?php echo $mtg->format($row['rank_order']);?></td>
					<td>
						<a href="staff/?pull=ranks&amp;action=view&amp;ID=<?php echo $row['rank_id'];?>">View</a> &middot;
						<a href="staff/?pull=ranks&amp;action=edit&amp;ID=<?php echo $row['rank_id'];?>">Edit</a> &middot;
						<a href="staff/?pull=ranks&amp;action=del&amp;ID=<?php echo $row['rank_id'];?>">Delete</a>
					</td>
				</tr><?php
			}
		}
	?></table><?php
}

function addRank($db, $mtg, $logs) {
	global $fields;
	if(!array_key_exists('submit', $_POST)) {
		?><form action="staff/?pull=ranks&amp;action=add" method="post">
			<table class="table" width="100%">
				<tr>
					<th width="25%">Name</th>
					<td width="75%"><input type="text" name="rank_name" /></td>
				</tr>
				<tr>
					<th>Description</th>
					<td><input type="text" name="rank_desc" /></td>
				</tr>
				<tr>
					<th>Colour</th>
					<td><input type="text" name="rank_colour" maxlength="6" /></td>
				</tr>
				<tr>
					<th>Order</th>
					<td><input type="number" name="rank_order" /></td>
				</tr><?php
				foreach($fields as $col) {
					$name = ucwords(str_replace('_', ' ', $col));
					?><tr>
						<th><?php echo str_replace(' Ip ', ' IP ', $name);?></th>
						<td><select name="<?php echo $col;?>" style="width:50%;">
							<option value="No" selected="selected">Disabled</option>
							<option value="Yes">Enabled</option>
						</select></td>
					</tr><?php
				}
				?><tr>
					<td colspan="2" class="center"><input type="submit" name="submit" value="Add Rank" /></td>
				</tr>
			</table>
		</form><?php
	} else {
		unset($_POST['submit']);
		$_POST['rank_name'] = isset($_POST['rank_name']) ? $db->escape($_POST['rank_name']) : null;
		if(empty($_POST['rank_name']))
			$mtg->error("You didn't select a valid name");
		$_POST['rank_colour'] = isset($_POST['rank_colour']) && ctype_alnum($_POST['rank_colour']) ? trim($_POST['rank_colour']) : null;
		$db->query('SELECT `rank_id` FROM `staff_ranks` WHERE `rank_name` = ?');
		$db->execute([$_POST['rank_name']]);
		if($db->num_rows())
			$mtg->error("A rank with that name already exists");
		$db->query('SELECT `rank_order` FROM `staff_ranks` ORDER BY `rank_order` DESC LIMIT 1');
		$db->execute();
		$order = !$_POST['rank_order'] || !ctype_digit($_POST['rank_order']) ? ($db->num_rows() ? $db->fetch_single() + 1 : 1 ) : $_POST['rank_order'];
		$db->startTrans();
		$db->query('SELECT `rank_order` FROM `staff_ranks` WHERE `rank_order` >= ?');
		$db->execute([$order]);
		if($db->num_rows()) {
			$db->query('UPDATE `staff_ranks` SET `rank_order` = `rank_order` + 1 WHERE `rank_order` >= ?');
			$db->execute([$order]);
		}
		$db->query('INSERT INTO `staff_ranks` (`rank_order`) VALUES (?)');
		$db->execute([$order]);
		$new = $db->insert_id();
		foreach($_POST as $what => $value) {
			$db->query('UPDATE `staff_ranks` SET `?` = ? WHERE `rank_id` = ?');
			$db->execute([$what, $value, $new]);
		}
		$db->endTrans();
		$logs->staff('Created a new staff rank: '.$mtg->format($_POST['rank_name']));
		$mtg->success('You\'ve created a new staff rank: '.$mtg->format($_POST['rank_name']));
	}
}
function editRank($db, $mtg, $logs) {
	global $fields;
	$_GET['step'] = isset($_GET['step']) && ctype_digit($_GET['step']) ? $_GET['step'] : null;
	switch($_GET['step']) {
		default:
			if(empty($_GET['ID']))
				$mtg->error('You didn\'t specify a valid rank');
			$db->query('SELECT `rank_name`, `rank_desc`, `rank_colour`, `rank_order`, `override_all` FROM `staff_ranks` WHERE `rank_id` = ?');
			$db->execute([$_GET['ID']]);
			if(!$db->num_rows())
				$mtg->error("That rank doesn't exist");
			$row = $db->fetch_row(true);
			?><form action="staff/?pull=ranks&amp;action=edit&amp;ID=<?php echo $_GET['ID'];?>&amp;step=1" method="post">
				<table class="table" width="100%">
					<tr>
						<th width="60%">Name</th>
						<td width="40%"><input type="text" name="rank_name" value="<?php echo $mtg->format($row['rank_name']);?>" /></td>
					</tr>
					<tr>
						<th>Colour</th>
						<td><input type="text" name="rank_colour" maxlength="6" value="<?php echo $row['rank_colour'];?>" /></td>
					</tr>
					<tr>
						<th>Description</th>
						<td><input type="text" name="rank_desc" value="<?php echo $row['rank_desc'];?>" /></td>
					</tr>
					<tr>
						<th>Order</th>
						<td><input type="text" name="rank_order" value="<?php echo $row['rank_order'];?>" /></td>
					</tr><?php
					foreach($fields as $col) {
						$name = ucwords(str_replace('_', ' ', $col));
						$db->query('SELECT `'.$col.'` FROM `staff_ranks` WHERE `rank_id` = ?');
						$db->execute([$_GET['ID']]);
						$single = $db->fetch_single();
						if($row['override_all'] == 'Yes')
							$single = 'Yes';
						?><tr>
							<th><?php echo str_replace(' Ip ', ' IP ', $name);?></th>
							<td><select name="<?php echo $col;?>" style="width:50%;">
								<option value="No" class="red"<?php echo $single == 'No' ? ' selected="selected"' : '';?>>Disabled</option>
								<option value="Yes" class="green"<?php echo $single == 'Yes' ? ' selected="selected"' : '';?>>Enabled</option>
							</select></td>
						</tr><?php
					}
					?><tr>
						<td colspan="2" class="center"><input type="submit" name="submit" value="Edit Rank" /></td>
					</tr>
				</table>
			</form><?php
			break;
		case 1:
			unset($_POST['submit']);
			$_POST['rank_name'] = isset($_POST['rank_name']) ? $db->escape($_POST['rank_name']) : null;
			if(empty($_POST['rank_name']))
				$mtg->error('You didn\'t select a valid name');
			$_POST['rank_colour'] = isset($_POST['rank_colour']) && ctype_alnum($_POST['rank_colour']) ? trim($_POST['rank_colour']) : null;
			$db->query('SELECT `rank_id` FROM `staff_ranks` WHERE `rank_name` = ? AND `rank_id` <> ?');
			$db->execute([$_POST['rank_name'], $_GET['ID']]);
			if($db->num_rows())
				$mtg->error('A rank with that name already exists');
			$db->startTrans();
			$db->query('REPLACE INTO `staff_ranks` (`rank_id`) VALUES (?)');
			$db->execute([$_GET['ID']]);
			$new = $db->insert_id();
			foreach($_POST as $what => $value) {
				$db->query('UPDATE `staff_ranks` SET `'.$what.'` = ? WHERE `rank_id` = ?');
				$db->execute([$value, $new]);
			}
			$db->endTrans();
			$logs->staff('Edited staff rank: '.$mtg->format($_POST['rank_name']));
			$mtg->success('You\'ve edited the staff rank: '.$mtg->format($_POST['rank_name']));
			listRanks($db, $mtg);
	}
}
function deleteRank($db, $mtg, $logs) {
	if(empty($_GET['ID']))
		$mtg->error("You didn't specify a valid rank");
	$db->query('SELECT `rank_name` FROM `staff_ranks` WHERE `rank_id` = ?');
	$db->execute([$_GET['ID']]);
	if(!$db->num_rows())
		$mtg->error('That rank doesn\'t exist');
	$rank = $mtg->format($db->fetch_single());
	if($_GET['ID'] == 1)
		$mtg->error('You can\'t delete that rank');
	if(!isset($_GET['ans'])) {
		?>Are you sure you want to delete <?php echo $rank;?>?<br />
		<a href="staff/?pull=ranks&amp;action=del&amp;ID=<?php echo $_GET['ID'];?>&amp;ans=yes">Yes</a><?php
	} else {
		$db->startTrans();
		$db->query('DELETE FROM `staff_ranks` WHERE `rank_id` = ?');
		$db->execute([$_GET['ID']]);
		$db->query('DELETE FROM `staff_ranks_rules` WHERE `rank` = ?');
		$db->execute([$_GET['ID']]);
		$db->query('UPDATE `users` SET `staff_rank` = 0 WHERE `staff_rank` = ?');
		$db->execute([$_GET['ID']]);
		$db->endTrans();
		$logs->staff('Deleted staff rank: '.$rank);
		$mtg->success('You\'ve deleted the staff rank: '.$rank);
	}
	listRanks($db, $mtg);
}
function viewRank($db, $mtg) {
	global $fields;
	if(empty($_GET['ID']))
		$mtg->error('You didn\'t specify a valid rank');
	$db->query('SELECT `rank_name`, `rank_colour`, `rank_desc`, `rank_order`, `override_all` FROM `staff_ranks` WHERE `rank_id` = ?');
	$db->execute([$_GET['ID']]);
	if(!$db->num_rows())
		$mtg->error('That rank doesn\'t exist');
	$row = $db->fetch_row(true);
	?><table class="table" width="100%">
		<tr>
			<th colspan="2"><?php echo $mtg->format($row['rank_name']);?> - Basics [<a href="staff/?pull=ranks&amp;action=edit&amp;ID=<?php echo $_GET['ID'];?>">Edit</a>]</th>
		</tr>
		<tr>
			<th width="60%">Colour</th>
			<td width="40" class="center" style="color:#<?php echo $row['rank_colour'];?>;">#<?php echo $row['rank_colour'];?></td>
		</tr>
		<tr>
			<th>Description</th>
			<td class="center"><?php echo $mtg->format($row['rank_desc']);?></td>
		</tr>
		<tr>
			<th>Order</th>
			<td class="center"><?php echo $mtg->format($row['rank_order']);?></td>
		</tr>
		<tr>
			<th colspan="2">Permissions</th>
		</tr><?php
		foreach($fields as $what) {
			$selectSingle = $db->query('SELECT `'.$what.'` FROM `staff_ranks` WHERE `rank_id` = ?');
			$db->execute([$_GET['ID']]);
			$single = $db->fetch_single();
			$enabled = $single == 'Yes' ? 'accept' : 'delete';
			$titleAlt = $single == 'Yes' ? 'Enabled' : 'Disabled';
			if($row['override_all'] == 'Yes'){
				$enabled = 'accept';
				$titleAlt = 'Enabled';
			}
			?><tr>
				<th><?php echo ucwords(str_replace('_', ' ', $what));?></th>
				<td class="center"><img src="images/silk/<?php echo $enabled;?>.png" title="<?php echo $titleAlt;?>" alt="<?php echo $titleAlt;?>" /></td>
			</tr><?php
		}
	?></table><?php
}
function setStaffRank($db, $mtg, $logs) {
	if(!isset($_POST['submit'])) {
		?><form action="staff/?pull=ranks&amp;action=set" method="post">
			<table class="table" width="100%">
				<tr>
					<th width="25%">Player</th>
					<td width="75%"><?php echo $users->listSelect('user1');?></td>
				</tr>
				<tr>
					<th><u>OR</u> Player ID</th>
					<td><input type="number" name="user2" /></td>
				</tr>
				<tr>
					<th>Rank</th>
					<td><select name="rank">
						<option value="0">None</option><?php
						$db->query('SELECT `rank_id`, `rank_name`, `rank_desc` FROM `staff_ranks` WHERE `rank_id` <> 1 ORDER BY `rank_order` ASC');
						$db->execute();
						if(!$db->num_rows())
							echo '<option value="0">No ranks available</option>';
						else {
							$rows = $db->fetch_row();
							foreach($rows as $row)
								printf('<option value="%u">%s - %s</option>', $row['rank_id'], $mtg->format($row['rank_name']), $mtg->format($row['rank_desc']));
						}
					?></select></td>
				</tr>
				<tr>
					<td colspan="2" class="center"><input type="submit" name="submit" value="Change Rank" /></td>
				</tr>
			</table>
		</form><?php
	} else {
		$_POST['user1'] = isset($_POST['user1']) && ctype_digit($_POST['user1']) ? $_POST['user1'] : null;
		$_POST['user2'] = isset($_POST['user2']) && ctype_digit($_POST['user2']) ? $_POST['user2'] : null;
		if(empty($_POST['user1']) && empty($_POST['user2']))
			$mtg->error("You didn't select a valid player");
		if(!empty($_POST['user1']) && !empty($_POST['user2']))
			$mtg->error("Select one option only");
		$_POST['user'] = empty($_POST['user2']) ? $_POST['user1'] : $_POST['user2'];
		if(in_array($_POST['user'], [1, 2]))
			$mtg->error("Owner ranks can't be changed");
		$db->query('SELECT `id` FROM `users` WHERE `userid` = ?');
		$db->execute([$_POST['user']]);
		if(!$db->num_rows())
			$mtg->error('That player doesn\'t exist');
		$_POST['rank'] = isset($_POST['rank']) && ctype_digit($_POST['rank']) ? $_POST['rank'] : 0;
		$rank = 'none';
		if($_POST['rank']) {
			$db->query('SELECT `rank_name` FROM `staff_ranks` WHERE `rank_id` = ?');
			$db->execute([$_POST['rank']]);
			if(!$db->num_rows())
				$mtg->error('That rank doesn\'t exist');
			$rank = $mtg->format($db->fetch_single());
		}
		$db->query('UPDATE `users` SET `staff_rank` = ? WHERE `id` = ?');
		$db->execute([$_POST['rank'], $_POST['user']]);
		$logs->staff('Set '.$mtg->username($_POST['user']).'\'s staff rank to '.$rank);
		$mtg->success('You\'ve set '.$mtg->username($_POST['user']).'\'s staff rank to '.$rank);
		if(isset($_POST['fromStaff']))
			manageStaff($db, $mtg, $logs);
	}
}
function manageStaff($db, $mtg, $logs) {
	$_GET['destaff'] = isset($_GET['destaff']) && ctype_digit($_GET['destaff']) ? $_GET['destaff']  : null;
	if(!empty($_GET['destaff'])) {
		$db->query('SELECT `staff_rank` FROM `users` WHERE `id` = ?');
		$db->execute([$_GET['destaff']]);
		if(!$db->num_rows())
			$mtg->error("That player doesn't exist");
		$target = $mtg->username($_GET['destaff']);
		if(!$db->fetch_single())
			$mtg->error($target.' isn\'t a member of staff');
		if($db->fetch_single() == 1)
			$mtg->error($target." can't be re-ranked");
		$db->query('UPDATE `users` SET `staff_rank` = 0 WHERE `id` = ?');
		$db->execute([$_GET['destaff']]);
		$logs->staff('Destaffed '.$target);
		$mtg->success('You\'ve destaffed '.$target);
	}
	$db->query('SELECT `rank_id`, `rank_name`, `rank_colour` FROM `staff_ranks` WHERE `rank_id` > 1 ORDER BY `rank_order` ASC');
	$db->execute();
	$rows = $db->fetch_row();
	foreach($rows as $row) {
		?><strong style="color:#<?php echo $row['rank_colour'];?>;font-size:1.1em;"><?php echo $mtg->format($row['rank_name']);?></strong><br />
		<table width="75%" cellspacing="1" class="table">
			<tr>
				<th>Member</th>
				<th>Level</th>
				<th>Last Seen</th>
				<th>Status</th>
				<th>Re-rank</th>
			</tr><?php
		$db->query('SELECT `id`, `level`, `laston`, `staff_rank` FROM `users` WHERE `staff_rank` = ? ORDER BY `userid` ASC');
		$db->execute([$row['rank_id']]);
		if(!$db->num_rows())
			echo '<tr><td colspan="5" class="center">There are no '.$mtg->format($row['rank_name']).'s</td></tr>';
		else {
			$members = $db->fetch_row();
			foreach($members as $user) {
				?><tr>
		 			<td><?php echo $mtg->username($user['userid'], true);?></td>
		 			<td><?php echo $mtg->format($user['level']);?></td>
		 			<td><?php echo date('F j, Y, g:i:s a', $user['laston']).'<br /><span class="small">('.$mtg->time_format(time() - $user['laston']).' ago)</span>';?></td>
		 			<td><?php echo $mtg->useronline($user['userid']);?></td>
		 			<td><form action="staff/?pull=ranks&amp;action=set" method="post">
			 			<input type="hidden" name="fromStaff" value="1" />
			 			<input type="hidden" name="user1" value="<?php echo $user['userid'];?>" />
		 				<select name="rank">
			 				<option value="0">None</option><?php
			 				$db->query('SELECT `rank_id`, `rank_name` FROM `staff_ranks` WHERE `rank_id` NOT IN(1, ?) ORDER BY `rank_order` ASC');
			 				$db->execute([$user['staff_rank']]);
			 				$ranks = $db->fetch_row();
			 				foreach($ranks as $rank)
			 					printf('<option value="%u">%s</option>', $rank['rank_id'], $mtg->format($rank['rank_name']));
			 			?></select>
			 			<input type="submit" name="submit" value="Re-rank" />
			 		</form></td>

		 		</tr><?php
			}
			?></table><br /><?php
		}
	}
}