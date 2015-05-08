<?php
namespace JBBCode;
require_once(__DIR__ . '/CodeDefinition.php');
require_once(__DIR__ . '/CodeDefinitionBuilder.php');
require_once(__DIR__ . '/CodeDefinitionSet.php');
require_once(__DIR__ . '/validators/CssColorValidator.php');
require_once(__DIR__ . '/validators/UrlValidator.php');
require_once(__DIR__ . '/validators/ImageValidator.php');
/**
 * Provides a default set of common bbcode definitions.
 *
 * @author jbowens
 */
class DefaultCodeDefinitionSet implements CodeDefinitionSet {
	/* The default code definitions in this set. */
	protected $definitions = array();
	/**
	 * Constructs the default code definitions.
	 */
	public function __construct() {
		$builder = new CodeDefinitionBuilder('b', '<strong>{param}</strong>'); /* [b] bold tag */
		array_push($this->definitions, $builder->build());
		$builder = new CodeDefinitionBuilder('i', '<em>{param}</em>'); /* [i] italics tag */
		array_push($this->definitions, $builder->build());
		$builder = new CodeDefinitionBuilder('u', '<u>{param}</u>'); /* [u] underline tag */
		array_push($this->definitions, $builder->build());
		$builder = new CodeDefinitionBuilder('br', '<br />{param}'); /* [br] new line tag */
		array_push($this->definitions, $builder->build());
		$urlValidator   = new \JBBCode\validators\UrlValidator();
		$imageValidator = new \JBBCode\validators\ImageValidator();
		$builder        = new CodeDefinitionBuilder('url', '<a href="{param}">{param}</a>'); /* [url] link tag */
		$builder->setParseContent(false)->setBodyValidator($urlValidator);
		array_push($this->definitions, $builder->build());
		$builder = new CodeDefinitionBuilder('url', '<a href="{option}">{param}</a>'); /* [url=http://example.com] link tag */
		$builder->setUseOption(true)->setParseContent(true)->setOptionValidator($urlValidator);
		array_push($this->definitions, $builder->build());
		$builder = new CodeDefinitionBuilder('urlnew', '<a href="{param}" target="new">{param}</a>'); /* [urlnew] link tag */
		$builder->setParseContent(false)->setBodyValidator($urlValidator);
		array_push($this->definitions, $builder->build());
		$builder = new CodeDefinitionBuilder('urlnew', '<a href="{option}" target="new">{param}</a>'); /* [urlnew=http://example.com] link tag */
		$builder->setUseOption(true)->setParseContent(true)->setOptionValidator($urlValidator);
		array_push($this->definitions, $builder->build());
		$builder = new CodeDefinitionBuilder('color', '<span style="color: {option}">{param}</span>'); /* [color] color tag */
		$builder->setUseOption(true)->setOptionValidator(new \JBBCode\validators\CssColorValidator());
		array_push($this->definitions, $builder->build());
		$builder = new CodeDefinitionBuilder('colour', '<span style="color: {option}">{param}</span>'); /* [colour] color tag */
		$builder->setUseOption(true)->setOptionValidator(new \JBBCode\validators\CssColorValidator());
		array_push($this->definitions, $builder->build());
		$builder = new CodeDefinitionBuilder('small', '<span class="small">{param}</span>'); /* [small] small text tag */
		array_push($this->definitions, $builder->build());
		$builder = new CodeDefinitionBuilder('big', '<span style="font-size:1.5em;">{param}</span>'); /* [big] big text tag */
		array_push($this->definitions, $builder->build());
		$builder = new CodeDefinitionBuilder('size', '<span style="font-size:{option}em;">{param}</span>'); /* [size] size tag, uses em measurement */
		$builder->setUseOption(true)->setParseContent(true);
		array_push($this->definitions, $builder->build());
		$builder = new CodeDefinitionBuilder('quote', '<div class="quotetop">Quote</div><div class="quotemain">{param}</div>'); /* [quote] quote tag */
		array_push($this->definitions, $builder->build());
		$builder = new CodeDefinitionBuilder('quote', '<div class="quotetop">Quote</div><div class="quotemain">{param}<br /><em class="small">- {option}</em></div>');  /* [quote=author]  tag */
		$builder->setUseOption(true)->setParseContent(true);
		array_push($this->definitions, $builder->build());
		$builder = new CodeDefinitionBuilder('s', '<span style="text-decoration:line-through;">{param}</span>');  /* [s] strikethrough tag */
		array_push($this->definitions, $builder->build());
		$builder = new CodeDefinitionBuilder('sub', '<sub>{param}</sub>');  /* [sub] subscript tag */
		array_push($this->definitions, $builder->build());
		$builder = new CodeDefinitionBuilder('sup', '<sup>{param}</sup>');  /* [sup] superscript tag */
		array_push($this->definitions, $builder->build());
		$builder = new CodeDefinitionBuilder('info', '<div class="info">{param}</div>'); /* [info] $mtg->info() tag */
		array_push($this->definitions, $builder->build());
		$builder = new CodeDefinitionBuilder('error', '<div class="error">{param}</div>'); /* [error] $mtg->error() tag */
		array_push($this->definitions, $builder->build());
		$builder = new CodeDefinitionBuilder('success', '<div class="success">{param}</div>'); /* [success] $mtg->success() tag */
		array_push($this->definitions, $builder->build());
		$builder = new CodeDefinitionBuilder('warning', '<div class="warning">{param}</div>'); /* [warning] $mtg->warning() tag */
		array_push($this->definitions, $builder->build());
		$builder = new CodeDefinitionBuilder('quo', '&ldquo;{param}&rdquo;'); /* [quo] HTML entity quotation marks tag */
		array_push($this->definitions, $builder->build());
		$builder = new CodeDefinitionBuilder('img', '<img src="{param}" />'); /* [img] image tag */
		$builder->setUseOption(false)->setParseContent(false)->setBodyValidator($imageValidator);
		array_push($this->definitions, $builder->build());
		$builder = new CodeDefinitionBuilder('img', '<img src="{param}" alt="{option}" />'); /* [img=alt text] image tag */
		$builder->setUseOption(true)->setParseContent(false)->setBodyValidator($imageValidator);
		array_push($this->definitions, $builder->build());
	}
	/**
	 * Returns an array of the default code definitions.
	 */
	public function getCodeDefinitions() {
		return $this->definitions;
	}
}