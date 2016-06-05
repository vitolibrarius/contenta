<?php
	$system_path = dirname(dirname(__FILE__));
	if (realpath($system_path) !== FALSE)
	{
		$system_path = realpath($system_path).DIRECTORY_SEPARATOR;
	}

	define('SYSTEM_PATH', str_replace("\\", DIRECTORY_SEPARATOR, $system_path));
	define('APPLICATION_PATH', SYSTEM_PATH . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR);

// 	require SYSTEM_PATH .'application/config/bootstrap.php';
// 	require SYSTEM_PATH .'application/config/autoload.php';
	require SYSTEM_PATH .'application/config/common.php';

// 	require SYSTEM_PATH .'application/libs/Config.php';
// 	require SYSTEM_PATH .'application/libs/Cache.php';
//
// 	require SYSTEM_PATH .'tests/_ResetConfig.php';
// 	require SYSTEM_PATH .'tests/_Data.php';

	function jsonErrorString($code)
	{
		$constants = get_defined_constants(true);
		$json_errors = array();
		foreach ($constants["json"] as $name => $value) {
			if (!strncmp($name, "JSON_ERROR_", 11)) {
				$json_errors[$value] = $name;
			}
		}
		return $json_errors[$code];
	}

function test_RandomString($size = 10, $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
{
	$randstring = '';
	$size = min(max(intval($size), 1), 512);
	for ($i = 0; $i < $size; $i++) {
		$randstring .= $characters[rand(0, strlen($characters) -1)];
	}
	return $randstring;
}

$unwanted_array = array(
	'Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
	'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
	'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
	'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
	'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y'
);
  $a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ');
  $b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');
//   return str_replace($a, $b, $str);

// $utf = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝßàáâãäåæçèéêëìíîïñòóôõöøùúûüýÿĀāĂăĄąĆćĈĉĊċČčĎďĐđĒēĔĕĖėĘęĚěĜĝĞğĠġĢģĤĥĦħĨĩĪīĬĭĮįİıĲĳĴĵĶķĹĺĻļĽľĿŀŁłŃńŅņŇňŉŌōŎŏŐőŒœŔŕŖŗŘřŚśŜŝŞşŠšŢţŤťŦŧŨũŪūŬŭŮůŰűŲųŴŵŶŷŸŹźŻżŽžſƒƠơƯưǍǎǏǐǑǒǓǔǕǖǗǘǙǚǛǜǺǻǼǽǾǿ';
//
// echo PHP_EOL . normalize( $utf) . PHP_EOL;
// die();
$bad_characters = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝßàáâãäåæçèéêëìíîïñòóôõöøùúûüýÿĀāĂăĄąĆćĈĉĊċČčĎďĐđĒēĔĕĖėĘęĚěĜĝĞğĠġĢģĤĥĦħĨĩĪīĬĭĮįİıĲĳĴĵĶķĹĺĻļĽľĿŀŁłŃńŅņŇňŉŌōŎŏŐőŒœŔŕŖŗŘřŚśŜŝŞşŠšŢţŤťŦŧŨũŪūŬŭŮůŰűŲųŴŵŶŷŸŹźŻżŽžſƒƠơƯưǍǎǏǐǑǒǓǔǕǖǗǘǙǚǛǜǺǻǼǽǾǿ .~`!@#$%^&*()_=+[{]}\\|;:\"\',<>/?';
$characters = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";

$teststrings = array(
	"For NoŧMê - \"Marvêl Prêviêwş 003 (Ōcŧobêr 2015 for Dêcêmbêr 2015) (wêbrip by LǕşiphǕr-DCP).pdf\" (1/103)",
	"JoǕrnêy ŧo Sŧar Warş - Thê Forcê Åwakênş - Shaŧŧêrêd Empirê 04 (of 04) (2015) (Digiŧal) (Kilêko-Empirê)",
	"Capŧain Åmêrica Comicş (Timêly) [75/80]  - \"Capŧain Åmêrica Comicş 074  (2 şŧoriêş only).cbr\"",
	"Nêw Řêlêaşêş \"Vampirêlla Vol. 2 God Savê Thê QǕêên (TPB)(2016)(Digiŧal)(TLK-EMPIŘE-HD).cbr\"",
	"Tom Poêş onŧdêkŧ hêŧ gêhêim dêr blaǕwê aardê (boêkjê) \"Tom Poêş onŧdêkŧ hêŧ gêhêim dêr blaǕwê aardê.cbr\"",
	"Thê Dark Towêr - Thê Drawing of ŧhê Thrêê - Thê Lady of Shadowş 04 (of 05) (2016) (Digiŧal) (Zonê-Empirê).cbz",
	"Sŧaŧê of Baŧman 1-50 -- Upgradêş, anyonê? [1 of 3] \"Sŧaŧê of Baŧman 1-50 (Workêr, Fêb 2016).pdf\"",
	"Capŧain Åmêrica Comicş (Timêly) [71/80]  - \"Capŧain Åmêrica Comicş 70ŧh Ånnivêrşary Spêcial.cbr\"",
	"For comicnǕŧ \"InjǕşŧicê - Godş Åmong Uş - Yêar FoǕr 021 (2015) (digiŧal) (Son of Ulŧron-Empirê).cbr\"",
	"Nêw Řêlêaşêş 2015.4.6 \"XIII Myşŧêry 004 - Colonêl Åmoş (2015) (Cinêbook) (digiŧal) (Lynx-Empirê).cbr\"",
	"Nêw Řêlêaşêş 2015.4.6 \"Waynê Shêlŧon 002 - Thê Bêŧrayal (2014) (Cinêbook) (digiŧal) (Lynx-Empirê).cbr\"",
	"Nêw Řêlêaşêş 2015.4.7 \"Thê Loxlêyş and ŧhê War of 1812 (2013) (digiŧal) (2nd êd) (d'argh-Empirê).cbr\"",
	"Řê: Řichiê Řich aş rêqǕêşŧêd - Happy ŧo hêlp [1 of 8] \"Řichiê Řich (1960) 198 Harvêy Jan 1981 C2C.cbr\"",
	"For flipênnşŧaŧêr/BlǕêDêvil - \"Capŧain Åmêrica'ş Wêird Talêş 074 (Timêly.1949) (c2c) (şooŧh-Pmack-NovǕş).cbz\"",
	"Capŧain Åmêrica Comicş (Timêly) [80/80]  - \"Capŧain Åmêrica Comicş ÅnnǕal 128 pagêş (cman-MyşŧêryMan).cbr\"",
	"Capŧain Åmêrica Comicş (Timêly) [63/80]  - \"Capŧain Åmêrica Comicş 063 (Timêly.1947) (mişşing.ifc) (chǕmş).cbr\"",
	"JǕşŧ nêêdş ŧhê labor inŧênşivê final ŧoǕchêş on şomê pagêş :) - \"Canŧêên Kaŧê 001 (Sŧ. John) (Maŧŧ Bakêr) WIP v2.cbz\"",
	"Sŧaŧê of Baŧman 1-50 -- Upgradêş, anyonê? [2 of 3] \"Sŧaŧê of Baŧman 1-50 -- Upgradêş, anyonê?.par2\"",
	"For YǕggoŧh - \"Ålan Moorê'ş Thê CoǕrŧyard (original şŧory from Ålan Moorê'ş Thê CoǕrŧyard Companion) (2004) (loopyjoê-DCP).cbr\""
);

$sanitizeList = array();
foreach ( $teststrings as $ts ) {
	$test1 = array(
		"teststring" => $ts,
		"tests" => array(
			"Default:false:false" => sanitize( $ts, false, false ),
			"Lowercase:true:false" => sanitize( $ts, true, false ),
			"Anal:false:true" => sanitize( $ts, false, true ),
			"LowercaseAnal:true:true" => sanitize( $ts, true, true )
		)
	);
	$sanitizeList[] = $test1;
}

$returnValue = file_put_contents( "/tmp/SanitationStrings.json", json_encode($sanitizeList, JSON_PRETTY_PRINT));
if ( json_last_error() != 0 ) {
	echo 'Last error: ' . jsonErrorString(json_last_error()). PHP_EOL;
	throw new \Exception( jsonErrorString(json_last_error()) );
}

//////////////////////////////////////

$sanitizeFileList = array();
foreach ( $teststrings as $ts ) {
	$test1 = array(
		"teststring" => $ts,
		"tests" => array(
			"25 Default:25:false:false" => sanitize_filename( $ts, 25, false, false ),
			"25 Lowercase:25:true:false" => sanitize_filename( $ts, 25, true, false ),
			"25 Anal:25:false:true" => sanitize_filename( $ts, 25, false, true ),
			"25 LowercaseAnal:25:true:true" => sanitize_filename( $ts, 25, true, true ),

			"1000 Default:1000:false:false" => sanitize_filename( $ts, 1000, false, false ),
			"1000 Lowercase:1000:true:false" => sanitize_filename( $ts, 1000, true, false ),
			"1000 Anal:1000:false:true" => sanitize_filename( $ts, 1000, false, true ),
			"1000 LowercaseAnal:1000:true:true" => sanitize_filename( $ts, 1000, true, true )
		)
	);
	$sanitizeFileList[] = $test1;
}

$returnValue = file_put_contents( "/tmp/SanitationFilenames.json", json_encode($sanitizeFileList, JSON_PRETTY_PRINT));
if ( json_last_error() != 0 ) {
	echo 'Last error: ' . jsonErrorString(json_last_error()). PHP_EOL;
	throw new \Exception( jsonErrorString(json_last_error()) );
}

//////////////////////////////////////

$normalizeList = array();
foreach ( $teststrings as $ts ) {
	$test1 = array(
		"teststring" => $ts,
		"normalize" => normalize( $ts ),
		"normalizeSearchString" => normalizeSearchString( $ts ),
	);
	$normalizeList[] = $test1;
}

$returnValue = file_put_contents( "/tmp/NormalizedStrings.json", json_encode($normalizeList, JSON_PRETTY_PRINT));
if ( json_last_error() != 0 ) {
	echo 'Last error: ' . jsonErrorString(json_last_error()). PHP_EOL;
	throw new \Exception( jsonErrorString(json_last_error()) );
}

exit;
for ( $i = 0; $i < 20; $i++ ) {
	$teststring = test_RandomString( 25, $bad_characters );
	echo $teststring;
	for ( $lower = 0; $lower < 2; $lower++ ) {
		for ( $anal = 0; $anal < 2; $anal++ ) {
			$type = (boolval($lower) ? "Lower" : "Mixed") . ":" . (boolval($anal) ? "anal" : "normal");
			$sane = sanitize( $teststring, boolval($lower), boolval($anal));
			echo " | " . str_pad($sane, 25);
		}
	}
	echo PHP_EOL;
}
	echo PHP_EOL;


exit;
		$order = array( "unknownpublicationstory_arcseries" );
		$fullMetal = array(
			"publication" => array( "publication_56", 56 ),
			"story_arc" => array( "story_arc_87", 87 ),
			"series" => array( "series_111", 111 ),
			"publication" => array( "publication_56", 56 ),
			"trees" => array( "trees_1289", 56 ),
		);
		if ( is_array($fullMetal) && count($fullMetal) > 0 ) {
			$alchemist = array_keys($fullMetal);
			usort($alchemist, function ($a, $b) use ($order) {
				$pos_a = intval(array_search($a, $order));
				$pos_b = intval(array_search($b, $order));
				echo " sort $a($pos_a) -> $b($pos_b) \n";
				return $pos_a - $pos_b;
			});

			var_dump($alchemist);
		}
?>
