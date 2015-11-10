<?php
define('HEADER_TEXT', 'Profiles');
require_once __DIR__ . '/includes/globals.php';
?><div class="content"><?php
$_GET['player'] = array_key_exists('player', $_GET) && ctype_digit($_GET['player']) ? $_GET['player'] : $my['id'];
if($_GET['player'] != $my['id']) {
	$db->query('SELECT `u`.`username`, `u`.`level`, `u`.`points`, `u`.`location`, `u`.`hospital`, `u`.`hospital_reason`, `u`.`jail`, `u`.`jail_reason`, `u`.`profile_picture`, `ue`.`primary`, `ue`.`secondary`, `ue`.`armour`, `uf`.`money` ' .
		'FROM `users` AS `u` ' .
		'LEFT JOIN `users_equipment` AS `ue` ON `u`.`id` = `ue`.`id` ' .
		'LEFT JOIN `users_finances` AS `uf` ON `u`.`id` = `uf`.`id` ' .
		'WHERE `u`.`id` = ?');
	$db->execute([$_GET['player']]);
	if(!$db->num_rows())
		$mtg->error('That player doesn\'t exist');
	$user = $db->fetch_row(true);
} else
	$user = $my;
?><table class="table" width="100%">
	<tr>
		<th width="33%">Profile</th>
		<th width="67%">Information</th>
	</tr>
	<tr>
		<td>
			<?php echo $users->name($_GET['player'], true);?><br />
			<div align="center"><?php echo $mtg->handleProfilePic($user['profile_picture']);?></div>
		</td>
		<td>
			<strong>Level:</strong> <?php echo $mtg->format($user['level']);?><br />
			<strong>Money:</strong> <?php echo $set['main_currency_symbol'].$mtg->format($user['money']);?><br />
			<strong>Points:</strong> <?php echo $mtg->format($user['points']);?><br /><?php
			if($user['hospital'])
				echo '<br />Hospitalized for a further '.$mtg->time_format($user['hospital'] * 60).'. Reason: '.stripslashes($user['hospital_reason']);
			if($user['jail'])
				echo '<br />Incarcerated for a further '.$mtg->time_format($user['jail'] * 60).'. Reason: '.stripslashes($user['jail_reason']);
		?></td>
	</tr>
</table>
</div>