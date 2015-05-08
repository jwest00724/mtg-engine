<?php
namespace JBBCode\visitors;
require_once(DIRNAME(__DIR__) . '/CodeDefinition.php');
require_once(DIRNAME(__DIR__) . '/DocumentElement.php');
require_once(DIRNAME(__DIR__) . '/ElementNode.php');
require_once(DIRNAME(__DIR__) . '/NodeVisitor.php');
require_once(DIRNAME(__DIR__) . '/TextNode.php');
/**
 * This visitor is used by the jBBCode core to enforce nest limits after
 * parsing. It traverses the parse graph depth first, removing any subtrees
 * that are nested deeper than an element's code definition allows.
 *
 * @author jbowens
 * @since May 2013
 */
class NestLimitVisitor implements \JBBCode\NodeVisitor {
	protected $depth = array(); /* A map from tag name to current depth. */
	public function visitDocumentElement(\JBBCode\DocumentElement $documentElement) {
		foreach($documentElement->getChildren() as $child)
			$child->accept($this);
	}
	public function visitTextNode(\JBBCode\TextNode $textNode) {
		/* Nothing to do. Text nodes don't have tag names or children. */
	}
	public function visitElementNode(\JBBCode\ElementNode $elementNode) {
		$tagName = strtolower($elementNode->getTagName());
		if(isset($this->depth[$tagName])) /* Update the current depth for this tag name. */
			$this->depth[$tagName]++;
		else
			$this->depth[$tagName] = 1;
		if($elementNode->getCodeDefinition()->getNestLimit() != -1 && $elementNode->getCodeDefinition()->getNestLimit() < $this->depth[$tagName]) /* Check if $elementNode is nested too deeply. */
			$elementNode->getParent()->removeChild($elementNode); /* This element is nested too deeply. We need to remove it and not visit any of its children. */
		else
			foreach($elementNode->getChildren() as $child) /* This element is not nested too deeply. Visit all of its children. */
				$child->accept($this);
		$this->depth[$tagName]--; /* Now that we're done visiting this node, decrement the depth. */
	}
}
