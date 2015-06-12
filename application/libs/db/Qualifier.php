<?php

namespace db;

use \Database as Database;
use \Localized as Localized;
use \Logger as Logger;
use \Model as Model;
use \Config as Config;

abstract class Qualifier
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

	public function __construct($prefix = '')
	{
		$this->tablePrefix = (string)$prefix;
	}

	public function prefixedAttribute( $attribute = '' )
	{
		return ':' . (strlen($this->tablePrefix) == 0 ? '' : $this->tablePrefix . '_') . sanitize($attribute, true, true);
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

	public static function Equals($prefix = '', $key = null, $value = null)
	{
		if ( is_null($key) || is_null($value) ) {
			throw new \Exception( "Must specify attribute key/value" );
		}

		return new BasicQualifier( $prefix, $key, Qualifier::EQ, $value );
	}

	abstract public function sqlParameters();
	abstract public function sqlStatement();
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

	public function __construct($prefix = '', $key = null, $op = Qualifier::EQ, $v = null)
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
		return $this->attribute . " " . $this->operator . " " . $this->prefixedAttribute( $this->attribute );
	}
}
