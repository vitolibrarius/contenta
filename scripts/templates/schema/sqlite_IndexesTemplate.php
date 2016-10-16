<?php // setup some script variables
	$mandatoryObjectAttributes = $this->mandatoryObjectAttributes();
	$lastMandatoryKey = array_last_key($mandatoryObjectAttributes);

	$objectAttributes = (is_array($this->attributes) ? $this->attributes : array());
	$lastAttributeKey = array_last_key($objectAttributes);

	$objectRelationships = (is_array($this->relationships) ? $this->relationships : array());;
	$lastRelation = array_last_key($objectRelationships);
	$mandatoryObjectRelations = (is_array($this->foreignKeyRelations()) ? $this->foreignKeyRelations() : array());
	$lastMandatoryRelation = array_last_key($mandatoryObjectRelations);
	$index_count = 0;
?>

/** <?php echo strtoupper($this->tableName()); ?> */
<?php foreach( $objectRelationships as $name => $detailArray ) : ?>
<?php if (isset($detailArray['isToMany'])) : ?>
<?php $joins = $detailArray['joins']; if ($detailArray['isToMany'] == false) : ?>
<?php if (count($joins) == 1) : ?><?php $join = $joins[0]; ?>
<?php $index_count++;
	$indexName = substr($this->tableName() . $detailArray["destination"] . '_' . $join["sourceAttribute"], 0, 20) . '_' . substr("00".dechex($index_count % 256),-2) . '_fk';
?>
DROP INDEX IF EXISTS <?php echo $this->tableName() . $detailArray["destination"]; ?>_fk;
DROP INDEX IF EXISTS <?php echo $indexName; ?>;
CREATE INDEX IF NOT EXISTS <?php echo $indexName; ?> on <?php echo $this->tableName(); ?> (<?php echo $join["sourceAttribute"]; ?>);
<?php endif; // multiple joins ?>
<?php endif; // isToMany or toOne ?>
<?php endif; // has isToMany ?>
<?php endforeach; // looprelationships ?>
<?php if (is_array($this->indexes)) : ?>
<?php foreach( $this->indexes as $detailArray ) : ?>
<?php	$index_count++;
		$columns = $detailArray["columns"];
		$old_indexName = $this->tableName() . '_' . implode("", $columns);
		$indexName = substr($this->tableName() . '_' . implode("", $columns), 0, 20) . '_' . substr("00".dechex($index_count % 256),-2);
		$unique = ( (isset($detailArray["unique"]) && $detailArray["unique"]) ? "UNIQUE" : "");
		$filtered_columns = array();
		foreach( $columns as $cname ) {
			$filtered_columns[] = $cname . ($this->isType_TEXT($cname) ? " COLLATE NOCASE" : "");
		}
?>
DROP INDEX IF EXISTS <?php echo $old_indexName; ?>;
DROP INDEX IF EXISTS <?php echo $indexName; ?>;
CREATE <?php echo $unique; ?> INDEX IF NOT EXISTS <?php echo $indexName; ?> on <?php echo $this->tableName(); ?> (<?php echo implode(",", $filtered_columns); ?>);
<?php endforeach; // loop indexes ?>
<?php endif; // has indexes ?>
