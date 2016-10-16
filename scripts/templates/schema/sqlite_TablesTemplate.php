<?php // setup some script variables
	$mandatoryObjectAttributes = $this->mandatoryObjectAttributes();
	$lastMandatoryKey = array_last_key($mandatoryObjectAttributes);

	$objectAttributes = (is_array($this->attributes) ? $this->attributes : array());
	$lastAttributeKey = array_last_key($objectAttributes);

	$objectRelationships = (is_array($this->relationships) ? $this->relationships : array());;
	$lastRelation = array_last_key($objectRelationships);
	$mandatoryObjectRelations = (is_array($this->foreignKeyRelations()) ? $this->foreignKeyRelations() : array());
	$lastMandatoryRelation = array_last_key($mandatoryObjectRelations);
// 	echo var_export($mandatoryObjectRelations, true) . PHP_EOL. PHP_EOL;
?>

/** <?php echo strtoupper($this->tableName()); ?> */
CREATE TABLE IF NOT EXISTS <?php echo $this->tableName(); ?> (
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

		echo "			" . $name . " " . $type . (in_array($name, $this->primaryKeys) ? " PRIMARY KEY" : "");
		if ($lastAttributeKey != $name || count($mandatoryObjectRelations) > 0 ) { echo ","; }
		echo PHP_EOL;
} ?>
<?php foreach( $mandatoryObjectRelations as $name => $detailArray ) : ?>
<?php if (isset($detailArray['isToMany'])) : ?>
<?php $joins = $detailArray['joins']; if ($detailArray['isToMany'] == false) : ?>
<?php if (count($joins) == 1) : ?><?php $join = $joins[0]; ?>
			FOREIGN KEY ( <?php echo $join["sourceAttribute"]; ?> ) REFERENCES <?php echo $detailArray["destinationTable"]; ?> ( <?php echo $join["destinationAttribute"]; ?> )<?php echo ($lastMandatoryRelation === $name ? "" : ","); ?>

<?php else : ?>
			FixMe: relationship <?php echo $name; ?> has multiple joins
<?php endif; // multiple joins ?>
<?php endif; // isToMany or toOne ?>
<?php else : ?>
	Error: relationship <?php echo $name; ?> does not define 'isToMany'
<?php endif; // has isToMany ?>
<?php endforeach; // looprelationships ?>
		);
