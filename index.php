<?php
require_once(__DIR__ . '/includes/globals.php');
?><p>
	<span style='font-size:1.3em;'>Hello there <?php echo $users->name($my['id']); ?>!</span><br />
	<span style='font-size:1.1em;'>Welcome <?php echo $my['last_seen'] ? 'back ' : ''; ?>to <?php echo $set['game_name']; ?></span>
</p>
<table width='100%'>
	<tr>
		<th width='12.5%'>Name</th>
		<td width='37.5%'><?php echo $users->name($my['id'], true); ?></td>
		<th width='12.5%'>Level/EXP</th>
		<td width='37.5%'><?php echo $mtg->format($my['level']); ?> (<?php echo $mtg->format($my['exp']); ?>/<?php echo $users->expRequired(); ?>)</td>
	</tr>
	<tr>
		<th>Money</th>
		<td><?php echo $set['main_currency_symbol'].$mtg->format($my['money']); ?></td>
		<th>Bank</th>
		<td><?php echo $my['bank'] > -1 ? $set['main_currency_symbol'].$mtg->format($my['bank']) : 'No account'; ?></td>
	</tr>
	<tr>
		<th>Points</th>
		<td><?php echo $mtg->format($my['points']); ?></td>
		<th>&nbsp;</th>
		<td>&nbsp;</td>
	</tr>
</table>