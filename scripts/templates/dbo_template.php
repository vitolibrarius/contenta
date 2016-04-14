namespace <?php echo $this->packageName(); ?>;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use <?php echo $this->modelPackageClassName(); ?> as <?php echo $this->modelClassName(); ?>;

/* import related objects */
<?php
	$aliases = $this->relationshipAliasStatements();
	foreach( $aliases as $table => $statements ) {
		echo implode(PHP_EOL, $statements) . PHP_EOL;
	}
?>

class <?php echo $this->dboClassName(); ?> extends <?php echo $this->dboBaseName(); ?>

{

}
