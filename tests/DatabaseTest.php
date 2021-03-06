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

use \model\media\Character as Character;
use \model\media\Character_Alias as Character_Alias;
use \model\media\Character_AliasDBO as Character_AliasDBO;
use \model\network\Endpoint as Endpoint;
use \model\network\Endpoint_Type as Endpoint_Type;
use \model\media\logs\Log as Log;
use \model\media\logs\Log_Level as Log_Level;
use \model\media\Network as Network;
use \model\version\Patch as Patch;
use \model\media\Publication as Publication;
use \model\media\Publication_Character as Publication_Character;
use \model\media\Publisher as Publisher;
use \model\media\Series as Series;
use \model\media\Series_Alias as Series_Alias;
use \model\media\Series_Character as Series_Character;
use \model\media\Story_Arc as Story_Arc;
use \model\media\Story_Arc_Character as Story_Arc_Character;
use \model\media\Story_Arc_Series as Story_Arc_Series;
use \model\media\User_Network as User_Network;
use \model\user\Users as Users;
use \model\version\Version as Version;
use \model\jobs\Job_Type as Job_Type;
use \model\jobs\Job_Running as Job_Running;
use \model\jobs\Job as Job;

$root = TEST_ROOT_PATH . "/" . basename(__FILE__, ".php");
SetConfigRoot( $root );

	require SYSTEM_PATH .'application/libs/db/ExportData.php';

use \db\ExportData_sqlite as ExportData_sqlite;

my_echo( );
my_echo( "Creating Database" );
Migrator::Upgrade( Config::GetLog() );

// load the default user
$user = Model::Named("Users")->objectForName('vito');
($user != false && $user->name == 'vito') || die("Could not find 'vito' user");

my_echo( "---------- Version ");
// $versions = Model::Named('Version')->allObjects();
$patches = Model::Named('Patch')->allObjects();
reportData($patches,  array( "displayName", "version/code", "formattedDateTime_created" ));

my_echo( "---------- Endpoint ");
$cv_endpoint_type = Model::Named('Endpoint_Type')->objectForCode(\model\network\Endpoint_Type::ComicVine);
($cv_endpoint_type != false && $cv_endpoint_type->code == 'ComicVine') || die("Could not find Endpoint_Type::ComicVine");

$rss_endpoint_type = Model::Named('Endpoint_Type')->objectForCode(\model\network\Endpoint_Type::RSS);
($rss_endpoint_type != false && $rss_endpoint_type->code == 'RSS') || die("Could not find Endpoint_Type::RSS");

$endpoint_model = Model::Named("Endpoint");
$endpoint_data = array(
	array(
		\model\network\Endpoint::name => "My ComicVine",
		\model\network\Endpoint::type_code => $cv_endpoint_type->code,
		\model\network\Endpoint::base_url => $cv_endpoint_type->api_url,
		\model\network\Endpoint::api_key => uuid(),
		\model\network\Endpoint::username => 'vito',
		\model\network\Endpoint::enabled => Model::TERTIARY_TRUE,
		\model\network\Endpoint::compressed => Model::TERTIARY_FALSE
	),
	array(
		\model\network\Endpoint::name => "Comicbook RSS",
		\model\network\Endpoint::type_code => $rss_endpoint_type->code,
		\model\network\Endpoint::base_url => "http://comicbook.source.com/rss?api=12345",
		\model\network\Endpoint::username => 'vito',
		\model\network\Endpoint::enabled => Model::TERTIARY_TRUE,
		\model\network\Endpoint::compressed => Model::TERTIARY_TRUE
	)

);
$endpoints = loadData( $endpoint_model, $endpoint_data );
$comicvine_endpoint = $endpoints[0];
$rss_endpoint = $endpoints[1];

my_echo( "---------- Jobs ");
$job_types = Model::Named('Job_Type')->allObjects();
reportData($job_types,  Model::Named('Job_Type')->allColumnNames());

$character_job_type = Model::Named('Job_Type')->objectForCode('character');
($character_job_type != false && $character_job_type->code == 'character') || die("Could not find 'character' job_type");

$rss_job_type = Model::Named('Job_Type')->objectForCode('rss');
($rss_job_type != false && $rss_job_type->code == 'rss') || die("Could not find 'rss' job_type");

$job_model = Model::Named("Job");
$job_data = array(
	array(
		\model\jobs\Job::type_code => $character_job_type->code,
		\model\jobs\Job::minute => 1,
		\model\jobs\Job::hour => 1,
		\model\jobs\Job::dayOfWeek => 1,
		\model\jobs\Job::parameter => '27add4363e9b138cd375b258db481b8f',
		\model\jobs\Job::next => null,
		\model\jobs\Job::one_shot => Model::TERTIARY_TRUE,
		\model\jobs\Job::enabled => Model::TERTIARY_TRUE
	),
	array(
		\model\jobs\Job::type_code => $rss_job_type->code,
		\model\jobs\Job::minute => "10",
		\model\jobs\Job::hour => "2,4,6,8",
		\model\jobs\Job::dayOfWeek => "*",
		\model\jobs\Job::parameter => '',
		\model\jobs\Job::endpoint_id => $rss_endpoint->id,
		\model\jobs\Job::next => null,
		\model\jobs\Job::one_shot => Model::TERTIARY_FALSE,
		\model\jobs\Job::enabled => Model::TERTIARY_TRUE
	),
	array(
		\model\jobs\Job::type_code => $rss_job_type->code,
		\model\jobs\Job::minute => "10",
		\model\jobs\Job::hour => "2,4,6,8",
		\model\jobs\Job::dayOfWeek => "*",
		\model\jobs\Job::parameter => '',
		\model\jobs\Job::endpoint_id => $rss_endpoint->id,
		\model\jobs\Job::next => null,
		\model\jobs\Job::one_shot => Model::TERTIARY_FALSE,
		\model\jobs\Job::enabled => Model::TERTIARY_FALSE
	)
);
$jobs = loadData( $job_model, $job_data, array("jobType", "endpoint", "minute", "hour", "dayOfWeek", "parameter", "next", "one_shot", "enabled") );
$character_job = $jobs[0];
$rss_job = $jobs[2];

$job_run_model = Model::Named("Job_Running");
$job_run_data = array(
	array(
		\model\jobs\Job_Running::job_id => $character_job->id,
		\model\jobs\Job_Running::type_code => $character_job_type->code,
		\model\jobs\Job_Running::processor => 'UploadImport',
		\model\jobs\Job_Running::guid => rand(),
		\model\jobs\Job_Running::pid => 3456
	),
	array(
		\model\jobs\Job_Running::job_id => $rss_job->id,
		\model\jobs\Job_Running::type_code => $rss_job_type->code,
		\model\jobs\Job_Running::processor => 'UploadImport',
		\model\jobs\Job_Running::guid => rand(),
		\model\jobs\Job_Running::pid => 98765
	)
);
$jobs_running = loadData( $job_run_model, $job_run_data, array("job", "jobType/name", "pid") );

my_echo( "---------- Publisher ");
$publisher_model = Model::Named("Publisher");
$publisher_data = array(
	array(
		\model\media\Publisher::name => "DC Comics",
		'xurl' => "http://comicvine.gamespot.com/",
		'xsource' => Endpoint_Type::ComicVine,
		'xid' => 433786,
		'xupdated' => time()
	),
	array(
		\model\media\Publisher::name => "Archie Comics"
	),
	array(
		\model\media\Publisher::name => "Marvel"
	)
);
$publishers = loadData( $publisher_model, $publisher_data );

my_echo( "---------- Character ");
$Character_model = Model::Named("Character");
$Character_data = array(
	array(
		\model\media\Character::name => "Batman",
		\model\media\Character::realname => "Batman",
		\model\media\Character::desc => "The dark knight",
		\model\media\Character::gender => "Male",
		\model\media\Character::publisher_id => $publishers[0]->id,
		'xurl' => "http://comicvine.gamespot.com/",
		'xsource' => Endpoint_Type::ComicVine,
		'xid' => 433786,
		'xupdated' => time()
	),
	array(
		\model\media\Character::name => "Robin",
		\model\media\Character::realname => "Robin",
		\model\media\Character::desc => "the boy blunder",
		\model\media\Character::gender => "Male",
		\model\media\Character::publisher_id => $publishers[0]->id
	),
	array(
		\model\media\Character::name => "Spiderman",
		\model\media\Character::realname => "Spider-man",
		\model\media\Character::desc => "Wall crawler",
		\model\media\Character::gender => "Male",
		\model\media\Character::publisher_id => $publishers[2]->id
	)
);
$Characters = loadData( $Character_model, $Character_data, array("name", "realname", "desc", "gender", "publisher", "series") );

$batman_character = $Characters[0];
$robin_character = $Characters[1];

$alias = $batman_character->addAlias("Bruce Wayne");
$alias instanceof Character_AliasDBO || die( "failed to create alias " . var_export($alias, true) );

$alias = $batman_character->addAlias("Bruce");
$alias instanceof Character_AliasDBO || die( "failed to create alias " . var_export($alias, true) );

$alias = $batman_character->addAlias("Bat-Man");
$alias instanceof Character_AliasDBO || die( "failed to create alias " . var_export($alias, true) );

reportData($batman_character->aliases(),  array( "name", "character" ));

my_echo( "---------- Series ");
$Series_model = Model::Named("Series");
$Series_data = array(
	array(
		\model\media\Series::name => "Batman",
		\model\media\Series::start_year => 2012,
		\model\media\Series::desc => "The dark knight comic series",
		\model\media\Series::publisher_id => $publishers[0]->id,
		'xurl' => "http://comicvine.gamespot.com/",
		'xsource' => Endpoint_Type::ComicVine,
		'xid' => 433786,
		'xupdated' => time()
	),
	array(
		\model\media\Series::name => "Spiderman",
		\model\media\Series::start_year => 2010,
		\model\media\Series::desc => "Wall crawler comic series",
		\model\media\Series::publisher_id => $publishers[2]->id
	),
	array(
		\model\media\Series::name => "Nightwing",
		\model\media\Series::start_year => 2011,
		\model\media\Series::desc => "The sidekick comic series",
		\model\media\Series::publisher_id => $publishers[0]->id
	)
);
$Series = loadData( $Series_model, $Series_data, array("name", "start_year", "desc", "publisher") );

$tdk_series = $Series[0];
$tdk_series->addAlias("The Dark Knight");
$tdk_series->addAlias("TDK");
$tdk_series->addAlias("Batman and Robin");
reportData($tdk_series->aliases(),  array( "name", "series" ));

$tdk_series->joinToCharacter($batman_character);
$tdk_series->joinToCharacter($robin_character);

$nightwing_series = $Series[2];
$nightwing_series->joinToCharacter($batman_character);
$nightwing_series->joinToCharacter($robin_character);

reportData($tdk_series->characters(),  array("name", "realname", "desc", "gender", "publisher", "series") );

$user->addSeries($tdk_series);
$user->addSeries($nightwing_series);
reportData(array($user),  array("name", "seriesBeingRead") );

my_echo( "---------- Story Arcs ");
$Story_Arc_model = Model::Named("Story_Arc");
$Story_Arc_data = array(
	array(
		\model\media\Story_Arc::name => "Crisis",
		\model\media\Story_Arc::desc => "It's a Crisis Story_Arc",
		\model\media\Story_Arc::publisher_id => $publishers[0]->id,
		'xurl' => "http://comicvine.gamespot.com/",
		'xsource' => Endpoint_Type::ComicVine,
		'xid' => 433786,
		'xupdated' => time()
	),
	array(
		\model\media\Story_Arc::name => "Of Gods and Men",
		\model\media\Story_Arc::desc => "It's a god Story_Arc",
		\model\media\Story_Arc::publisher_id => $publishers[2]->id
	),
	array(
		\model\media\Story_Arc::name => "Storm Warning",
		\model\media\Story_Arc::desc => "It's a Storm Warning Story_Arc",
		\model\media\Story_Arc::publisher_id => $publishers[0]->id
	)
);
$Story_Arcs = loadData( $Story_Arc_model, $Story_Arc_data, array("name", "desc", "publisher") );

$crisis_story_arc = $Story_Arcs[0];
$crisis_story_arc->joinToCharacter($batman_character);
$crisis_story_arc->joinToCharacter($robin_character);
$tdk_series->joinToStory_Arc($crisis_story_arc);
$nightwing_series->joinToStory_Arc($crisis_story_arc);

$storm_story_arc = $Story_Arcs[2];
$storm_story_arc->joinToCharacter($batman_character);
$storm_story_arc->joinToCharacter($robin_character);
$tdk_series->joinToStory_Arc($storm_story_arc);
$nightwing_series->joinToStory_Arc($storm_story_arc);

my_echo( "Characters attached to " . $crisis_story_arc );
reportData($crisis_story_arc->characters(),  array("name", "realname", "desc", "gender", "story_arcs") );

my_echo( "Story Arcs attached to " . $nightwing_series );
reportData($nightwing_series->story_arcs(),  array("name", "desc", "publisher", "characters") );


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
		\model\media\Publication::name => "The Big Burn: Sparks",
		\model\media\Publication::desc => "<p style=\"\"><em>As Two-Face continues his rampage through Gotham City, more light is shed on his past. Who is Carrie Kelley and how can her mysterious connection to Harvey Dent help Batman?<\/em><\/p>",
		\model\media\Publication::issue_num => 25,
		\model\media\Publication::pub_date => strtotime('2014-01-01'),
		\model\media\Publication::series_id => $tdk_series->id,
		'xurl' => "http://comicvine.gamespot.com/",
		'xsource' => Endpoint_Type::ComicVine,
		'xid' => 433786,
		'xupdated' => time()
	),
	array(
		\model\media\Publication::name => "Bad Blood",
		\model\media\Publication::desc => "Long description",
		\model\media\Publication::issue_num => 2,
		\model\media\Publication::pub_date => strtotime('2014-01-01'),
		\model\media\Publication::series_id => $nightwing_series->id
	),
	array(
		\model\media\Publication::name => "Knightmoves",
		\model\media\Publication::desc => "<p style=\"\"><em>As Knightmoves-Knightmoves Dent help Batman?<\/em><\/p>",
		\model\media\Publication::issue_num => 22,
		\model\media\Publication::pub_date => strtotime('2014-01-01'),
		\model\media\Publication::series_id => $tdk_series->id
	)
);
$Publications = loadData( $Publication_model, $Publication_data, array("name", "desc", "issue_num", "series") );

$burn_publication = $Publications[0];
$burn_publication->joinToCharacter($batman_character);
$burn_publication->joinToCharacter($robin_character);

$kmoves_publication = $Publications[2];
$kmoves_publication->joinToCharacter($batman_character);
$robin_character->joinToPublication($kmoves_publication);

my_echo( "Characters attached to " . $burn_publication );
reportData($burn_publication->characters(),  array("name", "realname", "desc", "gender", "Publications") );

$crisis_story_arc->joinToPublication($burn_publication);
$kmoves_publication->joinToStory_Arc($crisis_story_arc);

my_echo( "Story Arcs attached to " . $kmoves_publication );
reportData($kmoves_publication->story_arcs(),  array("name", "desc", "publisher", "publications") );

my_echo( "---------- Media ");
/**
				Media::created => time(),
				Media::publication_id => $publication->id,
				Media::type_code => $type->code,
				Media::filename => $filename,
				Media::original_filename => $original_file,
				Media::checksum => $checksum,
				Media::size =>$size
*/
$cbz_type = Model::Named('Media_Type')->objectForCode(\model\media\Media_Type::CBZ);
($cbz_type != false && $cbz_type->code == 'cbz') || die("Could not find Media_Type::CBZ");

$Media_model = Model::Named("Media");
$Media_data = array(
	array(
		\model\media\Media::type_code => $cbz_type->code,
		\model\media\Media::publication_id => $kmoves_publication->id,
		\model\media\Media::original_filename => 'vito',
		\model\media\Media::checksum => uuid(),
		\model\media\Media::size => 112233
	),
	array(
		\model\media\Media::type_code => $cbz_type->code,
		\model\media\Media::publication_id => $kmoves_publication->id,
		\model\media\Media::original_filename => 'Batman Beyond 001 (2012) v12.cbz',
		\model\media\Media::checksum => uuid(),
		\model\media\Media::size => 54321
	),
	array(
		\model\media\Media::type_code => $cbz_type->code,
		\model\media\Media::publication_id => $burn_publication->id,
		\model\media\Media::original_filename => 'Blackhawks 006 (2012) [Digital] (ZOOM-Empire).cbz',
		\model\media\Media::checksum => uuid(),
		\model\media\Media::size => 91919
	)
);
$media = loadData( $Media_model, $Media_data );
reportData($media,  array(
	"publication/issue_num",
	"publication/name",
	"publication/series/name",
	"publication/series/publisher/name"
	)
);

// using the dbo_function to set a value
dbo_setValueForKeypath( "publication/series/publisher/name", "DC Stinkers", $media[0] );
// using the __call overload method to set a value
$media[0]->{"publication/series/name"}("BatVito");

foreach( $media as $m ) {
	my_echo( $m->id
		. "\t" . $m->{"publication/issue_num"}()
		. "\t" . $m->{"publication/series/name"}()
		. "\t" . $m->{"publication/series/publisher/name"}()
	);
}

reportData($media,  array(
	"publication/issue_num",
	"publication/name",
	"publication/series/name",
	"publication/series/publisher/name"
	)
);

// \SQL::raw( "update publication set media_count = (select count(*) from media where media.publication_id = publication.id)" );
// \SQL::raw( "update series set pub_count = (select count(*) from publication where publication.series_id = series.id)" );
// \SQL::raw( "update series set pub_available = (select count(*) from publication where publication.series_id = series.id AND publication.media_count > 0)" );
// \SQL::raw( "update story_arc set pub_count = "
// 	. "(select count(*) from story_arc_publication join publication on story_arc_publication.publication_id = publication.id"
// 	. " where story_arc_publication.story_arc_id = story_arc.id)" );
// \SQL::raw( "update story_arc set pub_available = "
// 	. "(select count(*) from story_arc_publication join publication on story_arc_publication.publication_id = publication.id"
// 	. " where story_arc_publication.story_arc_id = story_arc.id AND publication.media_count > 0)" );

// \SQL::raw( "update series set pub_cycle = (
// 	select (julianday(max(pub_date), 'unixepoch') - julianday(min(pub_date), 'unixepoch')) / count(*)
// 	from publication where publication.series_id = series.id)" );

// \SQL::raw( "update story_arc set pub_cycle = (
// 	select (julianday(max(publication.pub_date), 'unixepoch') - julianday(min(publication.pub_date), 'unixepoch')) / count(*)
// 	from story_arc_publication join publication on story_arc_publication.publication_id = publication.id
// 	where story_arc_publication.story_arc_id = story_arc.id)" );

// \SQL::raw( "update series set pub_active = (
// 	select (((julianday('now') - julianday(max(pub_date), 'unixepoch'))/365) < 1)
// 	from publication where publication.series_id = series.id)" );

// \SQL::raw( "update story_arc set pub_active = (
// 	select (((julianday('now') - julianday(max(pub_date), 'unixepoch'))/365) < 1)
// 	from story_arc_publication join publication on story_arc_publication.publication_id = publication.id
// 	where story_arc_publication.story_arc_id = story_arc.id)" );


	$path = appendPath( "/tmp/", "DatabaseTest" );
		echo PHP_EOL . "exporting to " . $path . PHP_EOL;
	(is_dir($path) == false) || destroy_dir($path) || die( "Failed to delete $path" );
	$exporter = new ExportData_sqlite( $path, \Database::instance() );
	$exporter->exportAll();

my_echo( );
my_echo( );
