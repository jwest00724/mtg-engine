<?php
define('HEADER_TEXT', 'Tasks');
require_once __DIR__ . '/includes/globals.php';
if(array_key_exists('id', $_GET)) {
	$_GET['id'] = isset($_GET['id']) && ctype_digit($_GET['id']) ? $_GET['id'] : null;
	if(!empty($_GET['id'])) {
		$_GET['code'] = isset($_GET['code']) && ctype_alnum($_GET['code']) ? $_GET['code'] : null;
		if(!empty($_GET['code'])) {
			if($_GET['code'] == $_SESSION['code']) {
				$db->query('SELECT `name`, `formula`, `text_start`, `text_success`, `text_failure`, `text_hospital`, `text_jail`, `time_hospital`, `time_jail`, `text_reason_hospital`, `text_reason_jail`, `courses_required`, `groupID` FROM `tasks` WHERE `id` = ?');
				$db->execute([$_GET['id']]);
				if($db->num_rows()) {
					$task = $db->fetch_row(true);
					$db->query('SELECT `enabled` FROM `tasks_groups` WHERE `id` = ?');
					$db->execute([$task['groupID']]);
					if($db->fetch_single() != 1) {
						require_once __DIR__ . '/includes/class/jbbcode/Parser.php';
						$parser = new jBBCode\Parser();
						$parser->addCodeDefinitionSet(new JBBCode\DefaultCodeDefinitionSet());
						$find = ['[TOTAL_STATS]', '[STRENGTH]', '[AGILITY]', '[GUARD]', '[LABOUR]', '[IQ]', '[MONEY]', '[POINTS]', '[POWER]', '[ENERGY]', '[NERVE]', '[LIFE]', '[EXP]', '[EXP_GIVEN]', '[MONEY_GIVEN]', '[POINTS_GIVEN]', '[HOSPITAL_TIME]', '[JAIL_TIME]'];
						$task['money'] = $task['money'] > 0 ? mt_rand($task['money'] / 2, $task['money'] * 2) : 0;
						$task['points'] = $task['points'] > 0 ? mt_rand($task['points'] / 2, $task['points'] * 2) : 0;
						$repl = [$my['total_stats'], $my['strength'], $my['agility'], $my['guard'], $my['labour'], $my['IQ'], $my['money'], $my['points'], $my['power'], $my['energy'], $my['nerve'], $my['life'], $my['exp'], $task['xp_awarded'], '$'.$mtg->format($task['money']), $mtg->format($task['points']).' point'.$mtg->s($task['points']), $mtg->time_format($task['time_hospital'] * 60), $mtg->time_format($task['time_jail'] * 60)];
						$strs = ['text_start', 'text_success', 'text_failure', 'text_jail', 'text_reason_jail', 'text_hospital', 'text_reason_jail'];
						foreach($strs as $str)
							$task[$str] = str_replace($find, $repl, $task[$str]);
						$parser->parse(nl2br($mtg->format($task['text_start'])));
						echo '<p>',$parser->getAsHTML(),'</p>';
						$process = '$formula='.str_replace($find, $repl, $task['formula']);
						@eval($process);
						$rand = mt_rand(1, 100);
						if($rand >=0 && $rand <= 10) {
							$db->startTrans();
							$db->query('UPDATE `users` SET `?` = ?, `?_reason` = ? WHERE id = ?');
							$db->execute([$which, $task['time_'.$which], $which, $task['text_reason_'.$which]]);
							$row = $which == 'jail' ? 'jailed' : 'hospitalised';
							$db->query('UPDATE `users_stats` SET `tasks_?` = `tasks_?` + 1 WHERE `id` = ?');
							$db->execute([$row, $row, $my['id']]);
							$db->endTrans();
							$which = $task['time_jail'] > 0 ? 'jail' : 'hospital';
							$parser->parse(nl2br($mtg->format($task['text_'.$which])));
							echo '<p class="green">',$parser->getAsHTML(),'</p>';
						} else if($rand >= 11 && $rand <= 40) {
							$db->query('UPDATE `users_stats` SET `tasks_failed` = `tasks_failed` + 1 WHERE `id` = ?');
							$db->execute([$row, $row, $my['id']]);
							$parser->parse(nl2br($mtg->format($task['text_failure'])));
							echo '<p class="orange">',$parser->getAsHTML(),'</p>';
						} else {
							$db->startTrans();
							if($task['money'] > 0) {
								$db->query('UPDATE `users_finances` SET `money` = `money` + ? WHERE `id` = ?');
								$db->execute([$task['money'], $my['id']]);
							}
							if($row['points'] > 0) {
								$db->query('UPDATE `users_finances` SET `points` = `points` + ? WHERE `id` = ?');
								$db->execute([$task['points'], $my['id']]);
							}
							if($row['xp_awarded'] > 0) {
								$db->query('UPDATE `users` SET `exp` = `exp` + ? WHERE `id` = ?');
								$db->execute([$task['xp_awarded'], $my['id']]);
							}
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
		<th colspan="3" class="center"><?php echo $mtg->format($group['name']);?></th>
	</tr><?php
	$db->query('SELECT `id`, `name`, `nerve`, `courses_required` FROM `tasks` WHERE `groupID` = ? ORDER BY `nerve` ASC');
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
					$coursesInfo .= '<br /><span class="small';
					$db->query('SELECT `id` FROM `courses_complete` WHERE `course` = ? AND `user` = ?');
					$db->execute([$course['id'], $my['id']]);
					$coursesInfo .= ' '.($db->num_rows() ? 'green' : 'red').'">'.$mtg->format($course['name']).'</span>';
				}
			}
			?><tr>
				<td<?php echo !$n ? ' width="33%"' : '';?>><?php echo $mtg->format($task['name']).$coursesInfo;?></td>
				<td<?php echo !$n ? ' width="34%"' : '';?>><?php echo $mtg->format($task['nerve']);?></td>
				<td<?php echo !$n ? ' width="33%"' : '';?>><a href="tasks.php?id=<?php echo $task['id'];?>&amp;code=<?php echo $_SESSION['code'];?>">Attempt</a></td>
			</tr><?php
		}
	}
}
?></table>