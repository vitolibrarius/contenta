namespace <?php echo $this->packageName(); ?>;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use <?php echo $this->modelPackageClassName(); ?> as <?php echo $this->modelClassName(); ?>;

class <?php echo $this->dboClassName(); ?> extends DataObject
{
<?php
foreach( $this->attributes as $name => $detailArray ) {
	echo "\tpublic \$$name;" . PHP_EOL;
}
?>

<?php if ( is_null($this->displayAttribute()) == false ) : ?>
	public function displayName()
	{
		return $this-><?php echo $this->displayAttribute(); ?>;
	}
<?php endif; ?>

<?php foreach( $this->attributes as $name => $detailArray ) : ?>
<?php if (isset($detailArray['type']) && $detailArray['type'] == 'DATE') : ?>
	public function formattedDateTime<?php echo ucwords($name); ?>() { return $this->formattedDate( <?php echo $this->modelClassName() . "::" . $name; ?>, "M d, Y H:i" ); }
	public function formattedDate<?php echo ucwords($name); ?>() {return $this->formattedDate( <?php echo $this->modelClassName() . "::" . $name; ?>, "M d, Y" ); }
<?php endif; ?>
<?php endforeach; ?>

<?php if (is_array($this->relationships)) : ?>
<?php foreach( $this->relationships as $name => $detailArray ) : ?>
<?php if (isset($detailArray['isToMany'])) : ?>
<?php $joins = $detailArray['joins']; if ($detailArray['isToMany']) : ?>
	// to-many relationship
	public function <?php echo $name; ?>()
	{
<?php if (count($joins) == 1) : ?><?php $join = $joins[0]; ?>
		if ( isset( $this-><?php echo $join["sourceAttribute"]; ?> ) ) {
			$model = Model::Named('<?php echo $detailArray["destination"]; ?>');
			return $model->allObjectsForKeyValue( <?php echo $detailArray["destination"] . "::" . $join["destinationAttribute"]; ?>, $this-><?php echo $join["sourceAttribute"]; ?>);
		}
<?php else : ?>
			FixMe: relationship <?php echo $name; ?> has multiple joins
<?php endif; ?>

		return false;
	}
<?php else : ?>
	// to-one relationship
	public function <?php echo $name; ?>()
	{
<?php if (count($joins) == 1) : ?><?php $join = $joins[0]; ?>
		if ( isset( $this-><?php echo $join["sourceAttribute"]; ?> ) ) {
			$model = Model::Named('<?php echo $detailArray["destination"]; ?>');
			return $model->objectForId($this-><?php echo $join["sourceAttribute"]; ?>);
		}
<?php else : ?>
			FixMe: relationship <?php echo $name; ?> has multiple joins
<?php endif; ?>
		return false;
	}
<?php endif; ?>
<?php else : ?>
	Error: relationship <?php echo $name; ?> does not define 'isToMany'
<?php endif; ?>

<?php endforeach; ?>
<?php endif; ?>
}
