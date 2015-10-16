<?php

namespace db;

use \Database as Database;
use \Localized as Localized;
use \Logger as Logger;
use \Model as Model;
use \Config as Config;
use \Metadata as Metadata;

require_once APPLICATION_PATH . DIRECTORY_SEPARATOR . 'model/Character.php';
require_once APPLICATION_PATH . DIRECTORY_SEPARATOR . 'model/Character_Alias.php';
require_once APPLICATION_PATH . DIRECTORY_SEPARATOR . 'model/Endpoint.php';
require_once APPLICATION_PATH . DIRECTORY_SEPARATOR . 'model/Endpoint_Type.php';
require_once APPLICATION_PATH . DIRECTORY_SEPARATOR . 'model/Job.php';
require_once APPLICATION_PATH . DIRECTORY_SEPARATOR . 'model/Job_Running.php';
require_once APPLICATION_PATH . DIRECTORY_SEPARATOR . 'model/Job_Type.php';
require_once APPLICATION_PATH . DIRECTORY_SEPARATOR . 'model/Log.php';
require_once APPLICATION_PATH . DIRECTORY_SEPARATOR . 'model/Log_Level.php';
require_once APPLICATION_PATH . DIRECTORY_SEPARATOR . 'model/Media.php';
require_once APPLICATION_PATH . DIRECTORY_SEPARATOR . 'model/Media_Type.php';
require_once APPLICATION_PATH . DIRECTORY_SEPARATOR . 'model/Network.php';
require_once APPLICATION_PATH . DIRECTORY_SEPARATOR . 'model/Patch.php';
require_once APPLICATION_PATH . DIRECTORY_SEPARATOR . 'model/Publication.php';
require_once APPLICATION_PATH . DIRECTORY_SEPARATOR . 'model/Publication_Character.php';
require_once APPLICATION_PATH . DIRECTORY_SEPARATOR . 'model/Publisher.php';
require_once APPLICATION_PATH . DIRECTORY_SEPARATOR . 'model/Series.php';
require_once APPLICATION_PATH . DIRECTORY_SEPARATOR . 'model/Series_Alias.php';
require_once APPLICATION_PATH . DIRECTORY_SEPARATOR . 'model/Series_Character.php';
require_once APPLICATION_PATH . DIRECTORY_SEPARATOR . 'model/Story_Arc.php';
require_once APPLICATION_PATH . DIRECTORY_SEPARATOR . 'model/Story_Arc_Character.php';
require_once APPLICATION_PATH . DIRECTORY_SEPARATOR . 'model/Story_Arc_Publication.php';
require_once APPLICATION_PATH . DIRECTORY_SEPARATOR . 'model/Story_Arc_Series.php';
require_once APPLICATION_PATH . DIRECTORY_SEPARATOR . 'model/User_Network.php';
require_once APPLICATION_PATH . DIRECTORY_SEPARATOR . 'model/User_Series.php';
require_once APPLICATION_PATH . DIRECTORY_SEPARATOR . 'model/Users.php';
require_once APPLICATION_PATH . DIRECTORY_SEPARATOR . 'model/Version.php';

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
			$this->export( new $modelName() );
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
