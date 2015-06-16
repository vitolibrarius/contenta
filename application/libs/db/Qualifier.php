<?php

namespace db;

use \Database as Database;
use \Localized as Localized;
use \Logger as Logger;
use \Model as Model;
use \Config as Config;
use \SQL as SQL;
use \DataObject as DataObject;

abstract class Qualifier extends SQL
{
	const EQ				= '=';
	const NOT_EQ	 		= '!=';

	const LESS_THAN			= '<';
	const LESS_THAN_EQU		= '<=';
	const GREATER_THAN		= '>';
	const GREATER_THAN_EQ	= '=>';
	const LIKE_Q			= 'LIKE';
	const IS_Q				= 'IS';

	const IN_Q				= 'IN';
	const AND_Q				= 'AND';
	const OR_Q				= 'OR';
	const NOT_Q				= 'NOT';

	public $tablePrefix;
	public $parameterPrefix;

	public static function NextParameterPrefix()
	{
		static $qualifierCount = 0;
		return SQL::PrefixAlias( $qualifierCount++ );
	}

	public function __construct($prefix = '')
	{
		$this->tablePrefix = (string)$prefix;
		$this->parameterPrefix = Qualifier::NextParameterPrefix();
	}

	public function prefixedAttribute( $attribute = '' )
	{
		return ':' . (strlen($this->parameterPrefix) == 0 ? '' : $this->parameterPrefix . '_') . sanitize($attribute, true, true);
	}

	public static function PK( DataObject $data = null, $prefix = '' )
	{
		if ( is_null($data) ) {
			throw new \Exception( "You must specify the data to be qualified" );
		}
		$model = $data->model();
		return new BasicQualifier( $model->tablePK(), Qualifier::EQ, $data->pkValue(), $prefix );
	}

	public static function AndQualifier()
	{
		$args = func_get_args();
		$qualifiers = array();
		foreach( $args as $q ) {
			if ( $q instanceof Qualifier ) {
				$qualifiers[] = $q;
			}
			else {
				throw new \Exception( "Not a qualifier " . var_export($q, true));
			}
		}
		return new AndQualifier( $qualifiers );
	}

	public static function OrQualifier()
	{
		$args = func_get_args();
		$qualifiers = array();
		foreach( $args as $q ) {
			if ( $q instanceof Qualifier ) {
				$qualifiers[] = $q;
			}
			else {
				throw new \Exception( "Not a qualifier " . var_export($q, true));
			}
		}
		return new OrQualifier( $qualifiers );
	}

	public static function NotQualifier( Qualifier $qual = null)
	{
		if ( is_null($qual) ) {
			throw new \Exception( "Not a qualifier " . var_export($qual, true));
		}

		return new NotQualifier( $qual );
	}

	public static function Equals( $key = null, $value = null, $prefix = '')
	{
		if ( is_null($key) || is_null($value) ) {
			throw new \Exception( "Must specify attribute key/value" );
		}

		return new BasicQualifier( $key, Qualifier::EQ, $value, $prefix );
	}
}

class OrQualifier extends Qualifier
{
	public $qualifiers;
	public function __construct(array $qual = null)
	{
		parent::__construct(null);
		$this->qualifiers = $qual;
	}

	public function sqlParameters()
	{
		$params = array();
		foreach( $this->qualifiers as $idx => $q ) {
			$params = array_merge($params, (array)$q->sqlParameters());
		}
		return $params;
	}

	public function sqlStatement()
	{
		$statements = array();
		foreach( $this->qualifiers as $idx => $q ) {
			$statements[] = $q->sqlStatement();
		}
		return implode(" " . Qualifier::OR_Q . " ", $statements);
	}
}

class AndQualifier extends Qualifier
{
	public $qualifiers;
	public function __construct(array $qual = null)
	{
		parent::__construct(null);
		$this->qualifiers = $qual;
	}

	public function sqlParameters()
	{
		$params = array();
		foreach( $this->qualifiers as $idx => $q ) {
			$params = array_merge($params, (array)$q->sqlParameters());
		}
		return $params;
	}

	public function sqlStatement()
	{
		$statements = array();
		foreach( $this->qualifiers as $idx => $q ) {
			$statements[] = $q->sqlStatement();
		}
		return implode(" " . Qualifier::AND_Q . " ", $statements);
	}
}

class NotQualifier extends Qualifier
{
	public $qualifier;
	public function __construct( Qualifier $qual = null)
	{
		parent::__construct(null);
		$this->qualifier = $qual;
	}

	public function sqlParameters()
	{
		return $this->qualifier->sqlParameters();
	}

	public function sqlStatement()
	{
		return Qualifier::NOT_Q . " " . $this->qualifier->sqlStatement();
	}
}

class BasicQualifier extends Qualifier
{
	public $attribute;
	public $operator;
	public $value;

	public function __construct( $key = null, $op = Qualifier::EQ, $v = null, $prefix = '')
	{
		parent::__construct($prefix);
		if ( is_null($key) || is_null($v) ) {
			throw new \Exception( "Must specify attribute key/value" );
		}
		$this->attribute = $key;
		$this->operator = $op;
		$this->value = $v;
	}

	public function sqlParameters()
	{
		return array( $this->prefixedAttribute( $this->attribute ) => $this->value );
	}

	public function sqlStatement()
	{
		return
			(strlen($this->tablePrefix) == 0 ? '' : $this->tablePrefix . '.') . $this->attribute . " "
			. $this->operator . " " . $this->prefixedAttribute( $this->attribute );
	}
}
