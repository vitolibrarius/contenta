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
require SYSTEM_PATH .'application/libs/Logger.php';

require SYSTEM_PATH .'tests/_ResetConfig.php';
require SYSTEM_PATH .'tests/_Data.php';

$root = "/tmp/test/" . basename(__FILE__, ".php");
SetConfigRoot( $root );

use utilities\CronEvaluator as CronEvaluator;

$validationTests = array(
	array( CronEvaluator::MINUTE, "*", true ),
	array( CronEvaluator::MINUTE, "1,2,3", true ),
	array( CronEvaluator::MINUTE, "a", false ),
	array( CronEvaluator::MINUTE, '*/3', true),

	array( CronEvaluator::HOUR, "*", true ),
	array( CronEvaluator::HOUR, "1,2,3", true ),
	array( CronEvaluator::HOUR, "a", false ),
	array( CronEvaluator::HOUR, '*/3', true),

	array( CronEvaluator::DAYOFWEEK, "a", false ),
	array( CronEvaluator::DAYOFWEEK, '0', true),
	array( CronEvaluator::DAYOFWEEK, '1', true),
	array( CronEvaluator::DAYOFWEEK, '7', false),
	array( CronEvaluator::DAYOFWEEK, '*', true),
	array( CronEvaluator::DAYOFWEEK, '*/3', true),
	array( CronEvaluator::DAYOFWEEK, '1.', false),
	array( CronEvaluator::DAYOFWEEK, 'MON,WED,FRI', true),
);

foreach ( $validationTests as $test ) {
	try {
		CronEvaluator::validateExpressionPart( $test[0], $test[1] );
		if ( $test[2] != true ) {
			my_echo( "expression passed but should have failed " . var_export($test, true));
		}
	}
	catch ( exceptions\ValidationException $ve ) {
		//my_echo ( $ve );
		if ( $test[2] != false ) {
			my_echo( "expression failed but should have passed " . var_export($test, true));
		}
	}
}

$cronEval = new CronEvaluator( '*', '*', '*' );

my_echo( "Testing slash functions ");
$slashTests = array(
	array( '/5', '10', true ),
	array( '1-10/3', '4', true ),
);

foreach ( $slashTests as $test ) {
	my_echo( "-- ++ -- " . $test[0]);

	if ( $cronEval->isSlashRange( $test[0] ) != $test[2] ) {
		my_echo( "Error: should be " . ($test[2] ? "true" : "false") . " isSlashRange " . $test[0]);
	}
	else if ( $cronEval->isInSlashRange( $test[1],  $test[0] ) != $test[2] ) {
		my_echo( "Error: should be " . ($test[2] ? "true" : "false") . " isInSlashRange ( $test[1],  $test[0] )");
	}
}


my_echo( "Testing full expressions");
$expressionTests = array(
	array(
		CronEvaluator::MINUTE => "*",
		CronEvaluator::HOUR => "*",
		CronEvaluator::DAYOFWEEK => "*",
		"StartDate" => '2011-03-15 11:15:00',
		"Skip" => 0,
		"backward" => false,
		"MatchDate" => '2011-03-15 11:15:00'
	),
	array(
		CronEvaluator::MINUTE => "*",
		CronEvaluator::HOUR => "*",
		CronEvaluator::DAYOFWEEK => "*",
		"StartDate" => '2011-03-15 11:15:00',
		"Skip" => 1,
		"backward" => false,
		"MatchDate" => '2011-03-15 11:16:00'
	),
	array(
		CronEvaluator::MINUTE => "/5",
		CronEvaluator::HOUR => "4",
		CronEvaluator::DAYOFWEEK => "WED",
		"StartDate" => '2011-03-15 11:15:00',
		"Skip" => 0,
		"backward" => false,
		"MatchDate" => '2011-03-16 04:00:00'
	),
	array(
		CronEvaluator::MINUTE => "23",
		CronEvaluator::HOUR => "8",
		CronEvaluator::DAYOFWEEK => "MON-WED,FRI",
		"StartDate" => '2011-03-15 11:15:00',
		"Skip" => 0,
		"backward" => false,
		"MatchDate" => '2011-03-16 08:23:00'
	)
);

foreach ( $expressionTests as $test ) {
	my_echo( "+-= Testing expression (" .
		$test[CronEvaluator::MINUTE] . ", " .
		$test[CronEvaluator::HOUR] . ", " .
		$test[CronEvaluator::DAYOFWEEK] . ") ");

	try {
		$cronEval = new CronEvaluator(
			$test[CronEvaluator::MINUTE],
			$test[CronEvaluator::HOUR],
			$test[CronEvaluator::DAYOFWEEK]
		);

		$startDate = new \DateTime( $test['StartDate' ] );
		$matchDate = new \DateTime( $test['MatchDate' ] );
		$nextDate = $cronEval->nextDate($startDate,  $test['Skip']);
		if ( $nextDate->format('Y-m-d H:i:s') !== $test['MatchDate'] ) {
			my_echo( "Error: next date " . $nextDate->format('Y-m-d H:i:s') . ' = ' .  $test['MatchDate']);
		}
	}
	catch ( exceptions\ValidationException $ve ) {
		my_echo( $ve );
	}
}

$cronEval = new CronEvaluator( '/10', '/8', '*' );
$series = $cronEval->nextSeriesDates( null, 10 );
foreach( $series as $idx => $date ) {
	my_echo( "$idx: " . $date->format('Y-m-d H:i:s'));
}
