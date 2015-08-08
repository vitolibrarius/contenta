<?php

namespace connectors;

use \Config as Config;
use \Logger as Logger;
use \Model as Model;
use \Cache as Cache;
use \Database as Database;

use model\Endpoint as Endpoint;
use model\EndpointDBO as EndpointDBO;

class NewznabException extends \Exception {}

class NewznabConnector extends RSSConnector
{
	const NEWZNAB_CAT_BOOK = 7000;
	const NEWZNAB_CAT_COMIC = 7030;
	const NEWZNAB_CAT_EBOOK = 7020;

	public function __construct($endpoint)
	{
		parent::__construct($endpoint);
	}

	public function defaultParameters() {
		$defaultParam = array();
		$defaultParam["o"] = "xml";

		if ( empty($this->endpointAPIKey()) == false ) {
			$defaultParam["apikey"] = $this->endpointAPIKey();
		}
		return $defaultParam;
	}

	public function capabilities()
	{
		$params = $this->defaultParameters();
		$params['t'] = 'caps';

		// https://www.usenet-crawler.com/api?t=caps
		$detail_url = $this->endpointBaseURL() . "?" . http_build_query($params);
		list($details, $headers) = $this->performRequest( $detail_url );
		return $details;
	}

	public function searchBooks( $title, $author = null) {
		$params = $this->defaultParameters();
		$params['t'] = 'book';
		$params['extended'] = '1';
		$params['title'] = $title;

		if ( $author && strlen($author) > 0) {
			$params['author'] = $author;
		}

		// https://www.usenet-crawler.com/api?t=caps
		$detail_url = $this->endpointBaseURL() . "?" . http_build_query($params);
		list($details, $headers) = $this->performRequest( $detail_url );
		return $details;
	}

	public function searchComics( $query ) {
		return $this->search($query, null, array(NewznabConnector::NEWZNAB_CAT_BOOK,NewznabConnector::NEWZNAB_CAT_COMIC));
	}

	public function search($query, array $groups = null, array $categories = null)
	{
		$params = $this->defaultParameters();
		$params['t'] = 'search';
		$params['extended'] = '1';
		$params['q'] = $query;

		if ( $groups && count($groups) > 0) {
			$params['group'] = implode(',', array_values($groups));
		}

		if ( $categories && count($categories) > 0) {
			$params['cat'] = implode(',', array_values($categories));
		}

		$detail_url = $this->endpointBaseURL() . "?" . http_build_query($params);
		list($details, $headers) = $this->performRequest( $detail_url );
		return $details;
	}

	public function getNZB( $nzbid = null )
	{
		if ( is_null($nzbid) == false ) {
			// https://www.usenet-crawler.com/api?t=get&id=9ca52909ba9b9e5e6758d815fef4ecda
			$params = $this->defaultParameters();
			$params['t'] = 'get';
			$params['id'] = $nzbid;

			$detail_url = $this->endpointBaseURL() . "?" . http_build_query($params);
			return file_get_contents($detail_url);
		}
		return null;
	}

	public function performRequest($url, $force = false)
	{
		$this->xmlDocument = null;
		list($this->xmlDocument, $headers) = parent::performRequest($url, $force);
		if ( empty($this->xmlDocument) == true ) {
			throw new \Exception( "No Newznab data" );
		}
		return array($this->xmlDocument, $headers);
	}
}
