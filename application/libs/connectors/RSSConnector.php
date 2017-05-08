<?php

namespace connectors;

use \Config as Config;
use \Logger as Logger;
use \Model as Model;
use \Cache as Cache;
use \Database as Database;

use \model\network\Endpoint as Endpoint;
use \model\network\EndpointDBO as EndpointDBO;

class RSSException extends \Exception {}

class RSSConnector extends XML_EndpointConnector
{
	const RSS_CHANNEL = 'channel';
	const RSS_TITLE = 'title';
	const RSS_DESCRIPTION = 'description';
	const RSS_LINK = 'link';
	const RSS_LANGUAGE = 'language';
	const RSS_WEBMASTER = 'webMaster';
	const RSS_CATEGORY = 'category';
	const RSS_IMAGE = 'image';
	const RSS_ITEM = 'item';
	const RSS_URL = 'url';
	const RSS_GUID = 'guid';
	const RSS_COMMENTS = 'comments';
	const RSS_PUBDATE = 'pubDate';
	const RSS_ENCLOSURE = 'enclosure';
	const RSS_TYPE = 'type';
	const RSS_LENGTH = 'length';

	private $xmlDocument;

	public function __construct($endpoint)
	{
		parent::__construct($endpoint);
	}

	public function testConnnector()
	{
		$detail_url = $this->endpointBaseURL();
		list( $success, $message, $data ) = $this->performTestConnnector($detail_url);
		if ( $success == true ) {
			if ( isset( $data, $data['version'])) {
				$channel = $data->channel;
				$title = (string)(isset($channel->title) ? $channel->title : 'No Channel Title?');
				$items = (isset($channel->item) ? $channel->item : array());

				$message = $title . PHP_EOL . "(RSS version: " . $data['version'] . ")" .PHP_EOL;
			}
			else {
				$success = false;
				$message = "Does not seem to be a valid RSS document " . var_export($data, true);
			}
		}

		return array($success, $message);
	}

	public function performGET($url, $force = false)
	{
		$this->xmlDocument = null;
		list($this->xmlDocument, $headers) = parent::performGET($url, $force);
		return array($this->xmlDocument, $headers);
	}


	public function rssVersion() {
		if (empty($this->xmlDocument) == false) {
			return $this->xmlDocument['version'];
		}
		return 0;
	}

	public function channelTitle() {
		if (empty($this->xmlDocument) == false) {
			return $this->xmlDocument['version'];
		}
		return 0;
	}

}
