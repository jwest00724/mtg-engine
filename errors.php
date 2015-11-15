<?php
define('HEADER_TEXT', 'Error!');
require_once __DIR__ . '/includes/globals.php';
$_GET['which'] = isset($_GET['which']) && ctype_digit($_GET['which']) ? $_GET['which'] : null;
switch($_GET['which']) {
	case 400:
		$mtg->error("Bad request - try refreshing");
		break;
	case 401:
		$mtg->error("Authorisation required not given..");
		break;
	case 403:
		$mtg->error("You don't have access to that");
		break;
	case 404:
		$mtg->error("I'm afraid that page wasn't found!");
		break;
	case 405:
		$mtg->error("That method wasn't accepted");
		break;
	case 502:
		$mtg->error("There's a misconfiguration somewhere in the server... Hang on..");
		break;
	case 500: case 503:
		$mtg->error("There appears to be an issue with the server.. This is out of our hands, but we'll be back very soon!");
		break;
	default:
		$mtg->error("An error code wasn't supplied");
		break;
}