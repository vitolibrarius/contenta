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
require SYSTEM_PATH .'application/libs/Logger.php';

$config = Config::instance();
$config->setValue("Logging/type", "Print") || die("Failed to change the configured logger");
Logger::resetInstance();

$testdata = array(
	"'68 Hallowed Ground 2013 digital Son of Ultron-Empire.cbz" => array(
		"clean" => "'68 Hallowed Ground 2013 digital Son of Ultron Empire",
		"year" => "2013",
		"extension" => "cbz",
		"name" => "'68 Hallowed Ground",
	),
	"100 Bullets Brother Lono 06 of 8 2014 Digital Zone-Empire.cbz" => array(
		"clean" => "100 Bullets Brother Lono 06 2014 Digital Zone Empire",
		"year" => "2014",
		"issue" => "06",
		"extension" => "cbz",
		"name" => "100 Bullets Brother Lono",
	),
	"100 Bullets Brother Lono 07 of 8 2014 Digital Zone-Empire.cbz" => array(
		"clean" => "100 Bullets Brother Lono 07 2014 Digital Zone Empire",
		"year" => "2014",
		"extension" => "cbz",
		"issue" => "07",
		"name" => "100 Bullets Brother Lono",
	),
	"19 Charges Against The Young Herr Holm 2009 Digital Monafekk-Empire.cbz" => array(
		"clean" => "19 Charges Against The Young Herr Holm 2009 Digital Monafekk Empire",
		"year" => "2009",
		"extension" => "cbz",
		"name" => "19 Charges Against The Young Herr Holm",
	),
	"2000AD 1620 28 01 09 John Williams P.cbz" => array(
		"clean" => "2000AD 1620 28 01 09 John Williams P",
		"issue" => "09",
		"extension" => "cbz",
		"name" => "2000AD",
	),
	"2000AD 1857 2013  Nahga-Empire.cbz" => array(
		"clean" => "2000AD 1857 2013 Nahga Empire",
		"year" => "2013",
		"extension" => "cbz",
		"name" => "2000AD",
	),
	"30 Days of Night - 30 Days 'till Death 01 of 04 2008 digital-Empire.cbz" => array(
		"clean" => "30 Days of Night 30 Days 'till Death 01 2008 digital Empire",
		"year" => "2008",
		"extension" => "cbz",
		"issue" => "01",
		"name" => "30 Days of Night 30 Days 'till Death",
	),
	"300.cbz" => array(
		"clean" => "300",
		"name" => "300",
		"extension" => "cbz",
	),
	"Batman '66 019 2013 digital Son of Ultron-Empire.cbz" => array(
		"clean" => "Batman '66 019 2013 digital Son of Ultron Empire",
		"year" => "2013",
		"extension" => "cbz",
		"issue" => "019",
		"name" => "Batman '66",
	),
	"Batman '66 026 2014 digital Son of Ultron-Empire.cbz" => array(
		"clean" => "Batman '66 026 2014 digital Son of Ultron Empire",
		"extension" => "cbz",
		"year" => "2014",
		"issue" => "026",
		"name" => "Batman '66",
	),
	"Batman - Knightfall Part 01 - Broken Bat 2000 Digital - TheHand-Empire.cbz" => array(
		"clean" => "Batman Knightfall Part 01 Broken Bat 2000 Digital TheHand Empire",
		"year" => "2000",
		"extension" => "cbz",
		"issue" => "01",
		"name" => "Batman Knightfall Part 01 Broken Bat",
	),
	"Batman - Knightfall Part 02 - Who Rules The Night 2000 Digital - TheHand-Empire.cbz" => array(
		"clean" => "Batman Knightfall Part 02 Who Rules The Night 2000 Digital TheHand Empire",
		"extension" => "cbz",
		"year" => "2000",
		"issue" => "02",
		"name" => "Batman Knightfall Part 02 Who Rules The Night",
	),
	"Batman - The Dark Knight 002.cbz" => array(
		"clean" => "Batman The Dark Knight 002",
		"extension" => "cbz",
		"issue" => "002",
		"name" => "Batman The Dark Knight",
	),
	"Batman - The Dark Knight 007.cbz" => array(
		"clean" => "Batman The Dark Knight 007",
		"extension" => "cbz",
		"issue" => "007",
		"name" => "Batman The Dark Knight",
	),
	"Batman - Year One 1987 Digital - TheHand-Empire.cbz" => array(
		"clean" => "Batman Year One 1987 Digital TheHand Empire",
		"extension" => "cbz",
		"year" => "1987",
		"name" => "Batman Year One",
	),
	"Batman 80-Page Giant 2011 2011 Digital - TheHand-Empire.cbz" => array(
		"extension" => "cbz",
		"clean" => "Batman 80 Page Giant 2011 2011 Digital TheHand Empire",
		"year" => "2011",
		"issue" => "80",
		"name" => "Batman 80 Page Giant",
	),
	"Batman Beyond 001 (2012) v12.cbz" => array(
		"extension" => "cbz",
		"clean" => "Batman Beyond 001 2012 v12",
		"year" => "2012",
		"issue" => "001",
		"volume" => "v12",
		"name" => "Batman Beyond",
	),
	"Batman Black & White 004 2013 digital Son of Ultron-Empire.cbz" => array(
		"extension" => "cbz",
		"clean" => "Batman Black & White 004 2013 digital Son of Ultron Empire",
		"year" => "2013",
		"issue" => "004",
		"name" => "Batman Black & White",
	),
	"Batman The Complete Hush 2005 Zone.cbz" => array(
		"extension" => "cbz",
		"clean" => "Batman The Complete Hush 2005 Zone",
		"year" => "2005",
		"name" => "Batman The Complete Hush",
	),
	"Batman The Court of Owls Vol  1 2011  TPB Zone-Empire.cbz" => array(
		"clean" => "Batman The Court of Owls Vol 1 2011 TPB Zone Empire",
		"year" => "2011",
		"volume" => "Vol 1",
		"extension" => "cbz",
		"name" => "Batman The Court of Owls",
	),
	"Batman The Dark Knight 002 2011 Megan.cbr" => array(
		"clean" => "Batman The Dark Knight 002 2011 Megan",
		"extension" => "cbr",
		"year" => "2011",
		"issue" => "002",
		"name" => "Batman The Dark Knight",
	),
	"Batman The Dark Knight 007 2012 2 covers Megan.cbr" => array(
		"clean" => "Batman The Dark Knight 007 2012 2 covers Megan",
		"extension" => "cbr",
		"year" => "2012",
		"issue" => "007",
		"name" => "Batman The Dark Knight",
	),
	"Batman The Long Halloween 1998.cbz" => array(
		"clean" => "Batman The Long Halloween 1998",
		"year" => "1998",
		"extension" => "cbz",
		"name" => "Batman The Long Halloween",
	),
	"Batman Year One.cbz" => array(
		"clean" => "Batman Year One",
		"extension" => "cbz",
		"name" => "Batman Year One",
	),
	"Batman, Vampire Trilogy - Bloodstorm - Book 2.cbz" => array(
		"clean" => "Batman, Vampire Trilogy Bloodstorm Book 2",
		"extension" => "cbz",
		"volume" => "Book 2",
		"name" => "Batman, Vampire Trilogy Bloodstorm",
	),
	"Batman, Vampire Trilogy - Crimson Mist - Book 3.cbz" => array(
		"clean" => "Batman, Vampire Trilogy Crimson Mist Book 3",
		"extension" => "cbz",
		"volume" => "Book 3",
		"name" => "Batman, Vampire Trilogy Crimson Mist",
	),
	"Ben 10 002 2013 digital-Empire.cbz" => array(
		"extension" => "cbz",
		"clean" => "Ben 10 002 2013 digital Empire",
		"year" => "2013",
		"issue" => "002",
		"name" => "Ben 10",
	),
	"The 14 Carat Roadster 2012 digital-Empire.cbz" => array(
		"extension" => "cbz",
		"clean" => "The 14 Carat Roadster 2012 digital Empire",
		"year" => "2012",
		"issue" => "14",
		"name" => "The 14 Carat Roadster",
	),
	"The Batman - Judge Dredd Collection 2014 Digital K6-Empire.cbz" => array(
		"clean" => "The Batman Judge Dredd Collection 2014 Digital K6 Empire",
		"year" => "2014",
		"extension" => "cbz",
		"name" => "The Batman Judge Dredd Collection",
	)
);

$hasErrors = false;
foreach( $testdata as $filename => $expected ) {
	$mediaFilename = new utilities\MediaFilename($filename);
	$meta = $mediaFilename->updateFileMetaData(null);

	$result_diff_a = array_diff($meta, $expected);
	$result_diff_b = array_diff($expected, $meta);
	$result_assoc = array_diff_assoc($meta, $expected);
	if ( count($result_diff_a) > 0 || count($result_assoc) > 0 || count($result_diff_b) > 0) {
		echo $filename . PHP_EOL;
		$result = array_unique( array_merge(array_keys($result_diff_a), array_keys($result_diff_b), array_keys($result_assoc)) );
		foreach ($result as $key => $value) {
			$hasErrors = true;
			echo "	Expected value for [" . $value . "] = '"
				. (isset($expected[$value]) ? $expected[$value] : null) . "' but got '"
				. (isset($meta[$value]) ? $meta[$value] : null) . "'" . PHP_EOL;
		}
		echo PHP_EOL;
	}
}

if ( $hasErrors == false ) {
	echo "No errors encountered" . PHP_EOL;
}
?>