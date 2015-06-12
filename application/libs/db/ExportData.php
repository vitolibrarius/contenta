<?php

namespace db;

use \Database as Database;
use \Localized as Localized;
use \Logger as Logger;
use \Model as Model;
use \Config as Config;

use utilities\Metadata as Metadata;

class ExportData
{
	public function __construct($filename)
	{
		isset($filename) || die('Export filename is missing.');
		$this->metadata = new Metadata( $filename );
	}

	public function exportAll()
	{
		$models = array();
		foreach (get_declared_classes() as $class) {
			if (is_subclass_of($class, "\Model")) {
				$models[] = $class;
			}
		}

		foreach ( $models as $modelName ) {
			$this->export( new $modelName(Database::instance()) );
		}
	}

	public function export( Model $model )
	{
		if ( $model != null ) {
			$db = Database::instance();
			$sql = "SELECT * FROM " . $model->tableName();
			$statement = $db->prepare($sql);
			if ($statement && $statement->execute()) {
				$results = $statement->fetchAll();
				if ( is_array($results) && count($results) > 0 ) {
					$this->metadata->setMeta( $model->tableName(), $results );
				}
			}
		}
	}
}
