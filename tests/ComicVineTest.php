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

	use \model\network\Endpoint as Endpoint;
	use \model\network\Endpoint_Type as Endpoint_Type;

$root = TEST_ROOT_PATH . "/" . basename(__FILE__, ".php");
SetConfigRoot( $root, false );

my_echo( );
my_echo( "Creating Database" );
Migrator::Upgrade( Config::GetLog() );

my_echo( "---------- Endpoint ");
$cv_endpoint_type = Model::Named('Endpoint_Type')->objectForCode(\model\network\Endpoint_Type::ComicVine);
($cv_endpoint_type != false && $cv_endpoint_type->code == 'ComicVine') || die("Could not find Endpoint_Type::ComicVine");

$ep_model = Model::Named('Endpoint');
$points = $ep_model->allForTypeCode(Endpoint_Type::ComicVine);
if ( is_array($points) == false || count($points) == 0) {
	$metadata = metadataFor(Endpoint_Type::ComicVine . ".json");
	if ( $metadata->isMeta( \model\network\Endpoint::api_key ) == false )
	{
		$metadata->setMeta( \model\network\Endpoint::name, "My ComicVine" );
		$metadata->setMeta( \model\network\Endpoint::type_code, $cv_endpoint_type->code );
		$metadata->setMeta( \model\network\Endpoint::base_url, $cv_endpoint_type->api_url );
		$metadata->setMeta( \model\network\Endpoint::api_key, "YOUR API KEY HERE" );
		$metadata->setMeta( \model\network\Endpoint::username, 'vito' );
		$metadata->setMeta( \model\network\Endpoint::enabled, Model::TERTIARY_TRUE );
		$metadata->setMeta( \model\network\Endpoint::compressed, Model::TERTIARY_FALSE );

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


function downloadImages($type = '', $cvData ) {
	$imagepaths = array(
		"icon_url",
		"medium_url",
		"screen_url",
		"small_url",
		"super_url",
		"thumb_url",
		"tiny_url"
	);
	foreach( $imagepaths as $imgkey ) {
		$name = $type . "_" . array_valueForKeypath( "name", $cvData ) . "_" . $imgkey;
		$url = array_valueForKeypath( appendPath("image", $imgkey), $cvData );
		if ( is_null($url) ) {
			echo PHP_EOL . "no url for " . $imgkey;
		}
		else {
			$filename = downloadImage($url, Config::GetMedia(), $name );
			echo PHP_EOL . $imgkey . "\t" . $filename;
		}
	}
}

$points = $ep_model->allForTypeCode(Endpoint_Type::ComicVine);
($points != false && count($points) > 0) || die('No endpoint defined');

$epoint = $points[0];
echo "Connecting using " . $epoint . PHP_EOL;
$connection = new ComicVineConnector($epoint);
$connection->setDebuggingResponses(true);

$issue_tests = array(
	"Iron Man 20 (2014) (digital) (Minutemen-Midas).cbz" => array(
		"id" => 442923,
		"issue_number" => "20.INH",
		"cover_date" => "2014-03-01",
		"name" => null,
		"volume/name" => "Iron Man",
		"volume/id" => 53725
	),
	"Thor 006 (2015) (digital) (Minutemen-Midas).cbz" => array(
		"id" => 482168,
		"issue_number" => "6",
		"cover_date" => "2015-05-31",
		"name" => "Who Holds the Hammer? Who is Thor pt 1",
		"volume/name" => "Thor",
		"volume/id" => 77223
	),
	"FF 010 (2011) (Digital) (Zone-Empire).cbz" => array(
		"id" => 294897,
		"issue_number" => "10",
		"cover_date" => "2011-12-01",
		"name" => "What I Need",
		"volume/name" => "FF",
		"volume/id" => 39453
	),
	"Convergence - Batman and the Outsiders 002 (2015) (Digital) (ThatGuy-Empire).cbz"  => array(
		"id" => 489237,
		"issue_number" => "2",
		"cover_date" => "2015-07-01",
		"name" => "",
		"volume/name" => "Convergence Batman and the Outsiders",
		"volume/id" => 81465
	),
	"Marvel Zombies 001 (2015) (Digital) (Mephisto-Empire).cbz" => array(
		"id" => 491519,
		"issue_number" => "1",
		"cover_date" => "2015-08-31",
		"name" => "Journey Into Misery: Episode 1",
		"volume/name" => "Marvel Zombies",
		"volume/id" => 82505
	),
	"Convergence - Action Comics 01 (of 02) (2015) (Digital-Empire)" => array(
		"id" => 487164,
		"issue_number" => "1",
		"cover_date" => "2015-06-01",
		"name" => "",
		"volume/name" => "Convergence Action Comics",
		"volume/id" => 81577
	),
	"Tarot Witch Of The Black Rose 071(2011)(2 covers)(Digital)(Tyrant Lizard King-EMPIRE).cbz" => array(
		"id" => 305156,
		"issue_number" => "71",
		"cover_date" => "2011-11-18",
		"name" => "Temple of the Fallen Mermaids, Part Two",
		"volume/name" => "Tarot: Witch of the Black Rose",
		"volume/id" => 19691
	),
	"George R.R. Martin's A Game Of Thrones 017 (c2c) (2013).cbz" => array(
		"id" => 431452,
		"issue_number" => "17",
		"cover_date" => "2013-10-01",
		"name" => "",
		"volume/name" => "George R.R. Martin's A Game of Thrones",
		"volume/id" => 42976
	),
	"Green Lantern - Lost Army 002 (2015) (Digital-Empire).cbz" => array(
		"id" => 495248,
		"issue_number" => "2",
		"cover_date" => "2015-09-30",
		"name" => "Part 2",
		"volume/name" => "Green Lantern: The Lost Army",
		"volume/id" => 82860
	),
	"Zero 018 (2015) (Digital-Empire).cbz" => array(
		"id" => 493840,
		"issue_number" => "18",
		"cover_date" => "2015-07-31",
		"name" => "Chapter 18: Surrender",
		"volume/name" => "Zero",
		"volume/id" => 67426
	),
	"Planetary - 18 - 2004.cbz" => array(
		"id" => 54545,
		"issue_number" => "18",
		"cover_date" => "2004-02-24",
		"name" => "The Gun Club",
		"volume/name" => "Planetary",
		"volume/id" => 7506
	),
);

foreach( $issue_tests as $filename => $expected ) {
	$mediaFilename = new utilities\MediaFilename($filename);
	$meta = $mediaFilename->updateFileMetaData(null);

	echo "Searching for "
		. array_valueForKeypath( "name", $meta) . " - "
		. array_valueForKeypath( "issue", $meta) . " - "
		. array_valueForKeypath( "year", $meta);

	$issues = $connection->issue_searchFilteredForSeriesYear(
		array_valueForKeypath( "issue", $meta),
		array_valueForKeypath( "name", $meta),
		array_valueForKeypath( "year", $meta)
	);

	if ( is_array($issues) ) {
		switch( count($issues) ) {
			case 0:
				echo "	NO match" . PHP_EOL;
				break;
			case 1:
				$issue = array_pop($issues);
				compareComicVineDetails( $expected, $issue );
				$series = $connection->seriesDetails( array_valueForKeypath( "volume/id", $issue ));
				if ( $series != false ) {
					downloadImages( "series", $series );
				}

				$pub = $connection->issueDetails( array_valueForKeypath( "id", $issue ));
				$artists = array_valueForKeypath( "person_credits", $pub );
				if ( is_array($artists) ) {
					foreach ($artists as $artist ) {
						$role = array_valueForKeypath( "role", $artist );
						if ( in_array($role, array( "writer", "artist" ))) {
							echo PHP_EOL . array_valueForKeypath( "name", $artist );
							$cv_artist = $connection->personDetails(array_valueForKeypath( "id", $artist ));
							if ( $cv_artist != false ) {
								downloadImages( "artist", $cv_artist );
							}
						}
					}
				}
				break;
			default:
				foreach( $issues as $dict ) {
					echo  PHP_EOL . "\t"
						. array_valueForKeypath( "issue_number", $dict) . "\t"
						. array_valueForKeypath( "volume/name", $dict) . "\t"
						. array_valueForKeypath( "cover_date", $dict) . "\t"
						. levenshtein ( array_valueForKeypath( "name", $meta) , array_valueForKeypath( "volume/name", $dict) )
						. PHP_EOL;
				}
				break;
		}
	}
	else {
		echo "	NO match " . var_export($issues, true) . PHP_EOL;
	}

//  	die();
}
