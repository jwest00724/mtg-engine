<?php
define('HEADER_TEXT', 'Marketplace');
require_once __DIR__ . '/includes/globals.php';
require_once __DIR__ . '/includes/class/class_mtg_paginate.php';
$pages = new Paginator();
$_GET['ID'] = isset($_GET['ID']) && ctype_digit($_GET['ID']) ? $_GET['ID'] : null;
$_GET['action'] = isset($_GET['action']) && ctype_alpha($_GET['action']) ? strtolower(trim($_GET['action'])) : null;
$_GET['sub'] = isset($_GET['sub']) && ctype_alpha($_GET['sub']) ? strtolower(trim($_GET['sub'])) : null;
?><a href="markets.php?action=points"<?php echo $_GET['action'] == 'points' ? ' class="bold"' : '';?>>Points Market</a> &middot; <a href="markets.php?action=items"<?php echo $_GET['action'] == 'items' ? ' class="bold"' : '';?>>Items Market</a><?php
switch($_GET['action']) {
	case 'points':
		switch($_GET['sub']) {
			default:
				$db->query('SELECT COUNT(`id`) FROM `market_points`');
				$db->execute();
				$pages->items_total = $db->fetch_single();
				$pages->mid_range = 3;
				$pages->paginate();
				$db->query('SELECT * FROM `market_points` ORDER BY `time_added` DESC '.$pages->limit);
				$db->execute();
				?><h4><a href="markets.php?action=points&amp;sub=add">Add a Listing</a></h4>
				<p class="paginate"><?php echo $pages->display_pages();?></p>
				<table width="100%" class="pure-table pure-table-striped">
					<tr>
						<th width="25%">Seller</th>
						<th width="25%">Amount</th>
						<th width="25%">Price</th>
						<th width="25%">Actions</th>
					</tr><?php
					if(!$db->num_rows())
						echo '<tr><td colspan="4">There are no listings</td></tr>';
					else {
						$rows = $db->fetch_row();
						foreach($rows as $row) {
							?><tr>
								<td><?php echo $users->name($row['user'], true);?></td>
								<td><?php echo $mtg->format($row['qty']);?></td>
								<td><?php echo $set['main_currency_symbol'].$mtg->format($row['price']);?> each<br /><span class="small">(<?php echo $mtg->format($row['price'] * $row['qty']);?> total)</td>
								<td><?php echo $row['user'] == $my['id']
									? '<a href="markets.php?action=points&amp;sub=remove&amp;ID='.$row['id'].'">Remove</a>'
									: '<a href="markets.php?action=points&amp;sub=buy&amp;ID='.$row['id'].'">Purchase</a> &middot; <a href="markets.php?action=points&amp;sub=gift&amp;ID='.$row['id'].'">Gift</a>';
								?></td>
							</tr><?php
						}
					}
				?></table>
				<p class="paginate"><?php echo $pages->display_pages();?></p><?php
				break;
			case 'add':
				if(array_key_exists('submit', $_POST)) {
					$_POST['price'] = isset($_POST['price']) && ctype_digit(str_replace(',', '', $_POST['price'])) ? str_replace(',', '', $_POST['price']) : null;
					if(empty($_POST['price']))
						$mtg->error('You didn\'t enter a valid price');
					$_POST['qty'] = isset($_POST['qty']) && ctype_digit(str_replace(',', '', $_POST['qty'])) ? str_replace(',', '', $_POST['qty']) : null;
					if(empty($_POST['qty']))
						$mtg->error('You didn\'t enter a valid quantity');
					if($_POST['qty'] > $my['points'])
						$mtg->error('You don\'t have that many points');
					$db->startTrans();
					$db->query('UPDATE `users_finances` SET `points` = `points` - ? WHERE `id` = ?');
					$db->execute([$_POST['qty'], $my['id']]);
					$db->query('INSERT INTO `market_points` (`qty`, `user`, `price`) VALUES (?, ?, ?)');
					$db->execute([$_POST['qty'], $my['id'], $_POST['price']]);
					$db->endTrans();
					$mtg->success('You\'ve listed '.$mtg->format($_POST['qty']).' point'.$mtg->s($_POST['qty']).' on the market at '.$set['main_currency_symbol'].$mtg->format($_POST['price']).' each ('.$set['main_currency_symbol'].$mtg->format($_POST['price'] * $_POST['qty']).' total)');
				}
				?><form action="markets.php?action=points&amp;sub=add" method="post" class="pure-form">
					<div class="pure-control-group">
						<label for="quantity">Quantity</label>
						<input type="text" name="qty" placeholder="<?php echo $mtg->format($my['points']);?>" />
					</div>
					<div class="pure-control-group">
						<label for="price">Price each</label>
						<input type="text" name="price" />
					</div>
					<div class="pure-controls">
						<button type="submit" name="submit" class="pure-button pure-button-primary">List your points</button> <button type="reset" class="pure-button pure-button-secondary">Reset</button>
					</div>
				</form><?php
				break;
			case 'remove':
				if(empty($_GET['ID']))
					$mtg->error('You didn\'t specify a valid listing');
				$db->query('SELECT `qty`, `user` FROM `market_points` WHERE `id` = ?');
				$db->execute([$_GET['ID']]);
				if(!$db->num_rows())
					$mtg->error('That listing doesn\'t exist');
				$row = $db->fetch_row(true);
				if($row['user'] != $my['id'])
					$mtg->error('That isn\'t your listing');
				if(array_key_exists('ans', $_POST)) {
					$_POST['amnt'] = isset($_POST['amnt']) && ctype_digit(str_replace(',', '', $_POST['amnt'])) ? str_replace(',', '', $_POST['amnt']) : $row['qty'];
					if($_POST['amnt'] > $row['qty'])
						$mtg->error('You can\'t remove that many points from this listing');
					$db->startTrans();
					if($_POST['amnt'] == $row['qty']) {
						$db->query('DELETE FROM `market_points` WHERE `id` = ?');
						$db->execute([$_GET['ID']]);
					} else {
						$db->query('UPDATE `market_points` SET `qty` = `qty` - ? WHERE `id` = ?');
						$db->execute([$_POST['amnt'], $_GET['ID']]);
					}
					$db->query('UPDATE `users_finances` SET `points` = `points` + ? WHERE `id` = ?');
					$db->execute([$_POST['amnt'], $my['id']]);
					$db->endTrans();
					$mtg->success('You\'ve removed '.($_POST['amnt'] == $row['qty'] ? 'all of your points' : $mtg->format($_POST['amnt']).' point'.$mtg->s($_POST['amnt'])).' from the market');
				} else {
					?><form action="markets.php?action=points&amp;sub=remove&amp;ID=<?php echo $_GET['ID'];?>" method="post" class="pure-form">
						<div class="pure-control-group">
							<label for="amount">Amount to remove (leave blank to remove all)</label>
							<input type="text" name="amnt" placeholder="<?php echo $mtg->format($row['qty']);?>" />
						</div>
						<div class="pure-control-group">
							<button type="submit" name="ans" class="pure-button pure-button-primary">Remove Points</button>
							<button type="reset" class="pure-button pure-button-secondary">Reset</button>
						</div>
					</form><?php
				}
				break;
			case 'buy':
				if(empty($_GET['ID']))
					$mtg->error('You didn\'t specify a valid listing');
				$db->query('SELECT `price`, `qty`, `user` FROM `market_points` WHERE `id` = ?');
				$db->execute([$_GET['ID']]);
				if(!$db->num_rows())
					$mtg->error('That listing doesn\'t exist');
				$row = $db->fetch_row(true);
				if($row['user'] == $my['id'])
					$mtg->error('You can\'t purchase your own listing');
				if(array_key_exists('ans', $_POST)) {
					$_POST['amnt'] = isset($_POST['amnt']) && ctype_digit(str_replace(',', '', $_POST['amnt'])) ? str_replace(',', '', $_POST['amnt']) : $row['qty'];
					if($_POST['amnt'] > $row['qty'])
						$mtg->error('You can\'t buy that many points from this listing');
					$cost = $_POST['amnt'] * $row['price'];
					if($cost > $my['money'])
						$mtg->error('You don\'t have enough cash');
					$db->startTrans();
					if($_POST['amnt'] == $row['qty']) {
						$db->query('DELETE FROM `market_points` WHERE `id` = ?');
						$db->execute([$_GET['ID']]);
					} else {
						$db->query('UPDATE `market_points` SET `qty` = `qty` - ? WHERE `id` = ?');
						$db->execute([$_POST['amnt'], $_GET['ID']]);
					}
					$db->query('UPDATE `users_finances` SET `points` = `points` + ?, `money` = `money` - ? WHERE `id` = ?');
					$db->execute([$_POST['amnt'], $cost, $my['id']]);
					$db->query('UPDATE `users_finances` SET `money` = `money` + ? WHERE `id` = ?');
					$db->execute([$cost, $row['user']]);
					$out = $_POST['amnt'] == $row['qty'] ? 'all of the points' : $mtg->format($_POST['amnt']).' point'.$mtg->s($_POST['amnt']);
					$users->send_event($row['user'], '{id} purchased '.str_replace('the', 'your', $out).' from the Points Market', $my['id']);
					$db->endTrans();
					$mtg->success('You\'ve purchased '.$out.' from '.$users->name($row['user']).'\'s listing on the market');
				} else {
					?><form action="markets.php?action=points&amp;sub=buy&amp;ID=<?php echo $_GET['ID'];?>" method="post" class="pure-form">
						<div class="pure-control-group">
							<label for="amount">Amount to buy (leave blank to buy all)</label>
							<input type="text" name="amnt" placeholder="<?php echo $mtg->format($row['qty']);?>" />
						</div>
						<div class="pure-control-group">
							<button type="submit" name="ans" class="pure-button pure-button-primary">Purchase points</button>
							<button type="reset" class="pure-button pure-button-secondary">Reset</button>
						</div>
					</form><?php
				}
				break;
			case 'gift':
				if(empty($_GET['ID']))
					$mtg->error('You didn\'t specify a valid listing');
				$db->query('SELECT `price`, `qty`, `user` FROM `market_points` WHERE `id` = ?');
				$db->execute([$_GET['ID']]);
				if(!$db->num_rows())
					$mtg->error('That listing doesn\'t exist');
				$row = $db->fetch_row(true);
				if($row['user'] == $my['id'])
					$mtg->error('You can\'t purchase your own listing');
				if(array_key_exists('ans', $_POST)) {
					$_POST['amnt'] = isset($_POST['amnt']) && ctype_digit(str_replace(',', '', $_POST['amnt'])) ? str_replace(',', '', $_POST['amnt']) : $row['qty'];
					if($_POST['amnt'] > $row['qty'])
						$mtg->error('You can\'t buy that many points from this listing');
					$cost = $_POST['amnt'] * $row['price'];
					if($cost > $my['money'])
						$mtg->error('You don\'t have enough cash');
					$_POST['user'] = isset($_POST['user']) && ctype_digit($_POST['user']) ? $_POST['user'] : null;
					if(empty($_POST['user']))
						$mtg->error('You didn\'t select a valid recipient');
					$db->query('SELECT `id` FROM `users` WHERE `id` = ?');
					$db->execute([$_POST['user']]);
					if(!$db->num_rows())
						$mtg->error('The recipient you selected doesn\'t exist');
					$db->startTrans();
					if($_POST['amnt'] == $row['qty']) {
						$db->query('DELETE FROM `market_points` WHERE `id` = ?');
						$db->execute([$_GET['ID']]);
					} else {
						$db->query('UPDATE `market_points` SET `qty` = `qty` - ? WHERE `id` = ?');
						$db->execute([$_POST['amnt'], $_GET['ID']]);
					}
					$db->query('UPDATE `users_finances` SET `money` = `money` - ? WHERE `id` = ?');
					$db->execute([$_POST['amnt'], $cost, $my['id']]);
					$db->query('UPDATE `users_finances` SET `money` = `money` + ? WHERE `id` = ?');
					$db->execute([$cost, $row['user']]);
					$db->query('UPDATE `users_finances` SET `points`= `points` + ? WHERE `id` = ?');
					$db->execute([$_POST['amnt'], $_POST['user']]);
					$out = $_POST['amnt'] == $row['qty'] ? 'all of the points' : $mtg->format($_POST['amnt']).' point'.$mtg->s($_POST['amnt']);
					$users->send_event($row['user'], '{id} purchased '.str_replace('the', 'your', $out).' from the Points Market', $my['id']);
					$users->send_event($_POST['user'], '{id} purchased '.$mtg->format($_POST['amnt']).' point'.$mtg->s($_POST['amnt']).' from the market and gifted them to you', $my['id']);
					$db->endTrans();
					$mtg->success('You\'ve purchased '.$out.' from '.$users->name($row['user']).'\'s listing on the market');
				} else {
					?><form action="markets.php?action=points&amp;sub=gift&amp;ID=<?php echo $_GET['ID'];?>" method="post" class="pure-form">
						<div class="pure-control-group">
							<label for="amount">Amount to buy (leave blank to buy all)</label>
							<input type="text" name="amnt" placeholder="<?php echo $mtg->format($row['qty']);?>" />
						</div>
						<div class="pure-control-group">
							<label for="player">Recipient</label>
							<?php echo $users->selectList('user', false, [$my['id']]);?>
						</div>
						<div class="pure-control-group">
							<button type="submit" name="ans" class="pure-button pure-button-primary">Purchase and gift points</button>
							<button type="reset" class="pure-button pure-button-secondary">Reset</button>
						</div>
					</form><?php
				}
				break;
		}
		break;
	case 'items':
		$mtg->info('Coming soon!');
		break;
}