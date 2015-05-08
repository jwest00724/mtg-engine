<?php
require_once(__DIR__ . '/includes/globals_out.php');
if(isset($_SESSION['msg'])) {
	$mtg->error($_SESSION['msg'], false);
	unset($_SESSION['msg']);
}
?><form action='auth.php' method='post' class='pure-form pure-form-aligned'>
	<fieldset>
		<legend>Login here</legend>
		<label for='username'><input type='text' name='username' placeholder='Username' /></label>
		<label for='password'><input type='password' name='password' placeholder='Password' /></label>
		<input type='submit' class='pure-button pure-button-primary' value='Sign in' />
	</fieldset>
</form>