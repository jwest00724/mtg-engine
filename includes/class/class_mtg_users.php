<?php
namespace MTG;
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
		$db->query("UPDATE `users` SET `exp` = `exp` + ? WHERE `id` = ?");
		$db->execute([$amnt, $user]);
		return true;
	}
	public function selectList($ddname = 'user', $selected = -1, $notIn = []) {
		global $db, $mtg;
		$first = $selected == -1 ? 0 : 1;
		$ret = "<select name='".$ddname."'><option value='0'".($selected == -1 ? " selected='selected'" : '').">--- Select ---</option>";
		$first = 1;
		$extra = '';
		if(count($notIn))
			$extra .= " WHERE `id` NOT IN(".implode(',', $notIn).") ";
		$db->query("SELECT `id`, `username` FROM `users` ? ORDER BY `username` ASC");
		$db->execute([$extra]);
		$rows = $db->fetch_row();
		foreach($rows as $row) {
			$ret .= "\n<option value='".$row['id']."'";
			if($selected == $row['id'] || !$first || isset($_POST[$ddname]) && $_POST[$ddname] == $row['id']) {
				$ret .= " selected='selected'";
				$first = 1;
			}
			$ret .= ">".$mtg->format($row['username'])." [".$mtg->format($row['id'])."]</option>";
		}
		$ret .= "\n</select>";
		return $ret;
	}
	public function exists($id = 0) {
		global $db;
		if(!$id)
			return false;
		$db->query("SELECT `id` FROM `users` WHERE `id` = ?");
		$db->execute([$id]);
		return $db->num_rows() ? true : false;
	}
	public function hasAccess($what, $id = 0) {
		global $db, $my;
		if(!array_key_exists('userid', $_SESSION) || !isset($_SESSION['userid']) || empty($_SESSION['userid'])) // Redundancy check, should never be needed, added as a safeguard
			return false;
		if(!$id)
			$id = $my['id'];
		if($id == $my['id'] && !$my['staff_rank'])
			return false;
		$db->query("SELECT `staff_rank` FROM `users` WHERE `id` = ?");
		$db->execute([$id]);
		$rank = $db->fetch_single();
		if(!$rank)
			return false;
		$db->query("SELECT `".$what."`, `override_all` FROM `staff_ranks` WHERE `rank_id` = ?");
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
	public function send_event($id, $type = 'Uncategorized', $event) {
		global $db;
		if(!$this->exists($id))
			return false;
		$db->query("INSERT INTO `users_events` (`user`, `type`, `event`) VALUES (?, ?, ?)");
		$db->execute([$id, $type, $event]);
	}
	public function send_message($to, $from, $subject = 'No subject', $message) {
		global $db;
		if(!$this->exists($to))
			return false;
		$db->query("INSERT INTO `users_messages` (`sender`, `receiver`, `subject`, `message`) VALUES (?, ?, ?, ?)");
		$db->execute([$from, $to, $subject, $message]);
	}
	public function jhCheck() {
		global $my, $mtg;
		if($my['jail'] || $my['hospital'])
			$mtg->error("You're still in ".($my['jail'] ? 'jail' : 'hospital'));
	}
	public function hashPass($pass) {
		return crypt($pass, '$6$rounds=5000$haewrFEegfw4h3w5qatnjqw35xcHq$');
	}
	public function name($id, $showID = false, $format = true) {
		global $db, $my, $mtg;
		if(!$id)
			return "<span style='color:#555;font-style:italic;'>System</span>";
		$db->query("SELECT `username`, `staff_rank`, `hospital`, `jail` FROM `users` WHERE `id` = ?");
		$db->execute([$id]);
		if(!$db->num_rows())
			return "<span style='color:#555;font-style:italic;'>System</span>";
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
			$db->query("SELECT `rank_name`, `rank_colour` FROM `staff_ranks` WHERE `rank_id` = ?");
			$db->execute([$user['staff_rank']]);
			if(!$db->num_rows())
				$ret .= "<a href='profile.php?player=".$id."'>".$user['username']."</a>";
			else {
				$rank = $db->fetch_row(true);
				$ret .= "<a href='profile.php?player=".$id."' title='".$mtg->format($rank['rank_name'])."'><span style='color:#".$rank['rank_colour'].";'>".$user['username']."</span></a>";
			}
		} else
			$ret .= "<a href='profile.php?player=".$id."'>".$user['username']."</a>";
		if($showID)
			$ret .= " [".$mtg->format($id)."]";
		if($user['hospital'])
			$ret .= " <a href='hospital.php?ID=".$id."'><img src='img/silk/pill.png' title='Hospitalised' alt='Hospitalised' /></a>";
		if($user['jail'])
			$ret .= " <a href='jail.php?action=rescue&amp;ID=".$id."'><img src='img/silk/lock.png' title='Jailed' alt='Jailed' /></a>";
		return $ret;
	}
	public function expRequired($format = true) {
		global $db, $my, $mtg;
		$ret = pow($my['level'], 3) * pow($my['level'] + 2, 2);
		if($format)
			$ret = $mtg->format($ret);
		return $ret;
	}
}
$users = users::getInstance();