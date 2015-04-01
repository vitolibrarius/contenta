<?php

// guard to ensure basic configuration is loaded
defined('SYSTEM_PATH') || exit("SYSTEM_PATH not found.");

use utilities\Metadata as Metadata;

function my_echo($string ="") {
	echo $string . PHP_EOL;
}

function reportData($array, $columns) {
	if ( is_array($array) ) {
		if ( is_array($columns) ) {
			echo "Clazz\t";
			foreach ($columns as $key => $value) {
				echo $value . "\t";
			}
			echo PHP_EOL;
			foreach ($array as $key => $value) {
				echo $value->modelName(). "[" .$value->id."]" . "\t";
				foreach ($columns as $c => $cname) {
					$out = null;
					if ( property_exists($value, $cname)) {
						$out = $value->{$cname};
					}
					else if (method_exists($value, $cname)) {
						$out = $value->{$cname}();
					}
					if ( is_string($out) ) {
						echo $out . "\t";
					}
					else if ( is_a($out, '\DataObject')) {
						echo $out->__toString() . "\t";
					}
					else if ( is_array($out) ) {
						array_walk($out, function(&$val){ echo $val->__toString(); });
					}
					else {
						echo var_export($out, false) . "\t";
					}
				}
				echo PHP_EOL;
			}
		}
		else {
			my_echo( "reportData() Columns are not array" . var_export($columns, true) );
		}
	}
	else {
		my_echo( "reportData() data array is not array " .var_export($array, true) );
	}
	echo PHP_EOL;
}


function loadData( Model $model = null, array $data = array(), array $columns = null )
{
	$loaded = array();
	foreach($data as $record) {
		$newObjId = $model->createObject($record);

		( $newObjId != false ) || die('Failed to insert ' . $record );
		( is_array($newObjId) == false ) || die('Failed to insert ' . var_export( $record, true ) . PHP_EOL
			. 'Validation errors ' . var_export( $newObjId, true ). PHP_EOL);

		$obj = $model->objectForId($newObjId);
		( is_a($obj, DataObject::NameForModel($model)) ) ||
			die('Insert should be "' . DataObject::NameForModel($model) . '", wrong class from insert ' . var_export( $record, true ) . PHP_EOL
				. var_export( $obj, true ) . PHP_EOL);
		$loaded[] = $obj;
	}
	reportData($loaded,  (is_null($columns) ? array_keys($data[0]) : $columns) );
	return $loaded;
}

function metadataFor($name = null)
{
	if ( is_null($name) ) {
		$name = "test.json";
	}
	$path = appendPath( SYSTEM_PATH, "tests", $name );
	return new Metadata( $path );
}
