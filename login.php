<?php
require_once __DIR__ . '/includes/globals_out.php';
?><div class="header">
	<div class="logo"></div>
	<h1>Welcome to <?php echo $mtg->format($set['game_name']);?></h1>
	<h2 class="content-subhead">Please login</h2>
</div>
<div class="content"><?php
	if(isset($_SESSION['msg'])) {
		switch($_SESSION['msg']['type']) {
			case 'error':
				$mtg->error($_SESSION['msg']['content'], false);
				break;
			case 'success':
				$mtg->success($_SESSION['msg']['content']);
				break;
		}
		unset($_SESSION['msg']);
	}
	?><form action="auth.php" method="post" class="pure-form pure-form-aligned">
		<fieldset>
			<div class="pure-control-group">
				<label>Username</label>
				<input type="text" name="username" autofocus="autofocus" required />
			</div>
			<div class="pure-control-group">
				<label>Password</label>
				<input type="password" name="password" required />
			</div>
			<div class="pure-controls">
				<button type="submit" name="submit" value="true" class="pure-button pure-button-primary"><i class="fa fa-chevron-right"></i> Login</button>
			</div>
		</fieldset>
	</form>
	Not got an account? <a href="signup.php">Sign up for free</a> &middot; Or have you forgotten your password? <a href="reset.php">Reset it here!</a>
</div>