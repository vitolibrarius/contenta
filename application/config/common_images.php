<?php

/** common image file extensions
 */
function imageExtensions()
{
	return array( 'png', 'jpg', 'jpeg', 'gif' );
}

/** downloads an image for the given URL and stores it in the specified directory.
 *	assumes the image extension will be set by the downloaded content
 */
function downloadImage($url = null, $dir = null, $key_name_hint = null )
{
	if (is_null($url) || is_null($dir) ) {
		throw new \Exception("Blank url or destination directory");
	}
	else {
		if ( is_dir($dir) == false ) {
			throw new \Exception( $dir . " is not a directory");
		}
		else {
			if ( is_null($key_name_hint) ) {
				$filename = basename($url);
			}
			else {
				$extension = file_ext(basename($url));
				$filename = $key_name_hint . "." . $extension;
			}

			$options = array(
				'http'=>array(
					'method'=>"GET",
					'header'=>"Accept-language: en\r\n" .
					"User-Agent: " . CONTENTA_USER_AGENT
				)
			);
			$context = stream_context_create($options);
			$data = file_get_contents($url, false, $context);
			if ( $data != false )
			{
				$dest = appendPath($dir, $filename);
				if (file_put_contents( $dest, $data )) {
					return $filename;
				}
			}
			else {
				\Logger::logWarning( "Failed to load image from URL " . $url, $dir, $filename );
			}
		}
	}
	return null;
}

function open_image ($file)
{
	$image = false;

	if (extension_loaded('gd') &&
		function_exists('imagecreatefromjpeg') && function_exists('imagecreatefrompng') && function_exists('imagecreatefromgif')) {
		//detect type and process accordinally
		$size = getimagesize($file);
		switch($size["mime"]) {
			case "image/jpeg":
				$image = imagecreatefromjpeg($file); //jpeg file
				break;
			case "image/gif":
				$image = imagecreatefromgif($file); //gif file
				break;
			case "image/png":
				$image = imagecreatefrompng($file); //png file
				break;
			default:
				break;
		}
	}
	else {
		\Logger::logError( "Missing image functions 'imagecreatefromjpeg', 'imagecreatefrompng', 'imagecreatefromgif'" );
	}
	return $image;
}

function resize_image($sourcefile, $xmax, $ymax)
{
	if (function_exists('imagecreatetruecolor') && function_exists('imagecopyresampled')) {
		$image = open_image( $sourcefile );
		if ( $image != false ) {
			$x = imagesx($image);
			$y = imagesy($image);

			if($x <= $xmax && $y <= $ymax)
				return $image;

			if($x >= $y) {
				$newx = $xmax;
				$newy = $newx * $y / $x;
			}
			else {
				$newy = $ymax;
				$newx = $x / $y * $newy;
			}

			$image2 = imagecreatetruecolor($newx, $newy);
			imagecopyresampled($image2, $image, 0, 0, 0, 0, floor($newx), floor($newy), $x, $y);
			return $image2;
		}
	}
	else {
		\Logger::logError( "Missing image functions 'imagecreatetruecolor', 'imagecopyresampled'" );
	}

	return false;
}

