<?php

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
			$data = file_get_contents($url);
			if ( $data != false )
			{
				$dest = appendPath($dir, $filename);
				if (file_put_contents( $dest, $data )) {
					return $key_name_hint . "." . $extension;
				}
			}
			else {
				Logger::logWarning( "Failed to load image from URL " . $url, $dir, $filename );
			}
		}
	}
	return null;
}
