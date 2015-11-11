<?php
if(strpos($_SERVER['REQUEST_URI'], basename(__FILE__)) !== false)
	exit;
if(!defined('MTG_ENABLE'))
	exit;
class mtg_functions {
	static $inst = null;
	static public function getInstance() {
		if(self::$inst == null)
			self::$inst = new mtg_functions();
		return self::$inst;
	}
	public function format($str, $dec = 0) {
		if(is_numeric($str))
			return number_format($str, $dec);
		else
			$str = stripslashes(strip_tags($str, "<p><a><ul><ol><li>"));
		return $dec ? nl2br($str) : $str;
	}

	public function error($msg, $lock = true) {
		global $db, $my;
		echo "<div class='notification notification-error'><i class='fa fa-times-circle'></i><p>",$msg,"</p></div>";
		if($lock)
			exit;
	}

	public function success($msg, $lock = false) {
		$go = isset($_POST) ? '-2' : '-1';
		echo "<div class='notification notification-success'><i class='fa fa-check-circle'></i><p>",$msg,"</p></div>";
		if($lock)
			exit;
	}

	public function info($msg, $lock = false) {
		echo "<div class='notification notification-info'><i class='fa fa-info-circle'></i><p>",$msg,"</p></div>";
		if($lock)
			exit;
	}

	public function warning($msg, $lock = false) {
		echo "<div class='notification notification-secondary'><i class='fa fa-secondary-circle'></i><p>",$msg,"</p></div>";
		if($lock)
			exit;
	}

	public function s($num, $word = '') {
		if(!$word)
			return $num == 1 ? '' : 's';
		else {
			if(!ctype_alnum(substr($word, -1)))
				return null;
			if(substr($word, -2) == 'es')
				return $num == 1 ? '' : '\'';
			else if(substr($word, -1) == 'y')
				return $num == 1 ? 'y' : 'ies';
			else
				return substr($word, -1) == 's' ? '' : ($num == 1 ? '' : 's');
		}
	}

	public function time_format($seconds, $mode = 'long'){
		$names	= array(
			'long' => array('millenia', 'year', 'month', 'day', 'hour', 'minute', 'second'),
			'short' => array('mil', 'yr', 'mnth', 'day', 'hr', 'min', 'sec')
		);

		$seconds  = floor($seconds);

		$minutes  = intval($seconds / 60);
		$seconds -= ($minutes * 60);

		$hours	= intval($minutes / 60);
		$minutes -= ($hours * 60);

		$days	 = intval($hours / 24);
		$hours   -= ($days * 24);

		$months   = intval($days / 31);
		$days	-= ($months * 31);

		$years	= intval($months / 12);
		$months  -= ($years * 12);

		$millenia = intval($years / 1000);
		$years -= ($millenia * 1000);

		$result   = array();
		if($millenia)
			array_push($result, sprintf("%s %s", number_format($millenia), $names[$mode][0]));
		if($years)
			array_push($result, sprintf("%s %s%s", number_format($years), $names[$mode][1], $years == 1 ? "" : "s"));
		if($months)
			array_push($result, sprintf("%s %s%s", number_format($months), $names[$mode][2], $months == 1 ? "" : "s"));
		if($days)
			array_push($result, sprintf("%s %s%s", number_format($days), $names[$mode][3], $days == 1 ? "" : "s"));
		if($hours)
			array_push($result, sprintf("%s %s%s", number_format($hours), $names[$mode][4], $hours == 1 ? "" : "s"));
		if($minutes && count($result) < 2)
			array_push($result, sprintf("%s %s%s", number_format($minutes), $names[$mode][5], $minutes == 1 ? "" : "s"));
		if(($seconds && count($result) < 2) || !count($result))
			array_push($result, sprintf("%s %s%s", number_format($seconds), $names[$mode][6], $seconds == 1 ? "" : "s"));
		return implode(", ", $result);
	}
	public function checkExists($table, $col, $value, $where = '') {
		global $db;
		if(!$where)
			$where = $col;
		$db->query("SELECT `?` FROM `?` WHERE `?` = ?");
		$db->execute(array($col, $table, $where, $value));
		return $db->num_rows() ? true : false;
	}
	public function handleProfilePic($image, $dims = []) {
		$ret = '<img src="images/default.png" width= title="Default" class="image image-centered" />';
		$match = preg_match('/^user_images\/(.*)$/', $image);
		if(!$match)
			if(!filter_var($image, FILTER_VALIDATE_URL))
				return $ret;
		$image = $match ? 'http://'.$_SERVER['HTTP_HOST'].'/'.$image : $image;
		$stats = @getimagesize($image);
		if(!$stats[0] || !$stats[1])
			return $ret;
		if(count($dims) == 2) {
			$width = $dim[0];
			$height = $dim[1];
		} else {
			$dims = [250, 250];
			$width = $stats[0] > $dims[0] ? $dims[0] : $stats[0];
			$height = $stats[1] > $dims[1] ? $dims[1] : $stats[1];
		}
		return '<img src="'.$image.'" width="'.$width.'" height="'.$height.'" class="image image-centered" />';
	}
	public function codeVersion($type, $format = true) {
		global $set;
		if($type == 'installed')
			return $set['engine_version'];
		else if($type == 'repo') {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_URL, 'https://bitbucket.org/api/1.0/repositories/Magictallguy/mtg-engine/changesets/?limit=0');
			$result = curl_exec($ch);
			curl_close($ch);
			$repo = @json_decode($result);
			if(!is_object($repo))
				return '<span class="red">Couldn\'t get repo version</span>';
			$count = strlen($repo->count) == 3 ? '9.0.0'.$repo->count : '9.0.'.$repo->count;
		}
		if(!isset($count))
			$ret = '<span class="red">Couldn\'t get repo version</span>';
		else if($count == $set['engine_version'])
			$ret = '<span class="green">'.$set['engine_version'].'</span>';
		else if($count > $set['engine_version'])
			$ret = '<span class="orange">'.$count.'</span>';
		else if($count < $set['engine_version'])
			$ret = '<span class="blue">'.$count.'</span>';
		else
			$ret = 'What?';
		return $format ? $ret : strip_tags($ret);
	}
	function _ip() {
		if(!empty($_SERVER['HTTP_CLIENT_IP']))
			return $_SERVER['HTTP_CLIENT_IP'];
		else if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
			return count($ips) > 1 ? $ips[0] : $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else
			return $_SERVER['REMOTE_ADDR'];
	}
}

$mtg = mtg_functions::getInstance();