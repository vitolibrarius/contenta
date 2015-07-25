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

	use model\Endpoint as Endpoint;
	use model\Endpoint_Type as Endpoint_Type;

$root = TEST_ROOT_PATH . "/" . basename(__FILE__, ".php");
SetConfigRoot( $root, false );

my_echo( );
my_echo( "Creating Database" );
Migrator::Upgrade( Config::GetLog() );

my_echo( "---------- Endpoint ");
$cv_endpoint_type = Model::Named('Endpoint_Type')->endpointTypeForCode(model\Endpoint_Type::ComicVine);
($cv_endpoint_type != false && $cv_endpoint_type->code == 'ComicVine') || die("Could not find Endpoint_Type::ComicVine");

$ep_model = Model::Named('Endpoint');
$points = $ep_model->allForTypeCode(Endpoint_Type::ComicVine);
if ( is_array($points) == false || count($points) == 0) {
	$metadata = metadataFor(Endpoint_Type::ComicVine . ".json");
	if ( $metadata->isMeta( model\Endpoint::api_key ) == false )
	{
		$metadata->setMeta( model\Endpoint::name, "My ComicVine" );
		$metadata->setMeta( model\Endpoint::type_id, $cv_endpoint_type->id );
		$metadata->setMeta( model\Endpoint::base_url, $cv_endpoint_type->api_url );
		$metadata->setMeta( model\Endpoint::api_key, "YOUR API KEY HERE" );
		$metadata->setMeta( model\Endpoint::username, 'vito' );
		$metadata->setMeta( model\Endpoint::enabled, Model::TERTIARY_TRUE );
		$metadata->setMeta( model\Endpoint::compressed, Model::TERTIARY_FALSE );

		die( "Please configure the ComicVine.json config file with your API key" );
	}

	loadData( $ep_model, array($metadata->readMetadata()) );
}

function compareComicVineDetails( $expected, $issue )
{
	$matched = true;
	foreach( $expected as $keypath => $value ) {
		$found = array_valueForKeypath( $keypath, $issue);
		if ( $found != $value ) {
			echo "	** error $value != $found" . PHP_EOL;
			$matched = false;
		}
	}

	if ( $matched ) {
		echo "	all details match" . PHP_EOL;
	}
}

$points = $ep_model->allForTypeCode(Endpoint_Type::ComicVine);
($points != false && count($points) > 0) || die('No endpoint defined');

$epoint = $points[0];
echo "Connecting using " . $epoint . PHP_EOL;
$connection = new ComicVineConnector($epoint);

$series_tests = array(
	array(
		"query" => array(
			"name" => "Injustice: Gods Among Us - Year Two",
			"year" => "2014"
		),
		"expected" => array(
			"name" => "Abby Holland",
			"id" => 21292
		)
	)
);

foreach ( $series_tests as $test ) {
	$expected = array_valueForKeypath( "expected", $test);

	$name = array_valueForKeypath( "query/name", $test);
	$year = array_valueForKeypath( "query/year", $test);
	$xid = array_valueForKeypath( "query/xid", $test);

	echo "Searching for series "
		. $name . " - "
		. $year . " - "
		. $xid . PHP_EOL;
	$results = $connection->series_searchFilteredForYear($name, $year);
	if ( is_array($results) && count($results) == 1 ) {
		echo "	found single match" . PHP_EOL;
		compareComicVineDetails( $expected, array_pop($results) );
	}
	else {
		var_dump($results);
		foreach( $results as $dict ) {
			echo  array_valueForKeypath( "id", $dict) . "\t"
				. array_valueForKeypath( "name", $dict) . "\t"
				. array_valueForKeypath( "start_year", $dict) . "\t"
				. PHP_EOL;
		}
	}
}

die();

$character_tests = array(
	array(
		"query" => array(
			"name" => "Abby Holland",
			"gender" => "female"
		),
		"expected" => array(
			"name" => "Abby Holland",
			"gender" => 2,
			"id" => 21292
		)
	),
	array(
		"query" => array(
			"xid" => 1699
		),
		"expected" => array(
			"name" => "Batman",
			"id" => 1699
		)
	)
);

foreach ( $character_tests as $test ) {
	$expected = array_valueForKeypath( "expected", $test);

	$name = array_valueForKeypath( "query/name", $test);
	$gender = array_valueForKeypath( "query/gender", $test);
	$xid = array_valueForKeypath( "query/xid", $test);

	echo "Searching for characters "
		. $name . " - "
		. $gender . " - "
		. $xid . PHP_EOL;
	$results = $connection->character_search($xid, $name, $gender);
	if ( is_array($results) && count($results) == 1 ) {
		echo "	found single match" . PHP_EOL;
		compareComicVineDetails( $expected, array_pop($results) );
	}
	else {
		foreach( $results as $dict ) {
			echo  array_valueForKeypath( "id", $dict) . "\t"
				. array_valueForKeypath( "name", $dict) . "\t"
				. array_valueForKeypath( "gender", $dict) . "\t"
				. PHP_EOL;
		}
	}
}

$issue_tests = array(
	array(
		"query" => array(
			"volume" => 77223,
			"issue_number" => "6",
			"cover_date" => "2015-05-31",
		),
		"expected" => array(
			"name" => "Who Holds the Hammer? Who is Thor pt 1",
			"id" => 482168
		)
	),
	array(
		"query" => array(
			"volume" => array(68134,77223),
			"cover_date" => array("2015-01-31", "2015-05-31"),
		),
		"expected" => array(
			"name" => "Who Holds the Hammer? Who is Thor pt 1",
			"id" => 482168
		)
	),
	array(
		"query" => array(
			"volume" => array(68134,77223),
			"issue_number" => 6
		),
		"expected" => array(
			"name" => "Who Holds the Hammer? Who is Thor pt 1",
			"id" => 482168
		)
	),
);

foreach ( $issue_tests as $test ) {
	$expected = array_valueForKeypath( "expected", $test);

	$xid = array_valueForKeypath( "query/xid", $test);
	$vol = array_valueForKeypath( "query/volume", $test);
	$name = array_valueForKeypath( "query/name", $test);
	$aliases = array_valueForKeypath( "query/aliases", $test);
	$cover_date = array_valueForKeypath( "query/cover_date", $test);
	$issue_number = array_valueForKeypath( "query/issue_number", $test);

	echo "Searching for issues "
		. $xid . " - "
		. $vol . " - "
		. $name . " - "
		. $aliases . " - "
		. $cover_date . " - "
		. $issue_number . PHP_EOL;

	$results = $connection->issue_search($xid, $vol, $name, $aliases, $cover_date, $issue_number);
	if ( is_array($results) == false || count($results) == 0 ) {
		echo "	NO results" . PHP_EOL;
	}
	else if ( is_array($results) ) {
		if ( count($results) == 0 ) {
			echo "	found single match" . PHP_EOL;
			compareComicVineDetails( $expected, array_pop($results) );
		}
		else {
			foreach( $results as $dict ) {
				echo  array_valueForKeypath( "id", $dict) . "\t"
					. array_valueForKeypath( "issue_number", $dict) . "\t"
					. array_valueForKeypath( "cover_date", $dict) . "\t"
					. array_valueForKeypath( "store_date", $dict) . "\t"
					. array_valueForKeypath( "volume/id", $dict) . "\t"
					. array_valueForKeypath( "volume/name", $dict) . "\t"
					. PHP_EOL;
			}
		}
	}
	else {
		var_dump($results);
	}
}

die();
$issue_tests = array(
	"Thor 006 (2015) (digital) (Minutemen-Midas).cbz" => array(
		"id" => 482168,
		"issue_number" => "6",
		"cover_date" => "2015-05-31",
		"name" => "Who Holds the Hammer? Who is Thor pt 1",
		"volume/name" => "Thor",
		"volume/id" => 77223
	),
	"Convergence - Batman and the Outsiders 002 (2015) (Digital) (ThatGuy-Empire).cbz"  => array(
		"id" => 489237,
		"issue_number" => "2",
		"cover_date" => "2015-07-01",
		"name" => "",
		"volume/name" => "Convergence Batman and the Outsiders",
		"volume/id" => 81465
	),
	"Injustice - Gods Among Us - Year Four 005 (2015) (digital) (Son of Ultron-Empire).cbz" => array(
		"id" => 482168,
		"issue_number" => "6",
		"cover_date" => "2015-05-31",
		"name" => "Who Holds the Hammer? Who is Thor pt 1",
		"volume/name" => "Thor",
		"volume/id" => 77223
	),
);

foreach( $issue_tests as $filename => $expected ) {
	$mediaFilename = new utilities\MediaFilename($filename);
	$meta = $mediaFilename->updateFileMetaData(null);

	echo PHP_EOL;
	echo "Searching for "
		. array_valueForKeypath( "name", $meta) . " - "
		. array_valueForKeypath( "issue", $meta) . " - "
		. array_valueForKeypath( "year", $meta) . PHP_EOL;

	$issues = $connection->searchForIssue(
		array_valueForKeypath( "name", $meta),
		array_valueForKeypath( "issue", $meta),
		array_valueForKeypath( "year", $meta)
	);

	if ( is_array($issues) && count($issues) == 1 ) {
		echo "	found single match" . PHP_EOL;
		compareComicVineDetails( $expected, array_pop($issues) );
	}
	else {
		foreach( $issues as $dict ) {
			echo "\t"
				. array_valueForKeypath( "volume/name", $dict) . "\t"
				. array_valueForKeypath( "volume/id", $dict) . "\t"
				. levenshtein ( array_valueForKeypath( "name", $meta) , array_valueForKeypath( "volume/name", $dict) )
				. PHP_EOL;
		}
	}
}

die();

foreach( $issue_tests as $filename => $expected ) {
	$mediaFilename = new utilities\MediaFilename($filename);
	$meta = $mediaFilename->updateFileMetaData(null);

	echo PHP_EOL;
	echo "Searching for "
		. array_valueForKeypath( "name", $meta) . " - "
		. array_valueForKeypath( "issue", $meta) . " - "
		. array_valueForKeypath( "year", $meta) . PHP_EOL;

	$issues = null;
	$seriesPossible = $connection->search( "volume", array_valueForKeypath( "name", $meta));
	$seriesPossible = $connection->filterSeriesResultForYear( $seriesPossible, array_valueForKeypath( "year", $meta));
	if ( $seriesPossible != false ) {
		$matchVolumeId = array();
		foreach ($seriesPossible as $key => $item) {
			$matchVolId[] = $item['id'];
		}

		$issues = $connection->searchForIssuesMatchingSeriesAndYear(
			$matchVolId,
			array_valueForKeypath( "issue", $meta),
			array_valueForKeypath( "year", $meta)
		);

		if ( count($issues) == 1 ) {
			echo "	found single match" . PHP_EOL;
			compareComicVineDetails( $expected, $issues[0] );
		}
		else {
			echo "	** error found to many issues " . count($issues)  . " searching again " . PHP_EOL;
			$issues = $connection->searchForIssue(
				array_valueForKeypath( "name", $meta),
				array_valueForKeypath( "issue", $meta),
				array_valueForKeypath( "year", $meta)
			);
			if ( is_array($issues) && count($issues) == 1 ) {
				echo "	found single match" . PHP_EOL;
				compareComicVineDetails( $expected, array_pop($issues) );
			}
			else {
// 				reportData($issues, array( "id", "volume/name"));
				foreach( $issues as $dict ) {
					echo array_valueForKeypath( "volume/name", $dict) . "\t"
						. array_valueForKeypath( "volume/id", $dict) . "\t"
						. levenshtein ( array_valueForKeypath( "name", $meta) , array_valueForKeypath( "volume/name", $dict) )
						. PHP_EOL;
				}
				die( "Failed to find issue" . PHP_EOL);
			}
		}
	}
}
