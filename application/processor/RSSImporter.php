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
use model\RssDBO as RssDBO;

class RSSImporter extends EndpointImporter
{
	function __construct($guid)
	{
		parent::__construct($guid);
	}

	public function setEndpoint(EndpointDBO $point = null)
	{
		if ( is_null($point) == false ) {
			$type = $point->type();
			if ( $type == false || $type->code != Endpoint_Type::RSS ) {
				throw new Exception("Endpoint " . $point->displayName() . " is is not for " . Endpoint_Type::RSS);
			}
		}
		parent::setEndpoint($point);
	}


	public function processData()
	{
		Logger::logInfo( "processingData start", $this->type, $this->guid);
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

				$rss = $rss_model->objectForEndpointGUID($endpoint, $guid);
				if ( $rss instanceof RssDBO ) {
					$rss = $rss_model->update( $rss,
						(string)$item->title,
						strip_tags((string)$item->description),
						$publishedDate,
						$url,
						$len,
						$type,
						null,
						false
					);
				}
				else {
					$rss = $rss_model->create( $endpoint,
						(string)$item->title,
						strip_tags((string)$item->description),
						$publishedDate,
						$guid,
						$url,
						$len,
						$type,
						null,
						false
					);

					if ( $rss != false ) {
						$count++;
					}
				}
			}
			Logger::logInfo( "Imported $count new RSS items", $this->type, $this->guid);
		}

		$this->setPurgeOnExit(true);
		Logger::logInfo( "processingData end", $this->type, $this->guid);
	}
}
