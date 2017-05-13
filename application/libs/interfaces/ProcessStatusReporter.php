<?php

namespace interfaces;

interface ProcessStatusReporter
{
	public function setProcessMaximum($value = 100);
	public function setProcessMinimum($value = 0);
	public function setProcessCurrent($value);

	public function setProcessMessage($msg);
}
