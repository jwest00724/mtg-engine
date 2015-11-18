<?php
define('HEADER_TEXT', 'Forums');
require_once __DIR__ . '/includes/globals.php';
if(!$set['forums_enabled'])
	$mtg->error('The forum is currently closed');
$users->checkBan('forum');
require_once __DIR__ . '/includes/class/class_mtg_paginate.php';
$pages = new Paginator();
require_once __DIR__ . '/includes/class/jbbcode/Parser.php';
$parser = new jBBCode\Parser();
$parser->addCodeDefinitionSet(new JBBCode\DefaultCodeDefinitionSet());
require_once __DIR__ . '/includes/securimage/securimage.php';
$securimage = new Securimage();
function formatLatestPost($id) {
	global $db, $mtg, $parser;
	$db->query('SELECT `content` FROM `forums_posts` WHERE `parent_topic` = ? ORDER BY `posted` DESC LIMIT 1');
	$db->execute([$id]);
	if(!$db->num_rows())
		return 'n/a';
	$str = $db->fetch_single();
	$parser->parse($mtg->format($str));
	$text = $parser->getAsText();
	return strlen($text) <= 25 ? $text : substr($text, 0, 22).'...';
}
$_GET['ID'] = array_key_exists('ID', $_GET) && ctype_digit($_GET['ID']) ? $_GET['ID'] : null;
$_GET['action'] = array_key_exists('action', $_GET) && ctype_alpha($_GET['action']) ? strtolower(trim($_GET['action'])) : null;
$_GET['sub'] = array_key_exists('sub', $_GET) && ctype_alpha($_GET['sub']) ? strtolower(trim($_GET['sub'])) : null;
switch($_GET['action']) {
	case 'board':
		boardView($db, $my, $mtg, $set, $users, $board = 0);
		break;
	case 'topic':
		switch($_GET['sub']) {
			default:
				topicView($db, $my, $mtg, $set, $users, $topic = 0);
				break;
			case 'new':
				topicNew($db, $my, $mtg, $set, $users, $securimage);
				break;
		}
		break;
	case 'post':
		respond($db, $my, $mtg, $set, $users, $securimage);
		break;
	case 'admin':
		switch($_GET['sub']) {
			case 'pin':
				if(!$users->hasAccess('forum_topic_pin'))
					$mtg->error('You don\'t have access');
				topicPin($db, $my, $mtg, $set, $users, $logs);
				break;
			case 'lock':
				if(!$users->hasAccess('forum_topic_lock'))
					$mtg->error('You don\'t have access');
				topicLock($db, $my, $mtg, $set, $users, $logs);
				break;
			case 'deletetopic':
				if(!$users->hasAccess('forum_topic_delete'))
					$mtg->error('You don\'t have access');
				topicDelete($db, $my, $mtg, $set, $users, $logs);
				break;
			case 'editpost':
				postEdit($db, $my, $mtg, $set, $users, $logs, $securimage);
				break;
			case 'deletepost':
				postDelete($db, $my, $mtg, $set, $users, $logs);
				break;
			default:
				$mtg->info('No sub action specified', true);
				break;
		}
		break;
	case 'sub':
		topicSubscribe($db, $my, $mtg, $set, $users);
		break;
	default:
		index($db, $my, $mtg, $set, $users);
		break;
}
function index($db, $my, $mtg, $set, $users) {
	$db->query('SELECT `id`, `name`, `description`, `latest_post_id`, `latest_topic_id`, `latest_post_user`, `latest_post_time` FROM `forums` WHERE `publicity` = "all" ORDER BY `name` ASC');
	$db->execute();
	?><h3 class="content-subhead">Public Boards</h3>
	<p><table width="100%" class="pure-table pure-table-striped">
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
			?><tr>
				<td><a href="forum.php?action=board&amp;ID=<?php echo $row['id'];?>" class="medium"><?php echo $mtg->format($row['name']);?></a><br /><span class="small"><strong>Description:</strong> <?php echo $mtg->format($row['description']);?></span></td>
				<td><strong>Latest Post:</strong> <?php echo $row['latest_post_id'] ? '<a href="forum.php?action=topic&amp;ID='.$row['latest_topic_id'].'#'.$row['latest_post_id'].'">'.$mtg->format($row['name']).'</a><br /><strong>By:</strong> '.$users->name($row['latest_post_user']).'<br /><strong>At:</strong> '.date('H:i:s d/m/Y', strtotime($row['latest_post_time'])) : 'None';?></td>
			</tr><?php
		}
	}
	?></table></p><?php
	if(strtotime($my['upgraded']) >= time()) {
		$db->query('SELECT `id`, `name`, `description`, `latest_post_id`, `latest_topic_id`, `latest_post_user`, `latest_post_time` FROM `forums` WHERE `publicity` = "upgraded" ORDER BY `name` ASC');
		$db->execute();
		?><h3 class="content-subhead">Upgraded Boards</h3>
		<p><table width="100%" class="pure-table pure-table-striped">
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
				?><tr>
					<td><a href="forum.php?action=board&amp;ID=<?php echo $row['id'];?>" class="medium"><?php echo $mtg->format($row['name']);?></a><br /><span class="small"><strong>Description:</strong> <?php echo $mtg->format($row['description']);?></span></td>
					<td><strong>Latest Post:</strong> <?php echo $row['latest_post_id'] ? '<a href="forum.php?action=topic&amp;ID='.$row['latest_topic_id'].'#'.$row['latest_post_id'].'">'.$mtg->format($row['name']).'</a><br /><strong>By:</strong> '.$users->name($row['latest_post_user']).'<br /><strong>At:</strong> '.date('H:i:s d/m/Y', strtotime($row['latest_post_time'])) : 'None';?></td>
				</tr><?php
			}
		}
		?></table></p><?php
	}
	if($my['staff_rank']) {
		$db->query('SELECT `id`, `name`, `description`, `latest_post_id`, `latest_topic_id`, `latest_post_user`, `latest_post_time` FROM `forums` WHERE `publicity` = "staff" ORDER BY `name` ASC');
		$db->execute();
		?><h3 class="content-subhead">Staff Boards</h3>
		<p><table width="100%" class="pure-table pure-table-striped">
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
				?><tr>
					<td><a href="forum.php?action=board&amp;ID=<?php echo $row['id'];?>" class="medium"><?php echo $mtg->format($row['name']);?></a><br /><span class="small"><strong>Description:</strong> <?php echo $mtg->format($row['description']);?></span></td>
					<td><strong>Latest Post:</strong> <?php echo $row['latest_post_id'] ? '<a href="forum.php?action=topic&amp;ID='.$row['latest_topic_id'].'#'.$row['latest_post_id'].'">'.$mtg->format($row['name']).'</a><br /><strong>By:</strong> '.$users->name($row['latest_post_user']).'<br /><strong>At:</strong> '.date('H:i:s d/m/Y', strtotime($row['latest_post_time'])) : 'None';?></td>
				</tr><?php
			}
		}
		?></table></p><?php
	}
}
function boardView($db, $my, $mtg, $set, $users, $board = 0) {
	global $pages;
	if($board)
		$_GET['ID'] = $board;
	if(empty($_GET['ID']))
		$mtg->error('You didn\'t specify a valid board');
	$db->query('SELECT `name`, `publicity` FROM `forums` WHERE `id` = ?');
	$db->execute([$_GET['ID']]);
	if(!$db->num_rows())
		$mtg->error('That board doesn\'t exist');
	$row = $db->fetch_row(true);
	if($row['publicity'] == 'staff' && !$my['staff_rank'])
		$mtg->error('This board is for staff only. You don\'t have access');
	if($row['publicity'] == 'upgraded' && time() > strtotime($my['upgraded']))
		$mtg->error('This board is for upgraded members only. You don\'t have access');
	$db->query('SELECT COUNT(`id`) FROM `forums_topics` WHERE `parent_board` = ?');
	$db->execute([$_GET['ID']]);
	$pages->items_total = $db->fetch_single();
	$pages->mid_range = 3;
	$pages->paginate();
	$db->query('SELECT `id`, `name`, `description`, `latest_post_id`, `latest_post_user`, `latest_post_time`, `pinned`, `locked` FROM `forums_topics` WHERE `parent_board` = ? ORDER BY `pinned` DESC, `latest_post_time` DESC '.$pages->limit);
	$db->execute([$_GET['ID']]);
	?><h3 class="content-subhead"><a href="forum.php">Index</a> &rarr; <?php echo $mtg->format($row['name']);?> &rarr; <a href="forum.php?action=topic&amp;sub=new&amp;ID=<?php echo $_GET['ID'];?>">New Topic</a></h3>
	<div class="paginate"><?php echo $pages->display_pages();?></div>
	<table width="100%" class="pure-table pure-table-striped">
		<thead>
			<tr>
				<th width="60%">Topic</th>
				<th width="40%">Information</th>
			</tr>
		</thead><?php
		if(!$db->num_rows())
			echo '<tr><td colspan="2" class="center">There are no topics</td></tr>';
		else {
			$topics = $db->fetch_row();
			foreach($topics as $topic) {
				?><tr>
					<td><?php
						echo $topic['pinned'] ? '<img src="images/silk/exclamation.png" title="Pinned" alt="Pinned" /> ' : '';
						echo $topic['locked'] ? '<img src="images/silk/lock.png" title="Locked" alt="Locked" /> ' : '';
						?><a href="forum.php?action=topic&amp;ID=<?php echo $topic['id'];?>"><?php echo $mtg->format($topic['name']);?></a><br />
						<span class="small"><strong>Description:</strong> <?php echo $mtg->format($topic['description']);?></span>
					</td>
					<td><strong>Latest Post:</strong> <?php echo $topic['latest_post_id'] ? '<a href="forum.php?action=topic&amp;ID='.$topic['id'].'&amp;latest=1">'.formatLatestPost($topic['id']).'</a><br /><strong>By:</strong> '.$users->name($topic['latest_post_user']) : 'None';?></td>
				</tr><?php
			}
		}
	?></table>
	<div class="paginate"><?php echo $pages->display_pages();?></div><?php
}
function topicNew($db, $my, $mtg, $set, $users, $securimage) {
	if(empty($_GET['ID']))
		$mtg->error('You didn\'t specify a valid board');
	$db->query('SELECT `name`, `publicity`, `is_recycle` FROM `forums` WHERE `id` = ?');
	$db->execute([$_GET['ID']]);
	if(!$db->num_rows())
		$mtg->error('That board doesn\'t exist');
	$row = $db->fetch_row(true);
	if($row['publicity'] == 'staff' && !$my['staff_rank'])
		$mtg->error('This board is for staff only. You don\'t have access');
	if($row['publicity'] == 'upgraded' && time() > strtotime($my['upgraded']))
		$mtg->error('This board is for upgraded members only. You don\'t have access');
	if($row['is_recycle'])
		$mtg->error('You can\'t create topics in a recycle board');
	?><h3 class="content-subhead"><a href="forum.php">Index</a> &rarr; <a href="forum.php?action=board&amp;ID=<?php echo $_GET['ID'];?>"><?php echo $mtg->format($row['name']);?></a> &rarr; Adding a new topic</h3><?php
	if(array_key_exists('submit', $_POST)) {
		if($set['captcha_forums']) {
			$_POST['captcha_code'] = array_key_exists('captcha_code', $_POST) && ctype_digit($_POST['captcha_code']) && strlen($_POST['captcha_code']) == 6 ? $_POST['captcha_code'] : null;
			if($securimage->check($_POST['captcha_code']) == false)
				$mtg->error('You didn\'t enter a valid code');
		}
		$_POST['name'] = array_key_exists('name', $_POST) && is_string($_POST['name']) ? trim($_POST['name']) : null;
		if(empty($_POST['name']))
			$mtg->error('You didn\'t enter a valid name');
		$_POST['desc'] = array_key_exists('desc', $_POST) && is_string($_POST['desc']) ? trim($_POST['desc']) : 'n/a';
		$_POST['content'] = array_key_exists('content', $_POST) && is_string($_POST['content']) ? trim($_POST['content']) : null;
		if(empty($_POST['content']))
			$mtg->error('You didn\'t enter anything for your post');
		$db->startTrans();
		$db->query('INSERT INTO `forums_topics` (`name`, `description`, `parent_board`, `creator`) VALUES (?, ?, ?, ?)');
		$db->execute([$_POST['name'], $_POST['desc'], $_GET['ID'], $my['id']]);
		$topic = $db->insert_id();
		$db->query('INSERT INTO `forums_posts` (`user`, `content`, `parent_topic`) VALUES (?, ?, ?)');
		$db->execute([$my['id'], $_POST['content'], $topic]);
		$db->endTrans();
		reStatForum($db, $topic);
		$mtg->success('&ldquo;'.$mtg->format($_POST['name']).'&rdquo; has been created');
		boardView($db, $my, $mtg, $set, $users, $_GET['ID']);
	} else {
		?><form action="forum.php?action=topic&amp;sub=new&amp;ID=<?php echo $_GET['ID'];?>" method="post" class="pure-form pure-form-aligned">
			<div class="pure-control-group">
				<label for="name">Name</label>
				<input type="text" name="name" class="pure-u-1-2" required />
			</div>
			<div class="pure-control-group">
				<label for="desc">Description</label>
				<input type="text" name="desc" class="pure-u-1-2" placeholder="Optional" />
			</div>
			<div class="pure-control-group">
				<label for="content">Post</label>
				<textarea name="content" class="pure-u-1-2"></textarea>
			</div><?php
			if($set['captcha_forums']) {
				?><div class="pure-control-group">
					<label for="image">Captcha Image</label>
					<img id="captcha" src="/includes/securimage/securimage_show.php" alt="CAPTCHA Image" />
				</div>
				<div class="pure-control-group">
					<label for="code">Captcha Code</label>
					<input type="text" name="captcha_code" size="10" maxlength="6" class="pure-u-1-3" required />
					<a href="#" onclick="document.getElementById('captcha').src = '/includes/securimage/securimage_show.php?' + Math.random(); return false">[ Different Image ]</a>
				</div><?php
			}
			?><div class="pure-controls">
				<button type="submit" name="submit" value="true" class="pure-button pure-button-primary"><i class="fa fa-plus"></i> Post new topic</button>
				<button type="reset" class="pure-button pure-button-secondary"><i class="fa fa-recycle"></i> Reset</button>
			</div>
		</form><?php
	}
}
function topicView($db, $my, $mtg, $set, $users, $topic = 0) {
	global $pages, $parser;
	if($topic)
		$_GET['ID'] = $topic;
	if(empty($_GET['ID']))
		$mtg->error('You didn\'t specify a valid topic');
	$db->query('SELECT `name`, `description`, `parent_board`, `locked`, `pinned`, `creator`, `creation_time` FROM `forums_topics` WHERE `id` = ?');
	$db->execute([$_GET['ID']]);
	if(!$db->num_rows())
		$mtg->error('That topic doesn\'t exist');
	$topic = $db->fetch_row(true);
	$db->query('SELECT `name`, `publicity` FROM `forums` WHERE `id` = ?');
	$db->execute([$topic['parent_board']]);
	if(!$db->num_rows())
		$mtg->error('The parent board for this topic doesn\'t exit');
	$board = $db->fetch_row(true);
	if($board['publicity'] == 'staff' && !$my['staff_rank'])
		$mtg->error('That\'s a staff topic and you\'re not a staff member. You don\'t have access');
	if($board['publicity'] == 'upgraded' && time() > strtotime($my['upgraded']))
		$mtg->error('That topic is for members with upgraded accounts only. You don\'t have access');
	$db->query('SELECT COUNT(`id`) FROM `forums_posts` WHERE `parent_topic` = ? AND `deleted` = 0');
	$db->execute([$_GET['ID']]);
	$pages->items_total = $db->fetch_single();
	$pages->mid_range = 3;
	$pages->paginate();
	$db->query('SELECT * FROM `forums_posts` WHERE `parent_topic` = ? AND `deleted` = 0 ORDER BY `id` ASC '.$pages->limit);
	$db->execute([$_GET['ID']]);
	$posts = $db->fetch_row();
	?><h3 class="content-subhead"><a href="forum.php">Index</a> &rarr; <a href="forum.php?action=board&amp;ID=<?php echo $topic['parent_board'];?>"><?php echo $mtg->format($board['name']);?></a> &rarr; <?php echo $mtg->format($topic['name']);?></a></h3>
	<div class="small">
		This topic was created by <?php echo $users->name($topic['creator']);?> on <?php echo date('l, jS F \a\t H:i:s', strtotime($topic['creation_time']));?> and contains <?php echo $mtg->format($pages->items_total);?> post<?php echo $mtg->s($pages->items_total);
		if($topic['locked'])
			echo '<br />This topic is locked';
		if($topic['pinned'])
			echo '<br />This topic is pinned';
	?></div><?php
	if($users->hasAccess('forum_topic_delete')) {
		?><div class="forum-functions">
			<form action="forum.php?action=admin&amp;sub=deletetopic&amp;ID=<?php echo $_GET['ID'];?>" method="post" class="pure-form pure-form-aligned">
				<button type="submit" name="submit" value="true" class="pure-button pure-button-important-confirmation" title="Delete Topic"><i class="fa fa-trash"></i></button>&nbsp;
			</form>
		</div><?php
	}
	if($users->hasAccess('forum_topic_lock')) {
		?><div class="forum-functions">
			<form action="forum.php?action=admin&amp;sub=lock&amp;ID=<?php echo $_GET['ID'];?>" method="post" class="pure-form pure-form-aligned"><?php
				echo $topic['locked'] ? '<button type="submit" name="submit" value="true" class="pure-button pure-button-tertiary" title="Unlock"><i class="fa fa-unlock"></i></button>' : '<button type="submit" name="submit" value="true" class="pure-button pure-button-primary" title="Lock"><i class="fa fa-lock"></i></button>';
			?>&nbsp;</form>
		</div><?php
	}
	if($users->hasAccess('forum_topic_pin')) {
		?><div class="forum-functions">
			<form action="forum.php?action=admin&amp;sub=pin&amp;ID=<?php echo $_GET['ID'];?>" method="post" class="pure-form pure-form-aligned"><?php
				echo $topic['pinned'] ? '<button type="submit" name="submit" value="true" class="pure-button pure-button-tertiary" title="Unpin"><i class="fa fa-exclamation"></i></button>' : '<button type="submit" name="submit" value="true" class="pure-button pure-button-primary" title="Pin"><i class="fa fa-exclamation-triangle"></i></button>';
			?>&nbsp;</form>
		</div><?php
	}
	if($users->hasAccess('forum_topic_move')) {
		?><div class="forum-functions">
			<form action="forum.php?action=admin&amp;sub=move&amp;ID=<?php echo $_GET['ID'];?>" method="post" class="pure-form pure-form-aligned">
				<button type="submit" name="submit" value="true" class="pure-button pure-button-primary" title="Move"><i class="fa fa-truck"></i></button>&nbsp;
			</form>
		</div><?php
	}
	$db->query('SELECT `id` FROM `forums_subscriptions` WHERE `topic` = ? AND `user` = ?');
	$db->execute([$_GET['ID'], $my['id']]);
	?><div class="forum-functions">
		<form action="forum.php?action=sub&amp;ID=<?php echo $_GET['ID'];?>" method="post" class="pure-form pure-form-aligned">
			<button type="submit" name="submit" value="true" class="pure-button pure-button-<?php echo $db->num_rows() ? 'tertiary' : 'primary';?>"><i class="fa fa-eye"></i></button>&nbsp;
		</form>
	</div>
	<div class="paginate"><?php echo $pages->display_pages();?></div>
	<table width="100%" class="pure-table pure-table-striped">
		<thead>
			<tr>
				<th width="25%">Info</th>
				<th width="75%">Post</th>
			</tr>
		</thead><?php
		if(!count($posts))
			echo '<tr><td colspan="2" class="center">There are no posts. Wait...this shouldn\'t happen..</td></tr>';
		else
			foreach($posts as $post) {
				$parser->parse(nl2br($mtg->format($post['content'])));
				?><tr><?php
					if($post['user']) {
						?><td>
							<?php echo $users->name($post['user']);?><br />
							<a id="<?php echo $post['id'];?>" class="small gray"><?php echo date('H:i:s d/m/Y', strtotime($post['posted']));?></a>
						</td>
						<td><?php
							if($post['user'] == $my['id'] || $users->hasAccess('forum_post_delete')) {
								?><div class="forum-functions">
									<form action="forum.php?action=admin&amp;sub=deletepost&amp;ID=<?php echo $post['id'];?>" method="post" class="pure-form pure-form-aligned">
										<button type="submit" name="submit" class="pure-button pure-button-primary" title="Delete"><i class="fa fa-trash"></i></button>&nbsp;
									</form>
								</div><?php
							}
							if($post['user'] == $my['id'] || $users->hasAccess('forum_post_edit')) {
								?><div class="forum-functions">
									<form action="forum.php?action=admin&amp;sub=editpost&amp;ID=<?php echo $post['id'];?>" method="post" class="pure-form pure-form-aligned">
										<button type="submit" name="submit" class="pure-button pure-button-primary" title="Edit"><i class="fa fa-pencil"></i></button>&nbsp;
									</form>
								</div><?php
							}
							echo $parser->getAsHTML();
							if($post['edit_times']) {
								if($post['edit_times'] == 1)
									$edit = 'once';
								else if($post['edit_times'] == 2)
									$edit = 'twice';
								else
									$edit = $mtg->format($post['edit_times']).' times';
								echo '<div class="forum-edit">Last edited by '.$users->name($post['edit_user']).' at '.date('H:i:s d/m/Y', strtotime($post['edit_date'])).'<br />Edited '.$edit.' in total</div>';
							}
						?></td><?php
					} else
						echo '<td colspan="2" class="center">'.$mtg->format($post['content']).'</td>';
				?></tr><?php
			}
	?></table>
	<div class="paginate"><?php echo $pages->display_pages();?></div><?php
	if(!$topic['locked'] || $users->hasAccess('forum_post_locked')) {
		?><script type="javascript">
			$('textarea').enterKey(function() {$(this).closest('form').submit(); }, 'ctrl');
		</script>
		<p>
			<form action="forum.php?action=post&amp;ID=<?php echo $_GET['ID'];?>" method="post" class="pure-form pure-form-aligned">
				<div class="pure-control-group">
					<label for="post">Response</label>
					<textarea name="post" class="pure-u-1-2"></textarea>
				</div><?php
				if($set['captcha_forums']) {
					?><div class="pure-control-group">
						<label for="image">Captcha Image</label>
						<img id="captcha" src="/includes/securimage/securimage_show.php" alt="CAPTCHA Image" />
					</div>
					<div class="pure-control-group">
						<label for="code">Captcha Code</label>
						<input type="text" name="captcha_code" size="10" maxlength="6" class="pure-u-1-3" required />
						<a href="#" onclick="document.getElementById('captcha').src = '/includes/securimage/securimage_show.php?' + Math.random(); return false">[ Different Image ]</a>
					</div><?php
				}
				?><div class="pure-controls">
					<button type="submit" name="submit" value="true" class="pure-button pure-button-primary"><i class="fa fa-envelope"></i> Post your reply</button>
					<button type="reset" class="pure-button pure-button-secondary"><i class="fa fa-recycle"></i> Reset</button>
				</div>
			</form>
		</p><?php
	}
}
function respond($db, $my, $mtg, $set, $users, $securimage) {
	if(empty($_GET['ID']))
		$mtg->error('You didn\'t specify a valid topic in which to respond');
	if($set['captcha_forums']) {
		$_POST['captcha_code'] = array_key_exists('captcha_code', $_POST) && ctype_digit($_POST['captcha_code']) && strlen($_POST['captcha_code']) == 6 ? $_POST['captcha_code'] : null;
		if($securimage->check($_POST['captcha_code']) == false)
			$mtg->error('You didn\'t enter a valid code');
	}
	$db->query('SELECT `name`, `parent_board`, `locked` FROM `forums_topics` WHERE `id` = ?');
	$db->execute([$_GET['ID']]);
	if(!$db->num_rows())
		$mtg->error('That topic doesn\'t exist');
	$topic = $db->fetch_row(true);
	$db->query('SELECT `publicity`, `is_recycle` FROM `forums` WHERE `id` = ?');
	$db->execute([$topic['parent_board']]);
	if(!$db->num_rows())
		$mtg->error('The parent board for this topic doesn\'t exist');
	$board = $db->fetch_row(true);
	if($board['publicity'] == 'staff' && !$my['staff_rank'])
		$mtg->error('This board is for staff only. You don\'t have access');
	if($board['publicity'] == 'upgraded' && time() > strtotime($my['upgraded']))
		$mtg->error('This board is for upgraded members only. You don\'t have access');
	$_POST['post'] = array_key_exists('post', $_POST) && is_string($_POST['post']) ? trim($_POST['post']) : null;
	if(empty($_POST['post']))
		$mtg->error('You didn\'t enter a valid response');
	$db->query('SELECT `id` FROM `forums_posts` WHERE `parent_topic` = ? AND `content` = ? AND `user` = ? ORDER BY `id` DESC LIMIT 1');
	$db->execute([$_GET['ID'], $_POST['post'], $my['id']]);
	if($db->num_rows())
		$mtg->error('Double submission detected');
	$db->query('SELECT `user` FROM `forums_subscriptions` WHERE `topic` = ?');
	$db->execute([$_GET['ID']]);
	$db->startTrans();
	if($db->num_rows()) {
		$subscribers = $db->fetch_row();
		foreach($subscribers as $subscriber)
			$users->send_event($subscriber['user'], 'Subscription', 'There\'s a new post in <a href="forum.php?action=topic&amp;ID='.$_GET['ID'].'">'.$mtg->format($topic['name']).'</a>');
	}
	$db->query('INSERT INTO `forums_posts` (`parent_topic`, `user`, `content`) VALUES (?, ?, ?)');
	$db->execute([$_GET['ID'], $my['id'], $_POST['post']]);
	$post = $db->insert_id();
	$db->query('UPDATE `forums_topics` SET `latest_post_id` = ?, `latest_post_user` = ?, `latest_post_time` = ? WHERE `id` = ?');
	$db->execute([$post, $my['id'], date('Y-m-d H:i:s'), $_GET['ID']]);
	$db->query('UPDATE `forums` SET `latest_post_id` = ?, `latest_post_user` = ?, `latest_post_time` = ? WHERE `id` = ?');
	$db->execute([$post, $my['id'], date('Y-m-d H:i:s'), $_GET['ID']]);
	$db->endTrans();
	$mtg->success('Your response has been posted');
	unset($_POST);
	topicView($db, $my, $mtg, $set, $users, $_GET['ID']);
}
function topicPin($db, $my, $mtg, $set, $users, $logs) {
	if(empty($_GET['ID']))
		$mtg->error('You didn\'t specify a valid topic');
	$db->query('SELECT `name`, `pinned` FROM `forums_topics` WHERE `id` = ?');
	$db->execute([$_GET['ID']]);
	if(!$db->num_rows())
		$mtg->error('That topic doesn\'t exist');
	$row = $db->fetch_row(true);
	$act = $row['pinned'] ? 'unp' : 'p';
	$db->startTrans();
	$db->query('UPDATE `forums_topics` SET `pinned` = IF(`pinned` = 1, 0, 1) WHERE `id` = ?');
	$db->execute([$_GET['ID']]);
	$db->query('INSERT INTO `forums_posts` (`content`, `parent_topic`, `user`) VALUES ("This topic was '.$act.'inned by '.$my['username'].'", ?, 0)');
	$db->execute([$_GET['ID']]);
	$logs->staff(ucfirst($act).'inned topic: '.$mtg->format($row['name']));
	$db->endTrans();
	$mtg->success('You\'ve '.$act.'inned topic: '.$mtg->format($row['name']));
	topicView($db, $my, $mtg, $set, $users, $_GET['ID']);
}
function topicLock($db, $my, $mtg, $set, $users, $logs) {
	if(empty($_GET['ID']))
		$mtg->error('You didn\'t specify a valid topic');
	$db->query('SELECT `name`, `locked` FROM `forums_topics` WHERE `id` = ?');
	$db->execute([$_GET['ID']]);
	if(!$db->num_rows())
		$mtg->error('That topic doesn\'t exist');
	$row = $db->fetch_row(true);
	$act = $row['locked'] ? 'unl' : 'l';
	$db->startTrans();
	$db->query('UPDATE `forums_topics` SET `locked` = IF(`locked` = 1, 0, 1) WHERE `id` = ?');
	$db->execute([$_GET['ID']]);
	$db->query('INSERT INTO `forums_posts` (`content`, `parent_topic`, `user`) VALUES ("This topic was '.$act.'ocked by '.$my['username'].'", ?, 0)');
	$db->execute([$_GET['ID']]);
	$logs->staff(ucfirst($act).'ocked topic: '.$mtg->format($row['name']));
	$db->endTrans();
	$mtg->success('You\'ve '.$act.'ocked topic: '.$mtg->format($row['name']));
	topicView($db, $my, $mtg, $set, $users, $_GET['ID']);
}
function topicDelete($db, $my, $mtg, $set, $users, $logs, $id = 0) {
	if($id)
		$_GET['id'] = $id;
	if(empty($_GET['ID']))
		$mtg->error('You didn\'t specify a valid topic');
	$db->query('SELECT `name`, `parent_board`, `latest_post_id`, `latest_post_user`, `latest_post_time` FROM `forums_topics` WHERE `id` = ?');
	$db->execute([$_GET['ID']]);
	if(!$db->num_rows())
		$mtg->error('That topic doesn\'t exist');
	$row = $db->fetch_row(true);
	$db->query('SELECT `id` FROM `forums` WHERE `is_recycle` = 1');
	$db->execute();
	if($db->num_rows()) {
		$id = $db->fetch_single();
		if($id == $row['parent_board'])
			$mtg->error('This topic has already been recycled');
		$db->query('UPDATE `forums_topics` SET `parent_board` = ? WHERE `id` = ?');
		$db->execute([$id, $_GET['ID']]);
		$log = 'Recycled';
	} else {
		$db->startTrans();
		$db->query('DELETE FROM `forums_topics` WHERE `id` = ?');
		$db->execute([$_GET['ID']]);
		$db->query('DELETE FROM `forums_posts` WHERE `parent_topic` = ?');
		$db->execute([$_GET['ID']]);
		$db->endTrans();
		$log = 'Deleted';
	}
	reStatForum($db, $_GET['ID']);
	$logs->staff($log.' topic: '.$mtg->format($row['name']));
	$mtg->success('You\'ve '.strtolower($log).' topic: '.$mtg->format($row['name']));
	boardView($db, $my, $mtg, $set, $users, $row['parent_board']);
}
function postEdit($db, $my, $mtg, $set, $users, $logs, $securimage) {
	if(empty($_GET['ID']))
		$mtg->error('You didn\'t specify a valid post');
	$db->query('SELECT `user`, `content`, `parent_topic` FROM `forums_posts` WHERE `id` = ?');
	$db->execute([$_GET['ID']]);
	if(!$db->num_rows())
		$mtg->error('That post doesn\'t exist');
	$post = $db->fetch_row(true);
	if($post['user'] !=$my['id'] && !$users->hasAccess('forum_post_edit'))
		$mtg->error('You don\'t have access');
	$db->query('SELECT `name`, `parent_board` FROM `forums_topics` WHERE `id` = ?');
	$db->execute([$post['parent_topic']]);
	if(!$db->num_rows())
		$mtg->error('The parent topic for that post doesn\'t exist');
	$topic = $db->fetch_row(true);
	$db->query('SELECT `publicity` FROM `forums` WHERE `id` = ?');
	$db->execute([$topic['parent_board']]);
	if(!$db->num_rows())
		$mtg->error('The parent board for that post doesn\'t exist');
	$board = $db->fetch_row(true);
	if($board['publicity'] == 'staff' && !$my['staff_rank'])
		$mtg->error('That post is from a staff board, you don\'t have access');
	if($board['publicity'] == 'upgraded' && time() > strtotime($my['upgraded']))
		$mtg->error('That post is from an upgraded only board, you don\'t have access');
	if(array_key_exists('edits', $_POST)) {
		if($set['captcha_forums']) {
			$_POST['captcha_code'] = array_key_exists('captcha_code', $_POST) && ctype_digit($_POST['captcha_code']) && strlen($_POST['captcha_code']) == 6 ? $_POST['captcha_code'] : null;
			if($securimage->check($_POST['captcha_code']) == false)
				$mtg->error('You didn\'t enter a valid code');
		}
		$_POST['content'] = array_key_exists('content', $_POST) && is_string($_POST['content']) ? trim($_POST['content']) : null;
		if(empty($_POST['content']))
			$mtg->error('You didn\'t enter a valid text');
		$db->query('UPDATE `forums_posts` SET `content` = ?, `edit_user` = ?, `edit_times` = `edit_times` + 1, `edit_date` = ? WHERE `id` = ?');
		$db->execute([$_POST['content'], $my['id'], date('Y-m-d H:i:s'), $_GET['ID']]);
		if($post['user'] != $my['id'])
			$logs->staff('Edited post ID #'.$mtg->format($_GET['ID']).' in topic: '.$topic['name']);
		topicView($db, $my, $mtg, $set, $users, $post['parent_topic']);
	} else {
		?><form action="forum.php?action=admin&amp;sub=editpost&amp;ID=<?php echo $_GET['ID'];?>" method="post" class="pure-form pure-form-aligned">
			<div class="pure-control-group">
				<label for="poster">Poster</label>
				<?php echo $users->name($post['user']);?>
			</div>
			<div class="pure-control-group">
				<label for="content">Post</label>
				<textarea name="content" class="pure-u-1-2"><?php echo $mtg->format($post['content']);?></textarea>
			</div><?php
			if($set['captcha_forums']) {
				?><div class="pure-control-group">
					<label for="image">Captcha Image</label>
					<img id="captcha" src="/includes/securimage/securimage_show.php" alt="CAPTCHA Image" />
				</div>
				<div class="pure-control-group">
					<label for="code">Captcha Code</label>
					<input type="text" name="captcha_code" size="10" maxlength="6" class="pure-u-1-3" required />
					<a href="#" onclick="document.getElementById('captcha').src = '/includes/securimage/securimage_show.php?' + Math.random(); return false">[ Different Image ]</a>
				</div><?php
			}
			?><div class="pure-controls">
				<button type="submit" name="edits" value="true" class="pure-button pure-button-primary"><i class="fa fa-pencil"></i> Submit Edit</button>
				<button type="reset" class="pure-button pure-button-secondary"><i class="fa fa-recycle"></i> Reset</button>
			</div>
		</form><?php
	}
}
function postDelete($db, $my, $mtg, $set, $users, $logs) {
	if(empty($_GET['ID']))
		$mtg->error('You didn\'t specify a valid post');
	$db->query('SELECT `user`, `parent_topic` FROM `forums_posts` WHERE `id` = ? AND `deleted` = 0');
	$db->execute([$_GET['ID']]);
	if(!$db->num_rows())
		$mtg->error('That post doesn\'t exist');
	$post = $db->fetch_row(true);
	if($post['user'] != $my['id'] && !$users->hasAccess('forum_post_delete'))
		$mtg->error('You don\'t have access');
	$db->query('SELECT `name`, `parent_board` FROM `forums_topics` WHERE `id` = ?');
	$db->execute([$post['parent_topic']]);
	if(!$db->num_rows())
		$mtg->error('The parent topic for that post doesn\'t exist');
	$topic = $db->fetch_row(true);
	$db->query('SELECT `publicity`, `is_recycle` FROM `forums` WHERE `id` = ?');
	$db->execute([$topic['parent_board']]);
	if(!$db->num_rows())
		$mtg->error('The parent board for that post doesn\'t exist');
	$board = $db->fetch_row(true);
	if($board['publicity'] == 'staff' && !$my['staff_rank'])
		$mtg->error('That post is from a staff board, you don\'t have access');
	if($board['publicity'] == 'upgraded' && time() > strtotime($my['upgraded']))
		$mtg->error('That post is from an upgraded only board, you don\'t have access');
	$db->query('SELECT COUNT(`id`) FROM `forums_posts` WHERE `parent_topic` = ? AND `deleted` = 0');
	$db->execute([$post['parent_topic']]);
	if($db->num_rows() == 1)
		$mtg->error('You can\'t delete the only post in a topic');
	$db->query('UPDATE `forums_posts` SET `deleted` = 1 WHERE `id` = ?');
	$db->execute([$_GET['ID']]);
	reStatForum($db, $post['parent_topic']);
	if($post['user'] != $my['id'])
		$logs->staff('Deleted post #'.$mtg->format($_GET['ID']).' from forum topic: '.$mtg->format($topic['name']));
	$mtg->success('You\'ve deleted post #'.$mtg->format($_GET['ID']).' from topic: '.$mtg->format($topic['name']));
	topicView($db, $my, $mtg, $set, $users, $post['parent_topic']);
}
function topicSubscribe($db, $my, $mtg, $set, $users) {
	if(empty($_GET['ID']))
		$mtg->error('You didn\'t specify a valid topic');
	$db->query('SELECT `name`, `parent_board` FROM `forums_topics` WHERE `id` = ?');
	$db->execute([$_GET['ID']]);
	if(!$db->num_rows())
		$mtg->error('That topic doesn\'t exist');
	$topic = $db->fetch_row(true);
	$db->query('SELECT `id`, `publicity`, `is_recycle` FROM `forums` WHERE `id` = ?');
	$db->execute([$topic['parent_board']]);
	if(!$db->num_rows())
		$mtg->error('The parent board for that topic doesn\'t exist');
	$board = $db->fetch_row(true);
	if($board['publicity'] == 'staff' && !$my['staff_rank'])
		$mtg->error('That post is from a staff board, you don\'t have access');
	if($board['publicity'] == 'upgraded' && time() > strtotime($my['upgraded']))
		$mtg->error('That post is from an upgraded only board, you don\'t have access');
	$db->query('SELECT `id` FROM `forums_subscriptions` WHERE `user` = ? AND `topic` = ?');
	$db->execute([$my['id'], $_GET['ID']]);
	if($db->num_rows()) {
		$id = $db->fetch_single();
		$db->query('DELETE FROM `forums_subscriptions` WHERE `id` = ?');
		$db->execute([$id]);
		$action = 'un';
	} else {
		$db->query('INSERT INTO `forums_subscriptions` (`user`, `topic`) VALUES (?, ?)');
		$db->execute([$my['id'], $_GET['ID']]);
		$action = '';
	}
	$mtg->success('You\'ve '.$action.'subscribed to the topic: '.$mtg->format($topic['name']));
	topicView($db, $my, $mtg, $set, $users);
}
function reStatForum($db, $id) {
	// Get latest post in topic
	$db->query('SELECT `id`, `user`, `posted` FROM `forums_posts` WHERE `parent_topic` = ? AND `deleted` = 0 ORDER BY `posted` DESC LIMIT 1');
	$db->execute([$id]);
	if($db->num_rows()) {
		$post = $db->fetch_row(true);
		// Get topic
		$db->query('SELECT `parent_board` FROM `forums_topics` WHERE `id` = ?');
		$db->execute([$id]);
		if($db->num_rows()) {
			$board = $db->fetch_single();
			// Update forum and topic
			$db->startTrans();
			$db->query('UPDATE `forums_topics` SET `latest_post_id` = ?, `latest_post_user` = ?, `latest_post_time` = ? WHERE `id` = ?');
			$db->execute([$post['id'], $post['user'], $post['posted'], $id]);
			$db->query('UPDATE `forums` SET `latest_post_id` = ?, `latest_post_user` = ?, `latest_post_time` = ?, `latest_topic_id` = ? WHERE `id` = ?');
			$db->execute([$post['id'], $post['user'], $post['posted'], $id, $board]);
			$db->endTrans();
		}
	} else {
		$db->startTrans();
		$db->query('UPDATE `forums_topics` SET `latest_post_id` = 0, `latest_post_user` = 0, `latest_post_time` = "0000-00-00 00:00:00" WHERE `id` = ?');
		$db->execute([$id]);
		$db->query('UPDATE `forums` SET `latest_post_id` = 0, `latest_post_user` = 0, `latest_post_time` = "0000-00-00 00:00:00", `latest_topic_id` = 0 WHERE `id` = ?');
		$db->execute([$id]);
		$db->endTrans();
	}
}