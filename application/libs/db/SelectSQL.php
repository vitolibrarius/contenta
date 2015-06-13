<?php

namespace db;

use \Database as Database;
use \Localized as Localized;
use \Logger as Logger;
use \Model as Model;
use \SQL as SQL;

class SelectSQL extends SQL
{
	public $model;
	public $columns;
	public $order;

    public function __construct(Model $model, array $columns = array())
    {
    	parent::__construct();
    	if ( is_null($model) ) {
    		throw new \Exception( "Must specfify the model to selct from .. " );
    	}

    	if ( count($columns) == 0 ) {
    		$columns = $model->allColumnNames();
    	}

    	$this->model = $model;
    	$this->columns = $columns;
    	return $this;
    }

	public function orderby(array $order = null) {
		$this->order = $order;
	}

	public function sqlParameters()
	{
		return (isset($this->qualifier) ? $this->qualifier->sqlParameters() : null);
	}

	public function sqlStatement()
	{
		$components = array(
			SQL::CMD_SELECT,
			implode(",", $this->columns),
			SQL::SQL_FROM,
			$this->model->tableName(),
		);

		if ( isset($this->qualifier) ) {
			$components[] = SQL::SQL_WHERE;
			$components[] = $this->qualifier->sqlStatement();
		}

		if ( isset($this->order) ) {
			$components[] = SQL::SQL_ORDER;
			$orderClauses = array();
			foreach( $this->order as $clause ) {
				if ( is_array($clause) ) {
					if ( count($clause) != 1 ) {
						throw new \Exception( "Unable to parse order clause " . var_export($clause, true) );
					}
					foreach( $clause as $direction => $col ) {
						if ( is_int($direction) || strtoupper($direction) === SQL::SQL_ORDER_ASC) {
							$orderClauses[] = $col;
						}
						else if ( strtoupper($direction) === SQL::SQL_ORDER_DESC ) {
							$orderClauses[] = $col . ' ' . SQL::SQL_ORDER_DESC;
						}
						else {
							throw new \Exception( "Unable to order '$direction' on column $col" );
						}
					}
				}
				else if ( is_string($clause) ) {
					$orderClauses[] = $clause;
				}
				else {
					throw new \Exception( "Unable to order '$clause'" );
				}
			}
			$components[] = implode(",", $orderClauses);
		}

		return implode(" ", $components );
	}

}
