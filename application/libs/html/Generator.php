<?php

namespace html;

use \Closure as Closure;

/**
 * Class to generate HTML tags/attributes and content.
 */
class Generator {
	const INDENT_STR = "\t";

	static public $self_closing_tags = array(
		"base", "basefont", "br", "col", "frame", "hr", "input", "link", "meta", "param"
	);

	static private $indent_level = -1;

	static public function __callStatic($tag, $args)
	{
		$text = null;
		$attributes = array();
		$callback = null;
		foreach($args as $a){
			if (is_string($a)) {
				$text = $a;
			}
			else if (is_array($a)) {
				$attributes = $a;
			}
			else if ($a instanceof Closure) {
				$callback = $a;
			}
		}
		self::render($tag, $text, $attributes, $callback );
	}

	static private function render($tag, $text = null, Array $attributes = array(), Closure $callback = null)
	{
		echo self::indent();

		# self-closing tag
		if (in_array($tag, self::$self_closing_tags)) {
			echo "<{$tag}" . self::attributes($attributes) . " />" . PHP_EOL;
		}
		else {
			echo "<{$tag}" . self::attributes($attributes) . ">";

			if ($callback instanceof Closure) {
				try {
					ob_start();
					$callback();
					$data = ob_get_clean();

					echo PHP_EOL . $data . self::indent(false);
				}
				catch (Exception $e) {
					Logger::logException( $exception );
					echo "error!";
				}
			}
			else {
				echo $text;
			}

			echo "</{$tag}>" . PHP_EOL;
		}

		# outdent
		self::outdent();
	}

	static private function attributes(array $attributes)
	{
		if (count($attributes) < 1){
			return null;
		}

		return " ".implode(" ",
			array_kmap(
				function($k, $v) {
					return "{$k}=\"" . htmlspecialchars($v) . "\"";
				},
				$attributes
			)
		);
	}

	static public function comment($comment)
	{
		echo PHP_EOL . self::indent() . "<!-- {$comment} -->" . PHP_EOL;
		self::outdent();
	}

	static private function indent($increment=true)
	{
		if ($increment) {
			self::$indent_level++;
		}
		return str_repeat ( Generator::INDENT_STR , self::$indent_level );
	}

	static private function outdent()
	{
		self::$indent_level--;
	}
}
