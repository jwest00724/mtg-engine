<?php
define('HEADER_TEXT', 'Marketplace');
require_once __DIR__ . '/includes/globals.php';
require_once __DIR__ . '/includes/class/class_mtg_paginate.php';
$pages = new Paginator();
$_GET['ID'] = isset($_GET['ID']) && ctype_digit($_GET['ID']) ? $_GET['ID'] : null;
$_GET['action'] = isset($_GET['action']) && ctype_alpha($_GET['action']) ? strtolower(trim($_GET['action'])) : null;
$_GET['sub'] = isset($_GET['sub']) && ctype_alpha($_GET['sub']) ? strtolower(trim($_GET['sub'])) : null;
?><div class="content-menu">
	<a href="markets.php?action=points"<?php echo $_GET['action'] == 'points' ? ' class="bold"' : '';?>>Points Market</a> &middot;
	<a href="markets.php?action=items"<?php echo $_GET['action'] == 'items' ? ' class="bold"' : '';?>>Items Market</a>
</div><?php
switch($_GET['action']) {
	case 'points':
		switch($_GET['sub']) {
			default:
				if(array_key_exists('msg', $_SESSION)) {
					$mtg->success($_SESSION['msg']);
					unset($_SESSION['msg']);
				}
				$db->query('SELECT COUNT(`id`) FROM `market_points`');
				$db->execute();
				$pages->items_total = $db->fetch_single();
				$pages->mid_range = 3;
				$pages->paginate();
				$db->query('SELECT `id`, `user`, `qty`, `price` FROM `market_points` ORDER BY `price` * `qty` DESC '.$pages->limit);
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
								<td><?php echo $set['main_currency_symbol'].$mtg->format($row['price']);?> each<br /><span class="small">(<?php echo $set['main_currency_symbol'].$mtg->format($row['price'] * $row['qty']);?> total)</td>
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
					$_POST['qty'] = isset($_POST['qty']) && ctype_digit(str_replace(',', '', $_POST['qty'])) ? str_replace(',', '', $_POST['qty']) : $my['points'];
					if($_POST['qty'] > $my['points'])
						$mtg->error('You don\'t have that many points');
					$db->startTrans();
					$db->query('UPDATE `users_finances` SET `points` = `points` - ? WHERE `id` = ?');
					$db->execute([$_POST['qty'], $my['id']]);
					$db->query('INSERT INTO `market_points` (`qty`, `user`, `price`) VALUES (?, ?, ?)');
					$db->execute([$_POST['qty'], $my['id'], $_POST['price']]);
					$db->endTrans();
					$_SESSION['msg'] = 'You\'ve listed '.$mtg->format($_POST['qty']).' point'.$mtg->s($_POST['qty']).' on the market at '.$set['main_currency_symbol'].$mtg->format($_POST['price']).' each ('.$set['main_currency_symbol'].$mtg->format($_POST['price'] * $_POST['qty']).' total)';
					exit(header('Location: markets.php?action=points'));
				}
				?><form action="markets.php?action=points&amp;sub=add" method="post" class="pure-form pure-form-aligned">
					<div class="pure-control-group">
						<label for="quantity">Quantity (leave blank for all)</label>
						<input type="text" name="qty" placeholder="<?php echo $mtg->format($my['points']);?>" />
					</div>
					<div class="pure-control-group">
						<label for="price">Price each</label>
						<input type="text" name="price" />
					</div>
					<div class="pure-controls">
						<button type="submit" name="submit" class="pure-button pure-button-primary">List your points</button>
						<button type="reset" class="pure-button pure-button-secondary"><i class="fa fa-recycle"></i> Reset</button>
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
					$_SESSION['msg'] = 'You\'ve removed '.($_POST['amnt'] == $row['qty'] ? 'all of your points' : $mtg->format($_POST['amnt']).' point'.$mtg->s($_POST['amnt'])).' from the market';
					exit(header('Location: markets.php?action=points'));
				} else {
					?><form action="markets.php?action=points&amp;sub=remove&amp;ID=<?php echo $_GET['ID'];?>" method="post" class="pure-form pure-form-aligned">
						<div class="pure-control-group">
							<label for="amount">Amount to remove (leave blank to remove all)</label>
							<input type="text" name="amnt" placeholder="<?php echo $mtg->format($row['qty']);?>" />
						</div>
						<div class="pure-control-group">
							<button type="submit" name="ans" class="pure-button pure-button-primary">Remove points</button>
						<button type="reset" class="pure-button pure-button-secondary"><i class="fa fa-recycle"></i> Reset</button>
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
					$_SESSION['msg'] = 'You\'ve purchased '.$out.' from '.$users->name($row['user']).'\'s listing on the market';
					exit(header('Location: markets.php?action=points'));
				} else {
					?><form action="markets.php?action=points&amp;sub=buy&amp;ID=<?php echo $_GET['ID'];?>" method="post" class="pure-form pure-form-aligned">
						<div class="pure-control-group">
							<label for="amount">Amount to buy (leave blank to buy all)</label>
							<input type="text" name="amnt" placeholder="<?php echo $mtg->format($row['qty']);?>" />
						</div>
						<div class="pure-control-group">
							<button type="submit" name="ans" class="pure-button pure-button-primary">Purchase points</button>
						<button type="reset" class="pure-button pure-button-secondary"><i class="fa fa-recycle"></i> Reset</button>
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
					if($_POST['user'] == $my['id'])
						$mtg->error('You can\'t gift points to yourself');
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
					$_SESSION['msg'] = 'You\'ve purchased '.$out.' from '.$users->name($row['user']).'\'s listing on the market';
					exit(header('Location: markets.php?action=points'));
				} else {
					?><form action="markets.php?action=points&amp;sub=gift&amp;ID=<?php echo $_GET['ID'];?>" method="post" class="pure-form pure-form-aligned">
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
						<button type="reset" class="pure-button pure-button-secondary"><i class="fa fa-recycle"></i> Reset</button>
						</div>
					</form><?php
				}
				break;
		}
		break;
	case 'items':
		switch($_GET['sub']) {
			default:
				if(array_key_exists('msg', $_SESSION)) {
					$mtg->success($_SESSION['msg']);
					unset($_SESSION['msg']);
				}
				$db->query('SELECT COUNT(`id`) FROM `market_items`');
				$db->execute();
				$pages->items_total = $db->fetch_single();
				$pages->mid_range = 3;
				$pages->paginate();
				$db->query('SELECT `market_items`.`id`, `item`, `user`, `price`, `qty`, `currency`, `name`
					FROM `market_items`
					LEFT JOIN `items` ON `market_items`.`item` = `items`.`id`
					ORDER BY FIELD(`currency`, "money", "points") ASC, `price` * `qty` DESC '.$pages->limit);
				$db->execute();
				?><h4><a href="markets.php?action=items&amp;sub=add">Add a Listing</a></h4>
				<p class="paginate"><?php echo $pages->display_pages();?></p>
				<table width="100%" class="pure-table pure-table-striped">
					<tr>
						<th width="25%">Seller</th>
						<th width="25%">Item</th>
						<th width="25%">Price</th>
						<th width="25%">Actions</th>
					</tr><?php
					if(!$db->num_rows())
						echo '<tr><td colspan="4">There are no listings</td></tr>';
					else {
						$rows = $db->fetch_row();
						foreach($rows as $row) {
							$total = $row['price'] * $row['qty'];
							?><tr>
								<td><?php echo $users->name($row['user'], true);?></td>
								<td><?php echo $mtg->format($row['qty']);?>x <?php echo $items->name($row['item']);?></td>
								<td><?php echo $row['currency'] == 'money'
									? $set['main_currency_symbol'].$mtg->format($row['price'])
									: $mtg->format($row['price']).' point'.$mtg->s($row['price']);
								?> each<br /><span class="small">(<?php echo $row['currency'] == 'money' ? $set['main_currency_symbol'].$mtg->format($total) : $mtg->format($total).' point'.$mtg->s($total);?> total)</td>
								<td><?php echo $row['user'] == $my['id']
									? '<a href="markets.php?action=items&amp;sub=remove&amp;ID='.$row['id'].'">Remove</a>'
									: '<a href="markets.php?action=items&amp;sub=buy&amp;ID='.$row['id'].'">Purchase</a> &middot; <a href="markets.php?action=items&amp;sub=gift&amp;ID='.$row['id'].'">Gift</a>';
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
					$_POST['currency'] = isset($_POST['currency']) && in_array($_POST['currency'], ['money', 'points']) ? $_POST['currency'] : null;
					if(empty($_POST['currency']))
						$mtg->error('You didn\'t select a valid currency');
					$_POST['item'] = isset($_POST['item']) && ctype_digit($_POST['item']) ? $_POST['item'] : null;
					if(empty($_POST['item']))
						$mtg->error('You didn\'t select a valid item');
					$db->query('SELECT `name` FROM `items` WHERE `id` = ?');
					$db->execute([$_POST['item']]);
					if(!$db->num_rows())
						$mtg->error('That item doesn\'t exist');
					$item = $mtg->format($db->fetch_single());
					$db->query('SELECT `qty` FROM `inventory` WHERE `user` = ? AND `item` = ?');
					$db->execute([$my['id'], $_POST['item']]);
					if(!$db->num_rows())
						$mtg->error('You don\'t have any '.$item.$mtg->s(2, $item));
					$qty = $db->fetch_single();
					$_POST['qty'] = isset($_POST['qty']) && ctype_digit(str_replace(',', '', $_POST['qty'])) ? str_replace(',', '', $_POST['qty']) : $qty;
					if($_POST['qty'] > $qty)
						$mtg->error('You don\'t have '.$mtg->format($_POST['qty']).' '.$item.$mtg->s(2, $item));
					$db->startTrans();
					$users->takeItem($my['id'], $_POST['item'], $_POST['qty']);
					$db->query('INSERT INTO `market_items` (`item`, `qty`, `user`, `price`, `currency`) VALUES (?, ?, ?, ?, ?)');
					$db->execute([$_POST['item'], $_POST['qty'], $my['id'], $_POST['price'], $_POST['currency']]);
					$db->endTrans();
					$each = $_POST['currency'] == 'money' ? $set['main_currency_symbol'].$mtg->format($_POST['price']) : $mtg->format($_POST['price']).' point'.$mtg->s($_POST['price']);
					$total = $_POST['currency'] == 'money' ? $set['main_currency_symbol'].$mtg->format($_POST['price'] * $_POST['qty']) : $mtg->format($_POST['price'] * $_POST['qty']).' point'.$mtg->s($_POST['price'] * $_POST['qty']);
					$_SESSION['msg'] = 'You\'ve listed '.$mtg->format($_POST['qty']).' '.$item.$mtg->s($_POST['qty'], $item).' on the market at '.$each.' each ('.$total.' total)';
					exit(header('Location: markets.php?action=items'));
				}
				?><form action="markets.php?action=items&amp;sub=add" method="post" class="pure-form pure-form-aligned">
					<div class="pure-control-group">
						<label for="item">Item</label>
						<?php echo $users->listInventory($my['id']);?>
					</div>
					<div class="pure-control-group">
						<label for="quantity">Quantity (leave blank for all)</label>
						<input type="text" name="qty" />
					</div>
					<div class="pure-control-group">
						<label for="price">Price each</label>
						<input type="text" name="price" required />
					</div>
					<div class="pure-control-group">
						<label for="currency">Currency</label>
						<label for="money" class="pure-radio">
							<input id="money" type="radio" name="currency" value="money" checked="checked" /> Cash
						</label>
						<label for="points" class="pure-radio">
							<input id="points" type="radio" name="currency" value="points" /> Points
						</label>
					</div>
					<div class="pure-controls">
						<button type="submit" name="submit" class="pure-button pure-button-primary">List your item</button>
						<button type="reset" class="pure-button pure-button-secondary"><i class="fa fa-recycle"></i> Reset</button>
					</div>
				</form><?php
				break;
			case 'remove':
				if(empty($_GET['ID']))
					$mtg->error('You didn\'t select a valid listing');
				$db->query('SELECT `item`, `user`, `qty`, `price`, `items`.`name`
					FROM `market_items`
					LEFT JOIN `items` ON `market_items`.`item` = `items`.`id`
					WHERE `market_items`.`id` = ?');
				$db->execute([$_GET['ID']]);
				if(!$db->num_rows())
					$mtg->error('That listing doesn\'t exist');
				$row = $db->fetch_row(true);
				if($row['user'] != $my['id'])
					$mtg->error('This isn\'t your listing');
				if($row['qty'] == 1) {
					$_POST['amnt'] = 1;
					$_POST['ans'] = true;
				}
				if(array_key_exists('ans', $_POST)) {
					$_POST['amnt'] = isset($_POST['amnt']) && ctype_digit(str_replace(',', '', $_POST['amnt'])) ? str_replace(',', '', $_POST['amnt']) : $row['qty'];
					if($_POST['amnt'] > $row['qty'])
						$mtg->error('You can\'t remove that many '.$mtg->format($row['name']).$mtg->s(2, $row['name']).' from this listing');
					$db->startTrans();
					if($_POST['amnt'] == $row['qty']) {
						$db->query('DELETE FROM `market_items` WHERE `id` = ?');
						$db->execute([$_GET['ID']]);
					} else {
						$db->query('UPDATE `market_items` SET `qty` = `qty` - ? WHERE `id` = ?');
						$db->execute([$_POST['amnt'], $_GET['ID']]);
					}
					$users->giveItem($row['item'], $my['id'], $_POST['amnt']);
					$db->endTrans();
					$_SESSION['msg'] = 'You\'ve removed '.($_POST['amnt'] == $row['qty'] ? 'all' : $mtg->format($_POST['amnt'])).' of your '.$mtg->format($row['name']).$mtg->s(2, $row['name']).' from the market';
					exit(header('Location: markets.php?action=items'));
				} else {
					?><form action="markets.php?action=items&amp;sub=remove&amp;ID=<?php echo $_GET['ID'];?>" method="post" class="pure-form pure-form-aligned">
						<div class="pure-control-group">
							<label for="amount">Amount to remove (leave blank to remove all)</label>
							<input type="text" name="amnt" placeholder="<?php echo $mtg->format($row['qty']);?>" />
						</div>
						<div class="pure-control-group">
							<button type="submit" name="ans" class="pure-button pure-button-primary">Remove item</button>
						<button type="reset" class="pure-button pure-button-secondary"><i class="fa fa-recycle"></i> Reset</button>
						</div>
					</form><?php
				}
				break;
			case 'buy':
				if(empty($_GET['ID']))
					$mtg->error('You didn\'t specify a valid listing');
				$db->query('SELECT `price`, `qty`, `item`, `user`, `currency`, `name`
					FROM `market_items`
					LEFT JOIN `items` ON `market_items`.`item` = `items`.`id`
					WHERE `market_items`.`id` = ?');
				$db->execute([$_GET['ID']]);
				if(!$db->num_rows())
					$mtg->error('That listing doesn\'t exist');
				$row = $db->fetch_row(true);
				if($row['user'] == $my['id'])
					$mtg->error('You can\'t purchase your own listing');
				$item = $mtg->format($row['name']).$mtg->s(2, $row['name']);
				if(array_key_exists('ans', $_POST)) {
					$_POST['amnt'] = isset($_POST['amnt']) && ctype_digit(str_replace(',', '', $_POST['amnt'])) ? str_replace(',', '', $_POST['amnt']) : $row['qty'];
					if($_POST['amnt'] > $row['qty'])
						$mtg->error('You can\'t buy that many '.$item.' from this listing');
					$cost = $_POST['amnt'] * $row['price'];
					if($cost > $my[$row['currency']])
						$mtg->error('You don\'t have enough '.$row['currency']);
					$out = $_POST['amnt'] == $row['qty'] ? 'all' : $mtg->format($_POST['amnt']);
					$db->startTrans();
					if($_POST['amnt'] == $row['qty']) {
						$db->query('DELETE FROM `market_items` WHERE `id` = ?');
						$db->execute([$_GET['ID']]);
					} else {
						$db->query('UPDATE `market_items` SET `qty` = `qty` - ? WHERE `id` = ?');
						$db->execute([$_POST['amnt'], $_GET['ID']]);
					}
					$db->query('UPDATE `users_finances` SET `'.$row['currency'].'` = `'.$row['currency'].'` - ? WHERE `id` = ?');
					$db->execute([$cost, $my['id']]);
					$db->query('UPDATE `users_finances` SET `'.$row['currency'].'` = `'.$row['currency'].'` + ? WHERE `id` = ?');
					$db->execute([$cost, $row['user']]);
					$users->giveItem($row['item'], $my['id'], $_POST['amnt']);
					$users->send_event($row['user'], '{id} purchased '.$out.' of your '.$item.' from the Items Market', $my['id']);
					$db->endTrans();
					$_SESSION['msg'] = 'You\'ve purchased '.$out.' of the '.$item.' from '.$users->name($row['user']).'\'s listing on the market';
					exit(header('Location: markets.php?action=items'));
				} else {
					?><form action="markets.php?action=items&amp;sub=buy&amp;ID=<?php echo $_GET['ID'];?>" method="post" class="pure-form pure-form-aligned">
						<div class="pure-control-group">
							<label for="amount">Amount to buy (leave blank to buy all)</label>
							<input type="text" name="amnt" placeholder="<?php echo $mtg->format($row['qty']);?>" />
						</div>
						<div class="pure-control-group">
							<button type="submit" name="ans" class="pure-button pure-button-primary">Purchase points</button>
						<button type="reset" class="pure-button pure-button-secondary"><i class="fa fa-recycle"></i> Reset</button>
						</div>
					</form><?php
				}
				break;
			case 'gift':
				if(empty($_GET['ID']))
					$mtg->error('You didn\'t specify a valid listing');
				$db->query('SELECT `price`, `qty`, `item`, `user`, `currency`, `name`
					FROM `market_items`
					LEFT JOIN `items` ON `market_items`.`item` = `items`.`id`
					WHERE `market_items`.`id` = ?');
				$db->execute([$_GET['ID']]);
				if(!$db->num_rows())
					$mtg->error('That listing doesn\'t exist');
				$row = $db->fetch_row(true);
				if($row['user'] == $my['id'])
					$mtg->error('You can\'t purchase your own listing');
				$item = $mtg->format($row['name']).$mtg->s(2, $row['name']);
				if(array_key_exists('ans', $_POST)) {
					$_POST['user'] = isset($_POST['user']) && ctype_digit($_POST['user']) ? $_POST['user'] : null;
					if(empty($_POST['user']))
						$mtg->error('You didn\'t select a valid recipient');
					$db->query('SELECT `id` FROM `users` WHERE `id` = ?');
					$db->execute([$_POST['user']]);
					if(!$db->num_rows())
						$mtg->error('That player doesn\'t exist');
					$_POST['amnt'] = isset($_POST['amnt']) && ctype_digit(str_replace(',', '', $_POST['amnt'])) ? str_replace(',', '', $_POST['amnt']) : $row['qty'];
					if($_POST['amnt'] > $row['qty'])
						$mtg->error('You can\'t buy that many '.$item.' from this listing');
					$cost = $_POST['amnt'] * $row['price'];
					if($cost > $my[$row['currency']])
						$mtg->error('You don\'t have enough '.$row['currency']);
					$db->startTrans();
					if($_POST['amnt'] == $row['qty']) {
						$db->query('DELETE FROM `market_items` WHERE `id` = ?');
						$db->execute([$_GET['ID']]);
					} else {
						$db->query('UPDATE `market_items` SET `qty` = `qty` - ? WHERE `id` = ?');
						$db->execute([$_POST['amnt'], $_GET['ID']]);
					}
					$db->query('UPDATE `users_finances` SET `'.$row['currency'].'` = `'.$row['currency'].'` - ? WHERE `id` = ?');
					$db->execute([$cost, $my['id']]);
					$db->query('UPDATE `users_finances` SET `'.$row['currency'].'` = `'.$row['currency'].'` + ? WHERE `id` = ?');
					$db->execute([$cost, $row['user']]);
					$out = $_POST['amnt'] == $row['qty'] ? 'all' : $mtg->format($_POST['amnt']);
					$users->giveItem($row['item'], $_POST['user'], $_POST['amnt']);
					$users->send_event($row['user'], '{id} purchased '.$out.' of your '.$item.' from the Items Market', $my['id']);
					$users->send_event($_POST['user'], '{id} purchased '.$out.' '.$item.' from the Items Market and gifted '.($_POST['amnt'] == 1 ? 'it' : 'them').' to you', $my['id']);
					$db->endTrans();
					$_SESSION['msg'] = 'You\'ve purchased '.$out.' of the '.$mtg->format($row['name']).$mtg->s($_POST['amnt'], $row['name']).' from '.$users->name($row['user']).'\'s listing on the market';
					exit(header('Location: markets.php?action=items'));
				} else {
					?><form action="markets.php?action=items&amp;sub=buy&amp;ID=<?php echo $_GET['ID'];?>" method="post" class="pure-form pure-form-aligned">
						<div class="pure-control-group">
							<label for="amount">Amount to buy (leave blank to buy all)</label>
							<input type="text" name="amnt" placeholder="<?php echo $mtg->format($row['qty']);?>" />
						</div>
						<div class="pure-control-group">
							<label for="player">Recipient</label>
							<?php echo $users->selectList('user', false, [$my['id']]);?>
						</div>
						<div class="pure-control-group">
							<button type="submit" name="ans" class="pure-button pure-button-primary">Purchase points</button>
						<button type="reset" class="pure-button pure-button-secondary"><i class="fa fa-recycle"></i> Reset</button>
						</div>
					</form><?php
				}
				break;
		}
		break;
}