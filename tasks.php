<?php
define('HEADER_TEXT', 'Tasks');
require_once __DIR__ . '/includes/globals.php';
if(array_key_exists('id', $_GET)) {
	$_GET['id'] = isset($_GET['id']) && ctype_digit($_GET['id']) ? $_GET['id'] : null;
	if(!empty($_GET['id'])) {
		$_GET['code'] = isset($_GET['code']) && ctype_alnum($_GET['code']) ? $_GET['code'] : null;
		if(!empty($_GET['code'])) {
			if($_GET['code'] == $_SESSION['code']) {
				$db->query("SELECT `name`, `difficulty`, `initial_text`, `success_text`, `failure_text`, `hospital_text`, `jail_text`, `courses_required` FROM `tasks` WHERE `id` = ?");
				$db->execute([$_GET['id']]);
				if($db->num_rows()) {
					$find = ['TOTAL_STATS', 'STRENGTH', 'AGILITY', 'GUARD', 'LABOUR', 'IQ', 'MONEY', 'POINTS', 'POWER', 'ENERGY', 'NERVE', 'LIFE', 'EXP'];
					$repl = [$my['total_stats'], $my['strength'], $my['agility'], $my['guard'], $my['labour'], $my['IQ'], $my['money'], $my['points'], $my['power'], $my['energy'], $my['nerve'], $my['life'], $my['exp']];
				}
			}
		}
	}
}
$db->query("SELECT `id`, `name` FROM `tasks_groups` WHERE `enabled` = 1 ORDER BY `ordering` ASC");
$db->execute();
if(!$db->num_rows())
	$mtg->info('There are currently no tasks available', true);
$groups = $db->fetch_row();
?><table class="pure-table" width="100%"><?php
foreach($groups as $group) {
	?><tr>
		<th colspan="3" class="center"><?php echo $mtg->format($group['name']);?></th>
	</tr><?php
	$db->query("SELECT `id`, `name`, `power`, `difficulty`, `courses_required` FROM `tasks` WHERE `groupID` = ? ORDER BY `power` ASC");
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
				$db->query('SELECT `id`, `name` FROM `courses` WHERE `id` IN('.$task['courses_required'].')');
				$db->execute();
				$courses = $db->fetch_row();
				foreach($courses as $course) {
					$coursesInfo .= '<br /><span class="small"';
					$db->query("SELECT `id` FROM `courses_complete` WHERE `course` = ? AND `user` = ?");
					$db->execute([$course['id'], $my['id']]);
					$coursesInfo .= ' style="color:'.($db->num_rows() ? '#00AA0A' : '#F00').';">'.$mtg->format($course['name']).'</span>';
				}
			?><tr>
				<td<?php echo !$n ? ' width="33%"' : '';?>><?php echo $mtg->format($task['name']).$coursesInfo;?></td>
				<td<?php echo !$n ? ' width="34%"' : '';?>><?php echo $mtg->format($task['power']);?></td>
				<td<?php echo !$n ? ' width="33%"' : '';?>><a href="tasks.php?id=<?php echo $task['id'];?>&amp;code=<?php echo $_SESSION['code'];?>">Attempt</a></td>
			</tr><?php
		}
	}
?></table>