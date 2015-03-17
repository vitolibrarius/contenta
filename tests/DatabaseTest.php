<?php

	$system_path = dirname(dirname(__FILE__));
	if (realpath($system_path) !== FALSE)
	{
		$system_path = realpath($system_path).'/';
	}

	define('SYSTEM_PATH', str_replace("\\", "/", $system_path));
	define('APPLICATION_PATH', SYSTEM_PATH . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR);

	require SYSTEM_PATH .'application/config/bootstrap.php';
	require SYSTEM_PATH .'application/config/autoload.php';
	require SYSTEM_PATH .'application/config/common.php';
	require SYSTEM_PATH .'application/config/errors.php';
	require SYSTEM_PATH .'application/libs/Config.php';
	require SYSTEM_PATH .'application/libs/Cache.php';

use model\Character as Character;
use model\Character_Alias as Character_Alias;
use model\Endpoint as Endpoint;
use model\Endpoint_Type as Endpoint_Type;
use model\Log as Log;
use model\Log_Level as Log_Level;
use model\Network as Network;
use model\Patch as Patch;
use model\Publication as Publication;
use model\Publication_Character as Publication_Character;
use model\Publisher as Publisher;
use model\Series as Series;
use model\Series_Alias as Series_Alias;
use model\Series_Character as Series_Character;
use model\Story_Arc as Story_Arc;
use model\Story_Arc_Character as Story_Arc_Character;
use model\Story_Arc_Series as Story_Arc_Series;
use model\User_Network as User_Network;
use model\User_Series as User_Series;
use model\Users as Users;
use model\Version as Version;

	function my_echo($string ="") {
		echo $string . PHP_EOL;
	}

	destroy_dir( "/tmp/TestDatabase" ) || die( "Failed to remove last test run /tmp/TestDatabase");
	try {
		$config = Config::instance();

		// override the logger type
		$config->setValue("Repository/path", "/tmp/TestDatabase" );

		$config->setValue("Database/type", "sqlite" );
		$config->setValue("Database/path", "db" );

		$config->setValue("Logging/type", "Print") || die("Failed to change the configured Logging");
		$config->setValue("Logging/path", "logs") || die("Failed to change the configured Logging");

		$config->setValue("Repository/cache", "cache" );
		$config->setValue("Repository/processing", "processing" );

		my_echo( "** Configuration" );
		my_echo( "Repository " . $config->repositoryDirectory() );
		my_echo( "media " . $config->mediaDirectory() );
		my_echo( "cache " . $config->cacheDirectory() );
		my_echo( "processing " . $config->processingDirectory() );
		my_echo( "logs " . $config->loggingDirectory() );
	}
	catch (Exception $e) {
		my_echo( "Error :" . $e );
	}
	function reportData($array, $columns) {
		if ( is_array($array) ) {
			if ( is_array($columns) ) {
				echo "Clazz\t";
				foreach ($columns as $key => $value) {
					echo $value . "\t";
				}
				echo PHP_EOL;
				foreach ($array as $key => $value) {
					echo $value->modelName(). "[" .$value->id."]" . "\t";
					foreach ($columns as $c => $cname) {
						$out = null;
						if ( property_exists($value, $cname)) {
							$out = $value->{$cname};
						}
						else if (method_exists($value, $cname)) {
							$out = $value->{$cname}();
						}
						if ( is_string($out) ) {
							echo $out . "\t";
						}
						else if ( is_a($out, '\DataObject')) {
							echo $out->__toString() . "\t";
						}
						else if ( is_array($out) ) {
							array_walk($out, function(&$val){ echo $val->__toString(); });
//
// 							$small = array_map('__toString', $out);
// 							echo var_export($out, false) . "\t";
						}
						else {
							echo var_export($out, false) . "\t";
						}
					}
					echo PHP_EOL;
				}
			}
			else {
				my_echo( "reportData() Columns are not array" . var_export($columns, true) );
			}
		}
		else {
			my_echo( "reportData() data array is not array " .var_export($array, true) );
		}
		echo PHP_EOL;
	}

	function loadData( Model $model = null, array $data = array(), array $columns = null )
	{
		$loaded = array();
		foreach($data as $record) {
			$newObjId = $model->createObject($record);

			( $newObjId != false ) || die('Failed to insert ' . $record );
			( is_array($newObjId) == false ) || die('Failed to insert ' . var_export( $record, true ) . PHP_EOL
				. 'Validation errors ' . var_export( $newObjId, true ). PHP_EOL);

			$obj = $model->objectForId($newObjId);
			( is_a("DataObject", $obj) == false ) ||
				die('Wrong class from insert ' . var_export( $record, true ) . PHP_EOL . var_export( $obj, true ) . PHP_EOL);
			$loaded[] = $obj;
		}
		reportData($loaded,  (is_null($columns) ? array_keys($data[0]) : $columns) );
		return $loaded;
	}


my_echo( );
my_echo( "Creating Database" );
Migrator::Upgrade("/tmp/TestDatabase/logs");

// load the default user
$user = Model::Named("Users")->userByName('vito');
($user != false && $user->name == 'vito') || dir("Could not find 'vito' user");

my_echo( "---------- Version ");
$versions = Model::Named('Version')->allObjects();
reportData($versions,  array( "code", "hash_code" ));
reportData($versions[0]->patches(),  array( "displayName"));

my_echo( "---------- Endpoint ");
$endpoint_type = Model::Named('Endpoint_Type')->endpointTypeForCode(model\Endpoint_Type::ComicVine);
($endpoint_type != false && $endpoint_type->code == 'ComicVine') || dir("Could not find Endpoint_Type::ComicVine");

$endpoint_model = Model::Named("Endpoint");
$endpoint_data = array(
	array(
		model\Endpoint::name => "My ComicVine",
		model\Endpoint::type_id => $endpoint_type->id,
		model\Endpoint::base_url => $endpoint_type->api_url,
		model\Endpoint::api_key => uuid(),
		model\Endpoint::username => 'vito',
		model\Endpoint::enabled => Model::TERTIARY_TRUE,
		model\Endpoint::compressed => Model::TERTIARY_FALSE
	)
);
$endpoints = loadData( $endpoint_model, $endpoint_data );

my_echo( "---------- Publisher ");
$publisher_model = Model::Named("Publisher");
$publisher_data = array(
	array(
		model\Publisher::name => "DC Comics",
		'xurl' => "http:\/\/www.comicvine.com\/",
		'xsource' => Endpoint_Type::ComicVine,
		'xid' => 433786,
		'xupdated' => time()
	),
	array(
		model\Publisher::name => "Archie Comics"
	),
	array(
		model\Publisher::name => "Marvel"
	)
);
$publishers = loadData( $publisher_model, $publisher_data );

my_echo( "---------- Character ");
$Character_model = Model::Named("Character");
$Character_data = array(
	array(
		model\Character::name => "Batman",
		model\Character::realname => "Batman",
		model\Character::desc => "The dark knight",
		model\Character::gender => "Male",
		model\Character::publisher_id => $publishers[0]->id,
		'xurl' => "http:\/\/www.comicvine.com\/",
		'xsource' => Endpoint_Type::ComicVine,
		'xid' => 433786,
		'xupdated' => time()
	),
	array(
		model\Character::name => "Robin",
		model\Character::realname => "Robin",
		model\Character::desc => "the boy blunder",
		model\Character::gender => "Male",
		model\Character::publisher_id => $publishers[0]->id
	),
	array(
		model\Character::name => "Spiderman",
		model\Character::realname => "Spider-man",
		model\Character::desc => "Wall crawler",
		model\Character::gender => "Male",
		model\Character::publisher_id => $publishers[2]->id
	)
);
$Characters = loadData( $Character_model, $Character_data, array("name", "realname", "desc", "gender", "publisher", "series") );

$batman = $Characters[0];
$robin = $Characters[1];
$batman->addAlias("Bruce Wayne");
$batman->addAlias("Bruce");
$batman->addAlias("Bat-Man");
reportData($batman->aliases(),  array( "name", "character" ));

my_echo( "---------- Series ");
$Series_model = Model::Named("Series");
$Series_data = array(
	array(
		model\Series::name => "Batman",
		model\Series::start_year => 2012,
		model\Series::desc => "The dark knight comic series",
		model\Series::publisher_id => $publishers[0]->id,
		'xurl' => "http:\/\/www.comicvine.com\/",
		'xsource' => Endpoint_Type::ComicVine,
		'xid' => 433786,
		'xupdated' => time()
	),
	array(
		model\Series::name => "Spiderman",
		model\Series::start_year => 2010,
		model\Series::desc => "Wall crawler comic series",
		model\Series::publisher_id => $publishers[2]->id
	),
	array(
		model\Series::name => "Nightwing",
		model\Series::start_year => 2011,
		model\Series::desc => "The sidekick comic series",
		model\Series::publisher_id => $publishers[0]->id
	)
);
$Series = loadData( $Series_model, $Series_data, array("name", "start_year", "desc", "publisher") );

$tdk = $Series[0];
$tdk->addAlias("The Dark Knight");
$tdk->addAlias("TDK");
$tdk->addAlias("Batman and Robin");
reportData($tdk->aliases(),  array( "name", "series" ));

$tdk->joinToCharacter($batman);
$tdk->joinToCharacter($robin);

$nightwing = $Series[2];
$nightwing->joinToCharacter($batman);
$nightwing->joinToCharacter($robin);

reportData($tdk->characters(),  array("name", "realname", "desc", "gender", "publisher", "series") );

$user->addSeries($tdk);
$user->addSeries($nightwing);
reportData(array($user),  array("name", "seriesBeingRead") );

my_echo( "---------- Story Arcs ");
$Story_Arc_model = Model::Named("Story_Arc");
$Story_Arc_data = array(
	array(
		model\Story_Arc::name => "Crisis",
		model\Story_Arc::desc => "It's a Crisis Story_Arc",
		model\Story_Arc::publisher_id => $publishers[0]->id,
		'xurl' => "http:\/\/www.comicvine.com\/",
		'xsource' => Endpoint_Type::ComicVine,
		'xid' => 433786,
		'xupdated' => time()
	),
	array(
		model\Story_Arc::name => "Of Gods and Men",
		model\Story_Arc::desc => "It's a god Story_Arc",
		model\Story_Arc::publisher_id => $publishers[2]->id
	),
	array(
		model\Story_Arc::name => "Storm Warning",
		model\Story_Arc::desc => "It's a Storm Warning Story_Arc",
		model\Story_Arc::publisher_id => $publishers[0]->id
	)
);
$Story_Arcs = loadData( $Story_Arc_model, $Story_Arc_data, array("name", "desc", "publisher") );

$crisis = $Story_Arcs[0];
$crisis->joinToCharacter($batman);
$crisis->joinToCharacter($robin);
$tdk->joinToStory_Arc($crisis);
$nightwing->joinToStory_Arc($crisis);

$storm = $Story_Arcs[2];
$storm->joinToCharacter($batman);
$storm->joinToCharacter($robin);
$tdk->joinToStory_Arc($storm);
$nightwing->joinToStory_Arc($storm);

my_echo( "Characters attached to " . $crisis );
reportData($crisis->characters(),  array("name", "realname", "desc", "gender", "story_arcs") );

my_echo( "Story Arcs attached to " . $nightwing );
reportData($nightwing->story_arcs(),  array("name", "desc", "publisher", "characters") );


my_echo( "---------- Publications ");
$Publication_model = Model::Named("Publication");
/*
	const id =			'id';
	const series_id =	'series_id';
	const name =		'name';
	const desc =		'desc';
	const pub_date =	'pub_date';
	const created =		'created';
	const issue_num =	'issue_num';
	const xurl =		'xurl';
	const xsource =		'xsource';
	const xid =			;
	const xupdated =	'xupdated';
*/
$Publication_data = array(
	array(
		model\Publication::name => "The Big Burn: Sparks",
		model\Publication::desc => "<p style=\"\"><em>As Two-Face continues his rampage through Gotham City, more light is shed on his past. Who is Carrie Kelley and how can her mysterious connection to Harvey Dent help Batman?<\/em><\/p>",
		model\Publication::issue_num => 25,
		model\Publication::pub_date => strtotime('2014-01-01'),
		model\Publication::series_id => $tdk->id,
		'xurl' => "http:\/\/www.comicvine.com\/",
		'xsource' => Endpoint_Type::ComicVine,
		'xid' => 433786,
		'xupdated' => time()
	),
	array(
		model\Publication::name => "Bad Blood",
		model\Publication::desc => "Long description",
		model\Publication::issue_num => 2,
		model\Publication::pub_date => strtotime('2014-01-01'),
		model\Publication::series_id => $nightwing->id
	),
	array(
		model\Publication::name => "Knightmoves",
		model\Publication::desc => "<p style=\"\"><em>As Knightmoves-Knightmoves Dent help Batman?<\/em><\/p>",
		model\Publication::issue_num => 22,
		model\Publication::pub_date => strtotime('2014-01-01'),
		model\Publication::series_id => $tdk->id
	)
);
$Publications = loadData( $Publication_model, $Publication_data, array("name", "desc", "issue_num", "series") );

$burn = $Publications[0];
$burn->joinToCharacter($batman);
$burn->joinToCharacter($robin);

$kmoves = $Publications[2];
$kmoves->joinToCharacter($batman);
$robin->joinToPublication($kmoves);

my_echo( "Characters attached to " . $burn );
reportData($burn->characters(),  array("name", "realname", "desc", "gender", "Publications") );

$crisis->joinToPublication($burn);
$kmoves->joinToStory_Arc($crisis);

my_echo( "Story Arcs attached to " . $kmoves );
reportData($kmoves->story_arcs(),  array("name", "desc", "publisher", "publications") );

my_echo( );
my_echo( );
