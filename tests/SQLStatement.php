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

use \Config as Config;
use \Logger as Logger;
use \Processor as Processor;
use \Metadata as Metadata;

use model\Character as Character;
use model\Series_Character as Series_Character;

$root = TEST_ROOT_PATH . "/" . basename(__FILE__, ".php");
SetConfigRoot( $root, false);

my_echo( );
my_echo( "Creating Database" );
Migrator::Upgrade( Config::GetLog() );

//// Load test data
// load the default user
$user = Model::Named("Users")->userByName('vito');
($user != false && $user->name == 'vito') || die("Could not find 'vito' user");

////
$name = db\Qualifier::Equals( "name", "David" );
my_echo( "select * from table where " . $name->sqlStatement() . PHP_EOL . var_export($name->sqlParameters(), true));
my_echo( "- - - - -" . PHP_EOL);

$age = db\Qualifier::Equals( "age", "47" );
my_echo( "select * from table where " . $age->sqlStatement() . PHP_EOL . var_export($age->sqlParameters(), true));
my_echo( "- - - - -" . PHP_EOL);

$notname = db\Qualifier::NotQualifier( $name );
my_echo( "select * from table where " . $notname->sqlStatement() . PHP_EOL . var_export($notname->sqlParameters(), true));
my_echo( "- - - - -" . PHP_EOL);

$andname = db\Qualifier::AndQualifier( $name, $age, $notname );
my_echo( "select * from table where " . $andname->sqlStatement() . PHP_EOL . var_export($andname->sqlParameters(), true));
my_echo( "- - - - -" . PHP_EOL);

$select = \SQL::Select( Model::Named("Series") );
$select->where( $andname );
my_echo( "SQL: " . $select->sqlStatement() . PHP_EOL . var_export($select->sqlParameters(), true));
my_echo( "- - - - -" . PHP_EOL);

$select = \SQL::Select( Model::Named("Publisher") );
$select->where( $name );
$select->orderBy( array(
		model\Publisher::name,
		array( "desc" => model\Publisher::created )
	)
);
my_echo( "SQL: " . $select->sqlStatement() . PHP_EOL . var_export($select->sqlParameters(), true));
my_echo( "- - - - -" . PHP_EOL);

$select = \SQL::Select( Model::Named("Job_Type") );
$select->where( db\Qualifier::Equals( "scheduled", "1" ) );
$select->orderBy( array( "name"));
my_echo( "SQL: " . $select->sqlStatement() . PHP_EOL . var_export($select->sqlParameters(), true));
$results = $select->fetchAll();
reportData($results,  array( "id", "name", "code", "desc", "scheduled", "processor" ));
my_echo( "- - - - -" . PHP_EOL);

$delete = \SQL::DeleteObject( $user );
my_echo( "SQL: " . $delete->sqlStatement() . PHP_EOL . var_export($delete->sqlParameters(), true));
my_echo( "- - - - -" . PHP_EOL);

$insert = \SQL::Insert( Model::Named("Job_Type"));
$insert
	->addRecord( array( 'name' => 'test 1', 'code' => 'abc', 'desc' => 'abc test', 'processor' => "Processor" ) )
	->addRecord( array( 'name' => 'test 2', 'code' => 'abc', 'desc' => 'abc test', 'scheduled' => "1" ) )
	;
$success = $insert->commitTransaction();
my_echo( "SQL: " . $insert->sqlStatement() . PHP_EOL . var_export($insert->sqlParameters(), true) . PHP_EOL . "success = $success");
$results = \SQL::Select( Model::Named("Job_Type") )->fetchAll();
reportData($results,  array( "id", "name", "code", "desc", "scheduled", "processor" ));
my_echo( "- - - - -" . PHP_EOL);

$update = \SQL::UpdateObject( $user, array( 'name' => 'Sammy' ));
my_echo( "SQL: " . $update->sqlStatement() . PHP_EOL . var_export($update->sqlParameters(), true));
my_echo( "- - - - -" . PHP_EOL);

$select = \SQL::Select( Model::Named("Job_Type"), array( "count(*) as field") );
my_echo( "SQL: " . $select->sqlStatement() . PHP_EOL . var_export($select->sqlParameters(), true));
$results = $select->fetchAll();
my_echo( var_export($results, true) . "- - - - -" . PHP_EOL);

$update = \SQL::Update( Model::Named("Job_Type"), db\Qualifier::Equals( "code", "abc" ), array( 'code' => 'def' ));
$success = $update->commitTransaction();
my_echo( "SQL: " . $update->sqlStatement() . PHP_EOL . var_export($update->sqlParameters(), true) . PHP_EOL . "success = $success");
$results = \SQL::Select( Model::Named("Job_Type") )->fetchAll();
reportData($results,  array( "id", "name", "code", "desc", "scheduled", "processor" ));
my_echo( "- - - - -" . PHP_EOL);

$name1 = db\Qualifier::Equals( "name", "test 1" );
$name2 = db\Qualifier::Equals( "name", "test 2" );
$delete = \SQL::Delete( Model::Named("Job_Type") );
$delete->where(db\Qualifier::OrQualifier( $name1, $name2 ));
$success = $delete->commitTransaction();
my_echo( "SQL: " . $delete->sqlStatement() . PHP_EOL . var_export($delete->sqlParameters(), true) . PHP_EOL . "success = $success");
$results = \SQL::Select( Model::Named("Job_Type") )->fetchAll();
reportData($results,  array( "id", "name", "code", "desc", "scheduled", "processor" ));
my_echo( "- - - - -" . PHP_EOL);

$aggregate = \SQL::Aggregate( "count", Model::Named("Job_Type"), array("code"), db\Qualifier::Equals( "code", "abc" ), array("code") );
my_echo( "SQL: " . $aggregate->sqlStatement() . PHP_EOL . var_export($aggregate->sqlParameters(), true) );
my_echo( "- - - - -" . PHP_EOL);

$join = SQL::SelectJoin( Model::Named("Series"), null, db\Qualifier::Between( "startYear", 2001, 2007 ));
$join->joinOn( Model::Named("Series"), Model::Named("Publication"), array("name"), db\Qualifier::Equals( "name", "pubname" ));
my_echo( "SQL: " . $join->sqlStatement() . PHP_EOL . var_export($join->sqlParameters(), true));
my_echo( "- - - - -" . PHP_EOL);

$join = SQL::SelectJoin( Model::Named("Series"), null, db\Qualifier::Equals( "name", "David" ));
$join->joinOn( Model::Named("Series"), Model::Named("Publication"), array("name"), db\Qualifier::Equals( "name", "pubname" ));
$join->joinOn( Model::Named("Publication"), Model::Named("Media"), array("checksum"), db\Qualifier::Equals( "type_id", "1" ));
my_echo( "SQL: " . $join->sqlStatement() . PHP_EOL . var_export($join->sqlParameters(), true));
my_echo( "- - - - -" . PHP_EOL);

Model::Named('Series_Character')->updateAgregate(
				Character::TABLE, Series_Character::TABLE,
				Character::popularity, "count(*)",
				Character::id, Series_Character::character_id
			);
my_echo( "- - - - -" . PHP_EOL);
