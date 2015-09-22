<?php

// guard to ensure basic configuration is loaded
defined('SYSTEM_PATH') || exit("SYSTEM_PATH not found.");

use utilities\Metadata as Metadata;

function my_echo($string ="") {
	echo $string . PHP_EOL;
}

function clearAllData( Model $model = null) {
	if ( is_null($model) == false ) {
		// returns objects in batch of 50
		$allObjects = $model->allObjects();
		while ( is_array($allObjects) && count($allObjects) > 0) {
			foreach( $allObjects as $row ) {
				$model->deleteObject($row) || die( "Error deleting " . var_export($row, true));
			}
			$allObjects = $model->allObjects();
		}
	}
}

function reportData($array, $columns) {
	if ( is_array($array) ) {
		if ( is_array($columns) ) {
			echo "Clazz\t";
			foreach ($columns as $value) {
				echo $value . "\t";
			}
			echo PHP_EOL;
			foreach ($array as $value) {
				if ( is_a($value, '\DataObject')) {
					echo $value->modelName(). "[" .$value->id."]" . "\t";
				}
				else {
					echo gettype($value) . "\t";
				}
				foreach ($columns as $c => $cname) {
					if ( is_a($value, '\DataObject')) {
						$out = dbo_valueForKeypath( $cname, $value );
					}
					else {
						$out = array_valueForKeypath( $cname, $value );
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

function testFilePath($name = null, $purge = false)
{
	is_null($name) == false || die( "no test file specified");

	$path = appendPath( SYSTEM_PATH, "tests", $name );

	if ($purge == true && is_file($path) ) {
		unlink( $path );
	}
	return $path;
}

function testFilePathExists($name = null)
{
	$path = testFilePath( $name );
	return is_file($path);
}

function loadData( Model $model = null, array $data = array(), array $columns = null )
{
	$loaded = array();
	foreach($data as $record) {
		list($obj, $errorList) = $model->createObject($record);
		( is_a($obj, DataObject::NameForModel($model)) ) ||
			die('Insert should be "' . DataObject::NameForModel($model) . '", wrong class from insert ' . var_export( $record, true ) . PHP_EOL
				. var_export( $obj, true ) . PHP_EOL);
		$loaded[] = $obj;
	}
	reportData($loaded,  (is_null($columns) ? array_keys($data[0]) : $columns) );
	return $loaded;
}

function metadataFor($name = null, $purge = false)
{
	if ( is_null($name) ) {
		$name = "test.json";
	}
	$path = testFilePath( $name, $purge );
	return new Metadata( $path );
}
