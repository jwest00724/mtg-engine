<?php
/*Copyright (c) 2015 Orsokuma

Permission is hereby granted, free of charge, to any person obtaining a
copy of this software and associated documentation files (the "Software"),
to deal in the Software without restriction, including without limitation
the rights to use, copy, modify, merge, publish, distribute, sublicense,
and/or sell copies of the Software, and to permit persons to whom the
Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
DEALINGS IN THE SOFTWARE.
*/
if(strpos($_SERVER['REQUEST_URI'], basename(__FILE__)) !== false)
	exit;
if(!defined('MTG_ENABLE'))
	define('MTG_ENABLE', true);
$_SERVER['PHP_SELF'] = str_replace('/adz', '', $_SERVER['PHP_SELF']);
class headers {
	static $inst = null;
	static function getInstance($set) {
		if(self::$inst == null)
			self::$inst = new headers($set);
		return self::$inst;
	}
	function __construct($set) {
		global $css;
		header("Content-type: text/html;charset=UTF-8");
		?><!doctype html>
		<html lang="en">
		<head>
			<base href='http://mtgtest.tk/adz/' />
			<meta charset="utf-8">
			<title><?php echo $set['game_name']; ?></title>
			<meta name="author" content="Magictallguy" />
			<meta name="viewport" content="width=device-width, initial-scale=1.0" />
			<link rel="stylesheet" href="css/style.css" />
			<link rel="stylesheet" href="css/message.css" />
		</head>
		<body>
		<div class='logo'>&nbsp;</div>
		<div class='content-container'><?php
	}

	function __destruct() {
		global $set;
		?></div>
		</body>
		</html><?php
	}
}