<?php

namespace model;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

class LogDBO extends DataObject
{
	public $trace;
	public $trace_id;
	public $context;
	public $context_id;
	public $level;
	public $created;
	public $message;
	public $session;

	public function __toString()
	{
		return date('M d, Y', $this->created) . ' |'
			. $this->level . '| '
			. $this->context . '(' . $this->context_id . ')'
			. $this->message;
	}
}
