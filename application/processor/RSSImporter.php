<?php

namespace processor;

use \Processor as Processor;
use \Config as Config;
use \Logger as Logger;
use \Model as Model;
use \SimpleXMLElement as SimpleXMLElement;

use \model\network\Endpoint_Type as Endpoint_Type;
use \model\network\Endpoint as Endpoint;
use \model\network\EndpointDBO as EndpointDBO;

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
		list($xml, $headers) = $connection->performGET( $endpoint->base_url, true );
		if ( $xml instanceof SimpleXMLElement) {
			$rss_model = Model::Named('Rss');
			$count = 0;
			$count_existing = 0;
			$count_small = 0;
			$total = count($xml->channel->item);
			foreach ($xml->channel->item as $key => $item) {
				$guid = (string)(isset($item->guid) ? $item->guid : $item->link);
				$publishedDate = strtotime($item->pubDate);
				$url = (string)$item->link;
				$len = 0;
				$type = null;
				$password = false;
				if (isset($item->enclosure, $item->enclosure['url'])) {
					$url = (string)$item->enclosure['url'];
					$len = intval((string)$item->enclosure['length']);
					$type = (string)$item->enclosure['type'];
				}

				if ( $len > 0 ) {
					$rss = $rss_model->objectForGuid($guid);
					if ( $rss == false ) {
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
							Logger::logError( var_export($errors, true), $this->type, $this->guid);
						}
						$count++;
					}
					else {
						$count_existing++;
					}
				}
				else {
					$count_small++;
				}
			}

			Logger::logInfo( "RSS Imported $count, Existing $count_existing, Small $count_small / $total", $this->type, $endpoint->displayName());
		}
		else {
			Logger::logError( "RSS response is not XML " . var_export($xml, true), $this->type, $this->guid);
		}

		$this->setPurgeOnExit(true);
	}
}
