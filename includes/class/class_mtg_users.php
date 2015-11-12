<?php
namespace MTG;
/*DON'T BE A DICK PUBLIC LICENSE

Everyone is permitted to copy and distribute verbatim or modified copies of this license document, and changing it is allowed as long as the name is changed.

    DON'T BE A DICK PUBLIC LICENSE TERMS AND CONDITIONS FOR COPYING, DISTRIBUTION AND MODIFICATION

    Do whatever you like with the original work, just don't be a dick.

    Being a dick includes - but is not limited to - the following instances:

    1a. Outright copyright infringement - Don't just copy this and change the name.
    1b. Selling the unmodified original with no work done what-so-ever, that's REALLY being a dick.
    1c. Modifying the original work to contain hidden harmful content. That would make you a PROPER dick.

    If you become rich through modifications, related works/services, or supporting the original work, share the love. Only a dick would make loads off this work and not buy the original works creator(s) a pint.

    Code is provided with no warranty. Using somebody else's code and bitching when it goes wrong makes you a DONKEY dick. Fix the problem yourself. A non-dick would submit the fix back.
*/
if(!defined('MTG_ENABLE'))
	exit;
class users {
	static $inst = null;
	static public function getInstance() {
		if(self::$inst == null)
			self::$inst = new users();
		return self::$inst;
	}
	public function give_exp($user, $amnt = 0) {
		global $db;
		if(!$amnt)
			return false;
		$db->query("SELECT `id` FROM `users` WHERE `id` = ?");
		$db->execute([$user]);
		if(!$db->num_rows())
			return false;
		if(date('d/m') == '14/02')
			$amnt *= 6;
		$db->query('UPDATE `users` SET `exp` = `exp` + ? WHERE `id` = ?');
		$db->execute([$amnt, $user]);
		return true;
	}
	public function expRequired($format = true) {
		global $db, $my, $mtg;
		$ret = pow($my['level'], 3) * pow($my['level'] + 2, 2);
		if($format)
			$ret = $mtg->format($ret, 2);
		return $ret;
	}
	public function expPercent($format = true, $dec = 2) {
		global $my, $mtg;
		$math = $my['exp'] / $this->expRequired(false) * 100;
		if($format)
			$math = $mtg->format($math, $dec).'%';
		return $math;
	}
	public function listPlayers($ddname = 'user', $selected = null, $notIn = [], $pure = '') {
		global $db, $mtg;
		$first = $selected == null ? 0 : 1;
		$ret = '<select name="'.$ddname.'"'.($pure ? ' class="'.$pure.'"' : '').'><option value="0"'.($selected == null ? ' selected="selected"' : '').'>--- Select ---</option>';
		$first = 1;
		$extra = '';
		if(count($notIn))
			$extra .= ' WHERE `id` NOT IN('.implode(',', $notIn).') ';
		$db->query('SELECT `id`, `username` FROM `users` '.$extra.' ORDER BY `username` ASC');
		$db->execute();
		$rows = $db->fetch_row();
		foreach($rows as $row) {
			$ret .= "\n".'<option value="'.$row['id'].'"';
			if($selected == $row['id'] || !$first || isset($_POST[$ddname]) && $_POST[$ddname] == $row['id']) {
				$ret .= ' selected="selected"';
				$first = 1;
			}
			$ret .= '>'.$mtg->format($row['username']).' ['.$mtg->format($row['id']).']</option>';
		}
		$ret .= "\n".'</select>';
		return $ret;
	}
	public function exists($id = 0) {
		global $db;
		if(!$id)
			return false;
		$db->query('SELECT `id` FROM `users` WHERE `id` = ?');
		$db->execute([$id]);
		return $db->num_rows() ? true : false;
	}
	public function hasAccess($what, $id = 0) {
		global $db, $my;
		// Redundancy check, should never be needed, added as a safeguard
		if(!array_key_exists('userid', $_SESSION) || !isset($_SESSION['userid']) || empty($_SESSION['userid']))
			return false;
		if(!$id)
			$id = $my['id'];
		if($id == $my['id'] && !$my['staff_rank'])
			return false;
		$db->query('SELECT `staff_rank` FROM `users` WHERE `id` = ?');
		$db->execute([$id]);
		$rank = $db->fetch_single();
		if(!$rank)
			return false;
		$db->query('SELECT `'.$what.'`, `override_all` FROM `staff_ranks` WHERE `rank_id` = ?');
		$db->execute([$rank]);
		if(!$db->num_rows())
			return false;
		$perm = $db->fetch_row(true);
		if($perm['override_all'] == 'Yes')
			return true;
		if($perm[$what] != 'Yes')
			return false;
		return true;
	}
	public function send_event($id, $type = 'Uncategorized', $event, $extra = 0) {
		global $db;
		if(!$this->exists($id))
			return false;
		$db->query('INSERT INTO `users_events` (`user`, `type`, `event`, `extra`) VALUES (?, ?, ?, ?)');
		$db->execute([$id, $type, $event, $extra]);
	}
	public function send_message($to, $from, $subject = 'No subject', $message) {
		global $db;
		if(!$this->exists($to))
			return false;
		$db->query('INSERT INTO `users_messages` (`sender`, `receiver`, `subject`, `message`) VALUES (?, ?, ?, ?)');
		$db->execute([$from, $to, $subject, $message]);
	}
	public function jhCheck() {
		global $my, $mtg;
		if($my['jail'] || $my['hospital'])
			$mtg->error('You\'re still in '.($my['jail'] ? 'jail' : 'hospital'));
	}
	public function hashPass($pass) {
		return crypt($pass, '$6$rounds=5000$haewrFEegfw4h3w5qatnjqw35xcHq$');
	}
	public function name($id, $showID = false, $format = true) {
		global $db, $my, $mtg;
		if(!$id)
			return '<span style="color:#555;font-style:italic;">System</span>';
		$db->query('SELECT `username`, `staff_rank`, `hospital`, `jail` FROM `users` WHERE `id` = ?');
		$db->execute([$id]);
		if(!$db->num_rows())
			return '<span style="color:#555;font-style:italic;">System</span>';
		$user = $db->fetch_row(true);
		if(!$format) {
			$noformat = $mtg->format($user['username']);
			if($showID)
				$noformat .= ' ['.$id.']';
			return $noformat;
		}
		$ret = '';
		$user['username'] = $mtg->format($user['username']);
		if($user['staff_rank']) {
			$db->query('SELECT `rank_name`, `rank_colour` FROM `staff_ranks` WHERE `rank_id` = ?');
			$db->execute([$user['staff_rank']]);
			if(!$db->num_rows())
				$ret .= '<a href="profile.php?player='.$id.'">'.$user['username'].'</a>';
			else {
				$rank = $db->fetch_row(true);
				$ret .= '<a href="profile.php?player='.$id.'" title="'.$mtg->format($rank['rank_name']).'"><span style="color:#'.$rank['rank_colour'].';">'.$user['username'].'</span></a>';
			}
		} else
			$ret .= '<a href="profile.php?player='.$id.'">'.$user['username'].'</a>';
		if($showID)
			$ret .= ' ['.$mtg->format($id).']';
		if($user['hospital'])
			$ret .= ' <a href="hospital.php?ID='.$id.'"><img src="images/silk/pill.png" title="Hospitalised" alt="Hospitalised" /></a>';
		if($user['jail'])
			$ret .= ' <a href="jail.php?action=rescue&amp;ID='.$id.'"><img src="images/silk/lock.png" title="Jailed" alt="Jailed" /></a>';
		return $ret;
	}
	public function updateStatus($status) {
		global $db, $my;
		// $status = str_replace('their', ($my['gender'] == 'Male' ? 'his' : 'her'), $status); // A little personalisation
		if($my['status'] != $status) {
			$db->query('UPDATE `users` SET `status` = ? WHERE `id` = ?');
			$db->execute([$status, $my['id']]);
		}
	}
	public function displayStatus($user) {
		global $db, $my;
		if($user == $my['userid'])
			return stripslashes($my['status']);
		$db->query('SELECT `status`, `staff_rank` FROM `users` WHERE `userid` = ?');
		$db->execute([$user]);
		if(!$db->num_rows())
			return 'Being non-existant!';
		$get = $db->fetch_row(true);
		$status = stripslashes($get['status']);
		if(!$status)
			return 'Unknown';
		if(isOwner($user))
			return $status;
		return $get['staff_rank'] && !$my['staff_rank'] ? 'Staff secrecy' : $status;
	}
	public function checkBan($type = 'game', $id = null) {
		global $db, $my, $mtg;
		if(!$id)
			$id = $my['id'];
		$db->query('SELECT `time_enforced`, `time_expires`, `enforcer` FROM `users_bans` WHERE `user` = ? AND `ban_type` = ?');
		$db->execute([$my['id'], $id]);
		if(!$db->num_rows())
			return false;
		$ban = $db->fetch_row(true);
		switch($ban['ban_type']) {
			default: case 'game':
				$system = 'game';
				break;
			case 'messages':
				$system = 'messaging systems';
		}
		return $mtg->error('You\'ve been banned from the '.$system.'.<br />Your ban was enforced by '.$users->name($ban['enforcer']).' on '.date('l jS \of F Y h:i:sA', $ban['time_enforced']).' and is due to expire '.date('l jS \of F Y h:i:sA', $ban['time_expires']));
	}
	public function listInventory($id, $ddname = 'item', $selected = null, $notIn = []) {
		global $db, $mtg;
		if(!ctype_digit($id))
			return false;
		$first = $selected == null ? 0 : 1;
		$ret = '<select name="'.$ddname.'"><option value="0"'.($selected == null ? ' selected="selected"' : '').'>--- Select ---</option>';
		$first = 1;
		$extra = '';
		if(count($notIn))
			$extra .= ' AND `id` NOT IN('.implode(',', $notIn).') ';
		$db->query('SELECT `item`, `qty`, `items`.`id`, `items`.`name` FROM `inventory`
			LEFT JOIN `items` ON `inventory`.`item` = `items`.`id`
			WHERE `user` = ?'.$extra.'ORDER BY `name` ASC');
		$db->execute([$id]);
		$rows = $db->fetch_row();
		foreach($rows as $row) {
			$ret .= "\n".'<option value="'.$row['id'].'"';
			if($selected == $row['id'] || !$first || isset($_POST[$ddname]) && $_POST[$ddname] == $row['id']) {
				$ret .= ' selected="selected"';
				$first = 1;
			}
			$ret .= '>'.$mtg->format($row['name']).' [x'.$mtg->format($row['qty']).']</option>';
		}
		$ret .= "\n".'</select>';
		return $ret;
	}
	public function checkInventory($user, $item, $qty = 1) {
		global $db;
		if(!ctype_digit($user) || !ctype_digit($item) || !ctype_digit($qty))
			return false;
		//Check user first
		$db->query('SELECT `id` FROM `users` WHERE `id` = ?');
		$db->execute([$user]);
		if(!$db->num_rows())
			return false;
		//Check item second
		$db->query('SELECT `id` FROM `items` WHERE `id` = ?');
		$db->execute([$item]);
		if(!$db->num_rows())
			return false;
		//Finally, check user's inventory
		$db->query('SELECT `id` FROM `inventory` WHERE `user` = ? AND `item` = ? AND `qty` >= ?');
		$db->execute([$user, $item, $qty]);
		return $db->num_rows() ? true : false;
	}
	public function giveItem($item, $user = null, $qty = 1) {
		global $db, $my;
		if(!$user)
			$user = $my['id'];
		$db->query('SELECT `id` FROM `inventory` WHERE `item` = ? AND `user` = ?');
		$db->execute([$item, $user]);
		if(!$db->fetch_single())
			$db->query('INSERT INTO `inventory` (`qty`, `item`, `user`) VALUES (?, ?, ?)');
		else
			$db->query('UPDATE `inventory` SET `qty` = `qty` + ? WHERE `item` = ? AND `user` = ?');
		$db->execute([$qty, $item, $user]);
	}
	public function takeItem($user, $item, $qty = 1) {
		global $db;
		$db->query('SELECT `id`, `qty` FROM `inventory` WHERE `item` = ? AND `user` = ?');
		$db->execute([$item, $user]);
		$row = $db->fetch_row(true);
		if($qty >= $row['qty']) {
			$db->query('DELETE FROM `inventory` WHERE `id` = ?');
			$db->execute([$row['id']]);
		} else {
			$db->query('UPDATE `inventory` SET `qty` = `qty` - ? WHERE `id` = ?');
			$db->execute([$qty, $row['id']]);
		}
	}
	public function online($id = 0, $showAsIcon = true, $coloured = true) {
		global $db, $my;
		$onlineNoIcon = $coloured == true ? '<span class="green">Online</span>' : 'Online';
		$offlineNoIcon = $coloured == true ? '<span class="red">Offline</span>' : 'Offline';
		$onlineIcon = $showAsIcon == true ? '<img src="images/silk/user_green.png" title="Online" alt="Online" />' : $onlineNoIcon;
		$offlineIcon = $showAsIcon == true ? '<img src="images/silk/user_grey.png" title="Offline" alt="Offline" />' : $offlineNoIcon;
		if(!$id)
			$id = $my['id'];
		if($id == $my['id'])
			return $onlineIcon;
		$db->query('SELECT `last_seen` FROM `users` WHERE `id` = ?');
		$db->execute([$id]);
		if(!$db->num_rows())
			return $offlineIcon;
		return $db->fetch_single() >= time() - 900 ? $onlineIcon : $offlineIcon;
	}
}
$users = users::getInstance();