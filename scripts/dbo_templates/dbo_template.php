<?php if ( is_null($this->package) ) : ?>
namespace model;
<?php else : ?>
namespace model\<?php echo $this->package; ?>;
<?php endif; ?>

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

<?php if ( is_null($this->package) ) : ?>
use model\<?php echo $this->modelClassName(); ?> as <?php echo $this->modelClassName(); ?>;
<?php else : ?>
use model\<?php echo $this->package . "\\" . $this->modelClassName(); ?> as <?php echo $this->modelClassName(); ?>;
<?php endif; ?>

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
}
