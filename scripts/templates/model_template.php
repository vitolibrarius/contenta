namespace <?php echo $this->packageName();
	$mandatoryObjectAttributes = $this->mandatoryObjectAttributes();
	$lastMandatoryKey = array_last_key($mandatoryObjectAttributes);

	$objectAttributes = $this->attributes;
	$lastAttributeKey = array_last_key($objectAttributes);

	$objectRelationships = $this->relationships;
?>;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;

use <?php echo $this->dboPackageClassName(); ?> as <?php echo $this->dboClassName(); ?>;

/* import related objects */
<?php
	$aliases = $this->relationshipAliasStatements();
	foreach( $aliases as $table => $statements ) {
		echo implode(PHP_EOL, $statements) . PHP_EOL;
	}
?>

class <?php echo $this->modelClassName(); ?> extends <?php echo $this->modelBaseName(); ?>

{
	public function searchQualifiers( array $query )
	{
		$qualifiers = parent::searchQualifiers($query);
		return $qualifiers;
	}

	/**
	 *	Create/Update functions
	 */
<?php
	$createAttrList = array_keys($this->createObjectAttributes());
	$mandatoryAttrList = array_keys($this->mandatoryObjectAttributes());
	$createRelationsList = array_keys($this->mandatoryObjectRelations());
?>
	public function createObject( array $values = array())
	{
		if ( isset($values) ) {
			// massage values as necessary
		}

		return parent::createObject($values);
	}

	public function updateObject(DataObject $object = null, array $values = array()) {
		if (isset($object) && $object instanceof <?php echo $this->dboClassName(); ?> ) {
			// massage values as necessary
		}

		return parent::updateObject($object, $values);
	}

	public function attributesFor($object = null, $type = null) {
		$attrFor = array(<?php $lastKey = array_last_key($objectAttributes); foreach( $objectAttributes as $name => $detailArray ) {
			if ( $this->isPrimaryKey($name) === false )	echo "\n\t\t\t" . $this->modelClassName() . "::" . $name . ( $name != $lastKey ? "," : "");
		} ?>

		);
		return array_intersect_key($this->attributesMap(),array_flip($attrFor));
	}

	/*
	public function attributeIsEditable($object = null, $type = null, $attr)
	{
		// add customization here
		return parent::attributeIsEditable($object, $type, $attr);
	}
	*/

	/*
	public function attributeRestrictionMessage($object = null, $type = null, $attr)	{ return null; }
	public function attributePlaceholder($object = null, $type = null, $attr)	{ return null; }
	*/

	/*
	public function attributeDefaultValue($object = null, $type = null, $attr)
	{
		return parent::attributeDefaultValue($object, $type, $attr);
	}
	*/

	/*
	public function attributeEditPattern($object = null, $type = null, $attr)
	{
<?php foreach( $objectAttributes as $name => $detailArray ) : ?>
<?php if (isset($detailArray['inputPattern'])) : ?>
		if ( <?php echo $this->modelClassName() . "::" . $name; ?> == $attr ) {
			return "<?php echo $detailArray['inputPattern']; ?>";
		}

<?php endif; ?>
<?php endforeach; ?>
		return null;
	}
	*/

	public function attributeOptions($object = null, $type = null, $attr)
	{
<?php foreach( $objectRelationships as $name => $detailArray ) : ?>
<?php if (isset($detailArray['isToMany']) && $detailArray['isToMany'] == false) : ?>
<?php $joins = $detailArray['joins']; if (count($joins) == 1) : ?>
<?php $join = $joins[0]; ?>
		if ( <?php echo $this->modelClassName() . "::" . $join["sourceAttribute"]; ?> == $attr ) {
			$model = Model::Named('<?php echo $detailArray["destination"]; ?>');
			return $model->allObjects();
		}
<?php else : ?>
		// FixMe: relationship <?php echo $name; ?> has multiple joins
<?php endif; // multiple joins ?>
<?php endif; ?>
<?php endforeach; ?>
		return null;
	}

	/** Validation */
<?php foreach( $objectAttributes as $name => $detailArray ) : ?>
<?php if ( $this->isPrimaryKey($name) === false ) : ?>
/*
	function validate_<?php echo $name; ?>($object = null, $value)
	{
		return parent::validate_<?php echo $name; ?>($object, $value);
	}
*/

<?php endif; // not primaryKey ?>
<?php endforeach; ?>
}
