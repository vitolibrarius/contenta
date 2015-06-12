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
	const STORY_ARC_FIELDS =	"id,aliases,deck,description,first_appeared_in_issue,image,issues,name,publisher,site_detail_url";
	const VOLUME_FIELDS =		"id,aliases,characters,deck,description,first_issue,image,issues,name,publisher,site_detail_url,start_year";
	const VOLUME_SHORT_FIELDS =		"id,aliases,deck,description,image,name,publisher,site_detail_url,start_year";
	const ISSUE_FIELDS =		"id,aliases,character_credits,cover_date,deck,description,image,issue_number,name,person_credits,site_detail_url,story_arc_credits,volume";
	const PERSON_FIELDS =		"id,aliases,birth,country,created_characters,deck,description,gender,hometown,image,issues,name,site_detail_url,story_arc_credits,volume_credits";

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

	public function characterDetails( $id )
	{
		$query = array("field_list" => ComicVineConnector::CHARACTER_FIELDS);
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
			$params['query'] = urlencode($query_string);
		}
		else {
			throw new ComicVineParameterException("Unable to search for query of type " . var_export($query_string, true));
		}

		$search_url = $this->endpointBaseURL() . "/search/?" . http_build_query($params);
		return $this->performRequest( $search_url );
	}


	public function queryForSeriesName($name, $strict = false)
	{
		$query_string = $name;
		if ( $strict ) {
			$query_string = implode( ' AND ', explode(' ', strtolower($name)));
		}

		$params = $this->defaultParameters();
		$params = array_merge($params, array(
			"field_list" => ComicVineConnector::VOLUME_FIELDS,
			"sort" => "name"
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
		$query_string = $name;
		if ( $strict ) {
			$query_string = implode( ' AND ', explode(' ', strtolower($name)));
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
		if ( is_int( $year ) == false ) {
			$year = intval($year);
		}

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

				return $this->searchForIssuesMatchingSeriesAndYear($matchVolId, $issueNum, $year);
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

					if (abs($coverDate['year'] - intval($year)) <= 2) {
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

	public function performRequest($url, $force = false)
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

