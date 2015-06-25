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
	const IS_NULL_Q			= 'IS NULL';
	const IS_NOT_NULL_Q		= 'IS NOT NULL';

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

	public static function FK( $key = null, DataObject $data = null, $prefix = '' )
	{
		if ( is_null($key) ) {
			throw new \Exception( "You must specify the foreign key to be qualified" );
		}
		if ( is_null($data) ) {
			throw new \Exception( "You must specify the data to be qualified" );
		}
		return new BasicQualifier( $key, Qualifier::EQ, $data->pkValue(), $prefix );
	}

	public static function XID( $xid = null, $xsrc = null, $prefix = '' )
	{
		$qualifiers = array();
		if ( is_null($xid) ) {
			$qualifiers[] = Qualifier::IsNull( "xid" );
		}
		else {
			$qualifiers[] = Qualifier::Equals( "xid", $xid);
		}

		if ( is_null($xsrc) == false ) {
			$qualifiers[] = Qualifier::Equals( "xsource", $xsrc);
		}
		return Qualifier::AndQualifier( $qualifiers );
	}

	public static function AndQualifier()
	{
		$qualifiers = array_flatten( func_get_args(), 'db\Qualifier' );
		return new AndQualifier( $qualifiers );
	}

	public static function OrQualifier()
	{
		$qualifiers = array_flatten( func_get_args(), 'db\Qualifier' );
		return new OrQualifier( $qualifiers );
	}

	public static function NotQualifier( Qualifier $qual = null)
	{
		if ( is_null($qual) ) {
			throw new \Exception( "Not a qualifier " . var_export($qual, true));
		}

		return new NotQualifier( $qual );
	}

	public static function LikeQualifier( $key = null, $value = null, $prefix = '')
	{
		if ( is_null($key) || is_null($value) ) {
			throw new \Exception( "Must specify attribute key/value" );
		}

		return new BasicQualifier( $key, Qualifier::LIKE_Q, $value, $prefix );
	}

	public static function Equals( $key = null, $value = null, $prefix = '')
	{
		if ( is_null($key) || is_null($value) ) {
			throw new \Exception( "Must specify attribute key/value" );
		}

		return new BasicQualifier( $key, Qualifier::EQ, $value, $prefix );
	}

	public static function IsNull( $key = null, $prefix = '')
	{
		return new IsNullQualifier( $key, true, $prefix );
	}

	public static function IsNotNull( $key = null, $prefix = '')
	{
		return new IsNullQualifier( $key, false, $prefix );
	}

	public static function GreaterThan( $key = null, $value = null, $prefix = '')
	{
		return new BasicQualifier( $key, Qualifier::GREATER_THAN, $value, $prefix );
	}

	public static function GreaterThanEqual( $key = null, $value = null, $prefix = '')
	{
		return new BasicQualifier( $key, Qualifier::GREATER_THAN_EQ, $value, $prefix );
	}

	public static function LessThan( $key = null, $value = null, $prefix = '')
	{
		return new BasicQualifier( $key, Qualifier::LESS_THAN, $value, $prefix );
	}

	public static function LessThanEqual( $key = null, $value = null, $prefix = '')
	{
		return new BasicQualifier( $key, Qualifier::LESS_THAN_EQ, $value, $prefix );
	}

	public static function Between( $key = null, $lowValue = null, $highValue, $prefix = '')
	{
		return new AndQualifier( array(
				new BasicQualifier( $key, Qualifier::GREATER_THAN_EQ, $lowValue, $prefix ),
				new BasicQualifier( $key, Qualifier::LESS_THAN_EQ, $highValue, $prefix );
			), $prefix
		);

		return
	}

	public static function JoinQualifier($src_prefix, $src_attribute, $dest_prefix, $dest_attribute)
	{
		if ( isset($src_prefix, $src_attribute, $dest_prefix, $dest_attribute)) {
			return new JoinQualifier($src_prefix, $src_attribute, $dest_prefix, $dest_attribute);
		}

		throw new \Exception("Unable to join on empty list");
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
		return "(" . implode(" " . Qualifier::OR_Q . " ", $statements) . ")";
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
		return "(" . implode(" " . Qualifier::AND_Q . " ", $statements) . ")";
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

class InQualifier extends Qualifier
{
	public $attribute;
	public $inArray;
	public function __construct( $key = null, array $values = null, $prefix = '')
	{
		parent::__construct(null);
		if ( is_null($key) ) {
			throw new \Exception( "Must specify attribute key" );
		}
		if ( is_null($values) || count($values) == 0) {
			throw new \Exception( "Must specify the in values" );
		}
		$this->attribute = $key;
		$this->inArray = $values;
	}

	public function sqlParameters()
	{
		$args = array();
		foreach( $this->inArray as $idx => $value ) {
			$param = $this->prefixedAttribute( $this->attribute );
			$args[$param] = $value;
		}

		return $args;
	}

	public function sqlStatement()
	{
		$attr = (strlen($this->tablePrefix) == 0 ? '' : $this->tablePrefix . '.') . $this->attribute;
		$args = array();
		foreach( $this->inArray as $idx => $value ) {
			$args[] = $this->prefixedAttribute( $this->attribute );
		}
		return $attr . " ". Qualifier::IN_Q . " (" . implode(",", $args) . ")";
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
		if ( is_null($key) ) {
			throw new \Exception( "Must specify attribute key" );
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

class IsNullQualifier extends BasicQualifier
{
	public function __construct( $key = null, $direction = true, $prefix = '')
	{
		parent::__construct( $key, (boolval($direction) ? Qualifier::IS_NULL_Q : Qualifier::IS_NOT_NULL_Q), null, $prefix);
	}

	public function sqlParameters()
	{
		return null;
	}

	public function sqlStatement()
	{
		return (strlen($this->tablePrefix) == 0 ? '' : $this->tablePrefix . '.')
			. $this->attribute . " " . $this->operator;
	}
}

class JoinQualifier extends Qualifier
{
	public $att_a_prefix;
	public $att_b_prefix;
	public $att_a;
	public $att_b;

	public function __construct( $src_prefix, $src_attribute, $dest_prefix, $dest_attribute)
	{
		parent::__construct();
		if ( isset($src_prefix, $src_attribute, $dest_prefix, $dest_attribute)) {
			$this->att_a_prefix = $src_prefix;
			$this->att_b_prefix = $dest_prefix;
			$this->att_a = $src_attribute;
			$this->att_b = $dest_attribute;
		}
		else {
			throw new \Exception( "Incorrect number of joins specified, must be 2" . var_export($joins, true));
		}
	}

	public function sqlParameters()
	{
		return null;
	}

	public function sqlStatement()
	{
		return $this->att_a_prefix . "." . $this->att_a . " = " . $this->att_b_prefix . "." . $this->att_b;
	}
}
