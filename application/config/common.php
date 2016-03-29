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

function split_lines($str)
{
	return preg_split('/\n|\r/', $str, -1, PREG_SPLIT_NO_EMPTY);
}

function get_short_class($obj)
{
	return (new \ReflectionClass($obj))->getShortName();
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

function contains($haystack, $needle)
{
	return ($needle != '' && strpos($haystack, $needle) !== false);
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
		\Logger::logError('Zip error ' . $zip . ' ' . zipFileErrMsg($zip),  'zipFileList', basename($path));
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

function normalizeSearchString( $string = null )
{
	if ( is_null($string) == false ) {
		$string = strtolower($string);
		$string = preg_replace("/[^[:alnum:][:space:]]/ui", ' ', $string);
		$string = preg_replace('/\s+/', ' ', $string);
	}
	return $string;
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

function formatSizeUnits($value)
{
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

/** load these addition common functions after the base collection are loaded */
require_once SYSTEM_PATH . 'application/config/common_arrays.php';
require_once SYSTEM_PATH . 'application/config/common_filesystem.php';
require_once SYSTEM_PATH . 'application/config/common_http.php';
require_once SYSTEM_PATH . 'application/config/common_images.php';
require_once SYSTEM_PATH . 'application/config/common_dbo.php';
require_once SYSTEM_PATH . 'application/config/common_daemons.php';
