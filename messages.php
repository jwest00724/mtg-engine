<?php
define('HEADER_TEXT', 'Messaging');
require_once __DIR__ . '/includes/globals.php';
if(!$site->checkEnabled('messaging'))
	$mtg->error("The messaging ability has been disabled");
$users->checkBan('messages');
require_once __DIR__ . '/includes/class/jbbcode/Parser.php';
$parser = new JBBCode\Parser();
$parser->addCodeDefinitionSet(new JBBCode\DefaultCodeDefinitionSet());
require_once __DIR__ . '/includes/class/class_mtg_paginate.php';
$pages = new Paginator();
$_GET['ID'] = isset($_GET['ID']) && ctype_digit($_GET['ID']) ? $_GET['ID'] : null;
$_GET['action'] = isset($_GET['action']) && ctype_alpha($_GET['action']) ? strtolower(trim($_GET['action'])) : null;
$read = [
	1 => '<span class="small green">Read</span>',
	0 => '<span class="small red">Unread</span>'
];
?><table width="100%" class="pure-table">
	<tr>
		<th width="33%"><a href="messages.php">Inbox</a></th>
		<th width="34%"><a href="messages.php?action=write">Write</a></th>
		<th width="33%"><a href="messages.php?action=archive">Archive</a></th>
	</tr>
</table><?php
switch($_GET['action']) {
	case 'write':
		if(!array_key_exists('submit', $_POST)) {
			?><h3 class="content-subhead">Message - Compose</h3>
			<form action="messages.php?action=write" method="post" class="pure-form pure-form-aligned">
				<div class="pure-control-group">
					<label for="recipients">Enter player IDs, separated by a comma</label>
					<input type="text" class="pure-input-1-2" name="user2" value="<?php echo isset($_GET['player']) && ctype_digit($_GET['player']) ? $_GET['player'] : null;?>" placeholder="Example: 1,2,3" />
				</div>
				<div class="pure-control-group">
					<label for="subject">Subject</label>
					<input type="text" class="pure-input-1-2" name="subject" placeholder="Example: Hi there!" />
				</div>
				<div class="pure-control-group">
					<label for="message">Message</label>
					<textarea rows="7" cols="50" name="message" class="pure-input-1-2"><?php echo isset($_GET['msg']) ? urldecode($_GET['msg']) : null;?></textarea>
				</div>
				<div class="pure-controls">
					<button type="submit" name="submit" value="true" class="pure-button pure-button-primary"><i class="fa fa-envelope"></i> Send Message</button>
					<button type="reset" class="pure-button pure-button-secondary"><i class="fa fa-recycle"></i> Reset</button>
				</div>
			</form><?php
			if($users->exists($_GET['ID'])) {
				?><table width="100%" class="pure-table">
					<tr>
						<th colspan="2">The 5 most recent messages between you and <?php echo $users->name($_GET['ID']);?></th>
					</tr><?php
				$db->query('SELECT `time_sent`, `message`, `sender` FROM `users_messages` WHERE (`sender` = ? AND `receiver` = ?) OR (`receiver` = ? AND `sender` = ?) ORDER BY `time_sent` DESC LIMIT 5');
				$db->execute([$my['id'], $_GET['ID'], $_GET['ID'], $my['id']]);
				if(!$db->num_rows())
					echo '<tr><td colspan="2" class="center">You haven\'t spoken to '.$users->name($_GET['ID']).' yet</td></tr>';
				else {
					$rows = $db->fetch_row();
					foreach($rows as $r) {
						$parser->parse($mtg->format($r['message'], true));
						?><tr>
							<td width="25%" valign="top">
								<strong><?php echo $_GET['ID'] == $r['sender'] ? $users->name($_GET['ID']) : 'You';?> wrote:</strong><br />
								<span class="small"><?php echo date('F j, Y, g:i:s a', strtotime($r['time_sent']));?></span>
							</td>
							<td valign="top"><?php echo str_replace('[username]', $users->name($my['id']), $parser->getAsHTML());?></td>
						</tr><?php
					}
				}
				?></table><?php
			}
		} else {
			$subj = strip_tags($_POST['subject']);
			if(strlen($subj) > 50)
				$mtg->error('Subjects are limited to 50 characters');
			$msg  = strip_tags($_POST['message']);
			if(empty($msg))
				$mtg->error('You must enter a message');
			if(strlen($msg) > 65536)
				$mtg->error('Messages are limited to 65,536 characters');
			$_POST['user1'] = isset($_POST['user1']) && ctype_digit($_POST['user1']) ? $_POST['user1'] : null;
			$_POST['user2'] = isset($_POST['user2']) && is_string($_POST['user2']) ? str_replace(' ', '', trim($_POST['user2'])) : null;
			if(empty($_POST['user1']) && empty($_POST['user2']))
				$mtg->error('You must select at least 1 option as a recipient');
			if(!empty($_POST['user1']) && !empty($_POST['user2']))
				$mtg->error('You must select only 1 option as a recipient');
			$sendto  = empty($_POST['user1']) ? $_POST['user2'] : $_POST['user1'];
			$sentTo  = '';
			$unique = array_unique(array_values(array_filter(explode(',', $sendto))));
			if(count(array_keys($unique)) > 10)
				$mtg->error('You can\'t send the same message to more than 10 people at once');
			$ids     = [];
			$db->query("SELECT `id` FROM `users` ORDER BY `id` ASC");
			$db->execute();
			$rows = $db->fetch_row();
			foreach($rows as $row)
				$ids[] .= implode(',', $row);
			$uni   = array_intersect($ids, $unique);
			$count = count(array_keys($uni));
			if(!$count)
				$mtg->error('No players were found');
			$msg = $count > 1 ? $msg . "\r\n\r\nMessage sent to: " . messageUsernames($uni) : $msg;
			$db->startTrans();
			foreach($uni as $to) {
				$sentTo .= $users->name($to) . ', ';
				$users->send_message($to, $my['id'], $subj, $msg);
			}
			$db->endTrans();
			$mtg->success('Your message has been sent to ' . substr($sentTo, 0, -2));
		}
		break;
	case 'read':
		?><h3 class="content-subhead">Message - Read Message</h3><?php
		if(empty($_GET['ID']))
			$mtg->error('You didn\'t select a valid message');
		$db->query("SELECT `receiver`, `sender` FROM `users_messages` WHERE `id` = ?");
		$db->execute([$_GET['ID']]);
		if(!$db->num_rows())
			$mtg->error('That message doesn\'t exist');
		$msg = $db->fetch_row(true);
		if($msg['receiver'] != $my['id'])
			$mtg->error('That\'s not your message to read');
		$db->query('UPDATE `users_messages` SET `read` = 1 WHERE `receiver` = ? AND `sender` = ?');
		$db->execute([$my['id'], $msg['sender']]);
		$db->query('SELECT * FROM `users_messages` WHERE (`receiver` = ? AND `sender` = ?) OR (`receiver` = ? AND `sender` = ?) ORDER BY `time_sent` DESC LIMIT 20');
		$db->execute([$my['id'], $msg['sender'], $msg['sender'], $my['id']]);
		?><table width="100%" class="pure-table pure-table-striped">
			<tr>
				<th width="30%">Details</th>
				<th width="70%">Message</th>
			</tr><?php
		if(!$db->num_rows())
			echo '<tr><td colspan="2" class="center">No conversation was found between '.$users->name($msg['sender']).' and yourself</td></tr>';
		else {
			$rows = $db->fetch_row();
			foreach($rows as $row) {
				$parser->parse($mtg->format($row['message'], true));
				?><tr>
					<td>
						<strong>Sender:</strong> <?php echo $users->name($row['sender'], true, true);?><br />
						<strong>Subject:</strong> <?php echo $mtg->format($row['subject']);?><br />
						<strong>Sent:</strong> <?php echo date('F j, Y, g:i:sa', strtotime($row['time_sent']));?><br />
						<strong>Status:</strong> <?php echo $read[$row['read']];?>
					</td>
					<td><?php echo str_replace(['[username]', '{username}'], $users->name($my['id']), $parser->getAsHTML());?></td>
				</tr><?php
			}
		}
		?></table><?php
		break;
	case 'delete':
		?><h3 class="content-subhead">Message - Delete Message</h3><?php
		if(empty($_GET['ID']))
			$mtg->error('Invalid ID.');
		$db->query('SELECT `receiver` FROM `users_messages` WHERE `id` = ?');
		$db->execute([$_GET['ID']]);
		if(!$db->num_rows())
			$mtg->error('That message doesn\'t exist');
		if($db->fetch_single() != $my['id'])
	 		$mtg->error('That message isn\'t addressed to you');
		$db->query('UPDATE `users_messages` SET `deleted` = 1 WHERE `id` = ?');
		$db->execute([$_GET['ID']]);
		$mtg->success('Your message has been sent to your archive');
		break;
	case 'archive':
		?><h3 class="content-subhead">Message - Archived Messages</h3><?php
		$db->query('SELECT COUNT(`id`) FROM `users_messages` WHERE `deleted` = 1 AND `receiver` = ?');
		$db->execute([$my['id']]);
		$cnt = $db->fetch_single();
		if(!$cnt)
			$mtg->error('You have no archived messages');
		$pages->items_total = $cnt;
		$pages->paginate();
		$db->query('SELECT `id`, `sender`, `time_sent`, `subject`, `message` FROM `users_messages` WHERE `deleted` = 1 AND `receiver` = ? ORDER BY `id` DESC '.$pages->limit);
		$db->execute([$my['id']]);
		?><h4>Your archive</h4>
		<p class="paginate"><?php echo $pages->display_pages();?></p><br />
		<table class="pure-table pure-table-striped" width="100%">
			<tr>
				<th width="25%">Details</th>
				<th width="75%">Message</th>
			</tr><?php
			if(!$db->num_rows())
				echo '<tr><td colspan="2" class="center">You have no archived messages</td></tr>';
			else {
				$rows = $db->fetch_row();
				foreach($rows as $row) {
					$parser->parse($mtg->format($row['message'], true));
					?><tr>
						<td>
							<strong>From:</strong> <?php echo $users->name($row['sender'], true);?><br />
							<strong>Sent:</strong> <?php echo date('H:i:s d/m/Y', strtotime($row['time_sent']));?><br />
							<strong>Subject:</strong> <?php echo $mtg->format($row['subject']);?><br /><br />
							<a href="messages.php?action=restore&amp;ID=<?php echo $row['id'];?>">Move to Inbox</a>
						</td>
						<td><?php echo $parser->getAsHTML();?></td>
					</tr><?php
				}
			}
		?></table><br />
		<p class="paginate"><?php echo $pages->display_pages();?></p><?php
		break;
	case 'restore':
		?><h3 class="content-subhead">Message - Restore Message</h3><?php
		if(empty($_GET['ID']))
			$mtg->error('You didn\'t select a valid message');
		$db->query('SELECT `receiver`, `deleted` FROM `users_messages` WHERE `id` = ?');
		$db->execute([$_GET['ID']]);
		if(!$db->num_rows())
			$mtg->error('That message doesn\'t exist');
		$row = $db->fetch_row(true);
		if($row['receiver'] != $my['id'])
			$mtg->error('That message isn\'t yours');
		if(!$row['deleted'])
			$mtg->error('That message isn\'t marked as deleted');
		$db->query('UPDATE `users_messages` SET `deleted` = 0 WHERE `id` = ?');
		$db->execute([$_GET['ID']]);
		$mtg->success('The message has been moved back to your Inbox');
		break;
	default:
		?><h3 class="content-subhead">Messages</h3><?php
		?><table width="100%" class="pure-table pure-table-striped">
			<tr>
				<th width="20%">Conversation</th>
				<th width="60%">Most Recent Message Received</th>
				<th width="20%">Actions</th>
			</tr><?php
		$db->query('SELECT * FROM ( ' .
			'SELECT `id`, `sender`, `time_sent`, `message`, `read` FROM `users_messages` WHERE `receiver` = ? AND `deleted` = 0 ORDER BY `time_sent` DESC LIMIT 20) AS `conf` ' .
			'GROUP BY `sender` ORDER BY `id` DESC');
		$db->execute([$my['id']]);
		if(!$db->num_rows())
			echo '<tr><td colspan="3" class="center">You have no messages</td></tr>';
		else {
			$rows = $db->fetch_row();
			foreach($rows as $row) {
				$parser->parse($mtg->format($row['message'], true));
				?><tr>
					<td><?php echo $users->name($row['sender'], true);?></td>
					<td><?php echo str_replace(['[username]', '{username}'], $users->name($my['id']), $parser->getAsHTML()),'<br /><br /><span class="small">',date('F j, Y, g:i:s a', strtotime($row['time_sent'])),'</span> - '.$read[$row['read']];?></td>
					<td>
						<a href="messages.php?action=read&amp;ID=<?php echo $row['id'];?>">Read</a> &middot;
						<a href="messages.php?action=write&amp;player=<?php echo $row['sender'];?>">Respond</a> &middot;
						<a href="messages.php?action=delete&amp;ID=<?php echo $row['id'];?>">Archive</a>
					</td>
				</tr><?php
			}
		}
		?></table><?php
		break;
}

function messageUsernames(array $array = null) {
	global $users;
	$ret = '';
	if(!count($array))
		return null;
	foreach($array as $id)
		$ret .= $users->name($id, false, true).', ';
	return substr($ret, 0, -2);
}