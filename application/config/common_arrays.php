<?php

	function valueForKeypath( $keypath, $array, $separator = '/' )
	{
		if ( isset($keypath, $array) && strlen($keypath) > 0) {
			$result = $array;
			$path = explode($separator, $keypath);
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
