#! /usr/bin/env php
<?php

$system_path = dirname(dirname(__FILE__));
if (realpath($system_path) !== FALSE)
{
	$system_path = realpath($system_path). DIRECTORY_SEPARATOR;
}

define('SYSTEM_PATH', str_replace("\\", DIRECTORY_SEPARATOR, $system_path));
define('APPLICATION_PATH', SYSTEM_PATH . DIRECTORY_SEPARATOR . 'application');
define('MODELS_PATH', APPLICATION_PATH . DIRECTORY_SEPARATOR . 'model');

$models_path = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'models';
realpath($models_path) || die( "Could not find 'dbo_models'" );

$templates_path = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'templates';
realpath($templates_path) || die( "Could not find 'dbo_templates'" );

define('MODEL_BASE_TEMPLATE', $templates_path . DIRECTORY_SEPARATOR . 'model_base_template.php');
define('MODEL_TEMPLATE', $templates_path . DIRECTORY_SEPARATOR . 'model_template.php');
define('DBO_BASE_TEMPLATE', $templates_path . DIRECTORY_SEPARATOR . 'dbo_base_template.php');
define('DBO_TEMPLATE', $templates_path . DIRECTORY_SEPARATOR . 'dbo_template.php');

require SYSTEM_PATH .'application/config/bootstrap.php';
require SYSTEM_PATH .'application/config/autoload.php';
require SYSTEM_PATH .'application/config/common.php';
require SYSTEM_PATH .'application/config/errors.php';
require SYSTEM_PATH .'tests/_ResetConfig.php';

$root = TEST_ROOT_PATH . "/" . basename(__FILE__, ".php");
$tmp_dir = "/tmp/"; // sys_get_temp_dir();

SetConfigRoot( $root );

// load the parser class
require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'schemaModelParser.php';

class PhpTemplate extends schemaModelParser
{
    public function generate()
    {
        ob_start();

		echo "<?php" . PHP_EOL. PHP_EOL;
        include $this->path_to_file;
		echo PHP_EOL . "?" .">" . PHP_EOL;

        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }
}


$options = getopt( "f");
$force = (isset($options, $options['f']) ? true : false);

$model_files = array();
foreach (glob($models_path . DIRECTORY_SEPARATOR . "*.json") as $file) {
	echo PHP_EOL . $file .PHP_EOL;

	$model_meta = json_decode(file_get_contents($file), true);
	is_array($model_meta) || die("Failed to read $file" . PHP_EOL);

	$package = $model_meta['package'];
	$modelname = $model_meta['model'];
	$dboname = $model_meta['dbo'];
	$model_base_file = appendPath( MODELS_PATH, $package, "_" . $modelname) . ".php";
	$model_file = appendPath( MODELS_PATH, $package, $modelname) . ".php";
	$dbo_base_file = appendPath( MODELS_PATH, $package, "_" . $dboname) . ".php";
	$dbo_file = appendPath( MODELS_PATH, $package, $dboname) . ".php";
	$diff_files = array();

	/** create package directory */
	$packagePath = appendPath( MODELS_PATH, $package );
	is_dir($packagePath) ||  mkdir($packagePath) || die( 'Failed to created directory ' . $packagePath );

	/** generate base model file */
	$Template = new PhpTemplate(MODEL_BASE_TEMPLATE);
	$Template->setModel($model_meta);
	$model_data = $Template->generate();
	file_put_contents( $model_base_file, $model_data );

	/** generate model file, only if it does not exist */
	if ($force == false && is_file($model_file) ) {
		$tmp_file = appendPath( $tmp_dir, $package, $modelname) . ".php";
		is_dir(dirname($tmp_file)) ||  mkdir(dirname($tmp_file)) || die( 'Failed to created directory ' . dirname($tmp_file) );
		$diff_files[$model_file] = $tmp_file;
		$model_file = $tmp_file;
	}
	$Template = new PhpTemplate(MODEL_TEMPLATE);
	$Template->setModel($model_meta);
	$model_data = $Template->generate();
	file_put_contents( $model_file, $model_data );

	$clazz = "model\\" . $package . "\\" . $modelname;
	$instance = new $clazz();
	echo $clazz . " .. " . $instance->consistencyTest() . PHP_EOL;


	/** generate dbo base file */
	$Template = new PhpTemplate(DBO_BASE_TEMPLATE);
	$Template->setModel($model_meta);
	$dbo_data = $Template->generate();
	file_put_contents( $dbo_base_file, $dbo_data );

	/** generate dbo file, only if it does not exist */
	if ( $force == false && is_file($dbo_file) ) {
		$tmp_file = appendPath( $tmp_dir, $package, $dboname) . ".php";
		is_dir(dirname($tmp_file)) ||  mkdir(dirname($tmp_file)) || die( 'Failed to created directory ' . dirname($tmp_file) );
		$diff_files[$dbo_file] = $tmp_file;
		$dbo_file = $tmp_file;
	}
	$Template = new PhpTemplate(DBO_TEMPLATE);
	$Template->setModel($model_meta);
	$dbo_data = $Template->generate();
	file_put_contents( $dbo_file, $dbo_data );

	$clazz = "model\\" . $package . "\\" . $dboname;
	$instance = new $clazz();
	echo $clazz . " .. " . $instance->consistencyTest() . PHP_EOL;

// 	foreach( $diff_files as $prod => $temp ) {
// 		$retval = exec( "diff -q $prod $temp", $output, $rt );
// 		if ( $rt == 1 ) {
// 			echo
// // 	 		$retval = shell_exec( "opendiff $prod $temp > /dev/null 2>&1" );
// 	 	}
// 	}
}

?>
