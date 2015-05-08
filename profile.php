<?php
require_once(__DIR__ . '/includes/globals.php');
$_GET['player'] = isset($_GET['player']) && ctype_digit($_GET['player']) ? $_GET['player'] : $my['id'];
$db->query("SELECT u.username, u.level, u.points, u.location, u.hospital, u.hospital_reason, u.jail, u.jail_reason, u.profile_picture, ue.primary, ue.secondary, ue.armour, uf.money " .
	"FROM users AS u " .
	"LEFT JOIN users_equipment AS ue ON u.id = ue.id " .
	"LEFT JOIN users_finances AS uf ON u.id = uf.id " .
	"WHERE u.id = ?");
$db->execute(array($_GET['player']));
if(!$db->num_rows())
	$mtg->error("That player doesn't exist");
$user = $db->fetch_row(true);
?><table class='pure-table' width='100%'>
	<tr>
		<th width='33%'>Profile</th>
		<th width='67%'>Information</th>
	</tr>
	<tr>
		<td>
			<?php echo $users->name($_GET['player'], true); ?><br />
			<div align='center'><img src='<?php echo $mtg->format($user['profile_picture']); ?>' class='image image-centered' /></div>
		</td>
		<td>
			<strong>Level:</strong> <?php echo $mtg->format($user['level']); ?><br />
			<strong>Money:</strong> <?php echo $mtg->format($user['money']); ?><br />
			<strong>Points:</strong> <?php echo $mtg->format($user['points']); ?><br /><?php
			if($user['hospital'])
				echo "<br />Hospitalized for a further ".$mtg->time_format($user['hospital'] * 60).". Reason: ".stripslashes($user['hospital_reason']);
			if($user['jail'])
				echo "<br />Incarcerated for a further ".$mtg->time_format($user['jail'] * 60).". Reason: ".stripslashes($user['jail_reason']);