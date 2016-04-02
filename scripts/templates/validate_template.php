namespace <?php echo $this->packageName(); ?>;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Validation as Validation;

use <?php echo $this->modelPackageClassName(); ?> as <?php echo $this->modelClassName(); ?>;
use <?php echo $this->dboPackageClassName(); ?> as <?php echo $this->dboClassName(); ?>;

class <?php echo $this->modelClassName(); ?>_Validation extends Validation
{
	public function tableName() { return <?php echo $this->modelClassName(); ?>::TABLE; }

	public function attributesFor($object = null, $type = null) {
		return array( <?php foreach( $this->attributes as $name => $detailArray ) {
			switch ($detailArray['type']) {
				case 'DATE':
					$type = "Validation::DATE_TYPE";
					break;
				case 'INTEGER':
					$type = "Validation::INT_TYPE";
					break;
				case 'BOOLEAN':
					$type = 'Validation::FLAG_TYPE';
					break;
				default:
					$type = 'Validation::TEXT_TYPE';
					break;
			}

			if ($name != "id") {
				echo "\t\t$this->modelClassName()::$name => $type;" . PHP_EOL;
		}
	}?>
		);
		return array();
	}

	public function attributesMandatory($object = null)				 			{ return array(); }
	public function attributeName($object = null, $type = null, $attr)			{ return $this->attributeId($attr); }
	public function attributeIsEditable($object = null, $type = null, $attr)	{ return true; }
	public function attributeRestrictionMessage($object = null, $type = null, $attr)	{ return null; }
	public function attributeEditPattern($object = null, $type = null, $attr)	{ return null; }
	public function attributePlaceholder($object = null, $type = null, $attr)	{ return null; }
	public function attributeOptions($object = null, $type = null, $attr)		{ return null; }

}
