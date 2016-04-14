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
use \Validation as Validation;

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
	public function attributesFor($object = null, $type = null) {
		return array(
<?php foreach( $objectAttributes as $name => $detailArray ) {
	if ($name != "id") {
		echo "\t\t\t" . $this->modelClassName() . "::" . $name . " => "
			. $this->modelTypeForAttribute($name) . ($lastAttributeKey === $name ? "" : ",") . PHP_EOL;
	}
}?>
		);
	}

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

	public function attributeIsEditable($object = null, $type = null, $attr)
	{
		// add customization here
		return parent::attributeIsEditable($object, $type, $attr);
	}

	/*
	public function attributeRestrictionMessage($object = null, $type = null, $attr)	{ return null; }
	public function attributePlaceholder($object = null, $type = null, $attr)	{ return null; }
	*/

	public function attributeEditPattern($object = null, $type = null, $attr)
	{
<?php foreach( $objectAttributes as $name => $detailArray ) : ?>
<?php if (isset($detailArray['inputPattern'])) : ?>
		if ( $attr == <?php echo $this->modelClassName() . "::" . $name; ?> ) {
			return "<?php echo $detailArray['inputPattern']; ?>";
		}

<?php endif; ?>
<?php endforeach; ?>
		return null;
	}

	public function attributeOptions($object = null, $type = null, $attr)
	{
<?php foreach( $objectRelationships as $name => $detailArray ) : ?>
<?php if (isset($detailArray['isToMany']) && $detailArray['isToMany'] == false) : ?>
<?php $joins = $detailArray['joins']; if (count($joins) == 1) : ?>
<?php $join = $joins[0]; ?>
		if ( $attr = <?php echo $this->modelClassName() . "::" . $join["sourceAttribute"]; ?> ) {
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
<?php
 	$type = $this->modelTypeForAttribute($name);
	$mandatory = $this->isMandatoryAttribute($name);
	$textLength = (isset($details['length']) ? intval($details['length']) : 0);
if ( $this->isPrimaryKey($name) == false ) : ?>
	function validate_<?php echo $name; ?>($object = null, $value)
	{
<?php if ( $this->isType_TEXT($name) ) : ?>
<?php if ( $mandatory ) : ?>
		if (empty($value)) {
			return Localized::ModelValidation(
				$this->tableName(),
				<?php echo $this->modelClassName() . "::" . $name; ?>,
				"FIELD_EMPTY"
			);
		}
<?php endif; // mandatory ?>
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
		if ( filter_var($value, FILTER_VALIDATE_URL) == false) {
			return Localized::ModelValidation(
				$this->tableName(),
				<?php echo $this->modelClassName() . "::" . $name; ?>,
				"FILTER_VALIDATE_URL"
			);
		}
<?php endif; // url ?>
<?php endif; // TEXT ?>
<?php if ( $this->isType_DATE($name) ) : ?>
<?php endif; // DATE ?>
<?php if ( $this->isType_INTEGER($name) ) : ?>
<?php endif; // INT ?>
<?php if ( $this->isType_BOOLEAN($name) ) : ?>
<?php endif; // BOOLEAN ?>
<?php if ( $this->isUniqueAttribute($name) ) : ?>
		// make sure <?php echo ucwords($name); ?> is unique
		$existing = $this->objectFor<?php echo ucwords($name); ?>($value);
		if ( is_null($object) == false && $existing != false && $existing->id != $object->id) {
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
