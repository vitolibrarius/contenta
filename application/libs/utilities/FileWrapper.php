<?php

namespace utilities;

use \ZipArchive as ZipArchive;

use \Logger as Logger;
use \Cache as Cache;
use \ClassNotFoundException as ClassNotFoundException;

abstract class FileWrapper
{
	const FILELIST = 'filelist';

	final public static function instance($filepath)
	{
		$extension = file_ext($filepath);

		try {
			$fileClass = 'utilities\\' . $extension . 'FileWrapper';
			return new $fileClass($filepath);
		}
		catch ( ClassNotFoundException $exception ) {
			Logger::logException( $exception );
		}

		return false;
	}

	final public static function force($filepath, $extension = "txt")
	{
		try {
			$fileClass = 'utilities\\' . $extension . 'FileWrapper';
			return new $fileClass($filepath);
		}
		catch ( ClassNotFoundException $exception ) {
			Logger::logException( $exception );
		}

		return false;
	}

	public static function createWrapperForSource($sourcePath = null, $destinationPath = null, $extension = "zip")
	{
		try {
			$fileClass = 'utilities\\' . $extension . 'FileWrapper';
			return $fileClass::createWrapper($sourcePath, $destinationPath);
		}
		catch ( ClassNotFoundException $exception ) {
			Logger::logException( $exception );
		}

		return false;
	}

	public function __construct($path)
	{
		$this->sourcepath = $path;
	}

	public function __destruct()
	{
		if (is_dir($this->tempDirectory())) {
			destroy_dir($this->tempDirectory());
		}
	}

	public function tempDirectory() {
		$tempDir = appendPath(sys_get_temp_dir(), hash(HASH_DEFAULT_ALGO, $this->cacheKeyRoot()));
		is_dir($tempDir) || mkdir($tempDir, 0755) || die('failed to create temp dir ' . $tempDir);
		return $tempDir;
	}

	public function cacheKeyRoot() {
		return (is_file($this->sourcepath) ?
			hash_file(HASH_DEFAULT_ALGO, $this->sourcepath) :
			hash(HASH_DEFAULT_ALGO, $this->sourcepath)
		);
	}

	public function errorMessage( $errorCode = 0 )
	{
		return null;
	}

	public function testWrapper( &$errorCode = 0 )
	{
		$errorCode = 0;
		return null;
	}

	public function wrapperContents()
	{
		return basename($this->sourcepath);
	}

	public function wrapperContentCount()
	{
		$content = $this->wrapperContents();
		if ( is_array($content) ) {
			return count($content);
		}
		else if ( is_string($content) ) {
			return 1;
		}
		return 0;
	}

	public function wrappedDataForName($name)
	{
		if ( isset($name) == false || strlen($name) == 0) {
			return false;
		}

		$key = Cache::MakeKey( $this->cacheKeyRoot(), $name );
		return Cache::Fetch( $key );
	}

	public function wrappedThumbnailNameForName($name, $width = null, $height = null)
	{
		if ( isset($name) == false || strlen($name) == 0) {
			return false;
		}
		return file_ext_strip($name) . '-(' . $width . 'x'. $height . ')thumbnail.png';
	}

	public function wrappedThumbnailForName($name, $width = null, $height = null)
	{
		return false;
	}

	public function firstImageThumbnailName()
	{
		$content = $this->wrapperContents();
		if ( is_array($content) ) {
			foreach( $content as $item ) {
				$originalExt = file_ext($item);
				if ( in_array($originalExt, array('jpg','jpeg','gif','png')) == true ) {
					return $item;
				}
			}
		}
		else if ( is_string($content) ) {
			$originalExt = file_ext($content);
			if ( in_array($originalExt, array('jpg','jpeg','gif','png')) == true ) {
				return $content;
			}
		}

		return null;
	}

	public function unwrapToDirectory($dest = null)
	{
		return false;
	}
}

class zipFileWrapper extends FileWrapper
{
	public function __construct($path)
	{
		parent::__construct($path);
	}

	public static function createWrapper($sourcePath = null, $destinationPath = null)
	{
		if ( is_null($sourcePath) || is_null($destinationPath) ) {
			throw new Exception('Source and/or destination path is null.');
		}

		if ( file_exists($sourcePath) == false ) {
			throw new Exception("Source does not exist $sourcePath");
		}

		if ( file_exists($destinationPath) == true ) {
			unlink($destinationPath) || die("Source does not exist $destinationPath");
		}

		$success = Zip($sourcePath, $destinationPath);
		if ( $success == true ) {
			return FileWrapper::instance($destinationPath);
		}
		return false;
	}

	public function errorMessage( $errorCode = 0 )
	{
		if ( $errorCode > 0 ) {
			switch($errorCode) {
				case ZipArchive::ER_OPEN:
					return 'Unable to read file ' . $this->sourcepath;
				case ZipArchive::ER_NOENT:
					return 'No such file ' . $this->sourcepath;
				case ZipArchive::ER_NOZIP:
					return  'Not a zip file ' . $this->sourcepath;
				case ZipArchive::ER_INCONS:
					return 'Inconsistent file ' . $this->sourcepath;
				case ZipArchive::ER_CRC :
					return 'Failed checksum ' . $this->sourcepath;
				default:
					return 'ZIP Error Code ' . $errorCode;
			}
		}
		return null;
	}

	public function testWrapper( &$errorCode = 0 )
	{
		$errorCode = 0;
		$zip = new ZipArchive();
		$res = $zip->open($this->sourcepath, ZipArchive::CHECKCONS);
		if ( is_bool($res) == false && $res > 0 ) {
			$errorCode = $res;
			Logger::logError( $this->errorMessage($errorCode), get_class($this), basename($this->sourcepath) );
		}
		else {
			$zip->close();
		}
		return $this->errorMessage($errorCode);
	}

	public function wrapperContents()
	{
		$key = Cache::MakeKey( $this->cacheKeyRoot(), FileWrapper::FILELIST );
		$filelist = Cache::Fetch( $key );
		if ( $filelist == false ) {
			$filelist = array();
			$zip = zip_open($this->sourcepath);
			if (is_resource($zip)) {
				while ($zip_entry = zip_read($zip)) {
					$entrySize = zip_entry_filesize($zip_entry);
					if (empty($entrySize)) continue;

					$filelist[] = zip_entry_name($zip_entry);
				}

				zip_close($zip);

				if ($filelist != false) {
					if (sort($filelist) == false) {
						Logger::logError( 'Sort Failed ' . zipFileErrMsg($zip), $this->sourcepath );
					}
					Cache::Store($key, $filelist);
				}
			}
			else {
				Logger::logError( 'Zip error ' . zipFileErrMsg($zip), $this->sourcepath );
			}
		}
		return $filelist;
	}

	public function wrappedDataForName($name) {
		if ( isset($name) == false || strlen($name) == 0) {
			return false;
		}

		$data = parent::wrappedDataForName($name);
		if ( $data == false ) {
			$zip = zip_open($this->sourcepath);
			if (is_resource($zip)) {
				while ( $zip_entry = zip_read($zip) ) {
					if ($data != false) {
						break;
					}

					$entrySize = zip_entry_filesize($zip_entry);
					if (empty($entrySize)) continue;

					$entryName = zip_entry_name($zip_entry);
					if ( $entryName === $name ) {
						if (zip_entry_open($zip, $zip_entry, "r")) {
							$data = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
							if ( $data != false ) {
								$key = Cache::MakeKey( $this->cacheKeyRoot(), $name );
								Cache::Store($key, $data);
							}

							zip_entry_close($zip_entry);
						}
					}
				}

				zip_close($zip);
			}
			else {
				Logger::logError( 'Zip error ' . zipFileErrMsg($zip), $this->sourcepath );
			}
		}
		return $data;
	}

	public function wrappedThumbnailForName($name, $width = null, $height = null) {
		if ( isset($name) == false || strlen($name) == 0) {
			Logger::logError( 'wrappedThumbnailForName no name specified', 'FileWrapper' );
			return false;
		}

		$originalExt = file_ext($name);
		if ( in_array($originalExt, array('jpg','jpeg','gif','png')) == false ) {
			Logger::logWarning( 'Cannot create thumbnail for non-image entry ' . $name, $this->sourcepath );
			return false;
		}

		if ( (isset($width) == false) || (intval($width) < 10)) {
			$width = 300;
		}

		if ( (isset($height) == false) || (intval($height) < 10)) {
			$height = 300;
		}

		$thmbName = $this->wrappedThumbnailNameForName($name, $width, $height);
		$cacheThumbKey = Cache::MakeKey( $this->cacheKeyRoot(), $thmbName );
		$thumbnail = Cache::Fetch( $cacheThumbKey );

		if ( $thumbnail == false ) {
			$data = $this->wrappedDataForName($name);
			if ( $data != false ) {
				$tempPath = appendPath($this->tempDirectory(), sanitize_filename($name));
				$thmbPath = appendPath($this->tempDirectory(), sanitize_filename($thmbName));

				if (file_put_contents( $tempPath, $data ) ) {
					$resized = resize_image( $tempPath, $width, $height);
					imagepng($resized, $thmbPath );
					$thumbnail = file_get_contents($thmbPath);

					Cache::Store($cacheThumbKey, $thumbnail);

					unlink($tempPath);
					unlink($thmbPath);
				}
			}
			else {
				Logger::logError( 'No wrapped data for name', 'FileWrapper', $name );
			}
		}
		return $thumbnail;
	}

	public function unwrapToDirectory($dest = null)
	{
		return false;
	}
}

class cbzFileWrapper extends zipFileWrapper
{
	public function __construct($path)
	{
		parent::__construct($path);
	}

	public static function createWrapper($sourcePath = null, $destinationPath = null)
	{
		return parent::createWrapper($sourcePath, $destinationPath);
	}
}

class cbrFileWrapper extends FileWrapper
{
	public function __construct($path)
	{
		parent::__construct($path);
		$this->UNRAR_PATH = findPathForTool('unrar');
		if ($this->UNRAR_PATH == false) {
			throw new Exception('Could not find unrar. ');
		}
	}

	public function errorMessage( $errorCode = 0 )
	{
		if ( $errorCode > 0 ) {
			switch ($errorCode) {
				case 1: return 'RARX_WARNING'; break;
				case 2: return 'RARX_FATAL'; break;
				case 3: return 'RARX_CRC'; break;
				case 4: return 'RARX_LOCK'; break;
				case 5: return 'RARX_WRITE'; break;
				case 6: return 'RARX_OPEN'; break;
				case 7: return 'RARX_USERERROR'; break;
				case 8: return 'RARX_MEMORY'; break;
				case 9: return 'RARX_CREATE'; break;
				case 10: return 'RARX_NOFILES'; break;
				case 11: return 'RARX_BADPWD'; break;
				default:
					return 'RAR Error Code ' . $errorCode;
			}
		}

		return null;
	}

	public function testWrapper( &$errorCode = 0 )
	{
		$errorCode = 0;
		$cmd = $this->UNRAR_PATH . ' t "' . $this->sourcepath . '"';
		exec($cmd, $output, $success);
		if ( $success != 0 ) {
			$errorCode = $success;

			Logger::logWarning( $cmd, 'Unrar', 'command' );
			Logger::logWarning( 'output ' . var_export($output, true), 'Unrar', $success );
			Logger::logError( $this->errorMessage($errorCode), get_class($this), basename($this->sourcepath) );
		}

		return $this->errorMessage($errorCode);
	}

	public function unwrapToDirectory($dest = null)
	{
		if ( $this->testWrapper() != null ) {
			throw new Exception( "RAR file error" );
		}

		if ( is_null($dest) ) {
			throw new Exception( "Destination path is required" );
		}

		if ( file_exists( $dest ))
		{
			destroy_dir($dest);
		}
		mkdir($dest);

		$cmd = $this->UNRAR_PATH . ' x -r "' . $this->sourcepath . '" "' . $dest . '"';
		exec($cmd, $output, $success);
		if ( $success != 0 ) {
			Logger::logWarning( $cmd, 'Unrar', 'command' );
			Logger::logWarning( 'output ' . var_export($output, true), 'Unrar', $success );
			Logger::logError( 'RAR File corrupt', get_class($this), basename($this->sourceDir));
		}
		return ($success == 0);
	}
}



?>
