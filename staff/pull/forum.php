<?php
if(!defined('MTG_ENABLE'))
	exit;
$_GET['action'] = array_key_exists('action', $_GET) && ctype_alpha($_GET['action']) ? strtolower(trim($_GET['action'])) : null;
?><div class="content-menu">
	<a href="staff/?pull=forum&amp;action=add"<?php echo $_GET['action'] == 'add' ? ' class="bold"' : '';?>>Forums: Add Board</a> &middot;
	<a href="staff/?pull=forum&amp;action=edit"<?php echo $_GET['action'] == 'edit' ? ' class="bold"' : '';?>>Forums: Edit Board</a> &middot;
	<a href="staff/?pull=forum&amp;action=del"<?php echo $_GET['action'] == 'del' ? ' class="bold"' : '';?>>Forums: Delete Board</a>
</div><?php
switch($_GET['action']) {
	case 'add':
		if(!$users->hasAccess('staff_panel_forum_board_add'))
			$mtg->error('You don\'t have access');
		addBoard($db, $mtg, $logs);
		break;
	case 'edit':
		if(!$users->hasAccess('staff_panel_forum_board_edit'))
			$mtg->error('You don\'t have access');
		editBoard($db, $mtg, $logs);
		break;
	case 'del':
		if(!$users->hasAccess('staff_panel_forum_board_delete'))
			$mtg->error('You don\'t have access');
		deleteBoard($db, $mtg, $logs);
		break;
	default:
		$mtg->info('Please choose an action above');
		break;
}
function addBoard($db, $mtg, $logs) {
	?><h3 class="content-subhead">Adding a forum board</h3><?php
	$publicities = ['all', 'upgraded', 'staff'];
	if(array_key_exists('submit', $_POST)) {
		$_POST['name'] = array_key_exists('name', $_POST) && is_string($_POST['name']) ? trim($_POST['name']) : null;
		if(empty($_POST['name']))
			$mtg->error('You didn\'t enter a valid name');
		$_POST['publicity'] = array_key_exists('publicity', $_POST) && in_array($_POST['publicity'], $publicities) ? $_POST['publicity'] : null;
		if(empty($_POST['publicity']))
			$mtg->error('You didn\'t select a valid publicity');
		$db->query('SELECT `id` FROM `forums` WHERE `name` = ? AND `publicity` = ?');
		$db->execute([$_POST['name'], $_POST['publicity']]);
		if($db->num_rows())
			$mtg->error('Another '.($_POST['publicity'] == 'all' ? 'public' : $_POST['publicity']).' board with that name already exists');
		$_POST['recycle'] = isset($_POST['recycle']) ? 1 : 0;
		if($_POST['recycle']) {
			$db->query('SELECT `id` FROM `forums` WHERE `publicity` = ? AND `is_recycle` = 1');
			$db->execute([$_POST['publicity']]);
			if($db->num_rows())
				$mtg->error('A '.($_POST['publicity'] == 'all' ? 'public' : $_POST['publicity']).' recycling board already exists');
		}
		$_POST['desc'] = array_key_exists('desc', $_POST) && is_string($_POST['desc']) ? $_POST['desc'] : 'n/a';
		$db->query('INSERT INTO `forums` (`name`, `description`, `publicity`, `is_recycle`) VALUES (?, ?, ?, ?)');
		$db->execute([$_POST['name'], $_POST['desc'], $_POST['publicity'], $_POST['recycle']]);
		$logs->staff('Added '.($_POST['publicity'] == 'all' ? 'public' : $_POST['publicity']).' board: '.$mtg->format($_POST['name']));
		$mtg->success('Your '.($_POST['publicity'] == 'all' ? 'public' : $_POST['publicity']).' board has been added');
	}
	?><form action="staff/?pull=forum&amp;action=add" method="post" class="pure-form pure-form-aligned">
		<div class="pure-control-group">
			<label for="name">Name</label>
			<input type="text" name="name" class="pure-u-1-3" required />
		</div>
		<div class="pure-control-group">
			<label for="desc">Description</label>
			<input type="text" name="desc" class="pure-u-1-3" />
		</div>
		<div class="pure-control-group">
			<label for="publicity">Publicity</label>
			<select name="publicity">
				<option value="all">Public</option>
				<option value="upgraded">Upgraded Only</option>
				<option value="staff">Staff</option>
			</select>
		</div>
		<div class="pure-control-group">
			<label for="recycle">Recycle Bin</label>
			<input type="checkbox" name="recycle" value="1" />
		</div>
		<div class="pure-controls">
			<button type="submit" name="submit" value="true" class="pure-button pure-button-primary"><i class="fa fa-plus"></i> Add Board</button>
			<button type="reset" class="pure-button pure-button-secondary"><i class="fa fa-recycle"></i> Reset</button>
		</div>
	</form><?php
}
function editBoard($db, $mtg, $logs) {
	?><h3 class="content-subhead">Editing a forum board</h3><?php
	$publicities = ['all', 'upgraded', 'staff'];
	$_GET['step'] = array_key_exists('step', $_GET) && ctype_digit($_GET['step']) ? $_GET['step'] : null;
	switch($_GET['step']) {
		case 2:
			$_POST['id'] = array_key_exists('id', $_POST) && ctype_digit($_POST['id']) ? $_POST['id'] : null;
			if(empty($_POST['id']))
				$mtg->error('You didn\'t specify a valid board');
			$db->query('SELECT `id` FROM `forums` WHERE `id` = ?');
			$db->execute([$_POST['id']]);
			if(!$db->num_rows())
				$mtg->error('That board doesn\'t exist');
			$_POST['name'] = array_key_exists('name', $_POST) && is_string($_POST['name']) ? trim($_POST['name']) : null;
			if(empty($_POST['name']))
				$mtg->error('You didn\'t enter a valid name');
			$_POST['publicity'] = array_key_exists('publicity', $_POST) && in_array($_POST['publicity'], $publicities) ? $_POST['publicity'] : null;
			if(empty($_POST['publicity']))
				$mtg->error('You didn\'t select a valid publicity');
			$db->query('SELECT `id` FROM `forums` WHERE `name` = ? AND `publicity` = ? AND `id` <> ?');
			$db->execute([$_POST['name'], $_POST['publicity'], $_POST['id']]);
			if($db->num_rows())
				$mtg->error('Another '.($_POST['publicity'] == 'all' ? 'public' : $_POST['publicity']).' board with that name already exists');
			$_POST['recycle'] = isset($_POST['recycle']) ? 1 : 0;
			if($_POST['recycle']) {
				$db->query('SELECT `id` FROM `forums` WHERE `publicity` = ? AND `is_recycle` = 1 AND `id` <> ?');
				$db->execute([$_POST['publicity'], $_POST['id']]);
				if($db->num_rows())
					$mtg->error('Another '.($_POST['publicity'] == 'all' ? 'public' : $_POST['publicity']).' recycling board already exists');
			}
			$_POST['desc'] = array_key_exists('desc', $_POST) && is_string($_POST['desc']) ? $_POST['desc'] : 'n/a';
			$db->query('UPDATE `forums` SET `name` = ?, `description` = ?, `publicity` = ?, `is_recycle` = ? WHERE `id` = ?');
			$db->execute([$_POST['name'], $_POST['desc'], $_POST['publicity'], $_POST['recycle'], $_POST['id']]);
			$logs->staff('Edited '.($_POST['publicity'] == 'all' ? 'public' : $_POST['publicity']).' board: '.$mtg->format($_POST['name']));
			$mtg->success('Your '.($_POST['publicity'] == 'all' ? 'public' : $_POST['publicity']).' board has been edited');
			break;
		case 1:
			$_POST['id'] = array_key_exists('id', $_POST) && ctype_digit($_POST['id']) ? $_POST['id'] : null;
			if(empty($_POST['id']))
				$mtg->error('You didn\'t specify a valid board');
			$db->query('SELECT `name`, `description`, `publicity`, `is_recycle` FROM `forums` WHERE `id` = ?');
			$db->execute([$_POST['id']]);
			if(!$db->num_rows())
				$mtg->error('That board doesn\'t exist');
			$row = $db->fetch_row(true);
			?><form action="staff/?pull=forum&amp;action=edit&amp;step=2" method="post" class="pure-form pure-form-aligned">
				<input type="hidden" name="id" value="<?php echo $_POST['id'];?>" />
				<div class="pure-control-group">
					<label for="name">Name</label>
					<input type="text" name="name" class="pure-u-1-3" value="<?php echo $mtg->format($row['name']);?>" required />
				</div>
				<div class="pure-control-group">
					<label for="desc">Description</label>
					<input type="text" name="desc" class="pure-u-1-3" value="<?php echo $mtg->format($row['description']);?>" />
				</div>
				<div class="pure-control-group">
					<label for="publicity">Publicity</label>
					<select name="publicity">
						<option value="all"<?php echo $row['publicity'] == 'all' ? ' selected' : null;?>>Public</option>
						<option value="upgraded"<?php echo $row['publicity'] == 'upgraded' ? ' selected' : null;?>>Upgraded Only</option>
						<option value="staff"<?php echo $row['publicity'] == 'staff' ? ' selected' : null;?>>Staff</option>
					</select>
				</div>
				<div class="pure-control-group">
					<label for="recycle">Recycle Bin</label>
					<input type="checkbox" name="recycle" value="1"<?php echo $row['is_recycle'] ? ' checked' : null;?> />
				</div>
				<div class="pure-controls">
					<button type="submit" name="submit" value="true" class="pure-button pure-button-primary"><i class="fa fa-cog"></i> Edit Board</button>
					<button type="reset" class="pure-button pure-button-secondary"><i class="fa fa-recycle"></i> Reset</button>
				</div>
			</form><?php
			break;
		default:
			$db->query('SELECT `id`, `name` FROM `forums` ORDER BY FIELD(`publicity`, "all", "upgraded", "staff") ASC, `name` ASC');
			$db->execute();
			if(!$db->num_rows())
				$mtg->error('There are no boards to edit');
			$rows = $db->fetch_row();
			?><form action="staff/?pull=forum&amp;action=edit&amp;step=1" method="post" class="pure-form pure-form-aligned">
				<div class="pure-control-group">
					<label for="id">Board</label>
					<select name="id" class="pure-u-1-3"><?php
					foreach($rows as $row)
						printf('<option value="%u">%s</option>', $row['id'], $mtg->format($row['name']));
					?></select>
				</div>
				<div class="pure-controls">
					<button type="submit" name="submit" value="true" class="pure-button pure-button-primary"><i class="fa fa-cog"></i> Begin Edits</button>
					<button type="reset" class="pure-button pure-button-secondary"><i class="fa fa-recycle"></i> Reset</button>
				</div>
			</form><?php
			break;
	}
}
function deleteBoard($db, $mtg, $logs) {
	?><h3 class="content-subhead">Deleting a forum board</h3><?php
	if(array_key_exists('submit', $_POST)) {
		$_POST['id'] = array_key_exists('id', $_POST) && ctype_digit($_POST['id']) ? $_POST['id'] : null;
		if(empty($_POST['id']))
			$mtg->error('You didn\'t select a valid board');
		$db->query('SELECT `name`, `publicity` FROM `forums` WHERE `id` = ?');
		$db->execute([$_POST['id']]);
		if(!$db->num_rows())
			$mtg->error('That board doesn\'t exist');
		$row = $db->fetch_row(true);
		if(!array_key_exists('ans', $_GET)) {
			?><form action="staff/?pull=forum&amp;action=del&amp;ans=yes" method="post" class="pure-form">
				<input type="hidden" name="id" value="<?php echo $_POST['id'];?>" />
				Are you sure you wish to delete the forum board &ldquo;<?php echo $mtg->format($row['name']);?>&rdquo;?<br />
				<div class="pure-control-group">
					<label for="posts">Move/delete topics/posts<span class="red">*</span></label>
					<input type="checkbox" name="posts" value="1" />
				</div>
				<div class="pure-controls">
					<button type="submit" name="submit" value="true" class="pure-button pure-button-important-confirmation"><i class="fa fa-trash"></i> Yes, delete it</button>
				</div>
			</form>
			<div class="small">
				<span class="red">*</span>If you have a recycle bin set up for the selected board's publicity (all, upgraded, staff), then all topics and posts will be moved to it. If not, they will be deleted
			</div><?php
		} else {
			$db->startTrans();
			$db->query('DELETE FROM `forums` WHERE `id` = ?');
			$db->execute([$_POST['id']]);
			$log = 'deleted forum board: '.$mtg->format($row['name']);
			$_POST['posts'] = isset($_POST['posts']) ? 1 : 0;
			if($_POST['posts']) {
				$db->query('SELECT `id`, `name` FROM `forums` WHERE `is_recycle` = 1 AND `publicity` = ?');
				$db->execute([$row['publicity']]);
				if($db->num_rows()) {
					$rec = $db->fetch_row(true);
					$db->query('UPDATE `forums_topics` SET `parent_board` = ? WHERE `parent_board` = ?');
					$db->execute([$rec['id'], $_POST['id']]);
					$log .= ' and moved all topics/posts to '.$mtg->format($rec['name']);
				} else {
					$db->query('SELECT `id` FROM `forums_topics` WHERE `parent_board` = ?');
					$db->execute([$_POST['id']]);
					if($db->num_rows()) {
						$topics = $db->fetch_row();
						$db->query('DELETE FROM `forums_posts` WHERE `topic` IN('.implode(',', $topics).')');
						$db->execute();
					}
					$db->query('DELETE FROM `forums_topics` WHERE `parent_board` = ?');
					$db->execute([$_POST['id']]);
				}
			}
			$logs->staff(ucfirst($log));
			$db->endTrans();
			$mtg->success('You\'ve '.$log);
		}
	} else {
		$db->query('SELECT `id`, `name` FROM `forums` ORDER BY FIELD(`publicity`, "all", "upgraded", "staff") ASC, `name` ASC');
		$db->execute();
		if(!$db->num_rows())
			$mtg->error('There are no boards to delete');
		$rows = $db->fetch_row();
		?><form action="staff/?pull=forum&amp;action=del" method="post" class="pure-form pure-form-aligned">
			<div class="pure-control-group">
				<label for="id">Board</label>
				<select name="id" class="pure-u-1-3"><?php
				foreach($rows as $row)
					printf('<option value="%u">%s</option>', $row['id'], $mtg->format($row['name']));
				?></select>
			</div>
			<div class="pure-controls">
				<button type="submit" name="submit" value="true" class="pure-button pure-button-primary"><i class="fa fa-trash"></i> Delete board</button>
				<button type="reset" class="pure-button pure-button-secondary"><i class="fa fa-recycle"></i> Reset</button>
			</div>
		</form><?php
	}
}