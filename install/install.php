<?php
require_once __DIR__ . '/header.php';
define('MTG_ENABLE', true);
function error($msg) {
	exit('<div class="notification notification-error"><i class="fa fa-times-circle"></i><p>'.$msg.'</p></div>');
}
function success($msg) {
	echo '<div class="notification notification-success"><i class="fa fa-check-circle"></i><p>'.$msg.'</p></div>';
}
function info($msg) {
	echo '<div class="notification notification-info"><i class="fa fa-info-circle"></i><p>'.$msg.'</p></div>';
}
function warning($msg) {
	echo '<div class="notification notification-secondary"><i class="fa fa-secondary-circle"></i><p>'.$msg.'</p></div>';
}
if(!defined('PHP_VERSION_ID')) {
	$version = explode('.', PHP_VERSION);
	define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
}
function formatTimeZones(array $zones) {
	if(!count($zones))
		return 'Something screwed up..';
	foreach($zones as $zone) {
		$zone = explode('/', $zone);
		// Only use "friendly" continent names
		if(in_array($zone[0], ['Africa', 'America', 'Antarctica', 'Arctic', 'Asia', 'Atlantic', 'Australia', 'Europe', 'Indian', 'Pacific']))
			if(isset($zone[1]) != '')
				$locations[$zone[0]][$zone[0]. '/' . $zone[1]] = str_replace('_', ' ', $zone[1]);
	}
	return $locations;
}
function listTimeZones(array $list, $ddname = 'timezone') {
	if(!count($list))
		return 'Something screwed up...';
	$ret = '<select name="'.$ddname.'">';
	foreach($list as $key => $val) {
		$ret .= "\n".'<option value="0">------ '.$key.' -------</option>';
		foreach($val as $zone => $show)
			$ret .= "\n".'<option value="'.$zone.'">'.$show.'</option>';
	}
	$ret .= '</select>';
	return $ret;
}
$mainPath = dirname(__DIR__);
$mainPath = str_replace('install/home', '', $mainPath);
$paths = preg_match('/\/public_html/', $mainPath) ? explode('/public_html/', $mainPath) : ['/', ''];
$path = $paths[1];
$sqlPathMain = __DIR__ . '/sqls/db__structure.sql';
$sqlPathSettings = __DIR__ . '/sqls/db__settings_data.sql';
$steps = [1, 2, 3, 4, 5, 6, 7];
$_GET['step'] = isset($_GET['step']) && ctype_digit($_GET['step']) && in_array($_GET['step'], $steps) ? $_GET['step'] : 1;
?><div class="header">
	<h1>MTG Codes v9.1</h1>
	<h2>Installation</h2>
	<h3>Progress: <div class="pure-u-<?php echo $_GET['step'];?>-<?php echo count($steps);?>"></div></h3>
	<p>Step <?php echo $_GET['step'];?> of <?php echo count($steps);?></p>
</div>
<div class="content"><?php
switch($_GET['step']) {
	default: case 1:
		if(file_exists(DIRNAME(__DIR__) . '/includes/config.php'))
			error('It looks like your game may have already been installed.. Please check that <code>/includes/config.php</code> contains the correct information');
		?><h2 class="content-subhead">Let's do some checks first...</h2>
		<p>
			<form action="install.php?step=2" method="post" class="pure-form">
				<table class="pure-table" width="75%">
					<tr>
						<th width="25%">PHP Version</th>
						<td width="75%"><span class="<?php echo PHP_VERSION_ID >= 50400 ? 'green' : 'red';?>"><?php echo PHP_VERSION;?></span></td>
					</tr>
					<tr>
						<th>Main SQL File</th>
						<td><?php echo is_file($sqlPathMain) ? '<span class="green">Exists</span>' : '<span class="red">Doesn\'t exist!</span>';?></td>
					</tr>
					<tr>
						<th>Settings SQL File</th>
						<td><?php echo is_file($sqlPathSettings) ? '<span class="green">Exists</span>' : '<span class="red">Doesn\'t exist!</span>';?></td>
					</tr>
					<tr>
						<th>Game Directory</th>
						<td><input type="text" name="gamedir" value="/<?php echo $path;?>" size="100%" /></td>
					</tr>
					<tr>
						<td colspan="2" class="center"><input type="submit" value="check" class="pure-button pure-button-primary" /></td>
					</tr>
				</table>
			</form>
			*<strong>Game Directory:</strong> This is simply where you've uploaded the game - this is normally <code>/</code>.<br />
			Make sure that <code>/includes/config.php</code> is writable.<br />
			If you're not sure, just leave it blank and I'll try to install it anyway!
		</p><?php
		break;
	case 2:
		if(file_exists(DIRNAME(__DIR__) . '/includes/config.php'))
			error('It looks like your game may have already been installed.. Please check that <code>/includes/config.php</code> contains the correct information');
		$_POST['gamedir'] = isset($_POST['gamedir']) && is_string($_POST['gamedir']) ? $_POST['gamedir'] : null;
		$path = !empty($_POST['gamedir']) ? $_POST['gamedir'] : '/';
		$path = str_replace('//', '/', $mainPath . $path);
		if(!is_dir($path))
			error('That\'s not a valid directory path: '.$path);
		if(!is_dir($path))
			error('I couldn\'t find that directory. Are you sure you\'ve entered the correct game path?');
		$timezones = formatTimeZones(timezone_identifiers_list());
		?><h2 class="content-subhead">That checks out fine!</h2>
		<p>
			<form action="install.php?step=3" method="post" class="pure-form">
				<input type="hidden" name="gamedir" value="<?php echo $path;?>" />
				<table class="pure-table" width="75%">
					<tr>
						<th colspan="2" class="center">Database Configuration</th>
					</tr>
					<tr>
						<th width="25%">Host</th>
						<td width="75%"><input type="text" name="host" value="localhost" size="100%" /></td>
					</tr>
					<tr>
						<th>User</th>
						<td><input type="text" name="user" placeholder="root" size="100%" /></td>
					</tr>
					<tr>
						<th>Password</th>
						<td><input type="password" name="pass" size="100%" /></td>
					</tr>
					<tr>
						<th>Database</th>
						<td><input type="text" name="name" size="100%" /></td>
					</tr>
					<tr>
						<th>Time Offset</th>
						<td><?php echo listTimeZones($timezones);?></td>
					</tr>
					<tr>
						<td colspan="2" class="center"><input type="submit" value="Connect" class="pure-button pure-button-primary" /></td>
					</tr>
				</table>
			</form>
			*<strong>Host:</strong> This mostly speaks for itself. You need to enter the URL to your MySQL database.<br />
			&nbsp;&nbsp;&nbsp;&nbsp;- For most people, it's normally <code>localhost</code>, which is filled in by default.<br />
			*<strong>User:</strong> The name of the user you created when creating the database.<br />
			*<strong>Pass:</strong> This is the password you entered when creating the user.<br />
			*<strong>Database:</strong> And finally, the name of the database itself!
		</p><?php
		break;
	case 3:
		if(file_exists(DIRNAME(__DIR__) . '/includes/config.php'))
			error('It looks like your game may have already been installed.. Please check that <code>/includes/config.php</code> contains the correct information');
		$_POST['host'] = array_key_exists('host', $_POST) ? $_POST['host'] : null;
		if(empty($_POST['host']))
			error('You didn\'t enter a valid hostname');
		if($_POST['host'] != 'localhost')
			if(!@checkdnsrr($_POST['host']))
				warning('I couldn\'t verify that host. I\'ll continue attempting to install this for you anyway');
		$_POST['user'] = array_key_exists('user', $_POST) && !empty($_POST['user']) ? $_POST['user'] : 'root';
		$_POST['timezone'] = array_key_exists('timezone', $_POST) && is_string($_POST['timezone']) ? $_POST['timezone'] : null;
		if(empty($_POST['timezone']))
			error('You didn\'t select a valid timezone');
		$_POST['gamedir'] = isset($_POST['gamedir']) && is_string($_POST['gamedir']) ? $_POST['gamedir'] : null;
		$path = !empty($_POST['gamedir']) ? $_POST['gamedir'] : '';
		if(!is_dir($path))
			error('That\'s not a valid directory path');
		$includeDir = rtrim($path, '/').'/includes';
		$configFile = $includeDir.'/config.php';
		if(!is_dir($path))
			error('I couldn\'t find that directory. Are you sure you\'ve entered the correct game path?');
		$configuration = "<?php
if(!defined('MTG_ENABLE'))
	exit;
date_default_timezone_set('".$_POST['timezone']."');
define('DB_HOST', '".$_POST['host']."');
define('DB_USER', '".$_POST['user']."');
define('DB_PASS', '".$_POST['pass']."');
define('DB_NAME', '".$_POST['name']."');";
		if(!file_exists($configFile)) {
			info('The configuration file (<code>'.$configFile.'</code>) couldn\'t be found. Trying to create it now...');
			$creation = @fopen($configFile, 'w');
			if(!$creation)
				error('I couldn\'t open the config.php to edit! Please manually create it in the <code>/includes</code> directory');
			fwrite($creation, $configuration);
			fclose($creation);
			if(!$creation || !file_exists($configFile))
				error('The configuration file couldn\'t be created');
			else
				success('The configuration file has been created');
		}
		if(file_exists($configFile) && !is_writeable($configFile)) {
			?>Code required:<br /><textarea class="pure-input-1-2" rows="10" cols="70"><?php echo $configuration;?></textarea><br /><?php
			error('Unfortunately, the config.php exists, but couldn\'t be modified. Please make sure your <code>/includes/</code> directory and <code>/includes/config.php</code> is writable - or edit the file manually');
		} else {
			$creation = fopen($configFile, 'w');
			if(!$creation)
				error('I couldn\'t edit the config.php');
			fwrite($creation, $configuration);
			fclose($creation);
			if(!$creation || !file_exists($configFile))
				error('The configuration file couldn\'t be created');
			else
				success('The configuration file has been created');
		}
		info('Attempting connection to the database..');
		require_once $mainPath . '/includes/class/class_mtg_db_mysqli.php';
		success('We\'ve connected! Moving on...<meta http-equiv="refresh" content="2; url=install.php?step=4" />');
		break;
	case 4:
		require_once $mainPath . '/includes/class/class_mtg_db_mysqli.php';
		?><h2 class="content-subhead">We're connected! Let's install the database</h2><?php
		$templineMain = '';
		$lines = file($sqlPathMain);
		foreach ($lines as $line) {
			if(substr($line, 0, 2) == '--' || !$line)
				continue;
			$templineMain .= $line;
			if (substr(trim($line), -1, 1) == ';') {
				$db->query($templineMain);
				$db->execute();
				$templineMain = '';
			}
		}
		$templineSettings = '';
		$lines = file($sqlPathSettings);
		foreach ($lines as $line) {
			if(substr($line, 0, 2) == '--' || !$line)
				continue;
			$templineSettings .= $line;
			if (substr(trim($line), -1, 1) == ';') {
				$db->query($templineSettings);
				$db->execute();
				$templineSettings = '';
			}
		}
		if($db->tableExists('users'))
			success('Database installed, let\'s move on.<meta http-equiv="refresh" content="2; url=install.php?step=5" />');
		else
			error('The database didn\'t install.. Try importing it manually');
		break;
	case 5:
		?><h2 class="content-subhead">Database installed, let's configure the game</h2>
		<form action="install.php?step=6" method="post" class="pure-form-aligned">
			<table class="pure-table" width="75%">
				<tr>
					<th colspan="2" class="center">Basic Settings</th>
				</tr>
				<tr>
					<th width="25%">Game Name</th>
					<td width="75%"><input type="text" name="game_name" /></td>
				</tr>
				<tr>
					<th>Game Owner</th>
					<td><input type="text" name="game_owner" /></td>
				</tr>
				<tr>
					<th>Game Description</th>
					<td><textarea style="width:75%;" rows="10" name="game_description"></textarea></td>
				</tr>
				<tr>
					<th colspan="2" class="center">Your Account</th>
				</tr>
				<tr>
					<th>Username</th>
					<td><input type="text" name="username" /></td>
				</tr>
				<tr>
					<th>Password</th>
					<td><input type="password" name="pass" /></td>
				</tr>
				<tr>
					<th>Confirm Password</th>
					<td><input type="password" name="cpass" /></td>
				</tr>
				<tr>
					<th>Email</th>
					<td><input type="email" name="email" /></td>
				</tr>
				<tr>
					<td colspan="2" class="center"><input type="submit" class="pure-button pure-button-primary" name="submit" value="Modify Settings and Create Account" /></td>
				</tr>
			</table>
		</form>
		<p>
			*<strong>Game Name:</strong> The name of your game<br />
			*<strong>Game Owner:</strong> Put your name on it, it's yours!<br />
			*<strong>Game Description:</strong> Add a description of your game. Think quality over quantity ;)<br />
		</p><?php
		break;
	case 6:
		if(!isset($_POST['submit']))
			error('You didn\'t come from step 5..');
		if(empty($_POST['game_name']))
			error('You must enter a game name. If you\'re not sure, enter a temporarily value (such as &ldquo;To Be Named&rdquo;, for example)');
		require_once $mainPath . '/includes/class/class_mtg_db_mysqli.php';
		$db->query('UPDATE `game_settings` SET `value` = :value WHERE `name` = :name');
		$settings = [
			[
				':value' => $_POST['game_name'],
				':name' => 'game_name'
			],
			[
				':value' => $_POST['game_owner'],
				':name' => 'game_owner'
			],
			[
				':value' => $_POST['game_description'],
				':name' => 'game_description'
			]
		];
		$settings = array_shift($settings);
		foreach($settings as $param => $value)
			$db->bind($param, $value);
		$db->execute();
		if(empty($_POST['username']))
			error('You didn\'t enter a valid username');
		if(empty($_POST['pass']))
			error('You didn\'t enter a valid password');
		if(empty($_POST['cpass']))
			error('You didn\'t enter a valid password confirmation');
		if($_POST['pass'] != $_POST['cpass'])
			error('Your passwords didn\'t match');
		require_once $mainPath . '/includes/class/class_mtg_users.php';
		$db->startTrans();
		$db->query('INSERT INTO `staff_ranks` (`rank_id`, `rank_name`, `rank_order`, `rank_colour`, `override_all`) VALUES (1, "Owner", 1, "000033", "Yes")');
		$db->execute();
		$db->query('INSERT INTO `users` (`id`, `username`, `password`, `email`, `staff_rank`) VALUES (1, ?, ?, ?, 1)');
		$db->execute([$_POST['username'], $users->hashPass($_POST['pass']), $_POST['email']]);
		$id = $db->insert_id();
		$db->query('INSERT INTO `users_equipment` (`id`) VALUES (?)');
		$db->execute([$id]);
		$db->query('INSERT INTO `users_finances` (`id`) VALUES (?)');
		$db->execute([$id]);
		$db->query('INSERT INTO `users_stats` (`id`) VALUES (?)');
		$db->execute([$id]);
		$db->endTrans();
		success('Your game\'s basic settings have been installed and your account has been created!');
		?>I recommend that you remove this installation directory (keep a local backup, just in case).<br />
		I can try to delete it for you now if you'd like?<br />
		<a href="install.php?step=7">Yes, try and remove this directory</a><?php
		break;
	case 7:
		delete_files(__DIR__);
		if(!is_file(__DIR__ . '/index.php'))
			success('I\'ve managed to delete this install folder. Have fun!');
		else {
			$extra = null;
			if(chmod(__DIR__, 0600))
				$extra = '<br />I\'ve managed to set the directory permissions to 0600. That should offer a little protection, but I still highly recommend you delete this folder!';
			warning('I couldn\'t delete this folder. Please manually delete it.'.$extra);
		}
		break;
}
function delete_files($dir) {
	if(is_dir($dir)) {
		$objects = scandir($dir);
		foreach($objects as $object) {
			if($object != '.' && $object != '..') {
				if(filetype($dir.'/'.$object) == "dir")
					delete_files($dir.'/'.$object);
				else
					unlink($dir.'/'.$object);
			}
		}
		reset($objects);
		rmdir($dir);
	}
}
?></div>