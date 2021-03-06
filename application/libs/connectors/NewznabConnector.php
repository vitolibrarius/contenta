<?php

namespace connectors;

use \Config as Config;
use \Logger as Logger;
use \Model as Model;
use \Cache as Cache;
use \Database as Database;
use \SimpleXMLElement as SimpleXMLElement;

use \model\network\Endpoint as Endpoint;
use \model\network\EndpointDBO as EndpointDBO;

use \utilities\MediaFilename as MediaFilename;
use \exceptions\EndpointConnectionException as EndpointConnectionException;

class NewznabException extends \Exception {}

class NewznabConnector extends RSSConnector
{
	const NEWZNAB_VERSION = "API_VERSION";
	const NEWZNAB_CAT_BOOK = "Category_Book"; // 7000;
	const NEWZNAB_CAT_COMIC = "Category_Comic"; // 7030;
	const NEWZNAB_CAT_EBOOK = "Category_Ebook"; //7020;

	public function __construct($endpoint)
	{
		parent::__construct($endpoint);
	}

	public function testConnnector()
	{
		$params = $this->defaultParameters();
		$params['t'] = 'caps';
		$detail_url = $this->endpointBaseURL() . "?" . http_build_query($params);
		list( $success, $message, $data ) = $this->performTestConnnector($detail_url);
		if ( $success == true ) {
			if ( isset( $data, $data->server, $data->server['version'])) {
				$server = $data->server;
				$title = (string)(isset($server['title']) ? $server['title'] : 'No Title?');
				$message = $title . PHP_EOL . "(Newznab version: " . $server['version'] . ")" .PHP_EOL;
			}
			else {
				$success = false;
				$message = "Invalid RSS response [". $detail_url ."] " . var_export($data, true);
			}
		}

		return array($success, $message);
	}

	public function newznabCategory()
	{
		$keys = func_get_args();
		$teeth = array_filter($keys, 'is_string');

		$categories = array();
		$canRefresh = true;
		foreach( $teeth as $tooth ) {
			$category = $this->endpointParameterForKey($tooth);
			if ( empty($category) && $canRefresh ) {
				$this->refreshEndpointParameters();
				$canRefresh = false;

				$category = $this->endpointParameterForKey($tooth);
			}

			if ( empty($category) == false ) {
				$categories[] = $category;
			}
		}
		return $categories;
	}

	public function refreshEndpointParameters()
	{
		$xmlDocument = $this->capabilities();
		if ( $xmlDocument instanceof SimpleXMLElement && isset($xmlDocument->categories, $xmlDocument->categories->category) ) {
			$results = array();

			$results = array_setValueForKeypath(NewznabConnector::NEWZNAB_VERSION, "0.0", $results);
			if ( isset( $xmlDocument->server, $xmlDocument->server['version'])) {
				$server = $xmlDocument->server;
				$results = array_setValueForKeypath(NewznabConnector::NEWZNAB_VERSION, (string)$server['version'], $results);
			}

			$results = array_setValueForKeypath(NewznabConnector::NEWZNAB_CAT_BOOK, "7000", $results);
			$results = array_setValueForKeypath(NewznabConnector::NEWZNAB_CAT_COMIC, "7030", $results);
			$results = array_setValueForKeypath(NewznabConnector::NEWZNAB_CAT_EBOOK, "7030", $results);

			foreach ($xmlDocument->categories->category as $key => $category) {
				if ((string)(isset($category["name"], $category["id"]) ? $category["name"] : '') == "Books") {
					$cat_id = (string)$category["id"];
					$results = array_setValueForKeypath(NewznabConnector::NEWZNAB_CAT_BOOK, $cat_id, $results);

					foreach ($category->subcat as $subkey => $subcategory) {
						if ((string)(isset($subcategory["name"], $subcategory["id"]) ? $subcategory["name"] : '') == "Ebook") {
							$cat_id = (string)$subcategory["id"];
							$results = array_setValueForKeypath(NewznabConnector::NEWZNAB_CAT_EBOOK, $cat_id, $results);
						}

						if ((string)(isset($subcategory["name"], $subcategory["id"]) ? $subcategory["name"] : '') == "Comics") {
							$cat_id = (string)$subcategory["id"];
							$results = array_setValueForKeypath(NewznabConnector::NEWZNAB_CAT_COMIC, $cat_id, $results);
						}
					}
				}
			}
			$this->endpoint()->setJsonParameters($results);
			$this->endpoint()->saveChanges();
			return $results;
		}
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
		$details = $this->search(
			$query,
			null,
			$this->newznabCategory(NewznabConnector::NEWZNAB_CAT_BOOK, NewznabConnector::NEWZNAB_CAT_COMIC)
		);
		return ( $details instanceof SimpleXMLElement ? null : $details );
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

		if ( $xmlDocument instanceof SimpleXMLElement ) {
			if ( isset($xmlDocument->channel, $xmlDocument->channel->item) ) {
				$results = array();
				foreach ($xmlDocument->channel->item as $key => $item) {
					$record['title'] = (string)(isset($item->title) ? $item->title : '');
					$record['guid'] = (string)(isset($item->guid) ? $item->guid : $item->link);
					$record['safe_guid'] = sanitize_html_id($record['guid']);
					$record['publishedDate'] = strtotime($item->pubDate);
					$record['url'] = (string)$item->link;
					$record['password'] = false;
					$record['desc'] = strip_tags((string)$item->description);
					if (isset($item->enclosure, $item->enclosure['url'])) {
						$record['url'] = (string)$item->enclosure['url'];
						$record['len'] = (string)$item->enclosure['length'];
						$record['type'] = (string)$item->enclosure['type'];
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

					$results[] = $record;
				}
				return array( $results, $headers );
			}
			else if ($xmlDocument->getName() == 'error') {
				$code = (isset($xmlDocument['code']) ? (string)$xmlDocument['code'] : "-1");
				$desc = (isset($xmlDocument['description']) ? (string)$xmlDocument['description'] : $xmlDocument->asXML());
				Logger::logError( $desc, $this->endpointDisplayName(), $code);
			}
		}

		return false;
	}
}
