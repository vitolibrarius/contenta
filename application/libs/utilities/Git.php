<?php

namespace utilities;

use \Logger as Logger;
use utilities\ShellCommand as ShellCommand;

class Git
{
	protected static $binary = '/usr/bin/git';
	protected $repository = null;

	public function __construct($path = null) {
		if (is_string($path)) {
			$this->setPath($path);
		}
	}

	private function setPath($path)
	{
		$fullpath = realpath($path);
		if (is_dir($fullpath)) {
			$gitDir = appendPath($fullpath, ".git");
			if (file_exists($gitDir) && is_dir($gitDir)) {
				$this->repository = $fullpath;
			}
			else {
				throw new Exception( '"' . $fullpath . '" is not a git repository');
			}
		}
		else {
			throw new Exception('"' . $fullpath . '" is not a directory');
		}
	}

	protected function run($command)
	{
		$shell = ShellCommand::create(Git::$binary . ' ' . $command);
		$status = $shell->exec();
		return array(
			"status" => $status,
			"stdout" => $shell->stdout(),
			"stderr" => $shell->stderr()
		);
	}

	protected function checkOwnershipRecursive( $badFilesFound, $ownerid, $directory,  $limit, $exclusions )
	{
		if (is_dir($directory)) {
			foreach (scandir($directory) as $file)
			{
				if ($file == '.' || $file == '..') continue;

				$path = $directory . DIRECTORY_SEPARATOR . $file;
				if ( fileowner($path) != $ownerid && in_array($file, $exclusions) == false) {
					$badFilesFound[] = $path;
				}

				if (is_dir($path) === true)
				{
					$badFilesFound = $this->checkOwnershipRecursive( $badFilesFound, $ownerid, $path,  $limit, $exclusions );
				}

				if ( count($badFilesFound) >= $limit ) {
					return $badFilesFound;
				}
			}
		}

		return $badFilesFound;
	}

	public function checkRepositoryOwnership( $limit = 10, $exclusions = null )
	{
		$uid = 0;
		if ( function_exists("posix_geteuid") == true ) {
			$uid = posix_geteuid();
		}
		else {
			$uid = `id -u`;
		}

		$badFiles = array();
		$excluded = (is_array($exclusions) ? $exclusions : array());

		$badFiles = $this->checkOwnershipRecursive( $badFiles, $uid, $this->repository,  $limit, $excluded );

		return array(
			"status" => count($badFiles),
			"badFiles" => $badFiles
		);
	}

	public function status()
	{
		return $this->run("status -sb");
	}

	public function pull()
	{
		return $this->run("pull origin master");
	}
}
