<?php
namespace utilities;

use \Logger as Logger;
use \Cache as Cache;
use \ClassNotFoundException as ClassNotFoundException;

class Lock
{
	private $mypid;
	private $lockfile;

	function __construct($fullpath)
	{
		$this->mypid = getmypid();
		$this->lockfile = $fullpath;
	}

	private function isRunning($existing) {
		$shell = "ps " . ((PHP_OS === 'Darwin') ? ' ax ' : '') . "| awk '{print $1}'";
		$output = shell_exec(  $shell );
		$pids = explode(PHP_EOL, $output);
		return in_array($existing, $pids);
	}

	public function lock()
	{
		if (file_exists($this->lockfile)) {
			// Is running?
			$existing = file_get_contents($this->lockfile);
			if ($existing != $this->mypid && $this->isRunning($existing)) {
				return false;
			}
		}

		file_put_contents($this->lockfile, $this->mypid);
		return $this->mypid;
	}

	public function unlock()
	{
		if (file_exists($this->lockfile)) {
			$existing = file_get_contents($this->lockfile);
			if ($existing == $this->mypid ) {
				safe_unlink($this->lockfile);
			}
		}
		return true;
	}
}

