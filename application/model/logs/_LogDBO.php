<?php

namespace model\logs;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

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


	public function pkValue()
	{
		return $this->{Log::id};
	}

	public function modelName()
	{
		return "Log";
	}

	public function dboName()
	{
		return "\model\logs\LogDBO";
	}

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

	public function setLogLevel(Log_LevelDBO $obj = null)
	{
		if ( isset($obj, $obj->code) && (isset($this->level_code) == false || $obj->code != $this->level_code) ) {
			parent::storeChange( Log::level_code, $obj->code );
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
