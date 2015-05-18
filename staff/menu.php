<?php
if(!defined('MTG_ENABLE'))
	exit;
$links = array(
	'index.php' => 'Game',
	'staff' => 'Staff Index',
	'staff?action=settings' => 'Settings'
);
?><div class="grid_4">
	<div class="sidebar">
		<ul class="sidebar_menu"><?php
			foreach($links as $url => $disp)
				printf("<li%s><a href='%s'>%s</a></li>", $_SERVER['PHP_SELF'] == '/'.$url ? " class='current'" : '', $url, $disp);
	?>		</ul>
	</div>
</div>