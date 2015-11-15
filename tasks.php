<?php
define('HEADER_TEXT', 'Tasks');
require_once __DIR__ . '/includes/globals.php';
if(array_key_exists('id', $_GET)) {
	$_GET['id'] = isset($_GET['id']) && ctype_digit($_GET['id']) ? $_GET['id'] : null;
	if(!empty($_GET['id'])) {
		$_GET['code'] = isset($_GET['code']) && ctype_alnum($_GET['code']) ? $_GET['code'] : null;
		if(!empty($_GET['code'])) {
			if($_GET['code'] == $_SESSION['code']) {
				$_SESSION['code'] = md5(microtime(true));
				$db->query('SELECT * FROM `tasks` WHERE `id` = ?');
				$db->execute([$_GET['id']]);
				if($db->num_rows()) {
					$task = $db->fetch_row(true);
					if($task['upgraded_only'] && time() > strtotime($my['upgraded']))
						$mtg->error('That task can be completed only by those whom have an upgraded account.');
					if($task['nerve'] > $my['nerve'])
						$mtg->error('You don\'t have enough nerve to complete this task');
					if($task['courses_required']) {
						$required = explode(',', trim($task['courses_required']));
						if(count($required)) {
							foreach($required as $req) {
								$db->query('SELECT `id` FROM `users_courses_complete` WHERE `course` = ? AND `user` = ?');
								$db->execute([$req, $my['id']]);
								if(!$db->num_rows())
									$mtg->error('You haven\'t completed all the courses required for this task');
							}
						}
					}
					$db->query('SELECT `enabled` FROM `tasks_groups` WHERE `id` = ?');
					$db->execute([$task['group_id']]);
					if($db->fetch_single() == 1) {
						require_once __DIR__ . '/includes/class/jbbcode/Parser.php';
						$parser = new jBBCode\Parser();
						$parser->addCodeDefinitionSet(new JBBCode\DefaultCodeDefinitionSet());
						$find = ['[TOTAL_STATS]', '[STRENGTH]', '[AGILITY]', '[GUARD]', '[LABOUR]', '[IQ]', '[MONEY]', '[POINTS]', '[POWER]', '[ENERGY]', '[NERVE]', '[LIFE]', '[EXP]', '[EXP_GIVEN]', '[MONEY_GIVEN]', '[POINTS_GIVEN]', '[HOSPITAL_TIME]', '[JAIL_TIME]'];
						if($task['awarded_money_min'] && $task['awarded_money_max'])
							$task['money'] = mt_rand($task['awarded_money_min'], $task['awarded_money_max']);
						else if($task['awarded_money_min'] && !$task['awarded_money_max'])
							$task['money'] = $task['awarded_money_min'];
						else
							$task['money'] = 0;
						if($task['awarded_points_min'] && $task['awarded_points_max'])
							$task['points'] = mt_rand($task['awarded_points_min'], $task['awarded_points_max']);
						else if($task['awarded_points_min'] && !$task['awarded_points_max'])
							$task['points'] = $task['awarded_points_min'];
						else
							$task['points'] = 0;
						if($task['awarded_xp_min'] && $task['awarded_xp_max'])
							$task['xp_awarded'] = mt_rand($task['awarded_xp_min'], $task['awarded_xp_max']);
						else if($task['awarded_xp_min'] && !$task['awarded_xp_max'])
							$task['xp_awarded'] = $task['awarded_points_min'];
						else
							$task['xp_awarded'] = 0;
						$repl = [$my['total_stats'], $my['strength'], $my['agility'], $my['guard'], $my['labour'], $my['iq'], $my['money'], $my['points'], $my['power'], $my['energy'], $my['nerve'], $my['health'], $my['exp'], $task['xp_awarded'], $set['main_currency_symbol'].$mtg->format($task['money']), $mtg->format($task['points']).' point'.$mtg->s($task['points']), $mtg->time_format($task['time_hospital'] * 60), $mtg->time_format($task['time_jail'] * 60)];
						$strs = ['text_start', 'text_success', 'text_failure', 'text_jail', 'text_reason_jail', 'text_hospital', 'text_reason_jail'];
						foreach($strs as $str)
							if(array_key_exists($str, $task))
								$task[$str] = str_replace($find, $repl, $task[$str]);
						$parser->parse(nl2br($mtg->format($task['text_start'])));
						echo '<p>',$parser->getAsHTML(),'</p>';
						$process = '$formula = '.str_replace($find, $repl, $task['formula']).';';
						eval($process);
						$formula += mt_rand(0, 50);
						if($formula >=0 && $formula <= 10 && ($task['time_jail'] || $task['time_hospital'])) {
							if($task['time_jail'] && $task['time_hospital']) {
								$rand = mt_rand(0, 1);
								$which = $rand == 1 ? 'hospital' : 'jail';
							} else
								$which = $task['jail_time'] ? 'jail' : 'hospital';
							$db->startTrans();
							$db->query('UPDATE `users` SET `'.$which.'` = ?, `'.$which.'_reason` = ? WHERE id = ?');
							$db->execute([$task['time_'.$which], $task['text_reason_'.$which]]);
							$col = $which == 'jail' ? 'jailed' : 'hospitalised';
							$db->query('UPDATE `users_stats` SET `nerve` = `nerve` = ?, `tasks_'.$col.'` = `tasks_'.$col.'` + 1 WHERE `id` = ?');
							$db->execute([$task['nerve'], $my['id']]);
							$db->endTrans();
							$parser->parse(nl2br($mtg->format($task['text_'.$which])));
							echo '<p class="green">',$parser->getAsHTML(),'</p>';
						} else if($formula >= 11 && $formula <= 40 || (!$task['time_jail'] && !$task['time_hospital'])) {
							$db->query('UPDATE `users_stats` SET `nerve` = `nerve` - ?, `tasks_failed` = `tasks_failed` + 1 WHERE `id` = ?');
							$db->execute([$task['nerve'], $my['id']]);
							$parser->parse(nl2br($mtg->format($task['text_failure'])));
							echo '<p class="orange">',$parser->getAsHTML(),'</p>';
						} else {
							$db->startTrans();
							if($task['money'] > 0) {
								$db->query('UPDATE `users_finances` SET `money` = `money` + ? WHERE `id` = ?');
								$db->execute([$task['money'], $my['id']]);
							}
							if($task['points'] > 0) {
								$db->query('UPDATE `users_finances` SET `points` = `points` + ? WHERE `id` = ?');
								$db->execute([$task['points'], $my['id']]);
							}
							if($task['xp_awarded'] > 0) {
								$db->query('UPDATE `users` SET `exp` = `exp` + ? WHERE `id` = ?');
								$db->execute([$task['xp_awarded'], $my['id']]);
							}
							$db->query('UPDATE `users_stats` SET `nerve` = `nerve` - ?, `tasks_complete` = `tasks_complete` + 1 WHERE `id` = ?');
							$db->execute([$task['nerve'], $my['id']]);
							$db->endTrans();
							$parser->parse(nl2br($mtg->format($task['text_success'])));
							echo '<p class="green">',$parser->getAsHTML(),'</p>';
						}
					} else
						$mtg->error('That task isn\'t enabled', false);
				} else
					$mtg->error('That task doesn\'t exist', false);
			} else
				$mtg->error('Invalid code', false);
		} else
			$mtg->error('You didn\'t specify a code', false);
	} else
		$mtg->error('You didn\'t select a valid task', false);
}
$db->query('SELECT `id`, `name` FROM `tasks_groups` WHERE `enabled` = 1 ORDER BY `ordering` ASC');
$db->execute();
if(!$db->num_rows())
	$mtg->info('There are currently no tasks available', true);
$groups = $db->fetch_row();
?><table class="pure-table" width="100%"><?php
foreach($groups as $group) {
	?><tr>
		<thead><th colspan="3" class="center"><?php echo $mtg->format($group['name']);?></th></thead>
	</tr><?php
	$extra = !$my['upgraded'] ? ' AND `upgraded_only` = 0' : '';
	$db->query('SELECT `id`, `name`, `nerve`, `courses_required`, `upgraded_only` FROM `tasks` WHERE `group_id` = ?'.$extra.' ORDER BY `nerve` ASC');
	$db->execute([$group['id']]);
	if(!$db->num_rows())
		echo '<tr><td colspan="3" class="center">There are no '.$mtg->format($group['name']).' tasks available</td></tr>';
	else {
		$tasks = $db->fetch_row();
		$n = 0;
		$_SESSION['code'] = md5(microtime(true));
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
				<td<?php echo !$n ? ' width="33%"' : '';?>><?php echo $upgrade.$mtg->format($task['name']).$coursesInfo;?></td>
				<td<?php echo !$n ? ' width="34%"' : '';?>><?php echo $mtg->format($task['nerve']);?></td>
				<td<?php echo !$n ? ' width="33%"' : '';?>><a href="tasks.php?id=<?php echo $task['id'];?>&amp;code=<?php echo $_SESSION['code'];?>">Attempt</a></td>
			</tr><?php
		}
	}
}
?></table>