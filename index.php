<?php
define('HEADER_TEXT', 'Home');
require_once(__DIR__ . '/includes/globals.php');
?><p>
	<span style="font-size:1.3em;">Hello there <?php echo $users->name($my['id']);?>!</span><br />
	<span style="font-size:1.1em;">Welcome <?php echo $my['last_seen'] ? 'back ' : '';?>to <?php echo $set['game_name'];?></span>
</p>
<table width="100%" class="pure-table">
	<tr>
		<thead><th colspan="4">Information</th></thead>
	</tr>
	<tr>
		<th width="12.5%">Name</th>
		<td width="37.5%" class="right"><?php echo $users->name($my['id']);?></td>
		<th width="12.5%">Points</th>
		<td width="37.5%" class="right"><?php echo $mtg->format($my['points']);?></td>
	</tr>
	<tr>
		<th>Level</th>
		<td class="right"><?php echo $mtg->format($my['level']);?></td>
		<th>EXP</th>
		<td class="right"><?php echo $mtg->format($my['exp'], 2);?>/<?php echo $users->expRequired();?> (<?php echo $users->expPercent();?>)</td>
	</tr>
	<tr>
		<th>Money</th>
		<td class="right"><?php echo $set['main_currency_symbol'].$mtg->format($my['money']);?></td>
		<th>Bank</th>
		<td class="right"><?php echo $my['bank'] > -1 ? $set['main_currency_symbol'].$mtg->format($my['bank']) : 'No account';?></td>
	</tr>
	<tr>
		<thead><th colspan="4">Stats</th></thead>
	</tr>
	<tr>
		<th>Strength</th>
		<td class="right"><?php echo $mtg->format($my['strength']);?></td>
		<th>Agility</th>
		<td class="right"><?php echo $mtg->format($my['agility']);?></td>
	</tr>
	<tr>
		<th>Guard</th>
		<td class="right"><?php echo $mtg->format($my['guard']);?></td>
		<th>Labour</th>
		<td class="right"><?php echo $mtg->format($my['labour']);?></td>
	</tr>
	<tr>
		<th>Intelligence</th>
		<td class="right"><?php echo $mtg->format($my['iq']);?></td>
		<th>Total Stats</th>
		<td class="right"><?php echo $mtg->format($my['total_stats']);?></td>
	</tr>
</table>