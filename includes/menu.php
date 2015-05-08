<?php
if(!defined('MTG_ENABLE'))
	exit;
$links = array(
	'index' => 'Home',
	'crimes' => 'Crimes',
	'gym' => 'Gym',
);
?><div id='side-menu'>
	<ul><?php
		foreach($links as $url => $disp)
			printf("<li%s><a href='%s.php'>%s</a></li>", $_SERVER['PHP_SELF'] == '/'.$url.'.php' ? " class='current_page_item'" : '', $url, $disp);
	?></ul><?php
	if($users->hasAccess('staff_panel_access')) {
		?><ul>
			<li><a href='staff.php'>Staff Panel</a></li>
		</ul><?php
	}
?></div>