namespace <?php echo $this->packageName(); ?>;

<?php // setup some script variables
	$attributeList = (is_array($this->attributes) ? $this->attributes : array());
	$relationshipList = (is_array($this->mandatoryObjectRelations()) ? $this->mandatoryObjectRelations() : array());
?>

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use <?php echo $this->dboPackageClassName(); ?> as <?php echo $this->dboClassName(); ?>;

/** Sample Creation script */
		/** <?php echo strtoupper($this->tableName()); ?>

		$sql = "CREATE TABLE IF NOT EXISTS <?php echo $this->tableName(); ?> ( "
<?php foreach( $attributeList as $name => $detailArray ) : ?>
<?php switch ($detailArray['type']) {
	case 'DATE':
	case 'INTEGER':
	case 'BOOLEAN':
		$type = 'INTEGER';
		break;
	default:
		$type = 'TEXT';
		break;
	} ?>
			. <?php echo $this->modelPackageClassName() . "::" . $name . " . \" " . $type . (in_array($name, $this->primaryKeys) ? " PRIMARY KEY" : "") . ", \""; ?>

<?php endforeach; ?>
<?php if (is_array($relationshipList)) : ?>
<?php $lastkey = array_last_key($relationshipList); foreach( $relationshipList as $name => $detailArray ) : ?>
<?php if (isset($detailArray['isToMany'])) : ?>
<?php $joins = $detailArray['joins']; if ($detailArray['isToMany'] == false) : ?>
<?php if (count($joins) == 1) : ?><?php $join = $joins[0]; ?>
			. "FOREIGN KEY (". <?php echo $this->modelPackageClassName() . "::" . $join["sourceAttribute"]; ?> .")"
				. " REFERENCES " . <?php echo $detailArray["destination"]; ?>::TABLE . "(" . <?php echo $detailArray["destination"] . "::" .  $join["destinationAttribute"]; ?> . ")<?php echo ($lastkey === $name ? "" : "[$lastkey],"); ?>"
<?php else : ?>
			FixMe: relationship <?php echo $name; ?> has multiple joins
<?php endif; // multiple joins ?>
<?php endif; // isToMany or toOne ?>
<?php else : ?>
	Error: relationship <?php echo $name; ?> does not define 'isToMany'
<?php endif; // has isToMany ?>
<?php endforeach; // looprelationships ?>
<?php endif; // has relationships ?>
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
class <?php echo $this->modelClassName(); ?> extends Model
{
	const TABLE = '<?php echo $this->tableName(); ?>';
<?php
foreach( $attributeList as $name => $detailArray ) {
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
		return array(<?php $lastKey = array_last_key($attributeList); foreach( $attributeList as $name => $detailArray ) {
			echo "\n\t\t\t" . $this->modelClassName() . "::" . $name . ( $name != $lastKey ? "," : "");
		} ?>

		);
	}

	/** * * * * * * * * *
		Basic search functions
	 */
<?php foreach( $attributeList as $name => $detailArray ) : ?>
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

<?php if (is_array($relationshipList)) : ?>
<?php foreach( $relationshipList as $name => $detailArray ) : ?>
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
<?php foreach( $relationshipList as $name => $detailArray ) : ?>
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

<?php
	$createAttrList = array_keys($this->createObjectAttributes());
	$mandatoryAttrList = array_keys($this->mandatoryObjectAttributes());
	$createRelationsList = array_keys($this->mandatoryObjectRelations());
?>
	public function create( <?php echo implode(', ', array_map(function($item) { return '$' . $item; },
		array_merge( $createRelationsList, $createAttrList ))); ?>)
	{
		$obj = false;
		if ( isset(<?php echo implode(', ', array_map(function($item) { return '$' . $item; },
		array_merge( $createRelationsList, $mandatoryAttrList ))); ?>) ) {
			$params = array(
<?php foreach( $attributeList as $name => $detailArray ) : ?>
<?php if ( $this->isPrimaryKey($name) || $this->isRelationshipKey($name) ) : ?>
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
				if ( $<?php echo $name; ?> instanceof <?php echo $detailArray['destination'] ?>) {
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
		if ( is_array($result) && count($result) > 1 ) {
			throw new \Exception( <?php echo $name; ?> . " expected 1 result, but fetched " . count($result) );
		}

		return (is_array($result) ? $result[0] : false );
<?php else : ?>
		return $result;
<?php endif; // maxResults ?>
	}

<?php endforeach; // named fetches ?>
}
