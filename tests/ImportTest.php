<?php

	$system_path = dirname(dirname(__FILE__));
	if (realpath($system_path) !== FALSE)
	{
		$system_path = realpath($system_path).DIRECTORY_SEPARATOR;
	}

	define('SYSTEM_PATH', str_replace("\\", DIRECTORY_SEPARATOR, $system_path));
	define('APPLICATION_PATH', SYSTEM_PATH . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR);

	require SYSTEM_PATH .'application/config/bootstrap.php';
	require SYSTEM_PATH .'application/config/autoload.php';
	require SYSTEM_PATH .'application/config/common.php';
	require SYSTEM_PATH .'application/config/errors.php';
	require SYSTEM_PATH .'application/libs/Config.php';
	require SYSTEM_PATH .'application/libs/Cache.php';

	require SYSTEM_PATH .'tests/_ResetConfig.php';
	require SYSTEM_PATH .'tests/_Data.php';

	use connectors\ComicVineConnector as ComicVineConnector;
	use processor\ComicVineImporter as ComicVineImporter;
	use processor\JobEvaluator as JobEvaluator;

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

$root = TEST_ROOT_PATH . "/" . basename(__FILE__, ".php");
SetConfigRoot( $root, false );

my_echo( );
my_echo( "Creating Database" );
Migrator::Upgrade( Config::GetLog() );

$samples = array();
foreach (glob(SYSTEM_PATH . "/tests/samples/full_export.json") as $file) {
	my_echo( "Importing $file ");
	$import = new db\ImportData( $file );
	$import->importAll();
}

