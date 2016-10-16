<?php

const TABLE =		'table';
const ATTRIBUTES =	'attributes';

const AttributesPK =	'primaryKeys';
const AttributesIndexes =	'indexes';
const AttributesUnique =	'uniqueIndexes';

const ColumnName =		'column';
const ColumnType =		'type';
const ColumnAllowsNull ='nullable';
const ColumnCollation =	'collate';

abstract class schemaModelParser
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
		return ( $this->isType_TEXT($name) && ($name == 'xurl' || endsWith('_url', $name)) );
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

	public function estimateArgumentType($argName = '', $qualifiers) {
		if ( is_array( $qualifiers )) {
			foreach( $qualifiers as $aQualifier ) {
				if ( isset( $aQualifier['argAttribute']) && $aQualifier['argAttribute'] == $argName ) {
					if ( isset( $aQualifier['relationship'] )) {
						$detailArray = $this->detailsForRelationship($aQualifier['relationship']);
						return $detailArray['destination'] . 'DBO';
					}
					break;
				}
			}
		}
		return '';
	}

    public function modelTypeForAttribute($name = '') {
    	if ( $this->isRelationshipKey($name) ) {
    		return "Model::TO_ONE_TYPE";
    	}

    	$details = $this->detailsForAttribute($name);
    	$type = null;
    	if ( is_array($details) ) {
			$type = 'Model::TEXT_TYPE';
			if (isset($details['type']) ) {
				switch ($details['type']) {
					case 'TEXT':
						if ( isset($details['length']) && intval($details['length']) > 256) {
							$type = "Model::TEXTAREA_TYPE";
						}
						break;
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

    public function foreignKeyRelations() {
    	$relations = $this->relationships;
    	if ( is_array($relations) ) {
    		$creationAttr = array();
    		foreach( $relations as $name => $detailArray ) {
				if ( isset($detailArray['destination'], $detailArray['destinationTable']) ) {
					if ($detailArray['isToMany'] == false) {
		    			$creationAttr[$name] = $detailArray;
		    		}
    			}
    		}

    		return $creationAttr;
    	}
    	return null;
    }

    public function dependsOnTables() {
    	$relations = $this->relationships;
    	if ( is_array($relations) ) {
    		$dependsNames = array();
    		foreach( $relations as $name => $detailArray ) {
				if ( isset($detailArray['destination'], $detailArray['destinationTable']) ) {
					if ($detailArray['isToMany'] == false) {
		    			$dependsNames[] = $detailArray['destinationTable'];
					}
				}
    		}

    		return $dependsNames;
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

    public abstract function generate();
}

?>
