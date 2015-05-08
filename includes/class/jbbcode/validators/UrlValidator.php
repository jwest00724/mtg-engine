<?php
namespace JBBCode\validators;
require_once(DIRNAME(__DIR__) . '/InputValidator.php');
/**
 * An InputValidator for urls. This can be used to make [url] bbcodes secure.
 *
 * @author jbowens
 * @since May 2013
 */
class UrlValidator implements \JBBCode\InputValidator {
	/**
	 * Returns true iff $input is a valid url.
	 *
	 * @param $input  the string to validate
	 */
	public function validate($input) {
		$input = str_replace('http://drcity.org/', '', $input);
		$input = str_replace('http://www.drcity.org/', '', $input);
		$valid = filter_var($input, FILTER_SANITIZE_URL);
		return !!$valid;
	}
}
