<?php
define('HEADER_TEXT', 'Forums');
require_once __DIR__ . '/includes/globals.php';
if(!$set['forums_enabled'])
	$mtg->error('The forum is currently closed');
$users->checkBan('forum');
$mtg->info('Under development');
$_GET['ID'] = array_key_exists('ID', $_GET) && ctype_digit($_GET['ID']) ? $_GET['ID'] : null;
$_GET['action'] = array_key_exists('action', $_GET) && ctype_alpha($_GET['action']) ? strtolower(trim($_GET['action'])) : null;
switch($_GET['action']) {
	case 'topic':
		topicView($db, $my, $mtg, $users);
		break;
	default:
		index($db, $my, $mtg, $users);
		break;
}
function index($db, $my, $mtg, $users) {
	$db->query('SELECT `id`, `name`, `description` FROM `forums` WHERE `publicity` = "all" ORDER BY `name` ASC');
	$db->execute();
	?><h3 class="content-subhead">Public Boards</h3>
	<p><table width="100%" class="pure-form pure-form-striped">
		<thead>
			<tr>
				<th width="70%">Board</th>
				<th width="30%">Info</th>
			</tr>
		</thead><?php
	if(!$db->num_rows())
		echo '<tr><td colspan="2" class="center">There are no public boards</td></tr>';
	else {
		$rows = $db->fetch_row();
		foreach($rows as $row) {
			$latest = 'None';
			if($row['latest_post']) {
				$db->query('SELECT `id`, `name`, `poster` FROM `forums_topics` WHERE `parent_board` = ? ORDER BY `time` DESC LIMIT 1');
				$db->execute([$row['id']]);
				$last = $db->fetch_row(true);
				$latest = '<a href="forum.php?action=topic&amp;ID='.$last['id'].'">'.$mtg->format($last['name']).'</a><br /><strong>By:</strong> '.$users->name($last['poster']);
			}
			?><tr>
				<td><a href="forum.php?action=board&amp;ID=<?php echo $row['id'];?>" class="medium"><?php echo $mtg->format($row['name']);?></a></td>
				<td><strong>Latest Post:</strong> <?php echo $latest;?></td>
			</tr><?php
		}
	}
	?></table></p><?php
	if(strtolower($my['upgraded']) >= time()) {
		$db->query('SELECT `id`, `name`, `description` FROM `forums` WHERE `publicity` = "upgraded" ORDER BY `name` ASC');
		$db->execute();
		?><h3 class="content-subhead">Upgraded Boards</h3>
		<p><table width="100%" class="pure-form pure-form-striped">
			<thead>
				<tr>
					<th width="70%">Board</th>
					<th width="30%">Info</th>
				</tr>
			</thead><?php
		if(!$db->num_rows())
			echo '<tr><td colspan="2" class="center">There are no upgraded boards</td></tr>';
		else {
			$rows = $db->fetch_row();
			foreach($rows as $row) {
				$latest = 'None';
				if($row['latest_post']) {
					$db->query('SELECT `id`, `name`, `poster` FROM `forums_topics` WHERE `parent_board` = ? ORDER BY `time` DESC LIMIT 1');
					$db->execute([$row['id']]);
					$last = $db->fetch_row(true);
					$latest = '<a href="forum.php?action=topic&amp;ID='.$last['id'].'">'.$mtg->format($last['name']).'</a><br /><strong>By:</strong> '.$users->name($last['poster']);
				}
				?><tr>
					<td><a href="forum.php?action=board&amp;ID=<?php echo $row['id'];?>" class="medium"><?php echo $mtg->format($row['name']);?></a></td>
					<td><strong>Latest Post:</strong> <?php echo $latest;?></td>
				</tr><?php
			}
		}
		?></table></p><?php
	}
	if($my['staff_rank']) {
		$db->query('SELECT `id`, `name`, `description` FROM `forums` WHERE `publicity` = "staff" ORDER BY `name` ASC');
		$db->execute();
		?><h3 class="content-subhead">Staff Boards</h3>
		<p><table width="100%" class="pure-form pure-form-striped">
			<thead>
				<tr>
					<th width="70%">Board</th>
					<th width="30%">Info</th>
				</tr>
			</thead><?php
		if(!$db->num_rows())
			echo '<tr><td colspan="2" class="center">There are no staff boards</td></tr>';
		else {
			$rows = $db->fetch_row();
			foreach($rows as $row) {
				$latest = 'None';
				if($row['latest_post']) {
					$db->query('SELECT `id`, `name`, `poster` FROM `forums_topics` WHERE `parent_board` = ? ORDER BY `time` DESC LIMIT 1');
					$db->execute([$row['id']]);
					$last = $db->fetch_row(true);
					$latest = '<a href="forum.php?action=topic&amp;ID='.$last['id'].'">'.$mtg->format($last['name']).'</a><br /><strong>By:</strong> '.$users->name($last['poster']);
				}
				?><tr>
					<td><a href="forum.php?action=board&amp;ID=<?php echo $row['id'];?>" class="medium"><?php echo $mtg->format($row['name']);?></a></td>
					<td><strong>Latest Post:</strong> <?php echo $latest;?></td>
				</tr><?php
			}
		}
		?></table></p><?php
	}
}