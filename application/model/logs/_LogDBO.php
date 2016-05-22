<?php

namespace model\logs;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use \model\logs\Log as Log;

/* import related objects */
use \model\logs\Log_Level as Log_Level;
use \model\logs\Log_LevelDBO as Log_LevelDBO;

abstract class _LogDBO extends DataObject
{
	public $trace;
	public $trace_id;
	public $context;
	public $context_id;
	public $message;
	public $session;
	public $level_code;
	public $created;


	public function formattedDateTime_created() { return $this->formattedDate( Log::created, "M d, Y H:i" ); }
	public function formattedDate_created() {return $this->formattedDate( Log::created, "M d, Y" ); }


	// to-one relationship
	public function logLevel()
	{
		if ( isset( $this->level_code ) ) {
			$model = Model::Named('Log_Level');
			return $model->objectForCode($this->level_code);
		}
		return false;
	}

}

?>
