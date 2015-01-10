<?php

namespace utilities;

class NoCommandException extends \Exception {}

class ShellCommand
{
	protected $command;
	protected $commandAsExecuted;
	protected $paramArray = array();

	protected $capture_output = false;
	protected $output = null;
	protected $return_code = null;

	public static function create( $cmd, $param = null )
	{
		$shell = new ShellCommand();
		$shell->setCommand($cmd);
		$shell->addParameters($param);
		return $shell;
	}

	function __construct()
	{
	}

	public function setCommand($cmd)
	{
		$this->command = $cmd;
		return $this;
	}

	public function commandAsExecuted()
	{
		return $this->commandAsExecuted;
	}

	public function shellEscapedCommand()
	{
		$shellCmd = null;
		if ( empty($this->paramArray) == false) {
			$escaped_params = array();
			foreach ($this->paramArray as &$param) {
				$escaped_params[] = escapeshellarg($param);
			}
			array_unshift($escaped_params, $this->command);
			$shellCmd = implode(' ', $escaped_params);
		}
		else {
			$shellCmd = $this->command;
		}
		return $shellCmd;
	}

	public function addParameters($params)
	{
		if (is_array($params)) {
			if ( empty($params) == false) {
				$this->paramArray = array_merge($this->paramArray, $params);
			}
		}
		else if (is_null($params) == false) {
			$working = func_get_args();
			if ( empty($working) == false) {
				$this->paramArray = array_merge($this->paramArray, $working);
			}
		}
		return $this;
	}

	public function willCaptureOutput()
	{
		return $this->capture_output;
	}

	public function setCaptureOutput($on_off)
	{
		$this->capture_output = boolval($on_off);
		return $this;
	}

	public function shellOutput()
	{
		return $this->output;
	}

	public function shellOutputImploded($glue = '')
	{
		return (is_array($this->output) ? implode($glue, $this->output) : $glue);
	}

	public function return_code()
	{
		return $this->return_code;
	}

	public function exec()
	{
		if (empty($this->command)) {
			throw new NoCommandException('No command to execute');
		}

		$this->commandAsExecuted = $this->shellEscapedCommand();
		$this->output = array();

		$pipe = popen($this->commandAsExecuted, 'r');
		while( feof($pipe) == false ) {
			$line = trim(fgets($pipe));
			if ($this->capture_output) {
				$this->output[] = $line;
			}
		}

		$this->return_code = pclose($pipe);
		return $this->return_code;
	}
}
