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
	public $level;
	public $created;


	public function formattedDateTime_created() { return $this->formattedDate( Log::created, "M d, Y H:i" ); }
	public function formattedDate_created() {return $this->formattedDate( Log::created, "M d, Y" ); }


	// to-one relationship
	public function logLevel()
	{
		if ( isset( $this->level ) ) {
			$model = Model::Named('Log_Level');
			return $model->objectForCode($this->level);
		}
		return false;
	}

	public function setLogLevel(Log_LevelDBO $obj = null)
	{
		if ( isset($obj, $obj->code) && (isset($this->level) == false || $obj->code != $this->level) ) {
			parent::storeChange( Log::level, $obj->code );
			$this->saveChanges();
		}
	}


	/** Attributes */
	public function trace()
	{
		return parent::changedValue( Log::trace, $this->trace );
	}

	public function setTrace( $value = null)
	{
		parent::storeChange( Log::trace, $value );
	}

	public function trace_id()
	{
		return parent::changedValue( Log::trace_id, $this->trace_id );
	}

	public function setTrace_id( $value = null)
	{
		parent::storeChange( Log::trace_id, $value );
	}

	public function context()
	{
		return parent::changedValue( Log::context, $this->context );
	}

	public function setContext( $value = null)
	{
		parent::storeChange( Log::context, $value );
	}

	public function context_id()
	{
		return parent::changedValue( Log::context_id, $this->context_id );
	}

	public function setContext_id( $value = null)
	{
		parent::storeChange( Log::context_id, $value );
	}

	public function message()
	{
		return parent::changedValue( Log::message, $this->message );
	}

	public function setMessage( $value = null)
	{
		parent::storeChange( Log::message, $value );
	}

	public function session()
	{
		return parent::changedValue( Log::session, $this->session );
	}

	public function setSession( $value = null)
	{
		parent::storeChange( Log::session, $value );
	}


}

?>
