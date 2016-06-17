<?php

namespace processor;

use \Processor as Processor;
use \Config as Config;
use \Logger as Logger;
use \Model as Model;
use \SimpleXMLElement as SimpleXMLElement;

use model\Endpoint_Type as Endpoint_Type;
use model\Endpoint as Endpoint;
use model\EndpointDBO as EndpointDBO;

use \model\network\RssDBO as RssDBO;

class RSSImporter extends EndpointImporter
{
	function __construct($guid)
	{
		parent::__construct($guid);
	}

	public function processData()
	{
		$connection = $this->endpointConnector();
		$endpoint = $this->endpoint();
		list($xml, $headers) = $connection->performGET( $endpoint->base_url );
		if ( $xml instanceof SimpleXMLElement) {
			$rss_model = Model::Named('Rss');
			$count = 0;
			foreach ($xml->channel->item as $key => $item) {
				$guid = (string)(isset($item->guid) ? $item->guid : $item->link);
				$publishedDate = strtotime($item->pubDate);
				$url = (string)$item->link;
				$len = 0;
				$type = null;
				$password = false;
				if (isset($item->enclosure, $item->enclosure['url'])) {
					$url = (string)$item->enclosure['url'];
					$len = (string)$item->enclosure['length'];
					$type = (string)$item->enclosure['type'];
				}

/*
			Rss::pub_date => time(),
			Rss::guid => uuid(),
			Rss::clean_name => (isset($meta["name"]) ? $meta["name"] : null),
			Rss::clean_issue => (isset($meta["issue"]) ? $meta["issue"] : null),
			Rss::clean_year => (isset($meta["year"]) ? $meta["year"] : null),
			Rss::enclosure_url => "http://url/to/file",
			Rss::enclosure_length => 10000,
			Rss::enclosure_mime => null,
			Rss::enclosure_hash => '34e4eacc9168cf97e0625699e9b5cb65',
			Rss::enclosure_password => false
*/
				$rss = $rss_model->objectForGuid($guid);
				if ( $rss instanceof RssDBO ) {
					list($rss, $errors) = $rss_model->updateObject( $rss, array(
						"title" => (string)$item->title,
						"desc" => strip_tags((string)$item->description),
						"pub_date" => $publishedDate,
						"enclosure_url" => $url,
						"enclosure_length" => $len,
						"enclosure_mime" => $type
						)
					);
				}
				else {
					list($rss, $errors) = $rss_model->createObject( array(
						"endpoint" => $endpoint,
						"title" => (string)$item->title,
						"desc" => strip_tags((string)$item->description),
						"pub_date" => $publishedDate,
						"guid" => $guid,
						"enclosure_url" => $url,
						"enclosure_length" => $len,
						"enclosure_mime" => $type,
						"enclosure_hash" => null,
						"enclosure_password" => false
						)
					);

					if ( is_array($errors) && count($errors) > 0) {
						echo PHP_EOL . var_export($errors, true) . PHP_EOL;
					}
					if ( $rss != false ) {
						$count++;
					}
				}
			}

			if ( $count > 0 ) {
				Logger::logInfo( "Imported $count new RSS items", $this->type, $this->guid);
			}
		}

		$this->setPurgeOnExit(true);
	}
}
