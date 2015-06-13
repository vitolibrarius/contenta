<?php

namespace db;

use \Database as Database;
use \Localized as Localized;
use \Logger as Logger;
use \Model as Model;
use \SQL as SQL;

class DeleteSQL extends SQL
{
	public $model;

    public function __construct(Model $model, Qualifier $qualifier = null)
    {
    	parent::__construct();
    	if ( is_null($model) ) {
    		throw new \Exception( "Must specfify the model to selct from .. " );
    	}

    	$this->model = $model;
    	$this->where($qualifier);
    	return $this;
    }

	public function sqlParameters()
	{
		return (isset($this->qualifier) ? $this->qualifier->sqlParameters() : null);
	}

	public function sqlStatement()
	{
		$components = array(
			SQL::CMD_DELETE,
			SQL::SQL_FROM,
			$this->model->tableName(),
		);

		if ( isset($this->qualifier) ) {
			$components[] = SQL::SQL_WHERE;
			$components[] = $this->qualifier->sqlStatement();
		}

		return implode(" ", $components );
	}

}
