<?php
namespace JBBCode\validators;
require_once(DIRNAME(__DIR__) . '/InputValidator.php');
/**
 * An InputValidator for images. This can be used to make [img] bbcodes secure.
 *
 * @author Magictallguy
 * @since December 2014
 */
class ImageValidator implements \JBBCode\InputValidator {
	/**
	 * Returns true if $input is a valid url and image.
	 *
	 * @param $input  the string to validate
	 */
	public function validate($input) {
		if(!filter_var($input, FILTER_SANITIZE_URL))
			return false;
		if(!@exif_imagetype($input))
			return false;
		return true;
	}
}
