<?php

namespace connectors;

use \Config as Config;
use \Logger as Logger;
use \Model as Model;
use \Cache as Cache;
use \Database as Database;

use exceptions\EndpointConnectionException as EndpointConnectionException;

use model\Endpoint as Endpoint;
use model\EndpointDBO as EndpointDBO;

class ComicVineParameterException extends \Exception {}

/**
 * Class Processor
 */
class ComicVineConnector extends JSON_EndpointConnector
{
	const RESOURCE_ISSUE = 'issue';
	const RESOURCE_CHARACTER = 'character';
	const RESOURCE_PUBLISHER = 'publisher';
	const RESOURCE_CONCEPT = 'concept';
	const RESOURCE_LOCATION = 'location';
	const RESOURCE_PERSON = 'person';
	const RESOURCE_STORY_ARC = 'story_arc';
	const RESOURCE_VOLUME = 'volume';

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
	const VOLUME_FIELDS =		"id,aliases,characters,deck,description,first_issue,image,issues,name,publisher,site_detail_url,start_year,count_of_issues";
	const VOLUME_SHORT_FIELDS =	"id,aliases,deck,description,image,name,publisher,site_detail_url,start_year,count_of_issues";
	const ISSUE_FIELDS =		"id,aliases,character_credits,cover_date,deck,description,image,issue_number,name,person_credits,site_detail_url,story_arc_credits,volume";
	const PERSON_FIELDS =		"id,aliases,birth,country,created_characters,deck,description,gender,hometown,image,issues,name,site_detail_url,story_arc_credits,volume_credits";

	public $trace = false;

	public static function allResourceNames()
	{
		return array(
			ComicVineConnector::RESOURCE_ISSUE, ComicVineConnector::RESOURCE_CHARACTER, ComicVineConnector::RESOURCE_PUBLISHER,
			ComicVineConnector::RESOURCE_CONCEPT, ComicVineConnector::RESOURCE_LOCATION, ComicVineConnector::RESOURCE_PERSON,
			ComicVineConnector::RESOURCE_STORY_ARC, ComicVineConnector::RESOURCE_VOLUME
		);
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
			list($details, $headers) = $this->performGET( $detail_url, false );
		}
		catch ( \Exception $e ) {
			Logger::logException( $e );
			try {
				list($details, $headers) = $this->performGET( $detail_url, true );
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
		return $this->details(ComicVineConnector::RESOURCE_STORY_ARC, ComicVineConnector::TYPEID_STORY_ARC, $id, $query);;
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

		return $this->details(ComicVineConnector::RESOURCE_CHARACTER, ComicVineConnector::TYPEID_CHARACTER, $id, $query);
	}

	public function publisherDetails( $id )
	{
		$query = array("field_list" => ComicVineConnector::PUBLISHER_FIELDS);
		return $this->details(ComicVineConnector::RESOURCE_PUBLISHER, ComicVineConnector::TYPEID_PUBLISHER, $id, $query);
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
		return $this->details(ComicVineConnector::RESOURCE_VOLUME, ComicVineConnector::TYPEID_VOLUME, $id, $query);
	}

	public function issueDetails( $id )
	{
		$query = array("field_list" => ComicVineConnector::ISSUE_FIELDS);
		return $this->details(ComicVineConnector::RESOURCE_ISSUE, ComicVineConnector::TYPEID_ISSUE, $id, $query);
	}

	public function personDetails( $id )
	{
		$query = null; //array("field_list" => ComicVineConnector::PERSON_FIELDS);
		return $this->details(ComicVineConnector::RESOURCE_PERSON, ComicVineConnector::TYPEID_PERSON, $id, $query);
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
			$params['query'] = normalizeSearchString( $query_string );
		}
		else {
			throw new ComicVineParameterException("Unable to search for query of type " . var_export($query_string, true));
		}

		$search_url = $this->endpointBaseURL() . "/search/?" . http_build_query($params);
		list($details, $headers) = $this->performGET( $search_url );
		return $details;
	}

	private function addFilter( array &$filters, $key, $value )
	{
		if (is_array($value) && count($value) > 0) {
			$filters[$key] = implode( '|', $value);
		}
		else if ((is_string($value) && strlen($value) > 0) || (is_integer($value) && $value > 0)) {
			$filters[$key] = $value;
		}
	}

	private function addIntFilter( array &$filters, $key, $value )
	{
		if (is_array($value) && count($value) > 0) {
			$filters[$key] = implode( '|', array_kmap(
					function($k, $v) {
						return intval($v);
					},
					$value
				)
			);
		}
		else if ((is_string($value) && strlen($value) > 0) || (is_integer($value) && $value > 0)) {
			$filters[$key] = intval($value);
		}
	}

	private function addStrFilter( array &$filters, $key, $value )
	{
		if (is_array($value) && count($value) > 0) {
			$filters[$key] = implode( '|', array_kmap(
					function($k, $v) {
						return normalizeSearchString($v);
					},
					$value
				)
			);
		}
		else if (is_string($value) && strlen($value) > 0) {
			$filters[$key] = normalizeSearchString($value);
		}
	}

	private function addDateRangeForYear( array&$filters, $key, $year = null )
	{
		if (is_null($year) == false ) {
			// YYYY-MM-DD|YYYY-MM-DD
			$year = intval($year);
			$filters[$key] = $year . "-01-01|" . $year . "-12-31";
		}
	}

	public function resource_filtered( $resource, array $filters, $sort = null, $fields = null)
	{
		if ( isset($resource) == false || is_string($resource) == false || in_array($resource, ComicVineConnector::allResourceNames()) == false ) {
			throw new ComicVineParameterException("Unable to resource_filtered for resource of type " . var_export($resource, true));
		}

		if ( isset($filters) == false || is_array($filters) == false || count($filters) == false ) {
			throw new ComicVineParameterException("Unable to resource_filtered for filters of type " . var_export($filters, true));
		}

		$params = $this->defaultParameters();
		if (is_array($sort) && count($sort) > 0) {
			$params['sort'] = implode( ',', $sort);
		}
		else if ( is_string($sort)) {
			$params['sort'] = $sort;
		}

		if (is_array($fields) && count($fields) > 0) {
			$params['field_list'] = implode(',', $fields);
		}
		else if ( is_string($fields)) {
			$params['field_list'] = $fields;
		}

		$filter_array = array();
		foreach( $filters as $attribute => $f ) {
			$filter_array[] = $attribute . ":" . $f;
		}
		$params['filter'] = implode( ',', $filter_array);

		$search_url = $this->endpointBaseURL() ."/". $resource . "s/?" . http_build_query($params);
// 		echo $this->cleanURLForLog($search_url) . PHP_EOL;
		list($details, $headers) = $this->performGET( $search_url );
		return $details;
	}

	public function character_search($xid = null, $name = null, $gender = null)
	{
		$filters = array();
		$this->addIntFilter( $filters, 'id', $xid );
		$this->addStrFilter( $filters, 'name', $name );

		if ( is_string($gender) && strlen($gender) > 0) {
			$filters['gender'] = $gender;
		}

		return $this->resource_filtered(
			ComicVineConnector::RESOURCE_CHARACTER,
			$filters,
			array("name"),
			ComicVineConnector::CHARACTER_SHORT_FIELDS
		);
	}

	public function publisher_search($xid = null, $name = null, $aliases = null)
	{
		$filters = array();
		$this->addIntFilter( $filters, 'id', $xid );
		$this->addStrFilter( $filters, 'name', $name );
		$this->addStrFilter( $filters, 'alias', $aliases );
		return $this->resource_filtered(
			ComicVineConnector::RESOURCE_PUBLISHER,
			$filters,
			array("name"),
			ComicVineConnector::PUBLISHER_FIELDS
		);
	}

	public function story_arc_search($xid = null, $name = null, $aliases = null)
	{
		$filters = array();
		$this->addIntFilter( $filters, 'id', $xid );
		$this->addStrFilter( $filters, 'name', $name );
		$this->addStrFilter( $filters, 'alias', $aliases );
		return $this->resource_filtered(
			ComicVineConnector::RESOURCE_STORY_ARC,
			$filters,
			array("name"),
			ComicVineConnector::STORY_ARC_FIELDS
		);
	}

	public function series_search($xid = null, $name = null)
	{
		$filters = array();
		$this->addIntFilter( $filters, 'id', $xid );
		$this->addStrFilter( $filters, 'name', $name );
		return $this->resource_filtered(
			ComicVineConnector::RESOURCE_VOLUME,
			$filters,
			array("name"),
			ComicVineConnector::VOLUME_FIELDS
		);
	}

	public function issue_search($xid = null, $vol = null, $name = null, $aliases = null, $coverYear = null, $issue_number = null)
	{
		$filters = array();
		$this->addIntFilter( $filters, 'id', $xid );
		$this->addIntFilter( $filters, 'volume', $vol );
		$this->addDateRangeForYear( $filters, 'cover_date', $coverYear );
		$this->addIntFilter( $filters, 'issue_number', $issue_number );
		$this->addStrFilter( $filters, 'name', $name );
		$this->addStrFilter( $filters, 'alias', $aliases );

		if ( $this->trace) echo "	filters - " . var_export($filters, true) . PHP_EOL;

		return $this->resource_filtered(
			ComicVineConnector::RESOURCE_ISSUE,
			$filters,
			array("cover_date","issue_number"),
			ComicVineConnector::ISSUE_FIELDS
		);
	}

	private function series_filterForIssueYear( array $possible, $year = 0 )
	{
		if ( is_array($possible) && count($possible) > 0 ) {
			$filtered = array();
			$year = intval($year);

			foreach( $possible as $key => $item ) {
				$countOfIssues = max(1, isset($item['count_of_issues']) ? intval($item['count_of_issues']) : 1);
				$itemStartYear = isset($item['start_year']) ? intval($item['start_year']) : 0;
				//$yearDiffRange = ceil($countOfIssues/12) * 1.6;
				if ($year == 0 || ($year >= $itemStartYear)) { // && $year - $itemStartYear <= $yearDiffRange)) {
					if ( $this->trace)  echo "accepting $itemStartYear - $countOfIssues - " . $item["name"] . PHP_EOL;
					$filtered[] = $item;
				}
				else if ( $this->trace) {
					echo "rejecting $itemStartYear - $countOfIssues ||" . $item["id"] . " - ". $item["name"] . PHP_EOL;
				}
			}

			return $filtered;
		}
		return $possible;
	}

	private function series_filterForCloseName( array $possible, $name = null, $margin = 10 )
	{
		if ( is_array($possible) && count($possible) > 0 && is_string($name) && strlen($name) > 0) {
			$filtered = array();

			$margin = min( $margin, (strlen($name)/2) );
			foreach( $possible as $key => $item ) {
				 if ( levenshtein( $name, $item["name"] ) < $margin ) {
					if ( $this->trace) echo "accepting levenshtein(" . levenshtein( $name, $item["name"] ) . ") " . $item["name"] . PHP_EOL;
					$filtered[] = $item;
				}
				else if ( $this->trace)  {
					echo "rejecting levenshtein(" . levenshtein( $name, $item["name"] ) . ") " . $item["name"] . PHP_EOL;
				}
			}

			return $filtered;
		}
		return $possible;
	}

	public function series_searchFilteredForYear( $name, $year = 0)
	{
		$results = $this->series_search( null, $name );
		if ( $this->trace) echo "	series_searchFilteredForYear($name, $year) =" . count($results) . PHP_EOL;
		$results = $this->series_filterForCloseName( $results, $name, 10 );
		if ( $this->trace) echo "	series_searchFilteredForYear($name, $year) close =" . count($results) . PHP_EOL;
		$results = $this->series_filterForIssueYear( $results, $year );
		if ( $this->trace) echo "	series_searchFilteredForYear($name, $year) year =" . count($results) . PHP_EOL;
		if ( is_array($results) == false || count($results) == 0 ) {
			// try a fuzzy search
			$results = $this->search( ComicVineConnector::RESOURCE_VOLUME, $name );
			if ( $this->trace) echo "	series_searchFilteredForYear($name, $year) fuzzy =" . count($results) . PHP_EOL;
			$results = $this->series_filterForCloseName( $results, $name, 10 );
			if ( $this->trace) echo "	series_searchFilteredForYear($name, $year) fuzzy close =" . count($results) . PHP_EOL;
			$results = $this->series_filterForIssueYear( $results, $year );
			if ( $this->trace) echo "	series_searchFilteredForYear($name, $year) fuzzy year =" . count($results) . PHP_EOL;
		}

		return $results;
	}

	public function issue_searchFilteredForSeriesYear( $issueNumber = 0, $name, $year = 0 )
	{
		$results = $this->series_searchFilteredForYear( $name, $year );
		if ( is_array($results) && count($results) > 0 ) {
			$matchVolumeId = array_kmap(
				function($k, $v) {
					return $v['id'];
				},
				$results
			);

			if ( $this->trace) echo "issue_searchFilteredForSeriesYear volumes = " . var_export($matchVolumeId, true) .PHP_EOL;
			$results = $this->issue_search( null, $matchVolumeId, null, null, $year, $issueNumber);
			if ( $this->trace) echo  "issue_searchFilteredForSeriesYear results = " . count($results) . PHP_EOL;

			if ( is_array($results) ) {
				usort($results, function($a, $b) use ($name) {
					$a_series_name = array_valueForKeypath( "volume/name", $a );
					$b_series_name = array_valueForKeypath( "volume/name", $b );
					return (levenshtein( $a_series_name, $name ) < levenshtein( $b_series_name, $name )) ? -1 : 1;
				});
			}
		}
		else if ( $this->trace) {
			Logger::logError( "issue_searchFilteredForSeriesYear no results" );
		}
		return $results;
	}

	public function performGET($url, $force = true)
	{
		list($json, $headers) = parent::performGET($url, $force);
		if ( $json != false )
		{
			if ( $json['status_code'] == 1)
			{
				return array($json['results'], $headers);
			}
			else
			{
				Logger::logError( 'Error ' . $json['status_code'] . ': ' . $json['error']
					. 'with URL: ' . $this->cleanURLForLog($url), get_short_class($this), $this->endpoint());
				throw new EndpointConnectionException( $json['error'], $json['status_code'] );
			}
		}
		return false;
	}
}

