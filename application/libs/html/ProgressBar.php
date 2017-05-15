<?php

namespace html;

use \interfaces\ObjectProgress as ObjectProgress;
use \html\Element as H;

/**
 * Class to generate HTML progress bars.
 */
class ProgressBar
{
	public function elements($object = null)
	{
		$max = (float)1;
		$min = (float)0;
		$value = null;
		$percentage = "unknown";

		if ( is_null($object) == false && $object instanceof ObjectProgress) {
			$max = max((float)$object->progressMaximum(), 1);
			$min = (float)$object->progressMinimum();
			$value = (float)$object->progressCurrent();
			if ( is_null($value) == false ) {
				$percentage = (($value / ($max - $min)) * 100) . "%";
			}
		}

		$attributes = array();
		$attributes["class"] = ($object instanceof \DataObject ? $object->tableName() : "unknown");
		$attributes["min"] = $min;
		$attributes["max"] = $max;
		if ( is_null($value) == false ) {
			$attributes["value"] = $value;
		}

		$bar = H::div( array("class" => "progress"),
			H::progress( $attributes,
				H::em( null, "Progress: " . $percentage )
			),
			H::small( null, $value . " / " . $max )
		);
		return $bar;
	}

	public function render($object = null)
	{
		$bar = $this->elements($object);
		return (is_null($bar) ? null : $bar->render());
	}
}
