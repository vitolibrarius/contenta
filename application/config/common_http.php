<?php

function splitPOSTValues($array) {
	$ret = array();
	foreach ($array as $key => $value) {
		$components = explode(\Model::HTML_ATTR_SEPARATOR, $key);
		if (count($components) > 1) {
			$table = $components[0];
			$attr = $components[1];
			$model = \Model::Named($table);
			if ( $model != null ) {
				$type = $model->attributeType($attr);
				if ( is_null($type) == false ) {
					switch ($type) {
						case Model::DATE_TYPE:
							$value = strtotime($value);
							break;
						case Model::INT_TYPE:
							$value = intval($value);
							break;
						default:
							break;
					}
				}
			}

			if (isset($ret[$table])) {
				$ret[$table][$attr] = $value;
			}
			else {
				$ret[$table] = array( $attr => $value );
			}
		}
	}
	return $ret;
}

