<?php

namespace endpoints;

use \Config as Config;
use \Logger as Logger;
use \Model as Model;
use \Cache as Cache;
use \Database as Database;

use model\Endpoint as Endpoint;
use model\EndpointDBO as EndpointDBO;

class ComicVineParameterException extends \Exception {}

/**
 * Class Processor
 */
class ComicVineConnector extends JSON_EndpointConnector
{
	const TYPEID_ISSUE = '4000';
	const TYPEID_CHARACTER = '4005';
	const TYPEID_PUBLISHER = '4010';
	const TYPEID_CONCEPT = '4015';
	const TYPEID_LOCATION = '4015';
	const TYPEID_VOLUME = '4050';

	const PUBLISHER_FIELDS = "id,name,api_detail_url,image,deck,description,story_arcs";
	const CHARACTER_FIELDS = "id,image,name,real_name,aliases,api_detail_url,gender,publisher,deck,description,story_arc_credits,date_last_updated";

	public function __construct($point)
	{
		parent::__construct($point);
		if ( empty($this->endpointBaseURL()) || empty($this->endpointAPIKey()) ) {
			throw new InvalidEndpointConfigurationException("Missing base URL or API key");
		}
	}

	public function defaultParameters() {
		if ( isset($defaultParam) == false ) {
			$defaultParam = array(
				"api_key" => $this->endpointAPIKey(),
				"format" => "json"
			);
		}
		return $defaultParam;
	}

	/*
	 * Detail endpoint requests
	 */
	public function details($resource, $type = null, $id, $additionalParams = null) {
		$params = $this->defaultParameters();

		if ( is_array($additionalParams) ) {
			$params = array_merge($params, $additionalParams);
		}

		$detail_url = $this->endpointBaseURL() . "/" . $resource . "/"
			. ($type == null ? '' : $type . "-") . $id . "/?" . http_build_query($params);
		return $this->performRequest( $detail_url );
	}

	public function characterDetails( $id )
	{
		$query = array("field_list" => ComicVineConnector::CHARACTER_FIELDS);
		$details = false;
		try {
			$details = $this->details('character', ComicVineConnector::TYPEID_CHARACTER, $id, $query);
		}
		catch ( \Exception $e ) {
			Logger::logException( $e );
			try {
				$details = $this->details('character', ComicVineConnector::TYPEID_CHARACTER, $id, $query);
			}
			catch ( \Exception $e2 ) {
				Logger::logException( $e2 );
			}
		}

		if ( $details != false ) {
			$list =array("icon_url", "medium_url", "screen_url", "small_url", "super_url", "thumb_url", "tiny_url");
			foreach($list as $n) {
				if ( isset($details['image'][$n]) ) {
					downloadImage($details['image'][$n], $this->debugPath(), "character_" . $id . "_" . $n );
				}
			}
		}
		return $details;
	}

	public function publisherDetails( $id )
	{
		$query = array("field_list" => ComicVineConnector::PUBLISHER_FIELDS);
		$details = false;
		try {
			$details = $this->details('publisher', ComicVineConnector::TYPEID_PUBLISHER, $id, $query);
		}
		catch ( \Exception $e ) {
			Logger::logException( $e );
			try {
				$details = $this->details('publisher', ComicVineConnector::TYPEID_PUBLISHER, $id, $query);
			}
			catch ( \Exception $e2 ) {
				Logger::logException( $e2 );
			}
		}

		if ( $details != false ) {
			$list =array("icon_url", "medium_url", "screen_url", "small_url", "super_url", "thumb_url", "tiny_url");
			foreach($list as $n) {
				if ( isset($details['image'][$n]) ) {
					downloadImage($details['image'][$n], $this->debugPath(), "publisher_" . $id . "_" . $n );
				}
			}
		}
		return $details;
	}

	public function volumeDetails( $id )
	{
		return $this->details('volume', TYPEID_VOLUME, $id, null);
	}

	public function issueDetails( $id )
	{
		return $this->details('issue', TYPEID_ISSUE, $id, null);
	}


	/*
	 * Query endpoint requests
	 */
	public function search( $resources, $query_string, $additionalParams = null) {
		$params = $this->defaultParameters();

		if ( is_array($additionalParams) ) {
			$params = array_merge($params, $additionalParams);
		}

		if (is_array($resources)) {
			$params['resources'] = implode(',', $resources);
		}
		else if ( is_string($resources)) {
			$params['resources'] = $resources;
		}
		else {
			throw new ComicVineParameterException("Unable to search for resources of type " . var_export($resources, true));
		}

		if ( is_string($query_string)) {
			$params['query'] = $query_string;
		}
		else {
			throw new ComicVineParameterException("Unable to search for query of type " . var_export($query_string, true));
		}

		$search_url = $this->endpointBaseURL() . "/search/?" . http_build_query($params);
		return $this->performRequest( $search_url );
	}


	public function queryForVolumeName($name, $strict = false)
	{
		$query_string = $name;
		if ( $strict ) {
			$query_string = implode( ' AND ', explode(' ', strtolower($seriesName)));
		}
		return $this->search( "volume", $query_string, array(
			"field_list" => "name,id,start_year,publisher,image,description,count_of_issues",
			"sort" => "name,start_year"
			)
		);
	}

	public function queryForPublisherName($name, $strict = false)
	{
		$query_string = $name;
		if ( $strict ) {
			$query_string = implode( ' AND ', explode(' ', strtolower($seriesName)));
		}

		$params = $this->defaultParameters();
		$params = array_merge($params, array(
			"field_list" => ComicVineConnector::PUBLISHER_FIELDS,
			"sort" => "name"
			)
		);

		if ( is_string($query_string)) {
			$params['filter'] = 'name:' . $query_string;
		}
		else {
			throw new ComicVineParameterException("Unable to search for query of type " . var_export($query_string, true));
		}
		$search_url = $this->endpointBaseURL() . "/publishers/?" . http_build_query($params);
		return $this->performRequest( $search_url );
	}

	public function queryForCharacterName($name, $strict = false)
	{
		$query_string = $name;
		if ( $strict ) {
			$query_string = implode( ' AND ', explode(' ', strtolower($seriesName)));
		}

		$params = $this->defaultParameters();
		$params = array_merge($params, array(
			"field_list" => ComicVineConnector::CHARACTER_FIELDS,
			"sort" => "name"
			)
		);

		if ( is_string($query_string)) {
			$params['filter'] = 'name:' . $query_string;
		}
		else {
			throw new ComicVineParameterException("Unable to search for query of type " . var_export($query_string, true));
		}
		$search_url = $this->endpointBaseURL() . "/characters/?" . http_build_query($params);
		return $this->performRequest( $search_url );
	}

	public function performRequest($url)
	{
		$json = parent::performRequest($url);
		if ( $json != false )
		{
			if ( $json['status_code'] == 1)
			{
				return $json['results'];
			}
			else
			{
				Logger::logError( $json['status_code'] . ': ' . $json['error'], get_class($this), basename($this->sourceDir));
				return $json['status_code'] . ': ' . $json['error'];
			}
		}
		return false;
	}
}


/*


	public function searchForIssue($metadata)
	{
		$fileparts = $metadata['fileparts'];
		if ( isset($fileparts, $fileparts['name']) )
		{
			$searchKeys = array(
				'series' => $fileparts['name'],
				'issue_number' => isset($fileparts['issue']) ? $fileparts['issue'] : '',
				'year' => isset($fileparts['year']) ? $fileparts['year'] : ''
			);

			$seriesPossible = $this->queryForSeries($searchKeys);
			if ( $seriesPossible != false )
			{
				$matchVolId = array();
				foreach ($seriesPossible as $key => $item)
				{
					$matchVolId[] = $item['id'];
				}

				$matches = $this->issuesForVolumeIssueYear($matchVolId, $searchKeys['issue_number'], $searchKeys['year']);
				file_put_contents( appendPath($this->sourceDir, 'comicvine_searchForIssue.json'),
					json_encode($matches, JSON_PRETTY_PRINT)
				);

				return $matches;
			}
		}
		else
		{
			Logger::logInfo( 'searchForIssue - Not enough search data ' . var_export($searchKeys, true), get_class($this), basename($this->sourceDir) );
		}
		return false;
	}

	public function queryForSeries($searchKeys)
	{
		$json = $this->queryForSeriesName( $searchKeys['series'], true );
		if ( $json != false )
		{
			file_put_contents( appendPath($this->sourceDir, 'comicvine_queryForSeries.json'),
				json_encode($json, JSON_PRETTY_PRINT)
			);
			return $this->filterSeriesResults($json, $searchKeys);
		}
		else
		{
			$json = $this->queryForSeriesName( $searchKeys['series'], false );
			if ( $json != false )
			{
				file_put_contents( appendPath($this->sourceDir, 'comicvine_queryForSeries.json'),
					json_encode($json, JSON_PRETTY_PRINT)
				);
				return $this->filterSeriesResults($json, $searchKeys);
			}
			else {
				Logger::logInfo( 'queryForSeries - Search Failed', get_class($this), basename($this->sourceDir));
			}
		}
		return false;
	}

	function filterSeriesResults($results, $searchKeys)
	{
		$filtered = array();
		$year = isset($searchKeys['year']) ? intval($searchKeys['year']) : 0;
		$nameLength = strlen( $searchKeys['series']);
		foreach( $results as $key => $item )
		{
			$itemStartYear = isset($item['start_year']) ? intval($item['start_year']) : 0;
			if ($year == 0 || $year >= $itemStartYear)
			{
				$filtered[] = $item;
//				$cleanItemName = new MediaFilename($item['name']);
//				$fileparts = $cleanItemName->parsedValues();
//				if ( abs($nameLength - strlen($fileparts['name'])) <= 5 )
//				{
//					$filtered[] = $item;
//				}
			}
		}

		file_put_contents( appendPath($this->sourceDir, 'comicvine_filterSeriesResults.json'),
			json_encode($filtered, JSON_PRETTY_PRINT)
		);

		return $filtered;
	}

	public function queryForSeriesName($seriesName, $strict = false)
	{
		$query_string = $seriesName;
		if ( $strict ) {
			$query_string = implode( ' AND ', explode(' ', strtolower($seriesName)));
		}

		$search_url = $this->endpointBaseURL() . "/search/?api_key=" . $this->endpointAPIKey()
			. "&format=json&resources=volume&query=" . urlencode($query_string)
			. "&field_list=name,id,start_year,publisher,image,description,count_of_issues";

		return $this->performRequest($search_url);
	}

	public function issuesForVolumeIssueYear($volumeIdArray, $issueNum, $year)
	{
		$volumeFilter = 'volume:' . implode( '|', $volumeIdArray );
		$issueFilter = '';
		if (isset($issueNum) && strlen($issueNum) > 0) {
			if (intval($issueNum) > 0) {
				 $issueFilter .= ",issue_number:" . ltrim($issueNum, '0');
			}
			else if (ltrim($issueNum, '0') === '') {
				$issueFilter .= ",issue_number:0";
			}
		}

		$search_url = $this->endpointBaseURL() . "/issues/?api_key=" . $this->endpointAPIKey()
			. "&format=json&filter=" . $volumeFilter . $issueFilter
			. "&field_list=id,volume,issue_number,name,image,cover_date,site_detail_url,description";

		$candidate = $this->performRequest($search_url);
		file_put_contents( appendPath($this->sourceDir, 'comicvine_issuesForVolumeIssueYear.json'),
			json_encode($candidate, JSON_PRETTY_PRINT)
		);

		if ($candidate != false && count($candidate) == 1)
		{
			return $candidate;
		}

		if ( isset( $year ) && intval($year) > 1900 && $candidate != false )
		{
			$filterMatch = array();
			$filterWithinMargin = array();
			foreach ($candidate as $key => $value )
			{
				if ( isset( $value['cover_date'] ) ) {
					$coverDate = getDate(strtotime($value['cover_date']));

					// convert cover_date year to number
					if ( $coverDate['year'] == $year) {
						$filterMatch[] = $value;
					}
					else if (abs($coverDate['year'] - intval($year)) < 2) {
						$filterWithinMargin[] = $value;
					}
				}
			}

			switch ( count($filterMatch) )
			{
				case 0:
					if ( count($filterWithinMargin) > 0 ) {
						return $filterWithinMargin;
					}
					return false;
					break;

				case 1:
					return $filterMatch;
					break;

				default:
					return $filterMatch;
					break;
			}
		}

		return $candidate;
	}


	public function performRequest($url)
	{
		Logger::logInfo( 'Requesting: ' . str_replace ( $this->endpointAPIKey() , '[key]' , $url), get_class($this), basename($this->sourceDir));
		$data = file_get_contents($url);
		if ( $data != false )
		{
			$json = json_decode($data, true);
			if ( json_last_error() != 0 )
			{
				Logger::logError( 'JSON decode error: ' . jsonErrorString(json_last_error()), get_class($this), basename($this->sourceDir));
			}
			else if ( $json['status_code'] == 1)
			{
				return $json['results'];
			}
			else
			{
				Logger::logError( $json['status_code'] . ': ' . $json['error'], get_class($this), basename($this->sourceDir));
			}
		}
		return false;
	}

	function processData()
	{
		$meta = parent::processData();
		if ($meta === false) {
			return false;
		}

		$issue = $this->searchForIssue($meta);
		if ( $issue != false )
		{
			$meta['issue'] = $issue;

			if (count($issue) == 1) {
				$matchingIssue = $issue[0];
				$meta['issue'] = $issue[0];
				$series = $this->volumeDetails($matchingIssue['volume']['id']);
				if ( $series != false )
				{
					$meta['volume'] = $series;
				}
				else
				{
					Logger::logWarning( "failed to find volume", get_class($this), basename($this->sourceDir));
				}
			}
			else {
				Logger::logWarning( "found multiple matching issues", get_class($this), basename($this->sourceDir));
			}
		}
		else
		{
			Logger::logWarning( "Failed to find issue", get_class($this), basename($this->sourceDir));
		}

		$this->writeMetadata($meta);
		return true;
	}
}
*/
