namespace <?php echo $this->packageName(); ?>;

<?php // setup some script variables
	$mandatoryObjectAttributes = $this->mandatoryObjectAttributes();
	$lastMandatoryKey = array_last_key($mandatoryObjectAttributes);

	$objectAttributes = (is_array($this->attributes) ? $this->attributes : array());
	$lastAttributeKey = array_last_key($objectAttributes);

	$objectRelationships = (is_array($this->relationships) ? $this->relationships : array());;
	$mandatoryObjectRelations = (is_array($this->mandatoryObjectRelations()) ? $this->mandatoryObjectRelations() : array());
	$lastMandatoryRelation = array_last_key($mandatoryObjectRelations);
?>

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \exceptions\DeleteObjectException as DeleteObjectException;

use <?php echo $this->dboPackageClassName(); ?> as <?php echo $this->dboClassName(); ?>;

/* import related objects */
<?php
	$aliases = $this->relationshipAliasStatements();
	foreach( $aliases as $table => $statements ) {
		echo implode(PHP_EOL, $statements) . PHP_EOL;
	}
?>

/** Sample Creation script */
		/** <?php echo strtoupper($this->tableName()); ?> */
/*
		$sql = "CREATE TABLE IF NOT EXISTS <?php echo $this->tableName(); ?> ( "
<?php foreach( $objectAttributes as $name => $detailArray ) {
	switch ($detailArray['type']) {
		case 'DATE':
		case 'INTEGER':
		case 'BOOLEAN':
			$type = 'INTEGER';
			break;
		default:
			$type = 'TEXT';
			break;
		}

		echo "			. " . $this->modelClassName() . "::" . $name . " . \" " . $type . (in_array($name, $this->primaryKeys) ? " PRIMARY KEY" : "");
		if ($lastAttributeKey != $name || count($mandatoryObjectRelations) > 0 ) { echo ","; }
		echo " \"" . PHP_EOL;
} ?>
<?php foreach( $objectRelationships as $name => $detailArray ) : ?>
<?php if (isset($detailArray['isToMany'])) : ?>
<?php $joins = $detailArray['joins']; if ($detailArray['isToMany'] == false) : ?>
<?php if (count($joins) == 1) : ?><?php $join = $joins[0]; ?>
			. "FOREIGN KEY (". <?php echo $this->modelClassName() . "::" . $join["sourceAttribute"]; ?> .") REFERENCES " . <?php echo $detailArray["destination"]; ?>::TABLE . "(" . <?php echo $detailArray["destination"] . "::" .  $join["destinationAttribute"]; ?> . ")<?php echo ($lastMandatoryRelation === $name ? "" : ","); ?>"
<?php else : ?>
			FixMe: relationship <?php echo $name; ?> has multiple joins
<?php endif; // multiple joins ?>
<?php endif; // isToMany or toOne ?>
<?php else : ?>
	Error: relationship <?php echo $name; ?> does not define 'isToMany'
<?php endif; // has isToMany ?>
<?php endforeach; // looprelationships ?>
		. ")";
		$this->sqlite_execute( "<?php echo $this->tableName(); ?>", $sql, "Create table <?php echo $this->tableName(); ?>" );

<?php if (is_array($this->indexes)) : ?>
<?php foreach( $this->indexes as $detailArray ) : ?>
<?php	$columns = $detailArray["columns"];
		$indexName = $this->tableName() . '_' . implode("", $columns);
		$unique = ( (isset($detailArray["unique"]) && $detailArray["unique"]) ? "UNIQUE" : ""); ?>
		$sql = 'CREATE <?php echo $unique; ?> INDEX IF NOT EXISTS <?php echo $indexName; ?> on <?php echo $this->tableName(); ?> (<?php echo implode(",", $columns); ?>)';
		$this->sqlite_execute( "<?php echo $this->tableName(); ?>", $sql, "Index on <?php echo $this->tableName(); ?> (<?php echo implode(",", $columns); ?>)" );
<?php endforeach; // loop indexes ?>
<?php endif; // has indexes ?>
*/
abstract class <?php echo $this->modelBaseName(); ?> extends Model
{
	const TABLE = '<?php echo $this->tableName(); ?>';
<?php
foreach( $objectAttributes as $name => $detailArray ) {
	echo "\tconst $name = '$name';" . PHP_EOL;
}
?>

	public function tableName() { return <?php echo $this->modelClassName(); ?>::TABLE; }
	public function tablePK() { return <?php echo $this->modelClassName() . "::" . $this->primaryKeys[0]; ?>; }

	public function sortOrder()
	{
		return array(<?php $sort = $this->sort; $lastKey = array_last_key($sort); foreach( $this->sort as $k => $vector ) {
			echo "\n\t\t";
			foreach( $vector as $direction => $key ) {
				echo "\tarray( '" . $direction . "' => " . $this->modelClassName() . "::" . $key . ")";
			}
			if ( $k != $lastKey ) {
				echo ",";
			}
		}?>

		);
	}

	public function allColumnNames()
	{
		return array(<?php $lastKey = array_last_key($objectAttributes); foreach( $objectAttributes as $name => $detailArray ) {
			echo "\n\t\t\t" . $this->modelClassName() . "::" . $name . ( $name != $lastKey ? "," : "");
		} ?>

		);
	}

	/**
	 *	Simple fetches
	 */
<?php foreach( $objectAttributes as $name => $detailArray ) : ?>
<?php if (isset($detailArray['type'])) : ?>
<?php if ('TEXT' == $detailArray['type']) : ?>
<?php if ( $this->isUniqueAttribute($name) || $this->isPrimaryKey($name)) : ?>
	public function objectFor<?php echo ucwords($name); ?>($value)
	{
		return $this->singleObjectForKeyValue(<?php echo $this->modelClassName() . "::" . $name; ?>, $value);
	}
<?php else : ?>
	public function allFor<?php echo ucwords($name); ?>($value)
	{
		return $this->allObjectsForKeyValue(<?php echo $this->modelClassName() . "::" . $name; ?>, $value);
	}
<?php endif; // unique ?>

<?php if (isset($detailArray['partialSearch']) && $detailArray['partialSearch'] == true) : ?>
	public function allLike<?php echo ucwords($name); ?>($value)
	{
		return SQL::Select( $this )
			->where( Qualifier::Like( <?php echo $this->modelClassName() . "::" . $name; ?>, $value, SQL::SQL_LIKE_AFTER ))
			->orderBy( $this->sortOrder() )
			->limit( 50 )
			->fetchAll();
	}
<?php endif; //partial search ?>
<?php endif; // TEXT type ?>
<?php if ('INTEGER' == $detailArray['type'] && $this->isPrimaryKey($name) == false && $this->isRelationshipKey($name) == false) : ?>
<?php if ( $this->isUniqueAttribute($name)) : ?>
	public function objectFor<?php echo ucwords($name); ?>($value)
	{
		return $this->singleObjectForKeyValue(<?php echo $this->modelClassName() . "::" . $name; ?>, $value);
	}
<?php else : ?>
	public function allFor<?php echo ucwords($name); ?>($value)
	{
		return $this->allObjectsForKeyValue(<?php echo $this->modelClassName() . "::" . $name; ?>, $value);
	}
<?php endif; // unique ?>
<?php endif; // INTEGER type ?>

<?php else : ?>
			FixMe: attribute <?php echo $name; ?> does not define 'type'
<?php endif; // has type ?>
<?php endforeach; // attribute loop ?>

	/**
	 * Simple relationship fetches
	 */
<?php if (is_array($objectRelationships)) : ?>
<?php foreach( $objectRelationships as $name => $detailArray ) : ?>
<?php if (isset($detailArray['isToMany']) == false || $detailArray['isToMany'] == false) : ?>
<?php $joins = $detailArray['joins']; if (count($joins) == 1) : ?>
<?php $join = $joins[0]; ?>
	public function allFor<?php echo ucwords($name); ?>($obj)
	{
		return $this->allObjectsForFK(<?php echo $this->modelClassName() . "::" . $join["sourceAttribute"]; ?>, $obj, $this->sortOrder(), 50);
	}

	public function countFor<?php echo ucwords($name); ?>($obj)
	{
		if ( is_null($obj) == false ) {
			return $this->countForFK( <?php echo $this->modelClassName() . "::" . $join["sourceAttribute"]; ?>, $obj );
		}
		return false;
	}
<?php endif; // multiple joins ?>
<?php endif; //  toOne ?>
<?php endforeach; // looprelationships ?>

	public function joinAttributes( Model $joinModel = null )
	{
		if ( is_null($joinModel) == false ) {
			switch ( $joinModel->tableName() ) {
<?php foreach( $objectRelationships as $name => $detailArray ) : ?>
<?php $joins = $detailArray['joins']; if (count($joins) == 1) : ?>
<?php $join = $joins[0]; ?>
				case "<?php echo $detailArray["destinationTable"]; ?>":
					return array( <?php echo $this->modelClassName() . "::" . $join["sourceAttribute"]; ?>, "<?php echo $join["destinationAttribute"]; ?>"  );
					break;
<?php endif; // one join ?>
<?php endforeach; // looprelationships ?>
				default:
					break;
			}
		}
		return parent::joinAttributes( $joinModel );
	}
<?php endif; // has relationships ?>

	/**
	 *	Create/Update functions
	 */
<?php
	$createAttrList = array_keys($this->createObjectAttributes());
	$mandatoryAttrList = array_keys($this->mandatoryObjectAttributes());
	$createRelationsList = array_keys($this->mandatoryObjectRelations());
?>
	public function createObject( array $values = array() )
	{
		if ( isset($values) ) {

			// default values for attributes
<?php foreach( $objectAttributes as $name => $detailArray ) : ?>
<?php if ( $this->isPrimaryKey($name) === false && $this->isRelationshipKey($name) == false ) : ?>
			if ( isset($values['<?php echo $name; ?>']) == false ) {
				$default_<?php echo $name; ?> = $this->attributeDefaultValue( null, null, <?php echo $this->modelClassName() . "::" . $name; ?>);
				if ( is_null( $default_<?php echo $name; ?> ) == false ) {
					$values['<?php echo $name; ?>'] = $default_<?php echo $name; ?>;
				}
			}
<?php endif;  // primary key ?>
<?php endforeach; ?>

			// default conversion for relationships
<?php foreach( $this->relationships as $name => $detailArray ) : ?>
<?php $joins = $detailArray['joins']; if (count($joins) == 1) : ?>
<?php $join = $joins[0]; if ( $this->isPrimaryKey($join["sourceAttribute"]) == false ) : ?>
			if ( isset($values['<?php echo $name; ?>']) ) {
				$local_<?php echo $name; ?> = $values['<?php echo $name; ?>'];
				if ( $local_<?php echo $name; ?> instanceof <?php echo $detailArray['destination'] ?>DBO) {
					$values[<?php echo $this->modelClassName() . "::" . $join["sourceAttribute"]; ?>] = $local_<?php echo $name; ?>-><?php echo  $join["destinationAttribute"]; ?>;
				}
<?php $attD = $this->detailsForAttribute($join["sourceAttribute"]); ?>
				else if ( <?php echo ($attD['type'] == 'INTEGER' ? "is_integer(" : "is_string("); ?> $local_<?php echo $name; ?>) ) {
					$params[<?php echo $this->modelClassName() . "::" . $join["sourceAttribute"]; ?>] = $local_<?php echo $name; ?>;
				}
			}
<?php endif; // no primary key joins ?>
<?php endif; // one join ?>
<?php endforeach; ?>
		}
		return parent::createObject($values);
	}

	public function updateObject(DataObject $object = null, array $values = array()) {
		if (isset($object) && $object instanceof <?php echo $this->modelClassName(); ?> ) {
<?php foreach( $this->relationships as $name => $detailArray ) : ?>
<?php $joins = $detailArray['joins']; if (count($joins) == 1) : ?>
<?php $join = $joins[0]; if ( $this->isPrimaryKey($join["sourceAttribute"]) == false) : ?>
			if ( isset($values['<?php echo $name; ?>']) ) {
				$local_<?php echo $name; ?> = $values['<?php echo $name; ?>'];
				if ( $local_<?php echo $name; ?> instanceof <?php echo $detailArray['destination'] ?>DBO) {
					$values[<?php echo $this->modelClassName() . "::" . $join["sourceAttribute"]; ?>] = $local_<?php echo $name; ?>-><?php echo  $join["destinationAttribute"]; ?>;
				}
<?php $attD = $this->detailsForAttribute($join["sourceAttribute"]); ?>
				else if ( <?php echo ($attD['type'] == 'INTEGER' ? "is_integer(" : "is_string("); ?> $local_<?php echo $name; ?>) ) {
					$params[<?php echo $this->modelClassName() . "::" . $join["sourceAttribute"]; ?>] = $values['<?php echo $name; ?>'];
				}
			}
<?php endif; // no primary key joins ?>
<?php endif; // one join ?>
<?php endforeach; ?>
		}

		return parent::updateObject($object, $values);
	}

	/**
	 *	Delete functions
	 */
	public function deleteObject( DataObject $object = null)
	{
		if ( $object instanceof <?php echo $this->dboClassName(); ?> )
		{
<?php if (is_array($this->relationships)) : ?>
<?php foreach( $this->relationships as $name => $detailArray ) : ?>
<?php $joins = $detailArray['joins']; if (count($joins) == 1) : ?>
<?php $join = $joins[0]; ?>
<?php if (isset($detailArray['ownsDestination']) && $detailArray['ownsDestination']) : ?>
			$<?php echo $detailArray["destinationTable"]; ?>_model = Model::Named('<?php echo $detailArray["destination"]; ?>');
			if ( $<?php echo $detailArray["destinationTable"]; ?>_model->deleteAllForKeyValue(<?php echo $detailArray["destination"] . "::" . $join["destinationAttribute"]; ?>, $this-><?php echo $join["sourceAttribute"]; ?>) == false ) {
				return false;
			}
<?php else : // owns destination ?>
			// does not own <?php echo $name . " " . $detailArray["destination"]; ?>

<?php endif; // owns destination ?>
<?php endif; // one join ?>
<?php endforeach; // looprelationships ?>
<?php endif; // has relationships ?>
			return parent::deleteObject($object);
		}

		return false;
	}

<?php foreach( $objectRelationships as $name => $detailArray ) : ?>
<?php if (isset($detailArray['isToMany']) == false || $detailArray['isToMany'] == false) : ?>
<?php $joins = $detailArray['joins']; if (count($joins) == 1) : ?>
<?php $join = $joins[0]; ?>
	public function deleteAllFor<?php echo ucwords($name); ?>(<?php echo $detailArray['destination'] ?>DBO $obj)
	{
		$success = true;
		if ( $obj != false ) {
			$array = $this->allFor<?php echo ucwords($name); ?>($obj);
			while ( is_array($array) && count($array) > 0) {
				foreach ($array as $key => $value) {
					if ($this->deleteObject($value) == false) {
						$success = false;
						throw new DeleteObjectException("Failed to delete " . $value, $value->id );
					}
				}
				$array = $this->allFor<?php echo ucwords($name); ?>($obj);
			}
		}
		return $success;
	}
<?php endif; // multiple joins ?>
<?php endif; //  toOne ?>
<?php endforeach; // looprelationships ?>

	/**
	 * Named fetches
	 */
<?php foreach( $this->namedFetches() as $name => $details ) : ?>
	public function <?php echo $name; ?>( <?php echo (isset($details["arguments"]) ?
		implode(', ', array_map(function($item) { return '$' . $item; }, $details["arguments"])) : "" ); ?> )
	{
		$select = SQL::Select( $this );
		$select->orderBy( $this->sortOrder() );
<?php if (isset($details['qualifiers']) && is_array($details['qualifiers'])) : ?>
		$qualifiers = array();
<?php foreach( $details['qualifiers'] as $qualDetail ) : ?>
<?php if (isset($qualDetail['optional'], $qualDetail['argAttribute']) && boolval($qualDetail['optional'])) : ?>
		if ( isset($<?php echo $qualDetail['argAttribute']; ?>)) {
			$qualifiers[] = <?php echo $this->qualifierString($qualDetail); ?>;
		}
<?php else : ?>
		$qualifiers[] = <?php echo $this->qualifierString($qualDetail); ?>;
<?php endif; // optional ?>
<?php endforeach; ?>

		if ( count($qualifiers) > 0 ) {
			$select->where( Qualifier::Combine( '<?php echo (isset($details["semantic"]) ? $details["semantic"] : "AND"); ?>', $qualifiers ));
		}
<?php endif; // qualifiers ?>

		$result = $select->fetchAll();
<?php if (isset($details["maxCount"]) && $details["maxCount"] == 1) : ?>
		if ( is_array($result) ) {
			$result_size = count($result);
			if ( $result_size == 1 ) {
				return $result[0];
			}
			else if ($result_size > 1 ) {
				throw new \Exception( "<?php echo $name; ?> expected 1 result, but fetched " . count($result) );
			}
		}

		return false;
<?php else : ?>
		return $result;
<?php endif; // maxResults ?>
	}

<?php endforeach; // named fetches ?>

	/**
	 * Attribute editing
	 */
<?php if (is_array($mandatoryObjectAttributes) && count($mandatoryObjectAttributes) > 0  ) : ?>
	public function attributesMandatory($object = null)
	{
		if ( is_null($object) ) {
			return array(
<?php foreach( $mandatoryObjectAttributes as $name => $detailArray ) {
		echo "\t\t\t\t" . $this->modelClassName() . "::" . $name . ($lastMandatoryKey === $name ? "" : ",") . PHP_EOL;
}?>
			);
		}
		return parent::attributesMandatory($object);
	}
<?php endif; ?>

	public function attributesMap() {
		return array(
<?php foreach( $objectAttributes as $name => $detailArray ) : ?>
<?php if ( $this->isPrimaryKey($name) === false ) : ?>
			<?php echo $this->modelClassName() . "::" . $name . " => " . $this->modelTypeForAttribute($name) . ($lastAttributeKey === $name ? "" : ","); ?>

<?php endif;  // primary key ?>
<?php endforeach; ?>
		);
	}

	public function attributeDefaultValue($object = null, $type = null, $attr)
	{
		if ( isset($object) === false || is_null($object) == true) {
			switch ($attr) {
<?php foreach( $objectAttributes as $name => $detailArray ) : ?>
<?php if (isset($detailArray['default']) ) : ?>
				case <?php echo $this->modelClassName() . "::" . $name; ?>:
					return <?php echo $detailArray['default']; ?>;
<?php endif; ?>
<?php endforeach; ?>
			}
		}
		return parent::attributeDefaultValue($object, $type, $attr);
	}

	/**
	 * Validation
	 */
<?php foreach( $objectAttributes as $name => $detailArray ) : ?>
<?php
 	$type = $this->modelTypeForAttribute($name);
	$mandatory = $this->isMandatoryAttribute($name);
	$textLength = (isset($details['length']) ? intval($details['length']) : 0);
if ( $this->isPrimaryKey($name) === false ) : ?>
	function validate_<?php echo $name; ?>($object = null, $value)
	{
<?php if ( $mandatory ) : ?>
		// check for mandatory field
		if (isset($value) == false <?php if ( $this->isType_BOOLEAN($name) == false ) : ?>|| empty($value) <?php endif; // not boolean ?> ) {
			return Localized::ModelValidation(
				$this->tableName(),
				<?php echo $this->modelClassName() . "::" . $name; ?>,
				"FIELD_EMPTY"
			);
		}
<?php else : ?>
		// not mandatory field
		if (isset($value) == false <?php if ( $this->isType_BOOLEAN($name) == false ) : ?>|| empty($value) <?php endif; // not boolean ?> ) {
			return null;
		}
<?php endif; // mandatory ?>

<?php if ( $this->isType_TEXT($name) ) : ?>
<?php if ( $textLength > 0 ) : ?>
		// string length
		if (strlen($value) > <?php echo $textLength; ?> ) {
			return Localized::ModelValidation(
				$this->tableName(),
				<?php echo $this->modelClassName() . "::" . $name; ?>,
				"FIELD_TOO_LONG"
			);
		}
<?php endif; // textLength ?>
<?php if ( $this->isType_TEXT_URL($name) ) : ?>
		// url format
		if ( filter_var($value, FILTER_VALIDATE_URL) === false) {
			return Localized::ModelValidation(
				$this->tableName(),
				<?php echo $this->modelClassName() . "::" . $name; ?>,
				"FILTER_VALIDATE_URL"
			);
		}
<?php endif; // url ?>
<?php if ( $this->isType_TEXT_EMAIL($name) ) : ?>
		// email format
		if ( filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
			return Localized::ModelValidation(
				$this->tableName(),
				<?php echo $this->modelClassName() . "::" . $name; ?>,
				"FILTER_VALIDATE_EMAIL"
			);
		}
<?php endif; // email ?>
<?php endif; // TEXT ?>
<?php if ( $this->isType_DATE($name) ) : ?>
<?php if ( $this->isType_DATE_created($name) ) : ?>
		// created date is not changeable
		if ( isset($object, $object->created) ) {
			return Localized::ModelValidation(
				$this->tableName(),
				<?php echo $this->modelClassName() . "::" . $name; ?>,
				"IMMUTABLE"
			);
		}
<?php endif; // isType_DATE_created ?>
<?php endif; // DATE ?>
<?php if ( $this->isType_INTEGER($name) ) : ?>
		// integers
		if (filter_var($value, FILTER_VALIDATE_INT) === false) {
			return Localized::ModelValidation(
				$this->tableName(),
				<?php echo $this->modelClassName() . "::" . $name; ?>,
				"FILTER_VALIDATE_INT"
			);
		}
<?php endif; // INT ?>
<?php if ( $this->isType_BOOLEAN($name) ) : ?>
		// boolean

		// Returns TRUE for "1", "true", "on" and "yes"
		// Returns FALSE for "0", "false", "off" and "no"
		// Returns NULL otherwise.
		$v = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
		if (is_null($v)) {
			return Localized::ModelValidation(
				$this->tableName(),
				<?php echo $this->modelClassName() . "::" . $name; ?>,
				"FILTER_VALIDATE_BOOLEAN"
			);
		}
<?php endif; // BOOLEAN ?>
<?php if ( $this->isUniqueAttribute($name) ) : ?>
		// make sure <?php echo ucwords($name); ?> is unique
		$existing = $this->objectFor<?php echo ucwords($name); ?>($value);
		if ( $existing != false && ( is_null($object) || $existing->id != $object->id)) {
			return Localized::ModelValidation(
				$this->tableName(),
				<?php echo $this->modelClassName() . "::" . $name; ?>,
				"UNIQUE_FIELD_VALUE"
			);
		}
<?php endif; // unique ?>
		return null;
	}
<?php endif; // not primaryKey ?>
<?php endforeach; ?>
}
