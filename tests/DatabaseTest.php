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
use model\Job_Type as Job_Type;
use model\Job_Running as Job_Running;
use model\Job as Job;

$root = TEST_ROOT_PATH . "/" . basename(__FILE__, ".php");
SetConfigRoot( $root );

my_echo( );
my_echo( "Creating Database" );
Migrator::Upgrade( Config::GetLog() );

// load the default user
$user = Model::Named("Users")->userByName('vito');
($user != false && $user->name == 'vito') || die("Could not find 'vito' user");

my_echo( "---------- Version ");
$versions = Model::Named('Version')->allObjects();
reportData($versions,  array( "code", "hash_code" ));
reportData($versions[0]->patches(),  array( "displayName", "version/code" ));

my_echo( "---------- Endpoint ");
$cv_endpoint_type = Model::Named('Endpoint_Type')->endpointTypeForCode(model\Endpoint_Type::ComicVine);
($cv_endpoint_type != false && $cv_endpoint_type->code == 'ComicVine') || die("Could not find Endpoint_Type::ComicVine");

$rss_endpoint_type = Model::Named('Endpoint_Type')->endpointTypeForCode(model\Endpoint_Type::RSS);
($rss_endpoint_type != false && $rss_endpoint_type->code == 'RSS') || die("Could not find Endpoint_Type::RSS");

$endpoint_model = Model::Named("Endpoint");
$endpoint_data = array(
	array(
		model\Endpoint::name => "My ComicVine",
		model\Endpoint::type_id => $cv_endpoint_type->id,
		model\Endpoint::base_url => $cv_endpoint_type->api_url,
		model\Endpoint::api_key => uuid(),
		model\Endpoint::username => 'vito',
		model\Endpoint::enabled => Model::TERTIARY_TRUE,
		model\Endpoint::compressed => Model::TERTIARY_FALSE
	),
	array(
		model\Endpoint::name => "Comicbook RSS",
		model\Endpoint::type_id => $rss_endpoint_type->id,
		model\Endpoint::base_url => "http://comicbook.source.com/rss?api=12345",
		model\Endpoint::username => 'vito',
		model\Endpoint::enabled => Model::TERTIARY_TRUE,
		model\Endpoint::compressed => Model::TERTIARY_TRUE
	)

);
$endpoints = loadData( $endpoint_model, $endpoint_data );
$comicvine_endpoint = $endpoints[0];
$rss_endpoint = $endpoints[1];

my_echo( "---------- Jobs ");
$job_types = Model::Named('Job_Type')->allObjects();
reportData($job_types,  Model::Named('Job_Type')->allColumnNames());

$character_job_type = Model::Named('Job_Type')->jobTypeForCode('character');
($character_job_type != false && $character_job_type->code == 'character') || die("Could not find 'character' job_type");

$rss_job_type = Model::Named('Job_Type')->jobTypeForCode('rss');
($rss_job_type != false && $rss_job_type->code == 'rss') || die("Could not find 'rss' job_type");

$job_model = Model::Named("Job");
$job_data = array(
	array(
		model\Job::type_id => $character_job_type->id,
		model\Job::minute => 1,
		model\Job::hour => 1,
		model\Job::dayOfWeek => 1,
		model\Job::parameter => '27add4363e9b138cd375b258db481b8f',
		model\Job::next => null,
		model\Job::one_shot => Model::TERTIARY_TRUE,
		model\Job::enabled => Model::TERTIARY_TRUE
	),
	array(
		model\Job::type_id => $rss_job_type->id,
		model\Job::minute => "10",
		model\Job::hour => "2,4,6,8",
		model\Job::dayOfWeek => "*",
		model\Job::parameter => '',
		model\Job::endpoint_id => $rss_endpoint->id,
		model\Job::next => null,
		model\Job::one_shot => Model::TERTIARY_FALSE,
		model\Job::enabled => Model::TERTIARY_TRUE
	),
	array(
		model\Job::type_id => $rss_job_type->id,
		model\Job::minute => "10",
		model\Job::hour => "2,4,6,8",
		model\Job::dayOfWeek => "*",
		model\Job::parameter => '',
		model\Job::endpoint_id => $rss_endpoint->id,
		model\Job::next => null,
		model\Job::one_shot => Model::TERTIARY_FALSE,
		model\Job::enabled => Model::TERTIARY_FALSE
	)
);
$jobs = loadData( $job_model, $job_data, array("jobType", "endpoint", "minute", "hour", "dayOfWeek", "parameter", "next", "one_shot", "enabled") );
$character_job = $jobs[0];
$rss_job = $jobs[2];

$job_run_model = Model::Named("Job_Running");
$job_run_data = array(
	array(
		model\Job_Running::job_id => $character_job->id,
		model\Job_Running::job_type_id => $character_job_type->id,
		model\Job_Running::processor => 'UploadImport',
		model\Job_Running::guid => rand(),
		model\Job_Running::pid => 3456
	),
	array(
		model\Job_Running::job_id => $rss_job->id,
		model\Job_Running::job_type_id => $rss_job_type->id,
		model\Job_Running::processor => 'UploadImport',
		model\Job_Running::guid => rand(),
		model\Job_Running::pid => 98765
	)
);
$jobs_running = loadData( $job_run_model, $job_run_data, array("job", "jobType", "trace", "trace_id", "context", "context_id", "pid") );

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

$batman_character = $Characters[0];
$robin_character = $Characters[1];
$batman_character->addAlias("Bruce Wayne");
$batman_character->addAlias("Bruce");
$batman_character->addAlias("Bat-Man");
reportData($batman_character->aliases(),  array( "name", "character" ));

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
		model\Publication::name => "The Big Burn: Sparks",
		model\Publication::desc => "<p style=\"\"><em>As Two-Face continues his rampage through Gotham City, more light is shed on his past. Who is Carrie Kelley and how can her mysterious connection to Harvey Dent help Batman?<\/em><\/p>",
		model\Publication::issue_num => 25,
		model\Publication::pub_date => strtotime('2014-01-01'),
		model\Publication::series_id => $tdk_series->id,
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
		model\Publication::series_id => $nightwing_series->id
	),
	array(
		model\Publication::name => "Knightmoves",
		model\Publication::desc => "<p style=\"\"><em>As Knightmoves-Knightmoves Dent help Batman?<\/em><\/p>",
		model\Publication::issue_num => 22,
		model\Publication::pub_date => strtotime('2014-01-01'),
		model\Publication::series_id => $tdk_series->id
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
				Media::type_id => $type->id,
				Media::filename => $filename,
				Media::original_filename => $original_file,
				Media::checksum => $checksum,
				Media::size =>$size
*/
$cbz_type = Model::Named('Media_Type')->mediaTypeForCode(model\Media_Type::CBZ);
($cbz_type != false && $cbz_type->code == 'cbz') || die("Could not find Media_Type::CBZ");

$Media_model = Model::Named("Media");
$Media_data = array(
	array(
		model\Media::type_id => $cbz_type->id,
		model\Media::publication_id => $kmoves_publication->id,
		model\Media::original_filename => 'vito',
		model\Media::checksum => uuid(),
		model\Media::size => 112233
	),
	array(
		model\Media::type_id => $cbz_type->id,
		model\Media::publication_id => $kmoves_publication->id,
		model\Media::original_filename => 'Batman Beyond 001 (2012) v12.cbz',
		model\Media::checksum => uuid(),
		model\Media::size => 54321
	),
	array(
		model\Media::type_id => $cbz_type->id,
		model\Media::publication_id => $burn_publication->id,
		model\Media::original_filename => 'Blackhawks 006 (2012) [Digital] (ZOOM-Empire).cbz',
		model\Media::checksum => uuid(),
		model\Media::size => 91919
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

my_echo( );
my_echo( );
