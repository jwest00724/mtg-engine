<?php
require_once(__DIR__ . '/includes/globals_out.php');
if(isset($_SESSION['msg'])) {
	$mtg->error($_SESSION['msg'], false);
	unset($_SESSION['msg']);
}
?><form action='auth.php' method='post' class='pure-form pure-form-aligned'>
	<fieldset>
		<legend>Login</legend>
		<div class='pure-control-group'>
			<label for='username'>Username</label>
			<input type='text' name='username' placeholder='Username' />
		</div>
		<div class='pure-control-group'>
			<label for='password'>Password</label>
			<input type='password' name='password' placeholder='Password' />
		</div>
		<input type='submit' class='pure-button pure-button-primary' value='Sign in' />
	</fieldset>
</form>