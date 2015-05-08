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
		$db->execute(array($user));
		if(!$db->num_rows())
			return false;
		if(date('d/m') == '14/02')
			$amnt *= 6;
		$db->query("UPDATE `users` SET `exp` = `exp` + ? WHERE `id` = ?");
		$db->execute(array($amnt, $user));
		return true;
	}
	function dropdown($ddname = 'user', $selected = -1, $notIn = array()) {
		global $db, $mtg;
		$first = $selected == -1 ? 0 : 1;
		$ret = "<select name='".$ddname."'><option value='0'".($selected == -1 ? " selected='selected'" : '').">--- Select ---</option>";
		$first = 1;
		$extra = '';
		if(count($notIn))
			$extra .= " WHERE `id` NOT IN(".implode(',', $notIn).") ";
		$db->query("SELECT `id`, `username` FROM `users` ? ORDER BY `username` ASC");
		$db->execute(array($extra));
		$rows = $db->fetch_row();
		foreach($rows as $r) {
			$ret .= "\n<option value='".$r['id']."'";
			if($selected == $r['id'] || !$first || isset($_POST[$ddname]) && $_POST[$ddname] == $r['id']) {
				$ret .= " selected='selected'";
				$first = 1;
			}
			$ret .= ">".$mtg->format($r['username'])." [".$mtg->format($r['id'])."]</option>";
		}
		$ret .= "\n</select>";
		return $ret;
	}
	function exists($id = 0) {
		global $db;
		if(!$id)
			return false;
		$db->query("SELECT `id` FROM `users` WHERE `id` = ?");
		$db->execute(array($id));
		return $db->num_rows() ? true : false;
	}
	function hasAccess($what, $id = 0) {
		global $db, $my;
		if(!array_key_exists('userid', $_SESSION) || !isset($_SESSION['userid']) || empty($_SESSION['userid'])) // Redundancy check, should never be needed, added as a safeguard
			return false;
		if(!$id)
			$id = $my['id'];
		$db->query("SELECT `staff_rank` FROM `users` WHERE `id` = ?");
		$db->execute(array($id));
		$rank = $db->fetch_single();
		if(!$rank)
			return false;
		$db->query("SELECT `".$what."`, `override_all` FROM `staff_ranks` WHERE `rank_id` = ?");
		$db->execute(array($rank));
		if(!$db->num_rows())
			return false;
		$perm = $db->fetch_row(true);
		if($perm['override_all'] == 'Yes')
			return true;
		if($perm[$what] != 'Yes')
			return false;
		return true;
	}
	function jhCheck() {
		global $my, $mtg;
		if($my['jail'] || $my['hospital'])
			$mtg->error("You're still in ".($my['jail'] ? 'jail' : 'hospital'));
	}
	function hashPass($pass) {
		return crypt($pass, '$6$rounds=5000$haewrFEegfw4h3w5qatnjqw35xcHq$');
	}
}
$users = users::getInstance();