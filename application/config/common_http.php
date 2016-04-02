<?php

$version = currentVersionNumber();
define('CONTENTA_USER_AGENT', 'Contenta/'.$version.' (http://github.com/vitolibrarius/contenta; vitolibrarius @ gmail.com)');

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
						case Model::FLAG_TYPE:
							$value = (($value == 'on') || intval($value) > 0) ? 1 : 0;
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

function hashedPath($table = 'none', $id = 0, $filename = null)
{
	if ( $id > 0 ) {
		$mediaDir = Config::GetMedia( $table, substr("00".dechex($id % 256),-2), $id );
		makeRequiredDirectory($mediaDir, 'Media directory for ' . appendPath($table, substr("00".dechex($id % 256),-2), $id) );

		if ( is_null( $filename )) {
			return $mediaDir;
		}

		return appendPath( $mediaDir, $filename );
	}
	return null;
}

function hashedImagePath($table = 'none', $id = 0, $imagename = null)
{
	if ( $id > 0 ) {
		$base = hashedPath($table, $id, $imagename);
		foreach( imageExtensions() as $ext) {
			if ( file_exists( $base . '.' . $ext) ) {
				return $base . '.' . $ext;
			}
		}
	}
	return null;
}

function http_stringForCode($code = NULL) {
	if ($code !== NULL) {
		switch ($code) {
			case 100: $text = 'Continue'; break;
			case 101: $text = 'Switching Protocols'; break;
			case 200: $text = 'OK'; break;
			case 201: $text = 'Created'; break;
			case 202: $text = 'Accepted'; break;
			case 203: $text = 'Non-Authoritative Information'; break;
			case 204: $text = 'No Content'; break;
			case 205: $text = 'Reset Content'; break;
			case 206: $text = 'Partial Content'; break;
			case 300: $text = 'Multiple Choices'; break;
			case 301: $text = 'Moved Permanently'; break;
			case 302: $text = 'Moved Temporarily'; break;
			case 303: $text = 'See Other'; break;
			case 304: $text = 'Not Modified'; break;
			case 305: $text = 'Use Proxy'; break;
			case 400: $text = 'Bad Request'; break;
			case 401: $text = 'Unauthorized'; break;
			case 402: $text = 'Payment Required'; break;
			case 403: $text = 'Forbidden'; break;
			case 404: $text = 'Not Found'; break;
			case 405: $text = 'Method Not Allowed'; break;
			case 406: $text = 'Not Acceptable'; break;
			case 407: $text = 'Proxy Authentication Required'; break;
			case 408: $text = 'Request Time-out'; break;
			case 409: $text = 'Conflict'; break;
			case 410: $text = 'Gone'; break;
			case 411: $text = 'Length Required'; break;
			case 412: $text = 'Precondition Failed'; break;
			case 413: $text = 'Request Entity Too Large'; break;
			case 414: $text = 'Request-URI Too Large'; break;
			case 415: $text = 'Unsupported Media Type'; break;
			case 500: $text = 'Internal Server Error'; break;
			case 501: $text = 'Not Implemented'; break;
			case 502: $text = 'Bad Gateway'; break;
			case 503: $text = 'Service Unavailable'; break;
			case 504: $text = 'Gateway Time-out'; break;
			case 505: $text = 'HTTP Version not supported'; break;
			default:
				$text = 'Unknown http status code "' . htmlentities($code) . '"';
				break;
		}
		return $text;
	}

	return 'Unknown http status code "' . htmlentities($code) . '"';
}

if (!function_exists('http_parse_headers'))
{
    function http_parse_headers($raw_headers)
    {
        $headers = array();
        $key = ''; // [+]

        foreach(explode("\n", $raw_headers) as $i => $h)
        {
            $h = explode(':', $h, 2);

            if (isset($h[1]))
            {
                if (!isset($headers[$h[0]]))
                    $headers[$h[0]] = trim($h[1]);
                elseif (is_array($headers[$h[0]]))
                {
                    // $tmp = array_merge($headers[$h[0]], array(trim($h[1]))); // [-]
                    // $headers[$h[0]] = $tmp; // [-]
                    $headers[$h[0]] = array_merge($headers[$h[0]], array(trim($h[1]))); // [+]
                }
                else
                {
                    // $tmp = array_merge(array($headers[$h[0]]), array(trim($h[1]))); // [-]
                    // $headers[$h[0]] = $tmp; // [-]
                    $headers[$h[0]] = array_merge(array($headers[$h[0]]), array(trim($h[1]))); // [+]
                }

                $key = $h[0]; // [+]
            }
            else // [+]
            { // [+]
                if (substr($h[0], 0, 1) == "\t") // [+]
                    $headers[$key] .= "\r\n\t".trim($h[0]); // [+]
                elseif (!$key) // [+]
                    $headers[0] = trim($h[0]);trim($h[0]); // [+]
            } // [+]
        }

        return $headers;
    }
}

function ipToHex($ipAddress)
{
	$hex = '';
	if (strpos($ipAddress, ',') !== false) {
		$splitIp = explode(',', $ipAddress);
		$ipAddress = trim($splitIp[0]);
	}
	$isIpV6 = false;
	$isIpV4 = false;
	if (filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false) {
		$isIpV6 = true;
	}
	else if (filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false) {
		$isIpV4 = true;
	}
	if (!$isIpV4 && !$isIpV6) {
		return false;
	}

	// IPv4 format
	if ($isIpV4) {
		$parts = explode('.', $ipAddress);
		for($i = 0; $i < 4; $i++) {
			$parts[$i] = str_pad(dechex($parts[$i]), 2, '0', STR_PAD_LEFT);
		}
		$ipAddress = '::'.$parts[0].$parts[1].':'.$parts[2].$parts[3];
		$hex = join('', $parts);
	}
	// IPv6 format
	else {
		$parts = explode(':', $ipAddress);
		// If this is mixed IPv6/IPv4, convert end to IPv6 value
		if (filter_var($parts[count($parts) - 1], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false) {
			$partsV4 = explode('.', $parts[count($parts) - 1]);
			for($i = 0; $i < 4; $i++) {
				$partsV4[$i] = str_pad(dechex($partsV4[$i]), 2, '0', STR_PAD_LEFT);
			}
			$parts[count($parts) - 1] = $partsV4[0].$partsV4[1];
			$parts[] = $partsV4[2].$partsV4[3];
		}
		$numMissing = 8 - count($parts);
		$expandedParts = array();
		$expansionDone = false;
		foreach ($parts as $part) {
			if (!$expansionDone && $part == '') {
				for ($i = 0; $i <= $numMissing; $i++) {
					$expandedParts[] = '0000';
				}
				$expansionDone = true;
			}
			else {
				$expandedParts[] = $part;
			}
		}
		foreach ($expandedParts as &$part) {
			$part = str_pad($part, 4, '0', STR_PAD_LEFT);
		}
		$ipAddress = join(':', $expandedParts);
		$hex = join('', $expandedParts);
	}
	// Validate the final IP
	if (!filter_var($ipAddress, FILTER_VALIDATE_IP)) {
		return false;
	}
	return strtolower(str_pad($hex, 32, '0', STR_PAD_LEFT));
}
