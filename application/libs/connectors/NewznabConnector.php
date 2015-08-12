<?php

namespace connectors;

use \Config as Config;
use \Logger as Logger;
use \Model as Model;
use \Cache as Cache;
use \Database as Database;
use \SimpleXMLElement as SimpleXMLElement;

use model\Endpoint as Endpoint;
use model\EndpointDBO as EndpointDBO;

use utilities\MediaFilename as MediaFilename;

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
		list($details, $headers) = $this->performGET( $detail_url );
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
		list($details, $headers) = $this->performGET( $detail_url );
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
		list($details, $headers) = $this->performGET( $detail_url );
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

	public function performGET($url, $force = false)
	{
		list($xmlDocument, $headers) = parent::performGET($url, $force);
		if ( empty($xmlDocument) == true ) {
			throw new \Exception( "No Newznab data" );
		}

		if ( $xmlDocument instanceof SimpleXMLElement) {
			$results = array();
			foreach ($xmlDocument->channel->item as $key => $item) {
				$record['title'] = (isset($item->title) ? $item->title : '');
				$record['guid'] = (isset($item->guid) ? $item->guid : $item->link);
				$record['publishedDate'] = strtotime($item->pubDate);
				$record['url'] = $item->link;
				$record['password'] = false;
				$record['desc'] = strip_tags($item->description);
				if (isset($item->enclosure, $item->enclosure['url'])) {
					$record['url'] = $item->enclosure['url'];
					$record['len'] = $item->enclosure['length'];
					$record['type'] = $item->enclosure['type'];
				}
				$children = $item->children('newznab', true);
				foreach( $children as $node ) {
					$attr = $node->attributes();
					if ( $attr['name'] === 'password' ) {
						$record['password'] = boolval($attr['value']);
					}
				}

				if ( isset($item->title) ) {
					$mediaFilename = new MediaFilename($item->title);
					$record['metadata'] = $mediaFilename->updateFileMetaData(null);
				}

// 				$newznadAttr = $item->newznab:attr
				$results[] = $record;
			}
			return array( $results, $headers );
		}

		return array($xmlDocument, $headers);
	}
}
