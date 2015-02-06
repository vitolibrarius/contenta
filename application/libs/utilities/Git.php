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

	public function status()
	{
		return $this->run("status -sb");
	}

	public function pull()
	{
		return $this->run("pull");
	}
}
