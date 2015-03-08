<?php

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
