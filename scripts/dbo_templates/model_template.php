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

/** Sample Creation script
		$sql = "CREATE TABLE IF NOT EXISTS " . <?php echo $this->tableName(); ?> . " ( "
<?php foreach( $this->attributes as $name => $detailArray ) : ?>
			. <?php echo $this->modelClassName() . "::" . $name . " . \" " . $detailArray['type'] . (in_array($name, $this->primaryKeys) ? " PRIMARY KEY" : "") . ", \""; ?>

<?php endforeach; ?>
<?php if (is_array($this->relationships)) : ?>
<?php foreach( $this->relationships as $name => $detailArray ) : ?>
<?php if (isset($detailArray['isToMany'])) : ?>
<?php $joins = $detailArray['joins']; if ($detailArray['isToMany'] == false) : ?>
<?php if (count($joins) == 1) : ?><?php $join = $joins[0]; ?>
			. "FOREIGN KEY (". <?php echo $this->modelClassName() . "::" . $join["sourceAttribute"]; ?> .") REFERENCES " . <?php echo $detailArray["destination"]; ?>::TABLE . "(" . <?php echo $detailArray["destination"] . "::" .  $join["destinationAttribute"]; ?> . "),"
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
*/
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

<?php foreach( $this->attributes as $name => $detailArray ) : ?>
<?php if (isset($detailArray['type'])) : ?>
<?php if ('TEXT' == $detailArray['type']) : ?>
	public function allFor<?php echo ucwords($name); ?>($value)
	{
		return $this->allObjectsForKeyValue(<?php echo $this->modelClassName() . "::" . $name; ?>, $value);
	}

<?php if (isset($detailArray['partialSearch']) && $detailArray['partialSearch'] == true) : ?>
	public function allLike<?php echo ucwords($name); ?>($value)
	{
		return \SQL::Select( $this )
			->where( Qualifier::Like( <?php echo $this->modelClassName() . "::" . $name; ?>, normalizeSearchString($value), SQL::SQL_LIKE_AFTER ))
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
<?php endif; // multiple joins ?>

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
<?php endif; // multiple joins ?>
		return false;
	}
<?php endif; // isToMany or toOne ?>
<?php else : ?>
	Error: relationship <?php echo $name; ?> does not define 'isToMany'
<?php endif; // has isToMany ?>

<?php endforeach; // looprelationships ?>
<?php endif; // has relationships ?>
}
