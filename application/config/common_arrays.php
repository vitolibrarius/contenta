<?php

/**
 * this will walk a keypath down a nested associative array and return the leaf value.
 */
	function valueForKeypath( $keypath, $array, $separator = '/' )
	{
		if ( isset($keypath, $array) && strlen($keypath) > 0) {
			$result = $array;
			$path = array_filter( explode($separator, $keypath), 'strlen');
			foreach ($path as $idx => $item) {
				if (isset($result) && is_array($result) ) {
					if ( isset($result[$item])) {
						$result = $result[$item];
					}
					else {
						return null;
					}
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
	function setValueForKeypath($keypath, $value, $array, $separator = '/' )
	{
		if ( isset($keypath) && strlen($keypath) > 0) {
			$path = array_filter( explode($separator, $keypath), 'strlen');
			$lastKey = array_pop($path);
			if (isset($array) == false || $array == false ) {
				$array = array();
			}
			$subArray = &$array;

			foreach ($path as $idx => $item) {
				if (isset($subArray[$item]) ) {
					is_array($subArray[$item]) || die('Bad keypath ' . $keypath);
				}
				else {
					$subArray[$item] = array();
				}
				$subArray = &$subArray[$item];
			}

			if ( isset($value)) {
				$subArray[$lastKey] = $value;
			}
			else if (isset($subArray[$lastKey])) {
				unset($subArray[$lastKey]);
			}

			return $array;
		}
		return false;
	}

	function array_kmap(Closure $callback, Array $input)
	{
		$map = array();
		foreach ($input as $key => $value) {
			$ret = $callback($key, $value);
			if ($ret === false) {
				break;
			}
			array_push($map, $ret);
		}
		return $map;
	}
