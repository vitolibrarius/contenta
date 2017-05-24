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

function boolValue( $v, $default = false )
{
	// Returns TRUE for "1", "true", "on" and "yes"
	// Returns FALSE for "0", "false", "off" and "no"
	// Returns NULL otherwise.
	$v = filter_var($v, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
	if (is_null($v)) {
		$v = boolval($default);
	}
	return $v;
}

function split_lines($str)
{
	return preg_split('/\n|\r/', $str, -1, PREG_SPLIT_NO_EMPTY);
}

function get_short_class($obj)
{
	return (new \ReflectionClass($obj))->getShortName();
}

function startsWith($needle, $haystack)
{
	return $needle === "" || strpos($haystack, $needle) === 0;
}
function endsWith($needle, $haystack)
{
	return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
}

function contains($needle, $haystack)
{
	return ($needle != '' && strpos($haystack, $needle) !== false);
}

function zipFileList($path)
{
	$list = false;
	if ( is_file( $path ) ) {
		$zip = zip_open($path);
		if (is_resource($zip)) {
			$list = array();
			while ($zip_entry = zip_read($zip)) {
				$list[] = zip_entry_name($zip_entry);
			}
			zip_close($zip);
		}
	}
	return $list;
}

function classNames($file)
{
	$classNames = false;
	if ( is_file( $file ) ) {
		$php_file = file_get_contents($file);
		$tokens = token_get_all($php_file);
		$class_token = false;
		$classNames = array();
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

function sanitize_filename($string, $maxLength = 256, $force_lowercase = false, $anal = true)
{
	$ext = "." . file_ext($string);
	$clean = file_ext_strip($string);
	$clean = sanitize($clean, $force_lowercase, $anal );
	$max = min(max($maxLength, 10), 256) - strlen($ext) - 2;
	if (strlen($clean) > $max) {
		$characters = floor($max / 2);
		return substr($clean, 0, $characters) . '..'
			. substr($clean, -1 * $characters)
			. ((strlen($ext) > 1) ? $ext : "");
	}
	return $clean . ((strlen($ext) > 1) ? $ext : "");
}

function sanitize_html_id($string = '')
{
	if ( is_string($string) == false ) {
		$string = "$string";
	}
	$clean = str_replace(chr(0xCA), '', $string);

	if (preg_match('/[^a-zA-Z0-9_\-\s?!,]/', $clean) == true) {
		$strip = array("~", "`", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "_", "=", "+", "[", "{", "]",
			"}", "\\", "|", ";", ":", "\"", "'", ",", "<", ">", "/", "?", ".");
		$clean = str_replace($strip, "_", $clean);
	}
	return $clean;
}

function sanitize($string, $force_lowercase = true, $anal = false, $default = null)
{
	if ( is_string($string) == false ) {
		$string = "$string";
	}
	$clean = str_replace(chr(0xCA), '', $string);

	if (preg_match('/[^a-zA-Z0-9_\-.\s?!,]/', $string) == true) {
		$clean = normalize($clean);

		if ( $anal === true ) {
			$strip = array("~", "`", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "=", "+", "[", "{", "]",
				"}", "\\", "|", ";", ":", "\"", "'", ",", "<", ">", "/", "?");
			$clean = str_replace($strip, "_", $clean);
			// remove duplicates
			$clean = preg_replace('/(_)\1+/', '$1', $clean);
		}
	}

	if ($force_lowercase != false) {
		if (function_exists('mb_strtolower')) {
			$clean = mb_strtolower($clean, 'UTF-8');
		}
		else {
			$clean = strtolower($clean);
		}
	}

	$clean = trim($clean);

	return (strlen($clean) > 0 ? $clean : (is_null($default) ? randomString() : $default));
}

function normalize($string)
{
	// normalize characters
	$a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ');
	$b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');
	$clean = str_replace($a, $b, $string);

	return $clean;
}

function normalizeSearchString( $string = null )
{
	if ( is_string($string) == false ) {
		$string = "$string";
	}
	$clean = str_replace(chr(0xCA), '', $string);

	if (preg_match('/[^a-zA-Z0-9_\-.\s]/', $string) == true) {
		$clean = normalize($clean);

		$strip = array("~", "`", "!", "@", "#", "$", "%", "^", "*", "(", ")", "_", "=", "+", "[", "{", "]",
				"}", "\\", "|", ";", "\"", ",", "<", ">", "/", "?");
		$clean = str_replace($strip, " ", $clean);
		$clean = preg_replace("/-(-)+/us", "-", $clean);
		$clean = preg_replace("/_(_)+/us", "_", $clean);
		$clean = preg_replace("/\\.(\\.)+/us", ".", $clean);
		$clean = preg_replace("/\\s(\\s)+/us", " ", $clean);
	}

	if (function_exists('mb_strtolower')) {
		$clean = mb_strtolower($clean, 'UTF-8');
	}
	else {
		$clean = strtolower($clean);
	}

	$clean = trim($clean);

	return (strlen($clean) > 0 ? $clean : "Contenta Search");
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

function lines($string = null)
{
	if ( isset($string) && strlen($string) > 0 ) {
		$sentences = preg_split("/\\r\\n|\\r|\\n/", $string);
		return (is_array($sentences) ? array_filter($sentences) : $sentences);
	}
	return null;
}

function convertToBytes ($val = '')
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
			$val *= GIGABYTE;
			break;
		case 'm':
		case 'mb':
			$val *= MEGABYTE;
			break;
		case 'k':
		case 'kb':
			$val *= KILOBYTE;
			break;
	}

	return (int) $val;
}

function formatSizeUnits($value = null)
{
	if ( is_null($value) || (is_string($value) && strlen($value) == 0)) {
		return "unknown";
	}

	$bytes = convertToBytes($value);
	if ($bytes >= GIGABYTE)
	{
		$bytes = number_format($bytes / GIGABYTE, 2) . ' GB';
	}
	elseif ($bytes >= MEGABYTE)
	{
		$bytes = number_format($bytes / MEGABYTE, 2) . ' MB';
	}
	elseif ($bytes >= KILOBYTE)
	{
		$bytes = number_format($bytes / KILOBYTE, 2) . ' KB';
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

function formattedTimeElapsed ($diff = 0)
{
	if ( isset($diff) ) {
		if ( intval($diff) == 0 ) {
			return "less than one second";
		}
		else if ( intval($diff) > 0 ) {
			$tokens = array (
				31536000 => 'year',
				2592000 => 'month',
				604800 => 'week',
				86400 => 'day',
				3600 => 'hour',
				60 => 'minute',
				1 => 'second'
			);

			foreach ($tokens as $unit => $text) {
				if ($diff < $unit) {
					continue;
				}
				$numberOfUnits = floor($diff / $unit);
				return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'');
			}
		}
	}
    return '';
}

function Zip($source, $destination)
{
	if (!extension_loaded('zip') || !file_exists($source)) {
		return false;
	}

	$zip = new ZipArchive();
	if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
		\Logger::logError( 'Zip error ' . $zip . ' ' . zipFileErrMsg($zip), 'ZIPARCHIVE::CREATE', $path);
		return false;
	}

	$source = str_replace('\\', DIRECTORY_SEPARATOR, realpath($source));

	if (is_dir($source) === true)
	{
		$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);

		foreach ($files as $file)
		{
			$file = str_replace('\\', DIRECTORY_SEPARATOR, $file);

			// Ignore "." and ".." folders
			if( in_array(substr($file, strrpos($file, DIRECTORY_SEPARATOR)+1), array('.', '..')) )
				continue;

			$file = realpath($file);

			if (is_dir($file) === true)
			{
				$zip->addEmptyDir(str_replace($source . DIRECTORY_SEPARATOR, '', $file . DIRECTORY_SEPARATOR));
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
		\Logger::logError( 'Zip error ' . $zip . ' ' . zipFileErrMsg($zip), 'ZIPARCHIVE::CREATE', $path);
	}
	return $status;
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

function randomString($size = 10, $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
{
	$randstring = '';
	$size = min(max(intval($size), 1), 512);
	for ($i = 0; $i < $size; $i++) {
		$randstring .= $characters[rand(0, strlen($characters) -1)];
	}
	return $randstring;
}

/** load these addition common functions after the base collection are loaded */
require_once SYSTEM_PATH . 'application/config/common_arrays.php';
require_once SYSTEM_PATH . 'application/config/common_filesystem.php';
require_once SYSTEM_PATH . 'application/config/common_http.php';
require_once SYSTEM_PATH . 'application/config/common_images.php';
require_once SYSTEM_PATH . 'application/config/common_dbo.php';
require_once SYSTEM_PATH . 'application/config/common_daemons.php';
