<?php

namespace utilities;

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

	public function testWrapper()
	{
		return false;
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

	public function testWrapper()
	{
		$zip = new ZipArchive();
		$res = $zip->open($this->sourcepath, ZipArchive::CHECKCONS);
		$zip->close();

		if ( $res != true ) {
			switch($res) {
				case ZipArchive::ER_OPEN:
					Logger::logError( 'Unable to read file ' . $this->sourcepath, get_class($this), basename($this->sourcepath) );
					return false;
				case ZipArchive::ER_NOENT:
					Logger::logError( 'No such file ' . $this->sourcepath, get_class($this), basename($this->sourcepath)  );
					return false;
				case ZipArchive::ER_NOZIP:
					Logger::logError( 'Not a zip file ' . $this->sourcepath, get_class($this), basename($this->sourcepath)  );
					return false;
				case ZipArchive::ER_INCONS:
					Logger::logError( 'Inconsistent file ' . $this->sourcepath, get_class($this), basename($this->sourcepath)  );
					return false;
				case ZipArchive::ER_CRC :
					Logger::logError( 'Failed checksum ' . $this->sourcepath, get_class($this), basename($this->sourcepath)  );
					return false;
			}
		}
		return true;
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

	public function testWrapper()
	{
		$cmd = $this->UNRAR_PATH . ' t "' . $this->sourcepath . '"';
		exec($cmd, $output, $success);
		if ( $success != 0 ) {
			Logger::logWarning( $cmd, 'Unrar', 'command' );
			Logger::logWarning( 'output ' . var_export($output, true), 'Unrar', $success );
			$errorType = 'Code ' . $success;
			switch ($success) {
				case 1: $errorType = 'RARX_WARNING'; break;
				case 2: $errorType = 'RARX_FATAL'; break;
				case 3: $errorType = 'RARX_CRC'; break;
				case 4: $errorType = 'RARX_LOCK'; break;
				case 5: $errorType = 'RARX_WRITE'; break;
				case 6: $errorType = 'RARX_OPEN'; break;
				case 7: $errorType = 'RARX_USERERROR'; break;
				case 8: $errorType = 'RARX_MEMORY'; break;
				case 9: $errorType = 'RARX_CREATE'; break;
				case 10: $errorType = 'RARX_NOFILES'; break;
				case 11: $errorType = 'RARX_BADPWD'; break;
				default:
				break;
			}
			Logger::logError( 'RAR File ' . $errorType, get_class($this), basename($this->sourcepath));
			return false;
		}

		return true;
	}

	public function unwrapToDirectory($dest = null)
	{
		if ( $this->testWrapper() == false ) {
			throw new Exception( "RAR file error" );
		}

		if ( is_null($dest) ) {
			throw new Exception( "Destination path is required" );
		}

		$unrarTool = findPathForTool('unrar');
		if ($unrarTool == false) {
			throw new Exception('Could not find unrar tool.');
		}

		if ( file_exists( $dest ))
		{
			destroy_dir($dest);
		}
		mkdir($dest);

		$cmd = $unrarTool . ' x -r "' . $this->sourcepath . '" "' . $dest . '"';
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
