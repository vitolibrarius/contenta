<?php

namespace model\logs;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use model\logs\Log as Log;

class LogDBO extends DataObject
{
	public $id;
	public $trace;
	public $trace_id;
	public $context;
	public $context_id;
	public $message;
	public $session;
	public $level;
	public $created;


	public function formattedDateTimeCreated() { return $this->formattedDate( Log::created, "M d, Y H:i" ); }
	public function formattedDateCreated() {return $this->formattedDate( Log::created, "M d, Y" ); }

	// to-one relationship
	public function logLevel()
	{
		if ( isset( $this->level ) ) {
			$model = Model::Named('Log_Level');
			return $model->objectForCode($this->level);
		}
		return false;
	}

}

?>
