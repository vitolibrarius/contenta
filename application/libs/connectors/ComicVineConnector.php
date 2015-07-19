<?php

namespace connectors;

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
	const TYPEID_PERSON = '4040';
	const TYPEID_STORY_ARC = '4045';
	const TYPEID_VOLUME = '4050';

	const PUBLISHER_FIELDS =	"id,name,image,deck,description,story_arcs,site_detail_url";
	const CHARACTER_FIELDS =	"id,image,name,real_name,aliases,gender,publisher,deck,description,story_arc_credits,site_detail_url";
	const CHARACTER_SHORT_FIELDS =	"id,image,name,real_name,aliases,gender,publisher,deck,description,site_detail_url";
	const STORY_ARC_FIELDS =	"id,aliases,deck,description,first_appeared_in_issue,image,issues,name,publisher,site_detail_url";
	const VOLUME_FIELDS =		"id,aliases,characters,deck,description,first_issue,image,issues,name,publisher,site_detail_url,start_year";
	const VOLUME_SHORT_FIELDS =		"id,aliases,deck,description,image,name,publisher,site_detail_url,start_year";
	const ISSUE_FIELDS =		"id,aliases,character_credits,cover_date,deck,description,image,issue_number,name,person_credits,site_detail_url,story_arc_credits,volume";
	const PERSON_FIELDS =		"id,aliases,birth,country,created_characters,deck,description,gender,hometown,image,issues,name,site_detail_url,story_arc_credits,volume_credits";

	public static function normalizeQueryString( $query_string = null )
	{
		if ( is_null($query_string) == false ) {
			$query_string = strtolower($query_string);
			$query_string = preg_replace("/[^[:alnum:][:space:]]/ui", '', $query_string);
			$query_string = preg_replace('/\s+/', ' ', $query_string);
		}
		return $query_string;
	}

	public function __construct($point)
	{
		parent::__construct($point);
		if ( empty($this->endpointBaseURL()) || empty($this->endpointAPIKey()) ) {
			throw new InvalidEndpointConfigurationException("Missing base URL or API key");
		}
	}

	public function defaultParameters() {
		$defaultParam = array();
		$defaultParam["format"] = "json";

		if ( empty($this->endpointAPIKey()) == false ) {
			$defaultParam["api_key"] = $this->endpointAPIKey();
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

		try {
			$details = $this->performRequest( $detail_url, false );
		}
		catch ( \Exception $e ) {
			Logger::logException( $e );
			try {
				$details = $this->performRequest( $detail_url, true );
			}
			catch ( \Exception $e2 ) {
				Logger::logException( $e2 );
			}
		}
		return (isset($details) && is_array($details) ? $details : false);
	}

	public function story_arcDetails( $id )
	{
		$query = array("field_list" => ComicVineConnector::STORY_ARC_FIELDS);
		return $this->details('story_arc', ComicVineConnector::TYPEID_STORY_ARC, $id, $query);;
	}

	public function characterDetails( $id, $shortDetails = false )
	{
		$query = array();
		if ( $shortDetails ) {
			$query["field_list"] = ComicVineConnector::CHARACTER_SHORT_FIELDS;
		}
		else {
			$query["field_list"] = ComicVineConnector::CHARACTER_FIELDS;
		}

		return $this->details('character', ComicVineConnector::TYPEID_CHARACTER, $id, $query);
	}

	public function publisherDetails( $id )
	{
		$query = array("field_list" => ComicVineConnector::PUBLISHER_FIELDS);
		return $this->details('publisher', ComicVineConnector::TYPEID_PUBLISHER, $id, $query);
	}

	public function seriesDetails( $id, $shortDetails = false )
	{
		$query = array();
		if ( $shortDetails ) {
			$query["field_list"] = ComicVineConnector::VOLUME_SHORT_FIELDS;
		}
		else {
			$query["field_list"] = ComicVineConnector::VOLUME_FIELDS;
		}
		return $this->details('volume', ComicVineConnector::TYPEID_VOLUME, $id, $query);
	}

	public function issueDetails( $id )
	{
		$query = array("field_list" => ComicVineConnector::ISSUE_FIELDS);
		return $this->details('issue', ComicVineConnector::TYPEID_ISSUE, $id, $query);
	}

	public function personDetails( $id )
	{
		$query = null; //array("field_list" => ComicVineConnector::PERSON_FIELDS);
		return $this->details('person', ComicVineConnector::TYPEID_PERSON, $id, $query);
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
			$params['query'] = ComicVineConnector::normalizeQueryString( $query_string );
		}
		else {
			throw new ComicVineParameterException("Unable to search for query of type " . var_export($query_string, true));
		}

		$search_url = $this->endpointBaseURL() . "/search/?" . http_build_query($params);
		return $this->performRequest( $search_url );
	}


	public function queryForSeriesName($name, $strict = false)
	{
		$query_string = ComicVineConnector::normalizeQueryString( $name );
		if ( $strict ) {
			$query_string = implode( ',', preg_split('/\s+/', $query_string));
		}

		$params = $this->defaultParameters();
		$params = array_merge($params, array(
			"field_list" => ComicVineConnector::VOLUME_FIELDS,
			"sort" => "start_year,name"
			)
		);

		if ( is_string($query_string)) {
			$params['filter'] = 'name:' . $query_string;
		}
		else {
			throw new ComicVineParameterException("Unable to search for query of type " . var_export($query_string, true));
		}

		$search_url = $this->endpointBaseURL() . "/volumes/?" . http_build_query($params);
		return $this->performRequest( $search_url );
	}

	public function queryForPublisherName($name, $strict = false)
	{
		$query_string = ComicVineConnector::normalizeQueryString( $name );
		if ( $strict ) {
			$query_string = implode( ',', preg_split('/\s+/', $name));
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
		$query_string = ComicVineConnector::normalizeQueryString( $name );
		if ( $strict ) {
			$query_string = implode( ',', preg_split('/\s+/', $seriesName));
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

	public function queryForSeriesNameAndYear($seriesName = null, $year = null)
	{
		if ( is_string($seriesName) && strlen($seriesName) > 1 ) {
			$json = $this->queryForSeriesName( $seriesName, true );
			if ( $json != false ) {
				return $this->filterSeriesResultForYear($json, $year);
			}
			else {
				$json = $this->queryForSeriesName( $seriesName, false );
				if ( $json != false ) {
					return $this->filterSeriesResultForYear($json, $year);
				}
				else {
					Logger::logInfo( 'queryForSeries - Search Failed', get_short_class($this), $this->endpoint());
				}
			}
		}
		return false;
	}

	function filterSeriesResultForYear(Array $results = array(), $year = 0)
	{
		$filtered = array();
		$year = intval($year);

		foreach( $results as $key => $item ) {
			$itemStartYear = isset($item['start_year']) ? intval($item['start_year']) : 0;
			if ($year == 0 || $year >= $itemStartYear) {
				$filtered[] = $item;
			}
		}

		return $filtered;
	}

	public function searchForIssue($seriesName = null, $issueNum = null, $year = null)
	{
		if ( is_string($seriesName) ) {
			$seriesPossible = $this->queryForSeriesNameAndYear($seriesName, $year);
			if ( $seriesPossible != false ) {
				$matchVolumeId = array();
				foreach ($seriesPossible as $key => $item) {
					$matchVolId[] = $item['id'];
				}

				$possible = $this->searchForIssuesMatchingSeriesAndYear($matchVolId, $issueNum, $year);
				if ( is_array($possible) && count($possible) > 5 ) {
					$possible = array_filter($possible, function($v) use($seriesName){
							$l = levenshtein ( $seriesName , array_valueForKeypath( "volume/name", $v) );
							return ($l < 10);
						}
					);
				}
				return $possible;
			}
		}
		else {
			Logger::logInfo( 'searchForIssue - Not enough search data ' . var_export($metadata, true),
				get_short_class($this), $this->endpoint());
		}
		return false;
	}

	public function searchForIssuesMatchingSeriesAndYear( Array $volumeIdArray = array(), $issueNum = null, $year = null)
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

		$params = $this->defaultParameters();
		$params = array_merge($params, array(
			"field_list" => ComicVineConnector::ISSUE_FIELDS,
			"sort" => "name",
			"filter" => $volumeFilter . $issueFilter
			)
		);

		$search_url = $this->endpointBaseURL() . "/issues/?" . http_build_query($params);
		$candidate = $this->performRequest( $search_url );

		if ($candidate != false) {
			if ( count($candidate) == 1) {
				return $candidate;
			}

			if ( isset( $year ) && intval($year) > 1900)
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

						if (abs($coverDate['year'] - intval($year)) <= 2) {
							$filterWithinMargin[] = $value;
						}
					}
				}

				if ( count($filterMatch) == 0 ) {
					return (count($filterWithinMargin) > 0 ? $filterWithinMargin : false);
				}
				else {
					return $filterMatch;
				}
			}
		}

		return $candidate;
	}

	public function performRequest($url, $force = true)
	{
		$json = parent::performRequest($url, $force);
		if ( $json != false )
		{
			if ( $json['status_code'] == 1)
			{
				return $json['results'];
			}
			else
			{
				Logger::logError( 'Error ' . $json['status_code'] . ': ' . $json['error']
					. 'with URL: ' . $this->cleanURLForLog($url), get_short_class($this), $this->endpoint());
				return $json['status_code'] . ': ' . $json['error'];
			}
		}
		return false;
	}
}

