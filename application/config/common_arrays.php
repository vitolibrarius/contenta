<?php
	function array_first_value(&$array)
	{
		if (is_array($array) == false)
			return $array;

		if (count($array) == 0 )
			return null;

		reset($array);
		return $array[key($array)];
	}

	function array_last_value(&$array)
	{
		if (is_array($array) == false)
			return $array;

		if (count($array) == 0 )
			return null;

		end($array);
		return $array[key($array)];
	}

	function array_first_key(&$array)
	{
		if (is_array($array) == false)
			return $array;

		if (count($array) == 0 )
			return null;

		reset($array);
		return key($array);
	}

	function array_last_key(&$array)
	{
		if (is_array($array) == false)
			return $array;

		if (count($array) == 0 )
			return null;

		end($array);
		return key($array);
	}

	function array_recursive_ksort(&$array)
	{
	   foreach ($array as &$value) {
		  if (is_array($value)) {
		  	array_recursive_ksort($value);
		  }
	   }
	   return ksort($array);
	}

	function array_flatten( array $array = array(), $targetClass = null )
	{
		$merged = array();
		foreach ($array as $item) {
			if ( is_array($item) ) {
				$subArray = array_flatten( $item, $targetClass );
				$merged = array_merge($merged, $subArray);
			}
			else if (is_null($targetClass) || ( is_string($targetClass) && $item instanceof $targetClass)) {
				$merged[] = $item;
			}
			else {
				throw new \Exception( "Element is not of type $targetClass " . var_export($item, true));
			}
		}
		return $merged;
	}

/**
 * this will walk a keypath down a nested associative array and return the leaf value.
 */
	function array_valueForKeypath( $keypath, array $array = array(), $separator = '/' )
	{
		if ( isset($keypath, $array) && strlen($keypath) > 0) {
			$result = $array;
			$path = array_filter( explode($separator, $keypath), 'strlen');
			foreach ($path as $idx => $item) {
				if ( is_null($result) || is_bool($result) ) {
					return $result;
				}
				else if ( is_array($result) ) {
					$result = (isset($result[$item]) ? $result[$item] : null);
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
	function array_setValueForKeypath($keypath, $value, array &$array = null, $separator = '/' )
	{
		if ( isset($keypath) && strlen($keypath) > 0) {
			$path = array_filter( explode($separator, $keypath), 'strlen');
			$lastKey = array_pop($path);
			if (is_null($array) || $array == false ) {
				$array = array();
			}
			$subArray = &$array;

			foreach ($path as $idx => $item) {
				if (isset($subArray[$item]) ) {
					if ( is_array($subArray[$item]) == false) {
						throw new \Exception('Bad keypath ' . $keypath . " " . var_export($subArray, true));
					}
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

	function array_kmap(Closure $callback, Array $input = array())
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

	function array_group_by(Array $input = array(), Closure $callback )
	{
		$result = array();
		foreach ($input as $key => $value) {
			$groupKey = $callback($key, $value);
			array_setValueForKeypath( $groupKey, $value, $result );
		}
		return $result;
	}

	function array_filterForKeyValue(Array $input = array(), Array $filters = array())
	{
		if ( count($filters) > 0 ) {
			$results = array();
			foreach( $input as $record ) {
				$pass = true;
				foreach( $filters as $key => $expectedValue ) {
					$testValue = array_valueForKeypath( $key, $record );
					if ( $testValue != $expectedValue ) {
						$pass = false;
						break;
					}
				}

				if ( $pass == true ) {
					$results[] = $record;
				}
			}
			return $results;
		}
		return $input;
	}
