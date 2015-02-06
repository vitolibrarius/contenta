<?php

namespace utilities;

class NoCommandException extends \Exception {}

class ShellCommand
{
	protected $command;
	protected $commandAsExecuted;
	protected $paramArray = array();

	protected $capture_output = true;
	protected $std_out = null;
	protected $std_err = null;
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

	public function stdout()
	{
		return $this->std_out;
	}

	public function stderr()
	{
		return $this->std_err;
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
		$output_spec = array(
			1 => array('pipe', 'w'),
			2 => array('pipe', 'w'),
		);
		$output = array();

		$process = proc_open($this->commandAsExecuted, $output_spec, $output);
		$this->std_out = stream_get_contents($output[1]);
		$this->std_err = stream_get_contents($output[2]);
		foreach ($output as $pipe) {
			fclose($pipe);
		}
		$this->return_code = trim(proc_close($process));

		return $this->return_code;
	}
}
