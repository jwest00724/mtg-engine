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
		editTask($db, $mtg, $items, $logs);
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
		if(!$users->hasAccess('staff_panel_tasks_groups_delete'))
			$mtg->error('You don\'t have access');
		deleteTaskGroup($db, $mtg, $logs);
		break;
	default:
		manageTasks($db, $mtg);
		break;
}
function manageTasks($db, $mtg) {
	global $set, $items;
	?><h3 class="content-subhead">Tasks: Management</h3>
	<h4><a href="staff/?pull=tasks&amp;action=add">Add Task</a></h4><?php
	$db->query('SELECT `tasks`.`id`, `tasks`.`name` AS `taskname`, `nerve`, `courses_required`, `upgraded_only`, `awarded_money_min`, `awarded_money_max`, `awarded_points_min`, `awarded_points_max`, `awarded_item`, `awarded_xp_min`, `awarded_xp_max`, `tasks_groups`.`name` AS `groupname`, `tasks_groups`.`enabled`
		FROM `tasks`
		LEFT JOIN `tasks_groups` ON `tasks`.`group_id` = `tasks_groups`.`id`
		ORDER BY `tasks`.`id` ASC');
	$db->execute();
	?><table width="100%" class="pure-table">
		<thead>
			<tr>
				<th width="20%">Task</th>
				<th width="20%">Rewards</th>
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
				$rewards = '';
				if($task['awarded_xp_min'] && $task['awarded_xp_max'])
					$rewards .= '<strong>XP:</strong> Between '.$mtg->format($task['awarded_xp_min']).' and '.$mtg->format($task['awarded_xp_max']).'<br />';
				else if($task['awarded_xp_min'] && !$task['awarded_xp_max'])
					$rewards .= '<strong>XP:</strong> '.$mtg->format($task['awarded_xp_min']).'<br />';
				if($task['awarded_money_min'] && $task['awarded_money_max'])
					$rewards .= '<strong>Cash:</strong> Between '.$set['main_currency_symbol'].$mtg->format($task['awarded_money_min']).' and '.$set['main_currency_symbol'].$mtg->format($task['awarded_money_max']).'<br />';
				else if($task['awarded_money_min'] && !$task['awarded_money_max'])
					$rewards .= '<strong>Cash:</strong> '.$set['main_currency_symbol'].$mtg->format($task['awarded_money_min']).'<br />';
				if($task['awarded_points_min'] && $task['awarded_points_max'])
					$rewards .= '<strong>Points:</strong> Between '.$mtg->format($task['awarded_points_min']).' and '.$mtg->format($task['awarded_points_min']).'<br />';
				else if($task['awarded_points_min'] && !$task['awarded_points_max'])
					$rewards .= '<strong>Points:</strong> '.$mtg->format($task['awarded_points_min']).'<br />';
				if($task['awarded_item'])
					$rewards .= '<strong>Item:</strong> '.$items->name($task['awarded_item']).'<br />';
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
					<td><?php echo $upgrade.$mtg->format($task['taskname']).' ['.$task['nerve'].' nerve]';?></td>
					<td><?php echo $rewards ? $rewards : 'None';?></td>
					<td><?php echo $task['groupname'] ? $mtg->format($task['groupname']) : 'None';?></td>
					<td><?php echo $coursesInfo ? $coursesInfo : 'None';?></td>
					<td>
						<a href="staff/?pull=tasks&amp;action=edit&amp;ID=<?php echo $task['id'];?>">Edit</a> &middot;
						<a href="staff/?pull=tasks&amp;action=del&amp;ID=<?php echo $task['id'];?>">Delete</a>
					</td>
				</tr><?php
			}
		}
	?></table><?php
}
function addTask($db, $mtg, $items, $logs) {
	?><h3 class="content-subhead">Tasks: Creation</h3><?php
	$modifiers = ['[TOTAL_STATS]', '[STRENGTH]', '[AGILITY]', '[GUARD]', '[LABOUR]', '[IQ]', '[MONEY]', '[POINTS]', '[POWER]', '[ENERGY]', '[NERVE]', '[LIFE]', '[EXP]', '[EXP_GIVEN]', '[MONEY_GIVEN]', '[POINTS_GIVEN]', '[HOSPITAL_TIME]', '[JAIL_TIME]'];
	if(array_key_exists('submit', $_POST)) {
		$nums = ['nerve', 'group', 'xp_min', 'xp_max', 'money_min', 'money_max', 'points_min', 'points_max', 'item', 'item_qty', 'jail', 'hospital', 'group', 'upgraded'];
		foreach($nums as $what)
			$_POST[$what] = isset($_POST[$what]) && ctype_digit(str_replace(',', '', $_POST[$what])) ? str_replace(',', '', $_POST[$what]) : 0;
		if(!$_POST['group'])
			$mtg->error('You didn\'t select a valid group');
		$db->query('SELECT `name` FROM `tasks_groups` WHERE `id` = ?');
		$db->execute([$_POST['group']]);
		if(!$db->num_rows())
			$mtg->error('The task group you selected doesn\'t exist');
		$rewardsWithMinMax = ['money', 'points', 'xp'];
		foreach($rewardsWithMinMax as $what)
			if($_POST[$what.'_max'] && !$_POST[$what.'_min'])
				$mtg->error('You entered a maximum '.$what.' value, but not a minimum');
			else if($_POST[$what.'_max'] && $_POST[$what.'_min'] > $_POST[$what.'_max'])
				$mtg->error('You entered a higher minimum value for the '.$what.' reward');
		if($_POST['item'] && !$_POST['item_qty'])
			$mtg->error('You selected an item as a reward, but didn\'t enter a valid quantity');
		$db->query('SELECT `id` FROM `tasks` WHERE `name` = ?');
		$db->execute([$_POST['name']]);
		if($db->num_rows())
			$mtg->error('Another task with that name already exists');
		$db->query('INSERT INTO `tasks` (`name`, `nerve`, `formula`, `group_id`, `courses_required`, `text_start`, `text_success`, `text_failure`, `text_jail`, `text_hospital`, `time_jail`, `text_reason_jail`, `time_hospital`, `text_reason_hospital`, `upgraded_only`, `awarded_money_min`, `awarded_money_max`, `awarded_points_min`, `awarded_points_max`, `awarded_xp_min`, `awarded_xp_max`, `awarded_item`, `awarded_item_qty`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
		$db->execute([$_POST['name'], $_POST['nerve'], $_POST['formula'], $_POST['group'], $_POST['courses'], $_POST['start'], $_POST['success'], $_POST['failure'], $_POST['jailed'], $_POST['hospitalised'], $_POST['jail'], $_POST['reason_jail'], $_POST['hospital'], $_POST['reason_hospital'], $_POST['upgraded'], $_POST['money_min'], $_POST['money_max'], $_POST['points_min'], $_POST['points_max'], $_POST['xp_min'], $_POST['xp_max'], $_POST['item'], $_POST['item_qty']]);
		$logs->staff('Created task: '.$mtg->format($_POST['name']));
		$mtg->success('&ldquo;'.$mtg->format($_POST['name']).'&rdquo; has been created');
	}
	$db->query('SELECT `id`, `name` FROM `tasks_groups` ORDER BY FIELD(`enabled`, 1, 0) ASC, `ordering` ASC');
	$db->execute();
	if(!$db->num_rows())
		$mtg->error('You don\'t have any task groups - you\'ll need to create one first');
	$groupRows = $db->fetch_row();
	?><form action="staff/?pull=tasks&amp;action=add" method="post" class="pure-form pure-form-aligned">
		<legend>Basic task information</legend>
		<div class="pure-control-group">
			<label for="name">Name</label>
			<input type="text" name="name" class="pure-u-1-3" required />
		</div>
		<div class="pure-control-group">
			<label for="group">Group</label>
			<select name="group" class="pure-u-1-3"><?php
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
			<label for="courses">Courses required<span class="purple">*</span></label>
			<input type="text" name="courses" class="pure-u-1-3" placeholder="None" />
		</div>
		<div class="pure-control-group">
			<label for="upgraded" class="pure-checkbox">Upgraded account required</label>
			<input type="checkbox" name="upgraded" value="1" />
		</div>
		<legend>Task rewards</legend>
		<div class="pure-control-group">
			<label for="xp_min">Reward: EXP<span class="blue">*</span><span class="green">*</span></label>
			<input type="text" name="xp_min" class="pure-u-1-5" placeholder="0" /> to <input type="text" name="xp_max" class="pure-u-1-5" placeholder="0" />
		</div>
		<div class="pure-control-group">
			<label for="money_min">Reward: Cash<span class="blue">*</span><span class="green">*</span></label>
			<input type="text" name="money_min" class="pure-u-1-5" placeholder="0" /> to <input type="text" name="money_max" class="pure-u-1-5" placeholder="0" />
		</div>
		<div class="pure-control-group">
			<label for="points_min">Reward: Points<span class="blue">*</span><span class="green">*</span></label>
			<input type="text" name="points_min" class="pure-u-1-5" placeholder="0" /> to <input type="text" name="points_max" class="pure-u-1-5" placeholder="0" />
		</div>
		<div class="pure-control-group">
			<label for="item">Reward: Item<span class="blue">*</span><span class="brown">*</span></label>
			<?php echo $items->listAll('item', null, null, 'pure-u-1-5');?> x <input type="text" name="item_qty" class="pure-u-1-5" placeholder="0" />
		</div>
		<legend>Task punishments</legend>
		<div class="pure-control-group">
			<label for="jail-time">Jail Time (in minutes)<span class="blue">*</span></label>
			<input type="text" name="jail" class="pure-u-1-3" placeholder="0" />
		</div>
		<div class="pure-control-group">
			<label for="jail-reason">Jail Reason<span class="blue">*</span></label>
			<input type="text" name="reason_jail" class="pure-u-1-3" />
		</div>
		<div class="pure-control-group">
			<label for="hospital-time">Hospital Time (in minutes)<span class="blue">*</span></label>
			<input type="text" name="hospital" class="pure-u-1-3" placeholder="0" />
		</div>
		<div class="pure-control-group">
			<label for="hospital-reason">Hospital Reason<span class="blue">*</span></label>
			<input type="text" name="reason_hospital" class="pure-u-1-3" />
		</div>
		<legend>Task texts</legend>
		<div class="pure-control-group">
			<label for="text-start">Start</label>
			<textarea id="text-start" name="start" class="pure-u-1-3"></textarea>
		</div>
		<div class="pure-control-group">
			<label for="text-success">Success</label>
			<textarea id="text-success" name="success" class="pure-u-1-3"></textarea>
		</div>
		<div class="pure-control-group">
			<label for="text-failure">Failure</label>
			<textarea id="text-failure" name="failure" class="pure-u-1-3"></textarea>
		</div>
		<div class="pure-control-group">
			<label for="text-jailed">Jailed</label>
			<textarea id="text-jailed" name="jailed" class="pure-u-1-3"></textarea>
		</div>
		<div class="pure-control-group">
			<label for="text-hospitalised">Hospitalised</label>
			<textarea id="text-hospitalised" name="hospitalised" class="pure-u-1-3"></textarea>
		</div>
		<div class="pure-controls">
			<button type="submit" name="submit" class="pure-button pure-button-primary"><i class="fa fa-plus"></i> Create Task</button>
			<button type="reset" class="pure-button pure-button-secondary"><i class="fa fa-recycle"></i> Reset</button>
		</div>
	</form>
	<div class="small">
		<p><span class="red">*</span>This can be complicated. Process it as you would a maths sum. The closer to 100 your formula equals, the easier the task will be to complete. Your modifiers must be in capitals and surrounded by square brackets - the currently available modifiers are:<br /><?php echo implode(', ', $modifiers);?></p>
		<p><span class="blue">*</span>You can leave these blank</p>
		<p><span class="green">*</span>You can leave these blank. If you set their respective minimum values and wish to set these too, you must set a value <em>higher</em> than that of it's respective minimum value</p>
		<p><span class="brown">*</span>If you select an item, you must enter a quantity above 0</p>
		<p><span class="purple">*</span>You can leave this blank. This is a comma-separated list of course IDs (example: 1,4,5,8).</p>
	</div><?php
}
function editTask($db, $mtg, $items, $logs) {
	?><h3 class="content-subhead">Tasks: Modification</h3><?php
	if(empty($_GET['ID']))
		$mtg->error('You didn\'t specify a valid task');
	$db->query('SELECT * FROM `tasks` WHERE `id` = ?');
	$db->execute([$_GET['ID']]);
	if(!$db->num_rows())
		$mtg->error('That task doesn\'t exist');
	$row = $db->fetch_row(true);
	$modifiers = ['[TOTAL_STATS]', '[STRENGTH]', '[AGILITY]', '[GUARD]', '[LABOUR]', '[IQ]', '[MONEY]', '[POINTS]', '[POWER]', '[ENERGY]', '[NERVE]', '[LIFE]', '[EXP]', '[EXP_GIVEN]', '[MONEY_GIVEN]', '[POINTS_GIVEN]', '[HOSPITAL_TIME]', '[JAIL_TIME]'];
	if(array_key_exists('submit', $_POST)) {
		$nums = ['nerve', 'group', 'xp_min', 'xp_max', 'money_min', 'money_max', 'points_min', 'points_max', 'item', 'item_qty', 'jail', 'hospital', 'group', 'upgraded'];
		foreach($nums as $what)
			$_POST[$what] = isset($_POST[$what]) && ctype_digit(str_replace(',', '', $_POST[$what])) ? str_replace(',', '', $_POST[$what]) : 0;
		if(!$_POST['group'])
			$mtg->error('You didn\'t select a valid group');
		$db->query('SELECT `name` FROM `tasks_groups` WHERE `id` = ?');
		$db->execute([$_POST['group']]);
		if(!$db->num_rows())
			$mtg->error('The task group you selected doesn\'t exist');
		$rewardsWithMinMax = ['money', 'points', 'xp'];
		foreach($rewardsWithMinMax as $what)
			if($_POST[$what.'_max'] && !$_POST[$what.'_min'])
				$mtg->error('You entered a maximum '.$what.' value, but not a minimum');
			else if($_POST[$what.'_max'] && $_POST[$what.'_min'] > $_POST[$what.'_max'])
				$mtg->error('You entered a higher minimum value for the '.$what.' reward');
		if($_POST['item'] && !$_POST['item_qty'])
			$mtg->error('You selected an item as a reward, but didn\'t enter a valid quantity');
		$db->query('SELECT `id` FROM `tasks` WHERE `name` = ? AND `id` <> ?');
		$db->execute([$_POST['name'], $_GET['ID']]);
		if($db->num_rows())
			$mtg->error('Another task with that name already exists');
		$db->query('REPLACE INTO `tasks` (`id`, `name`, `nerve`, `formula`, `group_id`, `courses_required`, `text_start`, `text_success`, `text_failure`, `text_jail`, `text_hospital`, `time_jail`, `text_reason_jail`, `time_hospital`, `text_reason_hospital`, `upgraded_only`, `awarded_money_min`, `awarded_money_max`, `awarded_points_min`, `awarded_points_max`, `awarded_xp_min`, `awarded_xp_max`, `awarded_item`, `awarded_item_qty`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
		$db->execute([$_GET['ID'], $_POST['name'], $_POST['nerve'], $_POST['formula'], $_POST['group'], $_POST['courses'], $_POST['start'], $_POST['success'], $_POST['failure'], $_POST['jailed'], $_POST['hospitalised'], $_POST['jail'], $_POST['reason_jail'], $_POST['hospital'], $_POST['reason_hospital'], $_POST['upgraded'], $_POST['money_min'], $_POST['money_max'], $_POST['points_min'], $_POST['points_max'], $_POST['xp_min'], $_POST['xp_max'], $_POST['item'], $_POST['item_qty']]);
		$logs->staff('Edited task: '.$mtg->format($_POST['name']));
		$mtg->success('&ldquo;'.$mtg->format($_POST['name']).'&rdquo; has been edited');
		manageTasks($db, $mtg);
	} else {
		$db->query('SELECT `id`, `name` FROM `tasks_groups` ORDER BY FIELD(`enabled`, 1, 0) ASC, `ordering` ASC');
		$db->execute();
		if(!$db->num_rows())
			$mtg->error('You don\'t have any task groups - you\'ll need to create one first');
		$groupRows = $db->fetch_row();
		?><form action="staff/?pull=tasks&amp;action=edit&amp;ID=<?php echo $_GET['ID'];?>" method="post" class="pure-form pure-form-aligned">
			<legend>Basic task information</legend>
			<div class="pure-control-group">
				<label for="name">Name</label>
				<input type="text" name="name" class="pure-u-1-3" value="<?php echo $mtg->format($row['name']);?>" required />
			</div>
			<div class="pure-control-group">
				<label for="group">Group</label>
				<select name="group" class="pure-u-1-3"><?php
				foreach($groupRows as $group)
					printf('<option value="%u"%s>%s</option>', $group['id'], $row['group_id'] == $group['id'] ? ' selected' : '', $mtg->format($group['name']));
				?></select>
			</div>
			<div class="pure-control-group">
				<label for="nerve">Nerve</label>
				<input type="text" name="nerve" class="pure-u-1-3" value="<?php echo $mtg->format($row['nerve']);?>" required />
			</div>
			<div class="pure-control-group">
				<label for="formula">Difficulty formula<span class="red">*</span></label>
				<input type="text" name="formula" class="pure-u-1-3" value="<?php echo $mtg->format($row['formula']);?>" required />
			</div>
			<div class="pure-control-group">
				<label for="courses">Courses required<span class="purple">*</span></label>
				<input type="text" name="courses" class="pure-u-1-3" value="<?php echo $mtg->format($row['courses_required']);?>" placeholder="None" />
			</div>
			<div class="pure-control-group">
				<label for="upgraded" class="pure-checkbox">Upgraded account required</label>
				<input type="checkbox" name="upgraded" value="1"<?php echo $row['upgraded_only'] ? ' checked' : '';?> />
			</div>
			<legend>Task rewards</legend>
			<div class="pure-control-group">
				<label for="xp_min">Reward: EXP<span class="blue">*</span><span class="green">*</span></label>
				<input type="text" name="xp_min" class="pure-u-1-5" value="<?php echo $mtg->format($row['awarded_xp_min']);?>" placeholder="0" /> to <input type="text" name="xp_max" class="pure-u-1-5" value="<?php echo $mtg->format($row['awarded_xp_max']);?>" placeholder="0" />
			</div>
			<div class="pure-control-group">
				<label for="money_min">Reward: Cash<span class="blue">*</span><span class="green">*</span></label>
				<input type="text" name="money_min" class="pure-u-1-5" value="<?php echo $mtg->format($row['awarded_money_min']);?>" placeholder="0" /> to <input type="text" name="money_max" class="pure-u-1-5" value="<?php echo $mtg->format($row['awarded_money_max']);?>" placeholder="0" />
			</div>
			<div class="pure-control-group">
				<label for="points_min">Reward: Points<span class="blue">*</span><span class="green">*</span></label>
				<input type="text" name="points_min" class="pure-u-1-5" value="<?php echo $mtg->format($row['awarded_points_min']);?>" placeholder="0" /> to <input type="text" name="points_max" class="pure-u-1-5" value="<?php echo $mtg->format($row['awarded_points_max']);?>" placeholder="0" />
			</div>
			<div class="pure-control-group">
				<label for="item">Reward: Item<span class="blue">*</span><span class="brown">*</span></label>
				<?php echo $items->listAll('item', $row['awarded_item'], null, 'pure-u-1-5');?> x <input type="text" name="item_qty" class="pure-u-1-5" value="<?php echo $mtg->format($row['awarded_item_qty']);?>" placeholder="0" />
			</div>
			<legend>Task punishments</legend>
			<div class="pure-control-group">
				<label for="jail-time">Jail Time (in minutes)<span class="blue">*</span></label>
				<input type="text" name="jail" class="pure-u-1-3" value="<?php echo $mtg->format($row['time_jail']);?>" placeholder="0" />
			</div>
			<div class="pure-control-group">
				<label for="jail-reason">Jail Reason<span class="blue">*</span></label>
				<input type="text" name="reason_jail" class="pure-u-1-3" value="<?php echo $mtg->format($row['text_reason_jail']);?>" />
			</div>
			<div class="pure-control-group">
				<label for="hospital-time">Hospital Time (in minutes)<span class="blue">*</span></label>
				<input type="text" name="hospital" class="pure-u-1-3" value="<?php echo $mtg->format($row['time_hospital']);?>" placeholder="0" />
			</div>
			<div class="pure-control-group">
				<label for="hospital-reason">Hospital Reason<span class="blue">*</span></label>
				<input type="text" name="reason_hospital" class="pure-u-1-3" value="<?php echo $mtg->format($row['text_reason_hospital']);?>" />
			</div>
			<legend>Task texts</legend>
			<div class="pure-control-group">
				<label for="text-start">Start</label>
				<textarea id="text-start" name="start" class="pure-u-1-3"><?php echo $mtg->format($row['text_start']);?></textarea>
			</div>
			<div class="pure-control-group">
				<label for="text-success">Success</label>
				<textarea id="text-success" name="success" class="pure-u-1-3"><?php echo $mtg->format($row['text_success']);?></textarea>
			</div>
			<div class="pure-control-group">
				<label for="text-failure">Failure</label>
				<textarea id="text-failure" name="failure" class="pure-u-1-3"><?php echo $mtg->format($row['text_failure']);?></textarea>
			</div>
			<div class="pure-control-group">
				<label for="text-jailed">Jailed</label>
				<textarea id="text-jailed" name="jailed" class="pure-u-1-3"><?php echo $mtg->format($row['text_jail']);?></textarea>
			</div>
			<div class="pure-control-group">
				<label for="text-hospitalised">Hospitalised</label>
				<textarea id="text-hospitalised" name="hospitalised" class="pure-u-1-3"><?php echo $mtg->format($row['text_hospital']);?></textarea>
			</div>
			<div class="pure-controls">
				<button type="submit" name="submit" class="pure-button pure-button-primary"><i class="fa fa-cog"></i> Edit Task</button>
				<button type="reset" class="pure-button pure-button-secondary"><i class="fa fa-recycle"></i> Reset</button>
			</div>
		</form>
		<div class="small">
			<p><span class="red">*</span>This can be complicated. Process it as you would a maths sum. The closer to 100 your formula equals, the easier the task will be to complete. Your modifiers must be in capitals and surrounded by square brackets - the currently available modifiers are:<br /><?php echo implode(', ', $modifiers);?></p>
			<p><span class="blue">*</span>You can leave these blank</p>
			<p><span class="green">*</span>You can leave these blank. If you set their respective minimum values and wish to set these too, you must set a value <em>higher</em> than that of it's respective minimum value</p>
			<p><span class="brown">*</span>If you select an item, you must enter a quantity above 0</p>
			<p><span class="purple">*</span>You can leave this blank. This is a comma-separated list of course IDs (example: 1,4,5,8).</p>
		</div><?php
	}
}
function deleteTask($db, $mtg, $logs) {
	?><h3 class="content-subhead">Tasks: Deletion</h3><?php
	if(empty($_GET['ID']))
		$mtg->error('You didn\'t specify a valid task');
	$db->query('SELECT `name` FROM `tasks` WHERE `id`= ?');
	$db->execute([$_GET['ID']]);
	if(!$db->num_rows())
		$mtg->error('That task doesn\'t exist');
	$name = $mtg->format($db->fetch_single());
	if(!array_key_exists('submit', $_POST)) {
		?>Are you sure you wish to delete the &ldquo;<?php echo $name;?>?
		<form action="staff/?pull=tasks&amp;action=del&amp;ID=<?php echo $_GET['ID'];?>" method="post" class="pure-form">
			<button type="submit" name="submit" class="pure-button pure-button-important-confirmation">Yes, I'm sure</button>
		</form><?php
	} else {
		$db->query('DELETE FROM `tasks` WHERE `id` = ?');
		$db->execute([$_GET['ID']]);
		$logs->staff('Deleted task: '.$name);
		$mtg->success('You\'ve deleted the &ldquo;'.$name.'&rdquo;');
		manageTasks($db, $mtg);
	}
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
	?><h3 class="content-subhead">Tasks: Groups: Creation</h3><?php
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
			<button type="submit" name="submit" class="pure-button pure-button-primary"><i class="fa fa-plus"></i>Add Task Group</button>
			<button type="reset" class="pure-button pure-button-secondary"><i class="fa fa-recycle"></i> Reset</button>
		</div>
	</form>
	<div class="small">
		<span class="red">*</span>This will effect all tasks assigned to this group. Disabling it will prevent it (and all tasks associated) from being displayed<br />
		<span class="blue">*</span>This is for the order in which the groups will be displayed. Leave blank if you want the game to assign an order instead<br />
	</div><?php
}
function editTaskGroup($db, $mtg, $logs) {
	?><h3 class="content-subhead">Tasks: Groups: Modification</h3><?php
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
	?><h3 class="content-subhead">Tasks: Groups: Deletion</h3><?php
	if(empty($_GET['ID']))
		$mtg->error('You didn\'t specify a valid group');
	$db->query('SELECT `name` FROM `tasks_groups` WHERE `id` = ?');
	$db->execute([$_GET['ID']]);
	if(!$db->num_rows())
		$mtg->error('That task group doesn\'t exist');
	$name = $mtg->format($db->fetch_single());
	if(!array_key_exists('submit', $_POST)) {
		?>Are you sure you wish to delete the task group &ldquo;<?php echo $name;?>&rdquo;?<br />
		<form action="staff/?pull=tasks&amp;action=gdel&amp;ID=<?php echo $_GET['ID'];?>" method="post" class="pure-form pure-form-stacked">
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
				<button type="submit" name="submit" class="pure-button pure-button-important-confirmation"><i class="fa fa-trash"></i> Delete Task Group</button>
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
		$db->endTrans();
		$mtg->success('You\'ve deleted the task group: '.$name.$extra);
		manageTaskGroups($db, $mtg);
	}
}