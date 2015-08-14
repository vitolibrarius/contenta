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

$root = TEST_ROOT_PATH . "/" . basename(__FILE__, ".php");
SetConfigRoot( $root );

$testdata = array(
	"'68 Hallowed Ground 2013 digital Son of Ultron-Empire.cbz" => array(
		"clean" => "'68 Hallowed Ground 2013 digital Son of Ultron-Empire",
		"year" => "2013",
		"extension" => "cbz",
		"name" => "'68 Hallowed Ground",
	),
	"100 Bullets Brother Lono 06 of 8 2014 Digital Zone-Empire.cbz" => array(
		"clean" => "100 Bullets Brother Lono 06 2014 Digital Zone-Empire",
		"year" => "2014",
		"issue" => "06",
		"extension" => "cbz",
		"name" => "100 Bullets Brother Lono",
	),
	"100 Bullets Brother Lono 07 of 8 2014 Digital Zone-Empire.cbz" => array(
		"clean" => "100 Bullets Brother Lono 07 2014 Digital Zone-Empire",
		"year" => "2014",
		"extension" => "cbz",
		"issue" => "07",
		"name" => "100 Bullets Brother Lono",
	),
	"19 Charges Against The Young Herr Holm 2009 Digital Monafekk-Empire.cbz" => array(
		"clean" => "19 Charges Against The Young Herr Holm 2009 Digital Monafekk-Empire",
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
		"clean" => "2000AD 1857 2013 Nahga-Empire",
		"year" => "2013",
		"extension" => "cbz",
		"name" => "2000AD",
	),
	"30 Days of Night - 30 Days 'till Death 01 of 04 2008 digital-Empire.cbz" => array(
		"clean" => "30 Days of Night - 30 Days 'till Death 01 2008 digital-Empire",
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
		"clean" => "Batman '66 019 2013 digital Son of Ultron-Empire",
		"year" => "2013",
		"extension" => "cbz",
		"issue" => "019",
		"name" => "Batman '66",
	),
	"Batman '66 026 2014 digital Son of Ultron-Empire.cbz" => array(
		"clean" => "Batman '66 026 2014 digital Son of Ultron-Empire",
		"extension" => "cbz",
		"year" => "2014",
		"issue" => "026",
		"name" => "Batman '66",
	),
	"Batman - Knightfall Part 01 - Broken Bat 2000 Digital - TheHand-Empire.cbz" => array(
		"clean" => "Batman - Knightfall Part 01 - Broken Bat 2000 Digital - TheHand-Empire",
		"year" => "2000",
		"extension" => "cbz",
		"issue" => "01",
		"name" => "Batman Knightfall Part 01 - Broken Bat",
	),
	"Batman - Knightfall Part 02 - Who Rules The Night 2000 Digital - TheHand-Empire.cbz" => array(
		"clean" => "Batman - Knightfall Part 02 - Who Rules The Night 2000 Digital - TheHand-Empire",
		"extension" => "cbz",
		"year" => "2000",
		"issue" => "02",
		"name" => "Batman Knightfall Part 02 - Who Rules The Night",
	),
	"Batman - The Dark Knight 002.cbz" => array(
		"clean" => "Batman - The Dark Knight 002",
		"extension" => "cbz",
		"issue" => "002",
		"name" => "Batman The Dark Knight",
	),
	"Batman - The Dark Knight 007.cbz" => array(
		"clean" => "Batman - The Dark Knight 007",
		"extension" => "cbz",
		"issue" => "007",
		"name" => "Batman The Dark Knight",
	),
	"Batman - Year One 1987 Digital - TheHand-Empire.cbz" => array(
		"clean" => "Batman - Year One 1987 Digital - TheHand-Empire",
		"extension" => "cbz",
		"year" => "1987",
		"name" => "Batman Year One",
	),
	"Batman 80-Page Giant 2011 2011 Digital - TheHand-Empire.cbz" => array(
		"extension" => "cbz",
		"clean" => "Batman 80-Page Giant 2011 2011 Digital - TheHand-Empire",
		"year" => "2011",
		"issue" => "80",
		"name" => "Batman 80-Page Giant",
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
		"clean" => "Batman Black & White 004 2013 digital Son of Ultron-Empire",
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
		"clean" => "Batman The Court of Owls Vol 1 2011 TPB Zone-Empire",
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
		"clean" => "Batman, Vampire Trilogy - Bloodstorm - Book 2",
		"extension" => "cbz",
		"volume" => "Book 2",
		"name" => "Batman, Vampire Trilogy Bloodstorm -",
	),
	"Batman, Vampire Trilogy - Crimson Mist - Book 3.cbz" => array(
		"clean" => "Batman, Vampire Trilogy - Crimson Mist - Book 3",
		"extension" => "cbz",
		"volume" => "Book 3",
		"name" => "Batman, Vampire Trilogy Crimson Mist -",
	),
	"Ben 10 002 2013 digital-Empire.cbz" => array(
		"extension" => "cbz",
		"clean" => "Ben 10 002 2013 digital-Empire",
		"year" => "2013",
		"issue" => "002",
		"name" => "Ben 10",
	),
	"The 14 Carat Roadster 22.3 2012 digital-Empire.cbz" => array(
		"extension" => "cbz",
		"clean" => "The 14 Carat Roadster 22.3 2012 digital-Empire",
		"year" => "2012",
		"issue" => "22.3",
		"name" => "The 14 Carat Roadster",
	),
	"The 14 Carat Roadster 2012 digital-Empire.cbz" => array(
		"extension" => "cbz",
		"clean" => "The 14 Carat Roadster 2012 digital-Empire",
		"year" => "2012",
		"issue" => "14",
		"name" => "The 14 Carat Roadster",
	),
	"All-New X-Men 010.3 (2013) (Digital) (Zone-Empire).cbz" => array(
		"clean" => "All-New X-Men 010.3 2013 Digital Zone-Empire",
		"year" => "2013",
		"issue" => "010.3",
		"extension" => "cbz",
		"name" => "All-New X-Men",
	),
	"Angela - Asgard's Assassin 003 (2015) (Digital) (Zone-Empire).cbz" => array(
		"clean" => "Angela - Asgard's Assassin 003 2015 Digital Zone-Empire",
		"year" => "2015",
		"issue" => "003",
		"extension" => "cbz",
		"name" => "Angela Asgard's Assassin",
	),
	"1872 #1 SWA.cbz" => array(
		"clean" => "1872 1 SWA",
		"issue" => "1",
		"extension" => "cbz",
		"name" => "1872",
	),
	"13 COINS HC.cbz" => array(
		"clean" => "13 COINS HC",
		"extension" => "cbz",
		"name" => "13 COINS HC",
	),
	"ARROW SEASON 2.5 #10.cbz" => array(
		"clean" => "ARROW SEASON 2.5 10",
		"extension" => "cbz",
		"name" => "ARROW SEASON 2.5",
		"issue" => "10",
	),
	"ARROW SEASON 2.5 #10 (2015).cbz" => array(
		"clean" => "ARROW SEASON 2.5 10 2015",
		"year" => "2015",
		"extension" => "cbz",
		"name" => "ARROW SEASON 2.5",
		"issue" => "10",
	),
	"Swamp Thing 012(1974)(FB-DCP)(C2C).cbz" => array(
		"clean" => "Swamp Thing 012 1974 FB-DCP C2C",
		"year" => "1974",
		"extension" => "cbz",
		"name" => "Swamp Thing",
		"issue" => "012",
	),
	"Astro-City-The-Dark-Age-Book-Three - 1 - 2009.cbr" => array(
		"clean" => "Astro-City-The-Dark-Age-Book-Three - 1 - 2009",
		"year" => "2009",
		"extension" => "cbr",
		"name" => "Astro City The Dark Age Book Three",
		"issue" => "1",
	),

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
				. (isset($expected[$value]) ? $expected[$value] : 'null') . "' but got '"
				. (isset($meta[$value]) ? $meta[$value] : 'null') . "'" . PHP_EOL;
		}
		echo PHP_EOL;
	}
}

if ( $hasErrors == false ) {
	echo "No errors encountered" . PHP_EOL;
}
?>
