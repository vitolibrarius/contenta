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
