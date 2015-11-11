<?php
if(!defined('MTG_ENABLE'))
	exit;
?><ul class="pure-menu-list"><?php
$links = array(
	'index.php' => 'Game',
	'staff' => 'Staff Index',
	'staff/?pull=ranks' => 'Staff Rank Management',
	'staff/?pull=ranks&amp;action=currentstaff' => 'Staff Management',
	'staff/?pull=ranks&amp;action=set' => 'Set Player Staff Level',
	'staff?action=settings' => 'Settings'
);
foreach($links as $url => $disp)
	printf('<li class="pure-menu-item"><a href="%s" class="pure-menu-link%s">%s</a></li>'."\n", $url, $_SERVER['PHP_SELF'] == '/'.$url.'.php' ? ' pure-menu-selected' : null, $disp);
?><li class="pure-menu-item menu-item-divided"><a href='?action=logout' class="pure-menu-link">Logout</a></li>
</ul>