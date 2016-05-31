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
<?php foreach( $mandatoryObjectRelations as $name => $detailArray ) : ?>
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
	public function tablePK() { return <?php echo $this->modelClassName(); ?>::id; }

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
<?php else : ?>
			FixMe: attribute <?php echo $name; ?> does not define 'type'
<?php endif; // has type ?>
<?php endforeach; // attribute loop ?>

<?php if (is_array($mandatoryObjectRelations)) : ?>
<?php foreach( $mandatoryObjectRelations as $name => $detailArray ) : ?>
<?php if (isset($detailArray['isToMany']) == false || $detailArray['isToMany'] == false) : ?>
<?php $joins = $detailArray['joins']; if (count($joins) == 1) : ?>
<?php $join = $joins[0]; ?>
	public function allFor<?php echo ucwords($name); ?>($obj)
	{
		return $this->allObjectsForFK(<?php echo $this->modelClassName() . "::" . $join["sourceAttribute"]; ?>, $obj, $this->sortOrder(), 50);
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
	public function base_create( <?php echo implode(', ', array_map(function($item) { return '$' . $item; },
		array_merge( $createRelationsList, $createAttrList ))); ?>)
	{
		$obj = false;
		if ( isset(<?php echo implode(', ', array_map(function($item) { return '$' . $item; },
		array_merge( $createRelationsList, $mandatoryAttrList ))); ?>) ) {
			$params = array(
<?php foreach( $objectAttributes as $name => $detailArray ) : ?>
<?php if ( $this->isPrimaryKey($name) || $this->isMandatoryRelationshipKey($name) ) : ?>
<?php elseif ( in_array($name, $createAttrList) ) : ?>
				<?php echo $this->modelClassName() . "::" . $name; ?> => (isset($<?php echo $name; ?>) ? $<?php echo $name; ?> : <?php echo $this->defaultCreationValue($name); ?>),
<?php else : ?>
				<?php echo $this->modelClassName() . "::" . $name; ?> => <?php echo $this->defaultCreationValue($name); ?>,
<?php endif; // is in create attribute list ?>
<?php endforeach; ?>
			);

<?php foreach( $this->mandatoryObjectRelations() as $name => $detailArray ) : ?>
<?php $joins = $detailArray['joins']; if (count($joins) == 1) : ?>
<?php $join = $joins[0]; ?>
			if ( isset($<?php echo $name; ?>) ) {
				if ( $<?php echo $name; ?> instanceof <?php echo $detailArray['destination'] ?>DBO) {
					$params[<?php echo $this->modelClassName() . "::" . $join["sourceAttribute"]; ?>] = $<?php echo $name . "->" . $join["destinationAttribute"]; ?>;
				}
<?php $attD = $this->detailsForAttribute($join["sourceAttribute"]); ?>
				else if ( <?php echo ($attD['type'] == 'INTEGER' ? " is_integer($" : "is_string($") . $name . ")"; ?> ) {
					$params[<?php echo $this->modelClassName() . "::" . $join["sourceAttribute"]; ?>] = $<?php echo $name; ?>;
				}
			}
<?php endif; // one join ?>
<?php endforeach; ?>

			list( $obj, $errorList ) = $this->createObject($params);
			if ( is_array($errorList) ) {
				return $errorList;
			}
		}
		return $obj;
	}

	public function base_update( <?php echo $this->dboClassName(); ?> $obj,
		<?php echo implode(', ', array_map(function($item) { return '$' . $item; },
			array_merge( $createRelationsList, $createAttrList ))); ?>)
	{
		if ( isset( $obj ) && is_null($obj) == false ) {
			$updates = array();

<?php foreach( $objectAttributes as $name => $detailArray ) : ?>
<?php if ( $this->isPrimaryKey($name) == false && $this->isMandatoryRelationshipKey($name) == false && in_array($name, $createAttrList) ) : ?>
			if (isset($<?php echo $name; ?>) && (isset($obj-><?php echo $name; ?>) == false || $<?php echo $name; ?> != $obj-><?php echo $name; ?>)) {
				$updates[<?php echo $this->modelClassName() . "::" . $name; ?>] = $<?php echo $name; ?>;
			}
<?php endif; ?>
<?php endforeach; ?>

<?php foreach( $this->mandatoryObjectRelations() as $name => $detailArray ) : ?>
<?php $joins = $detailArray['joins']; if (count($joins) == 1) : ?>
<?php $join = $joins[0]; ?>
			if ( isset($<?php echo $name; ?>) ) {
				if ( $<?php echo $name; ?> instanceof <?php echo $detailArray['destination'] ?>DBO) {
					$updates[<?php echo $this->modelClassName() . "::" . $join["sourceAttribute"]; ?>] = $<?php echo $name . "->" . $join["destinationAttribute"]; ?>;
				}
<?php $attD = $this->detailsForAttribute($join["sourceAttribute"]); ?>
				else if ( <?php echo ($attD['type'] == 'INTEGER' ? " is_integer($" : "is_string($") . $name . ")"; ?> ) {
					$updates[<?php echo $this->modelClassName() . "::" . $join["sourceAttribute"]; ?>] = $<?php echo $name; ?>;
				}
			}
<?php endif; // one join ?>
<?php endforeach; ?>

			if ( count($updates) > 0 ) {
				list($obj, $errorList) = $this->updateObject( $obj, $updates );
				if ( is_array($errorList) ) {
					return $errorList;
				}
			}
		}
		return $obj;
	}

	/**
	 *	Delete functions
	 */
	public function deleteObject( DataObject $object = null)
	{
		if ( $object instanceof <?php echo $this->modelClassName(); ?> )
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
			// does not own <?php echo $detailArray["destination"]; ?>

<?php endif; // owns destination ?>
<?php endif; // one join ?>
<?php endforeach; // looprelationships ?>
<?php endif; // has relationships ?>
			return parent::deleteObject($object);
		}

		return false;
	}

<?php foreach( $mandatoryObjectRelations as $name => $detailArray ) : ?>
<?php if (isset($detailArray['isToMany']) == false || $detailArray['isToMany'] == false) : ?>
<?php $joins = $detailArray['joins']; if (count($joins) == 1) : ?>
<?php $join = $joins[0]; ?>
	public function deleteAllFor<?php echo ucwords($name); ?>(<?php echo $detailArray['destination'] ?>DBO $obj)
	{
		$success = true;
		if ( $obj != false ) {
			$array = $this->allFor<?php echo ucwords($name); ?>($obj);
			foreach ($array as $key => $value) {
				if ($this->deleteObject($value) == false) {
					$success = false;
					break;
				}
			}
		}
		return $success;
	}
<?php endif; // multiple joins ?>
<?php endif; //  toOne ?>
<?php endforeach; // looprelationships ?>

	/**
	 *	Named fetches
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

	/** Set attributes */
<?php foreach( $objectAttributes as $name => $detailArray ) : ?>
<?php if ( $this->isPrimaryKey($name) === false ) : ?>
	public function set<?php echo ucwords($name); ?>( <?php echo $this->dboClassName(); ?> $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(<?php echo $this->modelClassName() . "::" . $name; ?> => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

<?php endif; // not primaryKey ?>
<?php endforeach; ?>

	/** Validation */
<?php foreach( $objectAttributes as $name => $detailArray ) : ?>
<?php
 	$type = $this->modelTypeForAttribute($name);
	$mandatory = $this->isMandatoryAttribute($name);
	$textLength = (isset($details['length']) ? intval($details['length']) : 0);
if ( $this->isPrimaryKey($name) === false ) : ?>
	function validate_<?php echo $name; ?>($object = null, $value)
	{
<?php if ( $this->isType_TEXT($name) ) : ?>
		$value = trim($value);
<?php endif; // isType_TEXT ?>
<?php if ( $this->isRelationshipKey($name) ) : ?>
		if (isset($object-><?php echo $name; ?>) === false && empty($value) ) {
			return Localized::ModelValidation(
				$this->tableName(),
				<?php echo $this->modelClassName() . "::" . $name; ?>,
				"FIELD_EMPTY"
			);
		}
<?php else : ?>
<?php if ( $mandatory ) : ?>
		if (empty($value)) {
			return Localized::ModelValidation(
				$this->tableName(),
				<?php echo $this->modelClassName() . "::" . $name; ?>,
				"FIELD_EMPTY"
			);
		}
<?php endif; // mandatory ?>
<?php if ( $this->isType_TEXT($name) ) : ?>
<?php if ( $textLength > 0 ) : ?>
		if (strlen($value) > <?php echo $textLength; ?> ) {
			return Localized::ModelValidation(
				$this->tableName(),
				<?php echo $this->modelClassName() . "::" . $name; ?>,
				"FIELD_TOO_LONG"
			);
		}
<?php endif; // textLength ?>
<?php if ( $this->isType_TEXT_URL($name) ) : ?>
		if ( filter_var($value, FILTER_VALIDATE_URL) === false) {
			return Localized::ModelValidation(
				$this->tableName(),
				<?php echo $this->modelClassName() . "::" . $name; ?>,
				"FILTER_VALIDATE_URL"
			);
		}
<?php endif; // url ?>
<?php if ( $this->isType_TEXT_EMAIL($name) ) : ?>
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
		if (filter_var($value, FILTER_VALIDATE_INT) === false) {
			return Localized::ModelValidation(
				$this->tableName(),
				<?php echo $this->modelClassName() . "::" . $name; ?>,
				"FILTER_VALIDATE_INT"
			);
		}
<?php endif; // INT ?>
<?php if ( $this->isType_BOOLEAN($name) ) : ?>
		if ( is_null($value) ) {
			return Localized::ModelValidation(
				$this->tableName(),
				<?php echo $this->modelClassName() . "::" . $name; ?>,
				"FIELD_EMPTY"
			);
		}

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
<?php endif; // relationshipkey ?>
		return null;
	}
<?php endif; // not primaryKey ?>
<?php endforeach; ?>
}
