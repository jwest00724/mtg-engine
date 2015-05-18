<?php
require_once(__DIR__ . '/includes/globals_out.php');
if(isset($_SESSION['msg'])) {
	$mtg->error($_SESSION['msg'], false);
	unset($_SESSION['msg']);
}
?><form action="auth.php" method="post">
	<label>Username:</label>
	<div class="login_height"><input type="text" name="username" autofocus="autofocus" /></div>
	<label>Password:</label>
	<div class="login_height2"><input type="password" name="password" value="" /></div>
	<div class="input_button"><input type="submit" value="login" /></div>
</form>