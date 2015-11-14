<?php
$_GET['letter'] = isset($_GET['letter']) && ctype_alpha($_GET['letter']) && strlen($_GET['letter']) == 1 ? $_GET['letter'] : null;
?><!DOCTYPE html>
<html lang="en">
<head>
<base href="http://localhost/mtg-engine/images/silk/">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="MTG Codes v9" />
<meta name="author" content="Magictallguy" />
<title>Silk Icons - MTG Codes v9</title>
<link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/pure-min.css" />
<link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/grids-responsive-min.css" />
<!--[if lte IE 8]>
<link rel="stylesheet" href="css/layouts/side-menu-old-ie.css" />
<link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/grids-responsive-old-ie-min.css" />
<![endif]-->
<!--[if gt IE 8]><!-->
<link rel="stylesheet" href="../../css/layouts/side-menu.css" />
<!--<![endif]-->
<style type='text/css'>
	.center {
		text-align:center;
	}
</style>
<link rel="stylesheet" type="text/css" href="../../css/message.css" />
<link rel="stylesheet" type="text/css" href="../../css/style.css" />
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css" />
</head>
<body>
<div id="layout">
<div id="menu">
<a href="#menu" id="menuLink" class="menu-link"><span>&nbsp;</span></a>
<ul class="pure-menu-list">
<li class="pure-menu-item menu-item-divided"><a href="#" class="pure-menu-link pure-menu-heading">Menu</a></li><?php
$alpha = ['a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z'];
foreach($alpha as $letter)
	echo '<li class="pure-menu-item"><a href="index.php?letter='.$letter.'" class="pure-menu-link'.($_GET['letter'] == $letter ? ' pure-menu-selected' : '').'">'.strtoupper($letter).'</a></li>';
?></ul>
</div>
<div class="header">
	<h2>Silk Icons</h2>
</div>
<div class="content"><?php
if(!empty($_GET['letter'])) {
	$cnt = 0;
	$files = glob($_GET['letter'].'*.png');
	sort($files);
	foreach($files as $img) {
		echo '<img src="'.$img.'" title="'.$img.'" alt="'.$img.'" /> ';
		++$cnt;
		if(!($cnt % 35))
			echo '<br />';
	}
} else
	echo 'Select a letter from the menu!';
?></div>
<div class="footer">
	Silk Icons are &copy; to <a href="http://famfamfam.com" target="new">FamFamFam</a>
</div>
</div>
</body>
</html>