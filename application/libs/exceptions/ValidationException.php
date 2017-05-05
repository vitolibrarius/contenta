<?php

namespace exceptions;

class ValidationException extends \Exception
{
	public function __construct($message, $code = 0 ) {
		parent::__construct($message, intval($code));
	}
}
