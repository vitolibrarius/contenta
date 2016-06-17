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
    var $destinationRelationMap = null;

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

    public function dboBaseName() {
    	return "_" . $this->dboClassName();
    }

    public function dboClassName() {
    	return (isset($this->model['dbo']) ? $this->model['dbo'] : null);
    }

    public function modelBaseName() {
    	return "_" . $this->modelClassName();
    }

    public function modelClassName() {
    	return (isset($this->model['model']) ? $this->model['model'] : null);
    }

    public function packageName() {
		if ( is_null($this->package) ) {
			return 'model';
		}

		return 'model\\' . $this->package;
    }

    public function dboPackageClassName() {
    	return "\\" . $this->packageName() . "\\" . $this->dboClassName();
    }

    public function modelPackageClassName() {
    	return "\\" . $this->packageName() . "\\" . $this->modelClassName();
    }

    public function displayAttribute() {
    	$attributes = $this->attributes;
    	if ( is_array($attributes) && isset($attributes['name']) ) {
    		return 'name';
    	}
    	return null;
    }

    public function isPrimaryKey( $name = '' ) {
    	$pkAttributes = $this->primaryKeys;
    	if ( is_array($pkAttributes) && in_array($name, $pkAttributes)) {
    		return true;
    	}
    	return false;
    }

	public function isType_TEXT($name = '') {
		$type = $this->modelTypeForAttribute($name);
		return (is_null($type) == false && $type == 'Model::TEXT_TYPE');
	}

	public function isType_TEXT_URL($name = '') {
		return ( $this->isType_TEXT($name) && endsWith('_url', $name) );
	}

	public function isType_TEXT_EMAIL($name = '') {
		return ( $this->isType_TEXT($name) && endsWith('email', $name) );
	}

	public function isType_DATE($name = '') {
		$type = $this->modelTypeForAttribute($name);
		return (is_null($type) == false && $type == 'Model::DATE_TYPE');
	}

	public function isType_DATE_created($name = '') {
		return ( $this->isType_DATE($name) && endsWith('created', $name) );
	}

	public function isType_INTEGER($name = '') {
		$type = $this->modelTypeForAttribute($name);
		return (is_null($type) == false && $type == 'Model::INT_TYPE');
	}

	public function isType_BOOLEAN($name = '') {
		$type = $this->modelTypeForAttribute($name);
		return (is_null($type) == false && $type == 'Model::FLAG_TYPE');
	}

    public function modelTypeForAttribute($name = '') {
    	$details = $this->detailsForAttribute($name);
    	$type = null;
    	if ( is_array($details) ) {
			$type = 'Model::TEXT_TYPE';
			if (isset($details['type']) ) {
				switch ($details['type']) {
					case 'DATE':
						$type = "Model::DATE_TYPE";
						break;
					case 'INTEGER':
						$type = "Model::INT_TYPE";
						break;
					case 'BOOLEAN':
						$type = 'Model::FLAG_TYPE';
						break;
					default:
						break;
				}
			}
		}
    	return $type;
    }

    public function isRelationshipKey( $rel = '' ) {
    	$relations = $this->relationships;
    	if ( is_array($relations) ) {
    		foreach( $relations as $name => $details ) {
    			$joins = $details['joins'];
    			foreach( $joins as $idx => $join ) {
    				if ( isset($join['sourceAttribute']) && $join['sourceAttribute'] == $rel ) {
    					return true;
    				}
    			}
    		}
    	}
    	return false;
    }

    public function isMandatoryRelationshipKey( $rel = '' ) {
    	$relations = $this->mandatoryObjectRelations();
    	if ( is_array($relations) ) {
    		foreach( $relations as $name => $details ) {
    			$joins = $details['joins'];
    			foreach( $joins as $idx => $join ) {
    				if ( isset($join['sourceAttribute']) && $join['sourceAttribute'] == $rel ) {
    					return true;
    				}
    			}
    		}
    	}
    	return false;
    }

    public function relationshipForSourceKey( $key = '' ) {
    	$relations = $this->relationships;
    	if ( is_array($relations) ) {
    		foreach( $relations as $name => $details ) {
    			$joins = $details['joins'];
    			foreach( $joins as $idx => $join ) {
    				if ( isset($join['sourceAttribute']) && $join['sourceAttribute'] == $key ) {
    					return array($name, $details);
    				}
    			}
    		}
    	}
    	return false;
    }


    public function isUniqueAttribute( $attr = '' ) {
    	$indexes = $this->indexes;
    	if ( is_array($indexes) ) {
    		foreach( $indexes as $details ) {
    			$columns = (isset($details['columns']) ? $details['columns'] : array());
    			if ( isset($details['unique']) && $details['unique'] && count($columns) == 1 && in_array($attr, $columns)) {
					return true;
    			}
    		}
    	}
    	return false;
    }

    public function isMandatoryAttribute( $attr = '' ) {
    	$details = $this->detailsForAttribute($attr);
    	if ( is_array($details) ) {
			if ( isset($details['nullable']) && $details['nullable'] == false ) {
				return true;
			}
		}
    	return false;
    }

    public function defaultCreationValue( $attr = '' ) {
    	$details = $this->detailsForAttribute($attr);
    	if (isset($details['default']) ) {
    		return $details['default'];
    	}
    	if ( is_array($details) && isset($details['type']) ) {
    		switch ($details['type']) {
    			case 'BOOLEAN': return "Model::TERTIARY_TRUE";
    			case 'DATE': return "time()";
    			case 'TEXT': return "null";
    			default: break;
    		}
    	}
    	return "null";
    }

    public function detailsForAttribute( $attr = '' ) {
    	$attributes = $this->attributes;
    	if ( is_array($attributes) ) {
    		return (isset($attributes[$attr]) ? $attributes[$attr] : null);
    	}
    	return null;
    }

    public function detailsForRelationship( $rel = '' ) {
    	$relations = $this->relationships;
    	if ( is_array($relations) ) {
    		return (isset($relations[$rel]) ? $relations[$rel] : null);
    	}
    	return null;
    }

    public function createObjectAttributes() {
    	$attributes = $this->attributes;
    	if ( is_array($attributes) ) {
    		$creationAttr = array();
    		$ignoreAttr = array( 'created', 'updated' );
    		foreach( $attributes as $name => $details ) {
    			if ( $this->isPrimaryKey( $name ) == false
    				&& $this->isMandatoryRelationshipKey($name) == false
    				&& in_array($name, $ignoreAttr) == false ) {
	    			$creationAttr[$name] = $details;
    			}
    		}

    		return $creationAttr;
    	}
    	return null;
    }

    public function mandatoryObjectAttributes() {
    	$attributes = $this->createObjectAttributes();
    	if ( is_array($attributes) ) {
    		$creationAttr = array();
    		foreach( $attributes as $name => $details) {
    			if ( isset($details['nullable']) && $details['nullable'] == false ) {
	    			$creationAttr[$name] = $details;
    			}
    		}

    		return $creationAttr;
    	}
    	return null;
    }

    public function mandatoryObjectRelations() {
    	$relations = $this->relationships;
    	if ( is_array($relations) ) {
    		$creationAttr = array();
    		foreach( $relations as $name => $details ) {
    			if (isset( $details['isMandatory']) && $details['isMandatory']) {
	    			$creationAttr[$name] = $details;
    			}
    		}

    		return $creationAttr;
    	}
    	return null;
    }

    public function namedFetches() {
    	$fetches = $this->fetches;
		return (is_array($fetches) ? $fetches : array());
    }

	public function relationshipAliasStatements()
	{
		if ( is_null($this->destinationRelationMap) ) {
			$this->destinationRelationMap = array();
			$objectRelationships = (is_array($this->relationships) ? $this->relationships : array());;

			foreach( $objectRelationships as $name => $detailArray ) {
				if ( isset($detailArray['destination'], $detailArray['destinationPackage'], $detailArray['destinationTable']) ) {
					$destName = $detailArray['destination'];
					$destPackageName = $detailArray['destinationPackage'];
					$destTableName = $detailArray['destinationTable'];
					$this->destinationRelationMap[$destTableName] = array(
						"use " . $destPackageName . $destName . " as " . $destName . ";",
						"use " . $destPackageName . $destName . "DBO as " . $destName . "DBO;"
					);
				}
				else {
					Logger::logWarning( "Error: relationship " . $name . " missing one of 'destination', 'destinationPackage', 'destinationTable'");
				}
			}
		}
		return $this->destinationRelationMap;
	}

	public function sqlString( $sqlDetails )
	{
		$sqlPHPString = "";
		$type = (isset($sqlDetails['type']) ? $sqlDetails['type'] : "");
		$key = (isset($sqlDetails['keyAttribute']) ? $sqlDetails['keyAttribute'] : null);
		switch( $type ) {
			case "Aggregate":
				$function = (isset($sqlDetails['function']) ? $sqlDetails['function'] : null);
				$modelName = (isset($sqlDetails['model']) ? $sqlDetails['model'] : $this->modelClassName());
				$qualifiers = (isset($sqlDetails['qualifiers']) ? $sqlDetails['qualifiers'] : array());
				$qualString = "null";
				if ( is_array($qualifiers) && count($qualifiers) > 0 ) {
					if ( count($qualifiers) == 1 ) {
						$qualString = $this->qualifierString( array_pop($qualifiers) );
					}
					else {
						$semantic = (isset($sqlDetails["semantic"]) ? $sqlDetails["semantic"] : "AND");
						$qualString = "Qualifier::Combine( '" . $semantic . "', array( "
							. implode(',\n\t\t', array_map(function($item) { return $this->qualifierString( $item ); }, $qualifiers))
							. " )";
					}
				}

				$sqlPHPString = "SQL::Aggregate( '" . $function . "', Model::Named('" . $modelName . "'), "
					. "'" . $key . "', " . $qualString . ", null )";
				break;
			default:
				throw new \Exception( "Malformed qualfier details " . var_export($sqlDetails, true));
		}
		return $sqlPHPString;
	}

	public function qualifierString( $qualDetails )
	{
		$qualPHPString = "";
		$type = (isset($qualDetails['type']) ? $qualDetails['type'] : "");
		$key = (isset($qualDetails['keyAttribute']) ? $qualDetails['keyAttribute'] : null);
		$arg = (isset($qualDetails['argAttribute']) ? $qualDetails['argAttribute'] : null);

		switch( $type ) {
			case "Related":
				$relationName = (isset($qualDetails['relationship']) ? $qualDetails['relationship'] : null);
				if ( is_null($relationName) ) {
					throw new \Exception( "Malformed qualfier missing 'relationship' name " . var_export($qualDetails, true));
				}
				$relationDetails = $this->detailsForRelationship($relationName);
				if ( is_null($relationDetails) ) {
					throw new \Exception( "Malformed qualfier relationship named '" .$relationName. "' was not found");
				}
				$joins = (isset($relationDetails['joins']) ? $relationDetails['joins'] : null);
				if ( is_array($joins) == false || count($joins) != 1 ) {
					throw new \Exception( "Malformed qualfier relationship named '" .$relationName. "' bad join");
				}
				$theJoin = $joins[0];
				$srcAttribute = (isset($theJoin['sourceAttribute']) ? $theJoin['sourceAttribute'] : null);
				if ( is_null($srcAttribute) ) {
					throw new \Exception( "Malformed qualfier relationship named '" .$relationName. "' sourceAttribute not found");
				}

				if ( is_null($arg) ) {
					throw new \Exception( "Malformed qualfier missing arg attribute " . var_export($qualDetails, true));
				}

				$qualPHPString = "Qualifier::FK( '" . $srcAttribute . "', $" . $arg . ")";

				break;
			case "InSubQuery":
				$subDetails = (isset($qualDetails['subQuery']) ? $qualDetails['subQuery'] : array());
				if ( count($subDetails) == 0 ) {
					throw new \Exception( "Malformed qualfier missing subquery " . var_export($qualDetails, true));
				}
				$subDetailsPHPString = $this->sqlString( $subDetails );

				if ( is_null($key) ) {
					throw new \Exception( "Malformed qualfier missing key attribute " . var_export($qualDetails, true));
				}

				$qualPHPString = "Qualifier::InSubQuery( '" . $key . "', " . $subDetailsPHPString. ", null)";
				break;
			case "Equals":
			case "GreaterThanEquals":
			case "GreaterThan":
			case "LessThanEquals":
			case "LessThan":
				if ( is_null($key) ) {
					throw new \Exception( "Malformed qualfier missing key attribute " . var_export($qualDetails, true));
				}
				if ( is_null($arg) ) {
					throw new \Exception( "Malformed qualfier missing arg attribute " . var_export($qualDetails, true));
				}
				$qualPHPString = "Qualifier::" . $type . "( '" . $key . "', $" . $arg . ")";
				break;

			case "Like":
				if ( is_null($key) ) {
					throw new \Exception( "Malformed qualfier missing key attribute " . var_export($qualDetails, true));
				}
				if ( is_null($arg) ) {
					throw new \Exception( "Malformed qualfier missing arg attribute " . var_export($qualDetails, true));
				}
				$wild = "SQL::SQL_LIKE_AFTER";
				if (isset($qualDetails['wildcard'])) {
					if ( strtolower($qualDetails['wildcard']) == "before" ) {
						$wild = "SQL::SQL_LIKE_BEFORE";
					}
					else if ( strtolower($qualDetails['wildcard']) == "both" ) {
						$wild = "SQL::SQL_LIKE_BOTH";
					}
				}
				$qualPHPString = "Qualifier::Like( '" . $key . "', $" . $arg . ", " . $wild . ")";
				break;

			default:
				throw new \Exception( "Malformed qualfier details " . var_export($qualDetails, true));
		}
		return $qualPHPString;
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
	$Template = new Template(MODEL_BASE_TEMPLATE);
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
	$Template = new Template(MODEL_TEMPLATE);
	$Template->setModel($model_meta);
	$model_data = $Template->generate();
	file_put_contents( $model_file, $model_data );

	$clazz = "model\\" . $package . "\\" . $modelname;
	$instance = new $clazz();
	echo $clazz . " .. " . $instance->consistencyTest() . PHP_EOL;


	/** generate dbo base file */
	$Template = new Template(DBO_BASE_TEMPLATE);
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
	$Template = new Template(DBO_TEMPLATE);
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
