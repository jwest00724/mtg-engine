<?php
define('HEADER_TEXT', 'Marketplace');
require_once __DIR__ . '/includes/globals.php';
require_once __DIR__ . '/includes/class/class_mtg_paginate.php';
$pages = new Paginator();
$_GET['ID'] = isset($_GET['ID']) && ctype_digit($_GET['ID']) ? $_GET['ID'] : null;
$_GET['action'] = isset($_GET['action']) && ctype_alpha($_GET['action']) ? strtolower(trim($_GET['action'])) : null;
$_GET['sub'] = isset($_GET['sub']) && ctype_alpha($_GET['sub']) ? strtolower(trim($_GET['sub'])) : null;
switch($_GET['action']) {
	case 'points':
		switch($_GET['sub']) {
			default:
				$db->query("SELECT COUNT(`id`) FROM `markets_points`");
				$db->execute();
				$pages->items_total = $db->fetch_single();
				$pages->mid_range = 3;
				$pages->paginate();
				$db->query("SELECT * FROM `markets_points` ORDER BY `time_added` DESC ".$pages->limit);
				$db->execute();
				?><h4><a href='markets.php?action=points&amp;sub=add'>Add a Listing</a></h4>
				<p class='paginate'><?php echo $pages->display_pages();?></p>
				<table width='100%' class='pure-table pure-table-striped'>
					<tr>
						<th width='25%'>Seller</th>
						<th width='25%'>Amount</th>
						<th width='25%'>Price</th>
						<th width='25%'>Actions</th>
					</tr><?php
					if(!$db->num_rows())
						echo "<tr><td colspan='4'>There are no listings</td></tr>";
					else {
						$rows = $db->fetch_row();
						foreach($rows as $row) {
							?><tr>
								<td><?php echo $users->name($row['user'], true);?></td>
								<td><?php echo $mtg->format($row['qty']);?></td>
								<td><?php echo $row['currency'] == 'money'
									? $set['main_currency_symbol'].$mtg->format($row['price'])
									: $mtg->format($row['price']).' point'.$mtg->s($row['price']);
								?></td>
								<td><?php echo $row['user'] == $my['id']
									? "<a href='markets.php?action=points&amp;sub=remove&amp;ID=".$row['id']."'>Remove</a>"
									: "<a href='markets.php?action=points&amp;sub=buy&amp;ID=".$row['id']."'>Purchase</a> &middot; <a href='markets.php?action=points&amp;sub=gift&amp;ID=".$row['id']."'>Gift</a> &middot; <a href='markets.php?action=points&amp;sub=offer&amp;ID=".$row['id']."'>Make an Offer</a>";
								?></td>
							</tr><?php
						}
					}
				?></table>
				<p class='paginate'><?php echo $pages->display_pages();?></p><?php
				break;
		}
		break;
	case 'items':
		$mtg->info("Coming soon");
		break;
	default:
		?><a href='markets.php?action=points'>Points Market</a> &middot; <a href='markets.php?action=items'>Items Market</a><?php
		break;
}