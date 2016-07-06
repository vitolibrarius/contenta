<?php

namespace utilities;

use \Logger as Logger;
use \Cache as Cache;
use \ClassNotFoundException as ClassNotFoundException;

class MediaFilename
{
	public function __construct($filename, $skipExtension = false)
	{
		$this->sourcename = $filename;
		$this->skipExtension = $skipExtension;
	}

	public function parseYearFromFilename( $filename )
	{
		if (preg_match_all("/\\b(\\d{4})\\b/uU", $filename, $matches, 0))
		{
			$matches = array_reverse($matches[1]);
			$curYear = intval(date("Y")) + 1;
			foreach ($matches as $key => $value) {
				$year = intval($value);
				if ( $year > 1900 && $year <= $curYear) {
					return $year;
				}
			}
		}
		return false;
	}

	public function parseIssueFromFilename( $filename )
	{
		// first remove the volume and covers count from the string
		$result = preg_replace("/(?i)\\b(((v|vol|book)[\\.\\s]*(\\d{1,4}))|((\\d)[\\.\\s]*(covers)))\\b/u", "", $filename);
		$list = explode(' ', $result );
		if ( count($list) > 1 )
		{
			unset($list[0]);
			foreach ( array_reverse($list) as $key => $value)
			{
				if (preg_match("/(?<=-|\\b)(([0-9]{1,3}\\.[0-9]+)|([0-9]{1,3}))(?=\\b)/ui", $value, $matches, 0)) {
					return ltrim( $matches[0], '0' );
				}
			}
		}
		return false;
	}

	public function parseVolumeFromFilename( $filename )
	{
		if (preg_match("/(?i)\\b((v|vol|book)[\\.\\s]*(\\d{1,4}))\\b/u", $filename, $matches, 0))
		{
			return $matches[0];
		}
		return false;
	}

	public function parsePublicationNameFromFilename( $filename )
	{
		// remove vol / book / covers
		$working = preg_replace("/(\\b(v|vol|book)[\\.\\s]*\\d{1,4})|(\\d[\\.\\s]*covers)/uim", "", $filename);

		// remove No.01
		$working = preg_replace("/(\\b(No)[\\.\\s]*\\d{1,3})/uim", "", $working);

		// remove one shot
		$working = preg_replace("/\\b(tpb|os|one[ -]shot|ogn|gn)/uim", "", $working);

		// remove name dash and replace with ' '
		// change "Astro-City-The-Dark-Age-Book-Three - 1 - 2009" to "Astro City The Dark Age Book Three - 1 - 2009"
		if ( preg_match("/(\\s-\\s\\d{1,3}\\s-\\s\\d{4})/uiU", $working) > 0 ) {
			$working = preg_replace("/((\\D+)-)+/uiU", "$2 ", $working);
		}
 		$working = preg_replace("/(\\s-\\s)/uim", " ", $working, 1);

		// everything up to Month.Year
		if (preg_match("/(^.+)(?=\\b[-]?((Jan|Feb|Mar|March|Apr|April|May|Jun|June|Jul|July|Aug|Sep|Sept|Oct|Nov|Dec)[\\.\\s]\\d{4}))/uim",
			$working, $matches, 0))
		{
// 			echo "everything up to issue/year like $working" .PHP_EOL;
			return $matches[1];
		}

		// everything up to issue/year like XYZ Comic 001 2014
		if (preg_match("/(^.+)(?=\\b[-]?(([0-9]{1,3}\\.[0-9]+|[0-9]{1,4}))\\s+(\\d{4}))/uU", $working, $matches, 0))
		{
// 			echo "everything up to issue/year like $working" .PHP_EOL;
			return $matches[1];
		}

		// everything up to issue\syear like XYZ Comic 001 2014
		if (preg_match("/(^.+)((\\d{3})\\s+(\\d{4})).+/u", $working, $matches, 0))
		{
// 			echo "everything up to issue\syear like $working" .PHP_EOL;
			return $matches[1];
		}

		// everything up to the year
		if (preg_match("/(^.+)(?=\\b[-]?(\\d{4})).+/u", $working, $matches, 0))
		{
// 			echo "everything up to the year like $working" .PHP_EOL;
			return $matches[1];
		}

		$list = explode(' ', $working );
		if ( count($list) == 1)
		{
			return $list[0];
		}
		else
		{
			$issue = $this->parseIssueFromFilename($filename);
			$idx = array_search($issue, $list);
			if ( $idx != false ) {
				return implode(" ", array_slice($list, 0, $idx));
			}
		}

		return $working;
	}



	public function parsedValues()
	{
		$metadata = array();

		$clean = $this->sourcename;
		// yEnc format from RSS feeds, especially binsearch
		if (preg_match("/.*\"(.*)\" .*yEnc.*/uUs", $this->sourcename, $matches)) {
			$clean = $matches[1];
		}

		if ( is_bool($this->skipExtension) && $this->skipExtension == false) {
			$ext = file_ext($clean);
			if ( isset($ext) && strlen($ext) > 0) {
				$metadata['extension'] = strtolower($ext);
			}
			$clean = file_ext_strip($clean);
		}

		if (substr_count($clean, "_28") > 1 && substr_count($filename, "_29") > 1)
		{
			$clean = str_replace("_28", "(", $clean);
			$clean = str_replace("_29", ")", $clean);
		}

		$clean = str_replace("+", " ", $clean);
		$clean = str_replace("#", " ", $clean);

		// remove the file uniquing number system.  eg: Swamp Thing 007 (2012) (2 covers) (Megan-Empire).1.cbz
		$clean = preg_replace('/\.1$/', '', $clean);

		// remove 'c2c'
		if (substr_count($clean, "c2c") >= 1 ) {
			$clean = str_replace("c2c", "", $clean);
		}

		// replace parenthetical phrases with spaces
		$clean = preg_replace("/\\((.*?)\\)/us", " $1 ", $clean);
		$clean = preg_replace("/\\[(.*?)\\]/u", " $1 ", $clean);

		// remove '_'
		$clean = preg_replace("/([_])/u", " ", $clean);

		// remove '.' but not 22.3
		$clean = preg_replace("/(?<=vol(\\.|\\s)\\d)(\\.)|(?<=no(\\.|\\s)\\d)(\\.)|(?<=no(\\.|\\s)\\d\\d)(\\.)|(?<=\\D)(\\.)(?=\\D)/ui", " ", $clean);

		// remove any "of NN" phrase
		$clean = preg_replace("/(of [\\d]+)/ui", " ", $clean);

		// remove multiple spaces with single spaces
		$clean = preg_replace("/(  +)/u", " ", $clean);

		// remove any extra whitespace
		$metadata['clean'] = trim($clean);
		$year = $this->parseYearFromFilename($metadata['clean']);
		if ( $year != false )
		{
			$metadata['year'] = $year;
		}

		$issue = $this->parseIssueFromFilename($metadata['clean']);
		if ( $issue != false )
		{
			$metadata['issue'] = $issue;
		}

		$volume = $this->parseVolumeFromFilename($metadata['clean']);
		if ( $volume != false )
		{
			$metadata['volume'] = $volume;
		}

		$name = $this->parsePublicationNameFromFilename($metadata['clean']);
		if ( $name != false )
		{
			$metadata['name'] = trim($name);
		}

		return $metadata;
	}

	public function updateFileMetaData($metadata = null, $override = true)
	{
		$fileparts = isset($metadata) ? $metadata : array();
		$parts = $this->parsedValues();

		foreach ($parts as $key => $value) {
			if (isset($fileparts[$key]) == false || $override) {
				$fileparts[$key] = $value;
			}
		}
		return $fileparts;
	}
}

?>
