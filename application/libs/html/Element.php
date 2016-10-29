<?php

namespace html;

use \Closure as Closure;

/**
 * Class to generate HTML tags/attributes and content.
 */
abstract class Element
{
	const INDENT_STR = "\t";
	static private $indent_level = -1;
	static function indent($increment=true)
	{
		if ($increment) {
			self::$indent_level++;
		}
		return (self::$indent_level > 0 ? PHP_EOL : '') . str_repeat ( Element::INDENT_STR , Element::$indent_level );
	}

	static function outdent()
	{
		self::$indent_level--;
		return (self::$indent_level == 0 ? PHP_EOL : '');
	}

	static public function __callStatic($tag, $args)
	{
		$tag = Element::tag( $tag );
		foreach($args as $a){
			if (is_null($a)) {
				continue;
			}
			else if (is_string($a)) {
				$tag->addText($a);
			}
			else if (is_integer($a)) {
				$tag->addText((string)$a);
			}
			else if (is_array($a)) {
				$tag->addAttributes($a);
			}
			else if ($a instanceof Element) {
				$tag->addElement($a);
			}
			else if ($a instanceof Closure) {
				$cbResults = $a();
				if (is_null($cbResults) == false) {
					if ($cbResults instanceof Element) {
						$tag->addElement($cbResults);
					}
					else if ( is_array($cbResults) ) {
						foreach( $cbResults as $element ) {
							$tag->addElement($element);
						}
					}
					else {
						throw new \Exception("Unknown callback object for <$tag> ". var_export($element, true));
					}
				}
			}
			else {
				throw new \Exception("Unknown argument for <$tag> ". var_export($a, true));
			}
		}
		return $tag;
	}

	static public function tag( $tag = null )
	{
		return new TagElement( $tag );
	}

	public function __construct()
	{
	}

	abstract public function render();
}

class TextElement extends Element
{
	public $text;
	public function __construct($text = '')
	{
		parent::__construct();
		$this->text = $text;
	}

	public function render()
	{
		echo "{$this->text}";
	}
}

class Comment extends Element
{
	public function __construct($text = '')
	{
		parent::__construct($text);
	}

	public function render()
	{
		echo PHP_EOL . Element::indent() . "<!-- {$this->text} -->" . PHP_EOL;
		Element::outdent();
	}
}

class TagElement extends Element
{
	private $tag = null;
	private $attributeList = null;
	private $classList = null;
	private $contentList = null;
	private $autoclosed = false;

	private $self_closing_tags = array(
		"base", "basefont", "br", "col", "frame", "hr", "input", "link", "meta", "param"
	);

	public function __construct($tag = null)
	{
		parent::__construct();
		if ( is_null($tag) ) {
			throw new \Exception("Tag elements require a tag");
		}
		$this->tag = $tag;
		$this->autoclosed = in_array($tag, $this->self_closing_tags);
		$this->attributeList = array();
		$this->classList = array();
		$this->contentList = array();
	}

	public function addElement($tag = null)
	{
		if ( is_null($tag) == false ) {
			if ( $this->autoclosed ) {
				throw new \Exception("Tag element <". $this->tag . "> cannot have content " . $tag);
			}

			if(is_object($tag) && get_class($tag) == get_class($this)) {
				$htmlTag = $tag;
			}
			else {
				$htmlTag = new TagElement($tag);
			}
			$this->contentList[] = $htmlTag;
			return $htmlTag;
		}
		return $this;
	}

	public function addText($value = null)
	{
		if ( is_string($value) && strlen($value)) {
			if ( $this->autoclosed ) {
				throw new \Exception("Tag element <". $this->tag . "> cannot have text " . $value);
			}

			if(is_object($value) && $value instanceof TextElement) {
				$htmlTag = $value;
			}
			else {
				$htmlTag = new TextElement($value);
			}
			$this->contentList[] = $htmlTag;
		}
		return $this;
	}

	public function addAttributes( array $attr = array() )
	{
		foreach( $attr as $name => $value ) {
			$v_array = (array)$value;
			$this->attributeList[$name] = implode(" ", $v_array);
		}
		return $this;
	}

	public function setAttribute($name, $value)
	{
		$this->attributeList[$name] = $value;
		return $this;
	}

	public function id($value)
	{
		return $this->setAttribute('id', $value);
	}

	public function addClass($value)
	{
		$this->classList[] = $value;
		return $this;
	}

	public function classString()
	{
		return ( count($this->classList) > 0 ? ' class="' . implode(" ", $this->classList). '"' : "");
	}

	public function attributeString()
	{
		if (count($this->attributeList) < 1){
			return "";
		}

		return " ".implode(" ",
			array_kmap(
				function($k, $v) {
					return "{$k}=\"" . htmlspecialchars($v) . "\"";
				},
				$this->attributeList
			)
		);
	}

	public function render()
	{
		$indent = Element::indent();

		if ($this->autoclosed) {
			echo $indent . "<{$this->tag}" . $this->classString() . $this->attributeString() . " />" ;
		}
		else {
			echo $indent . "<{$this->tag}" . $this->classString() . $this->attributeString() . ">";
			foreach( $this->contentList as $element ) {
				$element->render();
			}
			echo $indent . "</{$this->tag}>";
		}

		echo Element::outdent();
	}
}

