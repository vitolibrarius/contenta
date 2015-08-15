<?php

namespace exceptions;

class ImportMediaException extends \Exception
{
	public function __construct($message, $code = 0 ) {
		parent::__construct($message, $code);
	}
}
