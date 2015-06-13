<?php

namespace db;

use \Database as Database;
use \Localized as Localized;
use \Logger as Logger;
use \Model as Model;
use \SQL as SQL;

class SelectSQL extends SQL
{
	const SQL_SELECT	= 'SELECT';
	const SQL_FROM		= 'FROM';
	const SQL_WHERE		= 'WHERE';
	const SQL_ORDER		= 'ORDER BY';
	const SQL_ORDER_ASC		= 'ASC';
	const SQL_ORDER_DESC	= 'DESC';

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
			SelectSQL::SQL_SELECT,
			implode(",", $this->columns),
			SelectSQL::SQL_FROM,
			$this->model->tableName(),
		);

		if ( isset($this->qualifier) ) {
			$components[] = SelectSQL::SQL_WHERE;
			$components[] = $this->qualifier->sqlStatement();
		}

		if ( isset($this->order) ) {
			$components[] = SelectSQL::SQL_ORDER;
			$orderClauses = array();
			foreach( $this->order as $clause ) {
				if ( is_array($clause) ) {
					if ( count($clause) != 1 ) {
						throw new \Exception( "Unable to parse order clause " . var_export($clause, true) );
					}
					foreach( $clause as $direction => $col ) {
						if ( is_int($direction) || strtoupper($direction) === SelectSQL::SQL_ORDER_ASC) {
							$orderClauses[] = $col;
						}
						else if ( strtoupper($direction) === SelectSQL::SQL_ORDER_DESC ) {
							$orderClauses[] = $col . ' ' . SelectSQL::SQL_ORDER_DESC;
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
