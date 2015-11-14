<?php
define('HEADER_TEXT', 'Bank');
require_once __DIR__ . '/includes/globals.php';
if(!$set['bank_enabled'])
	$mtg->error('The bank is currently closed');
if($my['bank'] < 0) {
	if(!array_key_exists('submit', $_POST)) {
		?><form action="bank.php" method="post" class="pure-form">
			<legend>
				Would you like to open a bank account?<br />
				It'll cost you <?php echo $set['main_currency_symbol'].$mtg->format($set['bank_cost']);?>
			</legend>
			<button type="submit" name="submit" class="pure-button pure-button-primary"<?php echo $set['bank_cost'] > $my['money'] ? ' disabled' : '';?>>Yes, open a new account</button>
		</form><?php
	} else {
		if($set['bank_cost'] > $my['money'])
			$mtg->error('You don\'t have enough cash');
		$db->query('UPDATE `users_finances` SET `bank` = 0, `money` = `money` - ? WHERE `id` = ?');
		$db->execute([$set['bank_cost'], $my['id']]);
		$_SESSION['msg'] = 'You\'ve opened a new bank account';
		exit(header('Location: bank.php'));
	}
} else {
	$_POST['amount'] = array_key_exists('amount', $_POST) && ctype_digit(str_replace(',', '', $_POST['amount'])) ? str_replace(',', '', $_POST['amount']) : null;
	$_GET['action'] = array_key_exists('action', $_GET) && ctype_alpha($_GET['action']) ? strtolower(trim($_GET['action'])) : null;
	switch($_GET['action']) {
		case 'deposit':
			bankDeposit($db, $my, $mtg, $set);
			break;
		case 'withdraw':
			bankWithdraw($db, $my, $mtg, $set);
			break;
		default:
			bankIndex($my, $mtg);
			break;
	}
}
function bankIndex($my, $mtg) {
	?><h3 class="content-subhead">Your bank account</h3><?php
	if(array_key_exists('msg', $_SESSION)) {
		$mtg->success($_SESSION['msg']);
		unset($_SESSION['msg']);
	}
	?><form action="bank.php?action=deposit" method="post" class="pure-form pure-form-aligned">
		<legend>Make a deposit</legend>
		<div class="pure-control-group">
			<label for="deposit">Leave blank to deposit all cash on hand</label>
			<input type="text" name="amount" placeholder="<?php echo $mtg->format($my['money']);?>" />
		</div>
		<div class="pure-controls">
			<button type="submit" name="deposit" class="pure-button pure-button-primary"><i class="fa fa-arrow-right"></i> Deposit</button>
			<button type="reset" class="pure-button pure-button-secondary"><i class="fa fa-recycle"></i> Reset</button>
		</div>
	</form>
	<form action="bank.php?action=withdraw" method="post" class="pure-form pure-form-aligned">
		<legend>Make a withdrawal</legend>
		<div class="pure-control-group">
			<label for="deposit">Leave blank to withdraw all cash in your bank</label>
			<input type="text" name="amount" placeholder="<?php echo $mtg->format($my['bank']);?>" />
		</div>
		<div class="pure-controls">
			<button type="submit" name="withdraw" class="pure-button pure-button-primary"><i class="fa fa-arrow-left"></i> Withdraw</button>
			<button type="reset" class="pure-button pure-button-secondary"><i class="fa fa-recycle"></i> Reset</button>
		</div>
	</form><?php
}
function bankDeposit($db, $my, $mtg, $set) {
	if(!array_key_exists('deposit', $_POST))
		$mtg->error('You can\'t walk directly into the vault');
	if(empty($_POST['amount']))
		$_POST['amount'] = $my['money'];
	if($_POST['amount'] > $my['money'])
		$mtg->error('You don\'t have enough cash on hand');
	$db->query('UPDATE `users_finances` SET `bank` = `bank` + ?, `money` = `money` - ? WHERE `id` = ?');
	$db->execute([$_POST['amount'], $_POST['amount'], $my['id']]);
	$my['money'] -= $_POST['amount'];
	$my['bank'] += $_POST['amount'];
	$_SESSION['msg'] = 'You\'ve deposited '.$set['main_currency_symbol'].$mtg->format($_POST['amount']).' into your bank account. You now have '.$set['main_currency_symbol'].$mtg->format($my['money']).' on hand and '.$set['main_currency_symbol'].$mtg->format($my['bank']).' in your bank account';
	exit(header('Location: bank.php'));
}
function bankWithdraw($db, $my, $mtg, $set) {
	if(!array_key_exists('withdraw', $_POST))
		$mtg->error('You can\'t walk directly into the vault');
	if(empty($_POST['amount']))
		$_POST['amount'] = $my['bank'];
	if($_POST['amount'] > $my['bank'])
		$mtg->error('You don\'t have enough cash in your bank account');
	$db->query('UPDATE `users_finances` SET `bank` = `bank` - ?, `money` = `money` + ? WHERE `id` = ?');
	$db->execute([$_POST['amount'], $_POST['amount'], $my['id']]);
	$my['money'] += $_POST['amount'];
	$my['bank'] -= $_POST['amount'];
	$_SESSION['msg'] = 'You\'ve withdrawn '.$set['main_currency_symbol'].$mtg->format($_POST['amount']).' from your bank account. You now have '.$set['main_currency_symbol'].$mtg->format($my['money']).' on hand and '.$set['main_currency_symbol'].$mtg->format($my['bank']).' in your bank account';
	exit(header('Location: bank.php'));
}