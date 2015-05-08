<?php
require_once(__DIR__ . '/header.php');
?><div class="header">
	<h1>MTG Codes v9</h1>
	<h2>Welcome to MTG Codes v9's very own installer!</h2>
</div>
<div class="content">
	<h2 class="content-subhead">How to use this installer</h2><?php
	if(file_exists(DIRNAME(__DIR__) . '/includes/config.php'))
		echo "<div class='notification notification-info'><i class='fa fa-info-circle'></i><p>It looks like your game may have already been installed.. Please check that <code>/includes/config.php</code> contains the correct information</p></div>";
	?><p>
		Over the next few steps, you're going to install MTG Codes v9.<br />
		Seeing as you're probably reading this from a browser, I'm going to assume that you've already uploaded all files and have read the steps in the README to reach this place - if not, <strong>go read them now!</strong><br /><br />
		Most of the info you're about to fill in can be easily changed in-game. The database configuration settings, however, require you to edit <code>/includes/config.php</code>.<br /><br />
		Don't worry too much, the installer's requirements speak for themselves as you reach them (minus the obvious ones!)
	</p>
	<h2 class='content-subhead'>Ready to get started?</h2>
	<p>
		Simply click on "Install" on the left-hand menu!
	</p>
</div>