<?php

// guard to ensure basic configuration is loaded
defined('SYSTEM_PATH') || exit("SYSTEM_PATH not found.");

function callerClassAndMethod($currentFunction = '')
{
	$trace = debug_backtrace();
	$caller = array_shift($trace);
	if ( $caller['function'] === 'callerClassAndMethod') {
		$caller = array_shift($trace);
	}

	if ( $caller['function'] === $currentFunction) {
		$caller = array_shift($trace);
	}
	return $caller;
}

function isset_default($variable, $default = null)
{
	return (isset($variable) ? $variable : $default);
}

function startsWith($haystack, $needle)
{
	return $needle === "" || strpos($haystack, $needle) === 0;
}
function endsWith($haystack, $needle)
{
	return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
}

function zipFileList($path)
{
	$list = false;
	$zip = zip_open($path);

	if (is_resource($zip))
	{
		$list = array();
		while ($zip_entry = zip_read($zip))
		{
			$list[] = zip_entry_name($zip_entry);
		}

		zip_close($zip);
	}
	else
	{
		Logger::logError('Zip error ' . $zip . ' ' . zipFileErrMsg($zip),  'zipFileList', basename($path));
	}
	return $list;
}

function classNames($file)
{
	$classNames = array();
	$php_file = file_get_contents($file);
	$tokens = token_get_all($php_file);
	$class_token = false;
	foreach ($tokens as $token) {
		if (is_array($token)) {
			if ($token[0] == T_CLASS) {
				$class_token = true;
			}
			else if ($class_token && $token[0] == T_STRING) {
				$classNames[strtolower($token[1])] = $token[1];
				$class_token = false;
			}
		}
	}
	return $classNames;
}

function uuid()
{
	return sprintf( '%08x-%04x-%04x-%02x%02x-%012x',
		mt_rand(),
		mt_rand(0, 65535),
		bindec(substr_replace(sprintf('%016b', mt_rand(0, 65535)), '0100', 11, 4)),
		bindec(substr_replace(sprintf('%08b', mt_rand(0, 255)), '01', 5, 2)),
		mt_rand(0, 255),
		mt_rand()
	);
}

function uuidShort()
{
	return sprintf( '%08x%04x%04x%02x%02x%012x',
		mt_rand(),
		mt_rand(0, 65535),
		bindec(substr_replace(sprintf('%016b', mt_rand(0, 65535)), '0100', 11, 4)),
		bindec(substr_replace(sprintf('%08b', mt_rand(0, 255)), '01', 5, 2)),
		mt_rand(0, 255),
		mt_rand()
	);
}

function sanitize_filename($string, $maxLength = 100, $force_lowercase = true, $anal = false)
{
	$clean = sanitize($string, $force_lowercase, $anal );
	$max = min(max($maxLength, 10), 255);
	if (strlen($clean) > $max) {
		$characters = floor($max / 2);
		return substr($clean, 0, $characters) . '...' . substr($clean, -1 * $characters);
	}
	return $clean;
}

function sanitize($string, $force_lowercase = true, $anal = false) {
	$strip = array("~", "`", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "_", "=", "+", "[", "{", "]",
				   "}", "\\", "|", ";", ":", "\"", "'", "&#8216;", "&#8217;", "&#8220;", "&#8221;", "&#8211;", "&#8212;",
				   "", "", ",", "<", ">", "/", "?");
	$clean = trim(str_replace($strip, "", strip_tags($string)));
	$clean = preg_replace('/\s+/', "-", $clean);
	$clean = ($anal) ? preg_replace("/[^a-zA-Z0-9]/", "", $clean) : $clean ;
	return ($force_lowercase) ?
		(function_exists('mb_strtolower')) ?
			mb_strtolower($clean, 'UTF-8') :
			strtolower($clean) :
		$clean;
}

function normalize($string)
{
	$string = htmlentities($string, ENT_QUOTES, 'UTF-8');
	$string = preg_replace('~&([a-z]{1,2})(acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i', '$1', $string);
	$string = html_entity_decode($string, ENT_QUOTES, 'UTF-8');
	$string = preg_replace(array('~[^0-9a-z]~i', '~[ -]+~'), ' ', $string);
	return trim($string, ' -');
}

function words($string)
{
	$words = explode(' ', trim($string));

	foreach($words as $key=>$word) {
		$last_char = $word[strlen($word)-1];
		$punctuations = '.;:!?,';

		// remove any punctuations or chracters from the end of the word
		$words[$key] = rtrim($word, $punctuations);
	}

	return $words;
}

function lines($string)
{
	preg_match_all('~.*?[?.!]~s', $string, $sentences);
	return array_map('trim', $sentences[0]);
}

function convertToBytes ($val)
{
	if (empty($val))
		return 0;

	$val = trim($val);

	preg_match('#([0-9]+)[\s]*([a-z]+)#i', $val, $matches);

	$last = '';
	if (isset($matches[2])) {
		$last = $matches[2];
	}

	if (isset($matches[1])) {
		$val = (int) $matches[1];
	}

	switch (strtolower($last))
	{
		case 'g':
		case 'gb':
			$val *= 1024 * 1024 * 1024;
			break;
		case 'm':
		case 'mb':
			$val *= 1024 * 1024;
			break;
		case 'k':
		case 'kb':
			$val *= 1024;
			break;
	}

	return (int) $val;
}

function formatSizeUnits($value)
{
	$bytes = convertToBytes($value);
	if ($bytes >= 1073741824)
	{
		$bytes = number_format($bytes / 1073741824, 2) . ' GB';
	}
	elseif ($bytes >= 1048576)
	{
		$bytes = number_format($bytes / 1048576, 2) . ' MB';
	}
	elseif ($bytes >= 1024)
	{
		$bytes = number_format($bytes / 1024, 2) . ' KB';
	}
	elseif ($bytes > 1)
	{
		$bytes = $bytes . ' bytes';
	}
	elseif ($bytes == 1)
	{
		$bytes = $bytes . ' byte';
	}
	else
	{
		$bytes = '0 bytes';
	}

	return $bytes;
}

function resize_image($sourcefile, $xmax, $ymax)
{
	$ext = file_ext($sourcefile);

	if ($ext == "jpg" || $ext == "jpeg")
		$im = imagecreatefromjpeg($sourcefile);
	elseif($ext == "png")
		$im = imagecreatefrompng($sourcefile);
	elseif($ext == "gif")
		$im = imagecreatefromgif($sourcefile);

	$x = imagesx($im);
	$y = imagesy($im);

	if($x <= $xmax && $y <= $ymax)
		return $im;

	if($x >= $y) {
		$newx = $xmax;
		$newy = $newx * $y / $x;
	}
	else {
		$newy = $ymax;
		$newx = $x / $y * $newy;
	}

	$im2 = imagecreatetruecolor($newx, $newy);
	imagecopyresized($im2, $im, 0, 0, 0, 0, floor($newx), floor($newy), $x, $y);
	return $im2;
}


function Zip($source, $destination)
{
	if (!extension_loaded('zip') || !file_exists($source)) {
		return false;
	}

	$zip = new ZipArchive();
	if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
		Logger::logError( 'Zip error ' . $zip . ' ' . zipFileErrMsg($zip), 'ZIPARCHIVE::CREATE', $path);
		return false;
	}

	$source = str_replace('\\', '/', realpath($source));

	if (is_dir($source) === true)
	{
		$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);

		foreach ($files as $file)
		{
			$file = str_replace('\\', '/', $file);

			// Ignore "." and ".." folders
			if( in_array(substr($file, strrpos($file, '/')+1), array('.', '..')) )
				continue;

			$file = realpath($file);

			if (is_dir($file) === true)
			{
				$zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
			}
			else if (is_file($file) === true)
			{
				$localfile = $file;
				if ( substr($localfile, 0, strlen($source)) == $source ) {
					$localfile = substr($localfile, strlen($source));
				}
				$zip->addFile($file, $localfile);
			}
		}
	}
	else if (is_file($source) === true)
	{
		$zip->addFromString(basename($source), file_get_contents($source));
	}

	$status = $zip->close();
	if ( $status != true ) {
		Logger::logError( 'Zip error ' . $zip . ' ' . zipFileErrMsg($zip), 'ZIPARCHIVE::CREATE', $path);
	}
	return $status;
}

if (!function_exists('http_response_code')) {
	function http_response_code($code = NULL) {
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
					exit('Unknown http status code "' . htmlentities($code) . '"');
				break;
			}

			$protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
			header($protocol . ' ' . $code . ' ' . $text);
			$GLOBALS['http_response_code'] = $code;
		}
		else {
			$code = (isset($GLOBALS['http_response_code']) ? $GLOBALS['http_response_code'] : 200);
		}

		return $code;
	}
}

function currentVersionNumber()
{
	$versionNum = '0.0.0';
	$versionPath = appendPath( SYSTEM_PATH, 'VERSION');
	if ( file_exists($versionPath) ) {
		$versionNum = trim(file_get_contents($versionPath));
	}
	return $versionNum;
}

function currentVersionHash()
{
	$shell = utilities\ShellCommand::create('git rev-parse --verify HEAD');
	if ( $shell->exec() == 0 ) {
		return trim($shell->stdout());
	}
	return "Error";
}

function currentRemoteHash()
{
	$shell = utilities\ShellCommand::create('git branch');
	if ( $shell->exec() == 0 ) {
		$branch = $shell->stdout();
		$branch = substr($branch, strpos($branch, "* "));

		$shell = utilities\ShellCommand::create("git ls-remote origin " . $branch);
		if ( $shell->exec() == 0 ) {
			$hash = $shell->stdout();
			$array = explode(" ", $hash);
			return $array[0];
		}
	}

	return "Unknown";
}

function currentChangeLog()
{
	$change_log = '';
	$path = appendPath( SYSTEM_PATH, 'CHANGES');
	if ( file_exists($path) ) {
		$change_log = trim(file_get_contents($path));
	}
	return $change_log;
}

/** load these addition common functions after the base collection are loaded */
require_once SYSTEM_PATH . 'application/config/common_arrays.php';
require_once SYSTEM_PATH . 'application/config/common_filesystem.php';
require_once SYSTEM_PATH . 'application/config/common_http.php';
require_once SYSTEM_PATH . 'application/config/common_images.php';
