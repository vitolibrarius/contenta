<?php if ( is_null($this->package) ) : ?>
namespace model;
<?php else : ?>
namespace model\<?php echo $this->package; ?>;
<?php endif; ?>

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

<?php if ( is_null($this->package) ) : ?>
use model\<?php echo $this->dboClassName(); ?> as <?php echo $this->dboClassName(); ?>;
<?php else : ?>
use model\<?php echo $this->package . "\\" . $this->dboClassName(); ?> as <?php echo $this->dboClassName(); ?>;
<?php endif; ?>

class <?php echo $this->modelClassName(); ?> extends Model
{
	const TABLE = '<?php echo $this->tableName(); ?>';
<?php
foreach( $this->attributes as $name => $detailArray ) {
	echo "\tconst $name = '$name';" . PHP_EOL;
}
?>

	public function tableName() { return <?php echo $this->modelClassName(); ?>::TABLE; }
	public function tablePK() { return <?php echo $this->modelClassName(); ?>::id; }
	public function sortOrder() { return array( 'asc' => array(<?php
	foreach( $this->sort as $v ) {
		echo $this->modelClassName() . "::" . $v . ", ";
	}
	?>)); }

	public function allColumnNames()
	{
		return array(
<?php
foreach( $this->attributes as $name => $detailArray ) {
		echo $this->modelClassName() . "::" . $name . ", ";
}
?>
		 );
	}
}
