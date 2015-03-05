<?php

namespace utilities;

use \Logger as Logger;
use \Cache as Cache;
use \ClassNotFoundException as ClassNotFoundException;

class MediaFilename
{
	public function __construct($filename)
	{
		$this->sourcename = $filename;
	}

	public function parseYearFromFilename( $filename )
	{
		if (preg_match_all("/\\b(\\d\\d\\d\\d)\\b/u", $filename, $matches, 0))
		{
			$matches = array_reverse($matches[1]);
			$curYear = intval(date("Y"));
			foreach ($matches as $key => $value) {
				$year = intval($value);
				if ( $year > 1980 && $year <= $curYear) {
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
				if (preg_match("/^[-]?(([0-9]*\\.[0-9]+|[0-9]{1,3}))\\b/um", $value, $matches, 0))
				{
					return $matches[0];
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
		$working = preg_replace("/((v|vol|book)[\\.\\s]*\\d{1,4})|(\\d[\\.\\s]*covers)/uim", "", $filename);

		// remove one shot
		$working = preg_replace("/\\b(tpb|os|one[ -]shot|ogn|gn)/uim", "", $working);

		// everything up to issue\syear like XYZ Comic 001 2014
		if (preg_match("/(^.+)(?=\\b[-]?(([0-9]*\\.[0-9]+|[0-9]{1,4}))\\s+(\\d\\d\\d\\d)).+/u", $working, $matches, 0))
		{
			return $matches[1];
		}

		// everything up to issue\syear like XYZ Comic 001 2014
		if (preg_match("/(^.+)((\\d{3})\\s+(\\d{4})).+/u", $working, $matches, 0))
		{
			return $matches[1];
		}

		// everything up to the year
		if (preg_match("/(^.+)(?=\\b[-]?(\\d{4})).+/u", $working, $matches, 0))
		{
			return $matches[1];
		}

		// all non-numeric text
		if (preg_match("/(^\\D+)\\s*/u", $working, $matches, 0))
		{
			return $matches[1];
		}

		$list = explode(' ', $working );
		if ( count($list) == 1)
		{
			return $list[0];
		}


		return false;
	}



	public function parsedValues()
	{
		$metadata = array();

		$ext = file_ext($this->sourcename);
		if ( isset($ext) && strlen($ext) > 0) {
			$metadata['extension'] = strtolower($ext);
		}

		$clean = file_ext_strip($this->sourcename);
		if (substr_count($clean, "_28") > 1 && substr_count($filename, "_29") > 1)
		{
			$clean = str_replace("_28", "(", $clean);
			$clean = str_replace("_29", ")", $clean);
		}

		$clean = str_replace("+", " ", $clean);
		$clean = str_replace("#", " ", $clean);

		// remove the file uniquing number system.  eg: Swamp Thing 007 (2012) (2 covers) (Megan-Empire).1.cbz
		$clean = preg_replace('/\.1$/', '', $clean);

		// replace parenthetical phrases with spaces
		$clean = preg_replace("/\\((.*?)\\)/us", " $1 ", $clean);
		$clean = preg_replace("/\\[(.*?)\\]/u", " $1 ", $clean);

		// remove '-' and '_'
		$clean = preg_replace("/([-_])/u", " ", $clean);

		// remove any "of NN" phrase
		$clean = preg_replace("/(of [\\d]+)/ui", " ", $clean);

		// remove multiple spaces with single spaces
		$clean = preg_replace("/(  +)/u", " ", $clean);

		$metadata['clean'] = $clean;
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