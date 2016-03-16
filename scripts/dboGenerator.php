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

$models_path = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'dbo_models';
realpath($models_path) || die( "Could not find 'dbo_models'" );

$templates_path = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'dbo_templates';
realpath($templates_path) || die( "Could not find 'dbo_templates'" );

define('MODEL_TEMPLATE', $templates_path . DIRECTORY_SEPARATOR . 'model_template.php');
define('DBO_TEMPLATE', $templates_path . DIRECTORY_SEPARATOR . 'dbo_template.php');

require SYSTEM_PATH .'application/config/bootstrap.php';
require SYSTEM_PATH .'application/config/autoload.php';
require SYSTEM_PATH .'application/config/common.php';
require SYSTEM_PATH .'application/config/errors.php';
require SYSTEM_PATH .'tests/_ResetConfig.php';

$root = TEST_ROOT_PATH . "/" . basename(__FILE__, ".php");
SetConfigRoot( $root );

const TABLE =		'table';
const ATTRIBUTES =	'attributes';

const AttributesPK =	'primaryKeys';
const AttributesIndexes =	'indexes';
const AttributesUnique =	'uniqueIndexes';

const ColumnName =		'column';
const ColumnType =		'type';
const ColumnAllowsNull ='nullable';
const ColumnCollation =	'collate';



class Template
{
    var $model = array();
    var $path_to_file= array();
    function __construct($path_to_file)
    {
         if(!file_exists($path_to_file))
         {
             trigger_error('Template File not found!',E_USER_ERROR);
             return;
         }
         $this->path_to_file = $path_to_file;
    }

    public function setModel(array $m)
    {
        $this->model = $m;
    }

    public function __get($key) {
    	return (isset($this->model[$key]) ? $this->model[$key] : null);
    }

    public function dboClassName() {
    	return (isset($this->model['dbo']) ? $this->model['dbo'] : null);
    }

    public function modelClassName() {
    	return (isset($this->model['model']) ? $this->model['model'] : null);
    }

    public function displayAttribute() {
    	$attributes = $this->attributes;
    	if ( is_array($attributes) && isset($attributes['name']) ) {
    		return 'name';
    	}
    	return null;
    }

    public function tableName() {
    	return (isset($this->model['table']) ? $this->model['table'] : null);
    }

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

/*
	const TABLE =		'version';
	const id =			'id';
	const code =		'code';
	const major =		'major';
	const minor =		'minor';
	const patch =		'patch';
	const created =		'created';
	const hash_code =	'hash_code';
		$sql = 'CREATE TABLE IF NOT EXISTS ' . Version::TABLE . " ( "
				. Version::id . " INTEGER PRIMARY KEY, "
				. Version::code . " TEXT COLLATE NOCASE, "
				. Version::major . " INTEGER, "
				. Version::minor . " INTEGER, "
				. Version::patch . " INTEGER, "
				. Version::created . " INTEGER, "
				. Version::hash_code . " TEXT "
				. ")";
*/
$model_files = array();
foreach (glob($models_path . DIRECTORY_SEPARATOR . "*.json") as $file) {
	echo PHP_EOL . $file .PHP_EOL;

	$model_meta = json_decode(file_get_contents($file), true);
	$package = $model_meta['package'];
	$modelname = $model_meta['model'];
	$dboname = $model_meta['dbo'];

	/** generate model file */
	$packagePath = appendPath( MODELS_PATH, $package );
	is_dir($packagePath) ||  mkdir($packagePath) || die( 'Failed to created directory ' . $packagePath );

	$Template = new Template(MODEL_TEMPLATE);
	$Template->setModel($model_meta);
	$model_data = $Template->generate();
	$model_file = appendPath( MODELS_PATH, $package, $modelname) . ".php";
	file_put_contents( $model_file, $model_data );
	$clazz = "model\\" . $package . "\\" . $modelname;

	$instance = new $clazz();
	echo $clazz . " .. " . $instance->consistencyTest() . PHP_EOL;
	$m = Model::Named( $package . "\\" . $modelname);
	echo $package . "\\" . $modelname . " .. " . $m->consistencyTest() . PHP_EOL;

	/** generate dbo file */
	$Template = new Template(DBO_TEMPLATE);
	$Template->setModel($model_meta);
	$dbo_data = $Template->generate();
	$dbo_file = appendPath( MODELS_PATH, $package, $dboname) . ".php";
	file_put_contents( $dbo_file, $dbo_data );
	$clazz = "model\\" . $package . "\\" . $dboname;

	$instance = new $clazz();
	echo $clazz . " .. " . $instance->consistencyTest() . PHP_EOL;
}

?>
