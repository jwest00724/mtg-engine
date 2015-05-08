<?php
namespace JBBCode;
require_once(__DIR__ . '/CodeDefinition.php');
use JBBCode\CodeDefinition;
/**
 * An interface for sets of code definitons.
 *
 * @author jbowens
 */
interface CodeDefinitionSet {
	/**
	 * Retrieves the CodeDefinitions within this set as an array.
	 */
	public function getCodeDefinitions();
}