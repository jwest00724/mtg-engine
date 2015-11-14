<?php
if(!defined('MTG_ENABLE'))
	exit;
if(!$users->hasAccess('staff_panel_tasks_manage'))
	$mtg->error('You don\'t have access');
$_GET['ID'] = array_key_exists('ID', $_GET) && ctype_digit($_GET['ID']) ? $_GET['ID'] : null;
$_GET['action'] = array_key_exists('action', $_GET) && ctype_alpha($_GET['action']) ? strtolower(trim($_GET['action'])) : null;
switch($_GET['action']) {
	case 'add':
		if(!$users->hasAccess('staff_panel_tasks_add'))
			$mtg->error('You don\'t have access');
		addTask($db, $mtg, $items, $logs);
		break;
	case 'edit':
		if(!$users->hasAccess('staff_panel_tasks_edit'))
			$mtg->error('You don\'t have access');
		editTask($db, $mtg, $logs);
		break;
	case 'del':
		if(!$users->hasAccess('staff_panel_tasks_delete'))
			$mtg->error('You don\'t have access');
		deleteTask($db, $mtg, $logs);
		break;
	case 'groups':
		if(!$users->hasAccess('staff_panel_tasks_groups_manage'))
			$mtg->error('You don\'t have access');
		manageTaskGroups($db, $mtg);
		break;
	case 'gadd':
		if(!$users->hasAccess('staff_panel_tasks_groups_add'))
			$mtg->error('You don\'t have access');
		addTaskGroup($db, $mtg, $logs);
		break;
	case 'gedit':
		if(!$users->hasAccess('staff_panel_tasks_groups_edit'))
			$mtg->error('You don\'t have access');
		editTaskGroup($db, $mtg, $logs);
		break;
	case 'gdel':
		if(!$users->hasAccess('staff_panel_tasks_groups_del'))
			$mtg->error('You don\'t have access');
		deleteTaskGroup($db, $mtg, $logs);
		break;
	default:
		manageTasks($db, $mtg);
		break;
}
function manageTasks($db, $mtg) {
	?><h3 class="content-subhead">Tasks: Management</h3>
	<h4><a href="staff/?pull=tasks&amp;action=add">Add Task</a></h4><?php
	$db->query('SELECT `tasks`.`id`, `tasks`.`name` AS `taskname`, `nerve`, `courses_required`, `upgraded_only`, `tasks_groups`.`name` AS `groupname`, `tasks_groups`.`enabled`
		FROM `tasks`
		LEFT JOIN `tasks_groups` ON `tasks`.`group_id` = `tasks_groups`.`id`
		ORDER BY `tasks`.`id` ASC');
	$db->execute();
	?><table width="100%" class="pure-table">
		<thead>
			<tr>
				<th width="20%">Task</th>
				<th width="20%">Nerve</th>
				<th width="20%">Group</th>
				<th width="20%">Courses</th>
				<th width="20%">Actions</th>
			</tr>
		</thead><?php
		if(!$db->num_rows())
			echo '<tr><td colspan="5" class="center">There are no tasks</td></tr>';
		else {
			$tasks = $db->fetch_row();
			foreach($tasks as $task) {
				$coursesInfo = '';
				if($task['courses_required']) {
					$db->query('SELECT `id`, `name` FROM `courses` WHERE `enabled` = 1 AND `id` IN('.$task['courses_required'].')');
					$db->execute();
					$courses = $db->fetch_row();
					foreach($courses as $course) {
						$db->query('SELECT `id` FROM `courses_complete` WHERE `course` = ? AND `user` = ?');
						$db->execute([$course['id'], $my['id']]);
						$coursesInfo .= '<br /><span class="small '.($db->num_rows() ? 'green' : 'red').'">'.$mtg->format($course['name']).'</span>';
					}
				}
				$upgrade = $task['upgraded_only'] ? '<img src="images/silk/joystick_add.png" title="Upgraded only!" alt="[Upgraded only]" /> ' : '';
				?><tr>
					<td><?php echo $upgrade.$mtg->format($task['taskname']);?></td>
					<td><?php echo $mtg->format($task['nerve']);?></td>
					<td><?php echo $task['groupname'] ? $mtg->format($task['groupname']) : 'None';?></td>
					<td><?php echo $coursesInfo ? $coursesInfo : 'None';?></td>
					<td>
						<a href="staff/?pull=tasks&amp;action=edit&amp;ID=<?php echo $row['id'];?>">Edit</a> &middot;
						<a href="staff/?pull=tasks&amp;action=del&amp;ID=<?php echo $row['id'];?>">Delete</a>
					</td>
				</tr><?php
			}
		}
	?></table><?php
}
function addTask($db, $mtg, $items) {
	?><h3 class="content-subhead">Tasks: Add</h3><?php
	if(array_key_exists('submit', $_POST)) {

	}
	$db->query('SELECT `id`, `name` FROM `tasks_groups` ORDER BY `name` ASC');
	$db->execute();
	if(!$db->num_rows())
		$mtg->error('You don\'t have any task groups - you\'ll need to create one first');
	$groupRows = $db->fetch_row();
	?><form action="staff/?pull=ranks&amp;action=add" method="post" class="pure-form pure-form-aligned">
		<div class="pure-control-group">
			<label for="name">Name</label>
			<input type="text" name="name" class="pure-u-1-3" required />
		</div>
		<div class="pure-control-group">
			<label for="group">Group</label>
			<select name="group"><?php
			foreach($groupRows as $row)
				printf('<option value="%u">%s</option>', $row['id'], $mtg->format($row['name']));
			?></select>
		</div>
		<div class="pure-control-group">
			<label for="nerve">Nerve</label>
			<input type="text" name="nerve" class="pure-u-1-3" required />
		</div>
		<div class="pure-control-group">
			<label for="formula">Difficulty formula<span class="red">*</span></label>
			<input type="text" name="formula" class="pure-u-1-3" required />
		</div>
		<div class="pure-control-group">
			<label for="reward-money">Reward: Cash</label>
			<input type="text" name="money" class="pure-u-1-3" placeholder="0" />
		</div>
		<div class="pure-control-group">
			<label for="reward-points">Reward: Points</label>
			<input type="text" name="points" class="pure-u-1-3" placeholder="0" />
		</div>
		<div class="pure-control-group">
			<label for="reward-points">Reward: Item</label>
			<?php echo $items->listAll('item', null, null, 'pure-u-1-3');?>
		</div>
	</form><?php
}
function manageTaskGroups($db, $mtg) {
	?><h3 class="content-subhead">Tasks: Group Management</h3>
	<h4><a href="staff/?pull=tasks&amp;action=gadd">Add Group</a></h4><?php
	$db->query('SELECT * FROM `tasks_groups` ORDER BY FIELD(`enabled`, 1, 0) ASC, `ordering` ASC');
	$db->execute();
	?><table width="100%" class="pure-table">
		<thead>
			<tr>
				<th width="50%">Group</th>
				<th width="10%">Ordering</th>
				<th width="40%">Actions</th>
			</tr>
		</thead><?php
	if(!$db->num_rows())
		echo '<tr><td colspan="3" class="center">There are no task groups</td></tr>';
	else {
		$rows = $db->fetch_row();
		foreach($rows as $row) {
			?><tr>
				<td><?php echo $mtg->format($row['name']);?></td>
				<td><?php echo $mtg->format($row['ordering']);?></td>
				<td>
					<a href="staff/?pull=tasks&amp;action=gedit&amp;ID=<?php echo $row['id'];?>">Edit</a> &middot;
					<a href="staff/?pull=tasks&amp;action=gdel&amp;ID=<?php echo $row['id'];?>">Delete</a>
				</td>
			</tr><?php
		}
	}
	?></table><?php
}
function addTaskGroup($db, $mtg, $logs) {
	?><h3 class="content-subhead">Tasks: Add Group</h3><?php
	$db->query('SELECT MAX(`ordering`) FROM `tasks_groups`');
	$db->execute();
	$order = $db->num_rows() ? $db->fetch_single() + 1 : 1;
	if(array_key_exists('submit', $_POST)) {
		$_POST['name'] = isset($_POST['name']) && is_string($_POST['name']) ? trim($_POST['name']) : null;
		if(empty($_POST['name']))
			$mtg->error('You didn\'t enter a valid group name');
		$_POST['enabled'] = isset($_POST['enabled']) && ctype_digit($_POST['enabled']) ? 1 : 0;
		$_POST['order'] = isset($_POST['order']) && ctype_digit($_POST['order']) ? $_POST['order'] : $order;
		$db->query('SELECT `id` FROM `tasks_groups` WHERE `name` = ?');
		$db->execute([$_POST['name']]);
		if($db->num_rows())
			$mtg->error('Another task group with that name already exists');
		$db->startTrans();
		$db->query('UPDATE `tasks_groups` SET `ordering` = `ordering` + 1 WHERE `ordering` >= ?');
		$db->execute([$_POST['order']]);
		$db->query('INSERT INTO `tasks_groups` (`name`, `ordering`, `enabled`) VALUES (?, ?, ?)');
		$db->execute([$_POST['name'], $_POST['order'], $_POST['enabled']]);
		$db->endTrans();
		$order += 1;
		$logs->staff('Created task group: '.$_POST['name']);
		$mtg->success('Task group &ldquo;'.$mtg->format($_POST['name']).'&rdquo; has been created');
	}
	?><form action="staff/?pull=tasks&amp;action=gadd" method="post" class="pure-form pure-form-aligned">
		<div class="pure-control-group">
			<label for="name">Name</label>
			<input type="text" name="name" class="pure-u-1-3" required />
		</div>
		<div class="pure-control-group">
			<label for="enabled">Group enabled<span class="red">*</span></label>
			<input type="checkbox" name="enabled" value="1" />
		</div>
		<div class="pure-control-group">
			<label for="order">Order to display<span class="blue">*</span></label>
			<input type="text" name="order" placeholder="<?php echo $order;?>" class="pure-u-1-3" />
		</div>
		<div class="pure-controls">
			<button type="submit" name="submit" class="pure-button pure-button-primary">Add Task Group</button>
			<button type="reset" class="pure-button pure-button-secondary"><i class="fa fa-recycle"></i> Reset</button>
		</div>
	</form>
	<div class="small">
		<span class="red">*</span>This will effect all tasks assigned to this group. Disabling it will prevent it (and all tasks associated) from being displayed<br />
		<span class="blue">*</span>This is for the order in which the groups will be displayed. Leave blank if you want the game to assign an order instead<br />
	</div><?php
}
function editTaskGroup($db, $mtg, $logs) {
	?><h3 class="content-subhead">Tasks: Edit Group</h3><?php
	if(empty($_GET['ID']))
		$mtg->error('You didn\'t specify a valid group');
	$db->query('SELECT * FROM `tasks_groups` WHERE `id` = ?');
	$db->execute([$_GET['ID']]);
	if(!$db->num_rows())
		$mtg->error('That task group doesn\'t exist');
	$row = $db->fetch_row(true);
	if(array_key_exists('submit', $_POST)) {
		$_POST['name'] = isset($_POST['name']) && is_string($_POST['name']) ? trim($_POST['name']) : null;
		if(empty($_POST['name']))
			$mtg->error('You didn\'t enter a valid group name');
		$_POST['enabled'] = isset($_POST['enabled']) && ctype_digit($_POST['enabled']) ? 1 : 0;
		$_POST['order'] = isset($_POST['order']) && ctype_digit($_POST['order']) ? $_POST['order'] : $order;
		$db->query('SELECT `id` FROM `tasks_groups` WHERE `name` = ? AND `id` <> ?');
		$db->execute([$_POST['name'], $_GET['ID']]);
		if($db->num_rows())
			$mtg->error('Another task group with that name already exists');
		$db->query('UPDATE `tasks_groups` SET `name` = ?, `ordering` = ?, `enabled` = ? WHERE `id` = ?');
		$db->execute([$_POST['name'], $_POST['order'], $_POST['enabled'], $_GET['ID']]);
		$logs->staff('Edited task group: '.$_POST['name']);
		$mtg->success('Task group &ldquo;'.$mtg->format($_POST['name']).'&rdquo; has been edited');
		manageTaskGroups($db, $mtg);
	} else {
		?><form action="staff/?pull=tasks&amp;action=gedit&amp;ID=<?php echo $_GET['ID'];?>" method="post" class="pure-form pure-form-aligned">
			<div class="pure-control-group">
				<label for="name">Name</label>
				<input type="text" name="name" class="pure-u-1-3" value="<?php echo $mtg->format($row['name']);?>" required />
			</div>
			<div class="pure-control-group">
				<label for="enabled">Group enabled<span class="red">*</span></label>
				<input type="checkbox" name="enabled" value="1"<?php echo $row['enabled'] ? ' checked' : '';?> />
			</div>
			<div class="pure-control-group">
				<label for="order">Order to display<span class="blue">*</span></label>
				<input type="text" name="order" value="<?php echo $row['ordering'];?>" class="pure-u-1-3" />
			</div>
			<div class="pure-controls">
				<button type="submit" name="submit" class="pure-button pure-button-primary">Edit Task Group</button>
				<button type="reset" class="pure-button pure-button-secondary"><i class="fa fa-recycle"></i> Reset</button>
			</div>
		</form>
		<div class="small">
			<span class="red">*</span>This will effect all tasks assigned to this group. Disabling it will prevent it (and all tasks associated) from being displayed<br />
			<span class="blue">*</span>This is for the order in which the groups will be displayed. Leave blank if you want the game to assign an order instead<br />
		</div><?php
	}
}
function deleteTaskGroup($db, $mtg, $logs) {
	if(empty($_GET['ID']))
		$mtg->error('You didn\'t specify a valid group');
	$db->query('SELECT `name` FROM `tasks_groups` WHERE `id` = ?');
	$db->execute([$_GET['ID']]);
	if(!$db->num_rows())
		$mtg->error('That task group doesn\'t exist');
	$name = $mtg->format($db->fetch_single());
	if(!array_key_exists('submit', $_POST)) {
		?>Are you sure you wish to delete the task group &ldquo;<?php echo $name;?>&rdquo;?<br />
		<form action="staff/?pull=tasks&amp;action=gdel&amp;ID=<?php echo $_GET['ID'];?>" method="post" class="pure-form pure-form-aligned">
			<div class="pure-control-group">
				<label for="confirmation" class="pure-checkbox">
					<input type="checkbox" id="confirmation" name="confirmation" value="1" required />
					Yes, I am sure I want to delete <?php echo $name;?>
				</label>
			</div>
			<div class="pure-control-group">
				<label for="delete-tasks" class="pure-checkbox">
					<input type="checkbox" id="delete-tasks" name="delete_tasks" value="1" />
					I would also like to delete all tasks assigned to this group
				</label>
			</div>
			<div class="pure-controls">
				<button type="submit" name="submit" class="pure-button pure-button-important-confirmation">Delete Task Group</button>
				<button type="reset" class="pure-button pure-button-secondary"><i class="fa fa-recycle"></i> Reset</button>
			</div>
		</form><?php
	} else {
		$_POST['confirmation'] = isset($_POST['confirmation']) ? true : false;
		$_POST['delete_tasks'] = isset($_POST['delete_tasks']) ? true : false;
		if($_POST['confirmation'] !== true)
			$mtg->error('You didn\'t check the deletion confimation box');
		$extra = '';
		$db->startTrans();
		$db->query('DELETE FROM `tasks_groups` WHERE `id` = ?');
		$db->execute([$_GET['ID']]);
		if($_POST['delete_tasks'] === true) {
			$db->query('DELETE FROM `tasks` WHERE `group_id` = ?');
			$db->execute([$_GET['ID']]);
			$extra = ' and all tasks assigned';
		}
		$logs->staff('Deleted task group &ldquo;'.$name.'&rdquo;'.$extra);
		$mtg->success('You\'ve deleted the task group: '.$name);
		listTaskGroups($db, $mtg);
	}
}