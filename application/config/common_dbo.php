<?php

use \DataObject as DataObject;

/**
* this will walk a keypath down a nested associative array and return the leaf value.
*/
function dbo_valueForKeypath( $keypath, DataObject $dbo = null, $separator = '/' )
{
	if ( isset($keypath, $dbo) && strlen($keypath) > 0) {
		$result = $dbo;
		$path = array_filter( explode($separator, $keypath), 'strlen');
		foreach ($path as $idx => $item) {
			if (is_array($result) && isset($result[$item])) {
				$result = $result[$item];
			}
			else if ( property_exists($result, $item)) {
				$result = $result->{$item};
			}
			else if (method_exists($result, $item)) {
				$result = $result->{$item}();
			}
			else {
				return null;
			}
		}
		return $result;
	}
	return null;
}

/**
* this will walk a keypath down a nested associative array and set leaf value for the keypath
*/
function dbo_setValueForKeypath($keypath, $value, DataObject $dbo = null, $separator = '/' )
{
	if ( isset($keypath, $dbo) && strlen($keypath) > 0) {
		$path = array_filter( explode($separator, $keypath), 'strlen');
		$lastKey = array_pop($path);
		$target_dbo = $dbo;

		foreach ($path as $idx => $item) {
			if (is_array($target_dbo) && isset($target_dbo[$item])) {
				$target_dbo = $target_dbo[$item];
			}
			else if ( property_exists($target_dbo, $item)) {
				$target_dbo = $target_dbo->{$item};
			}
			else if (method_exists($target_dbo, $item)) {
				$target_dbo = $target_dbo->{$item}();
			}
			else {
				$target_dbo = null;
			}
		}

		if ( $target_dbo instanceof DataObject ) {
			$updates = array();
			$updates[$lastKey] = $value;
			$target_dbo->model()->updateObject($target_dbo, $updates );
			return true;
		}
	}
	return false;
}
