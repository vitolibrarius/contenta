<?php

namespace connectors;

use \Config as Config;
use \Logger as Logger;
use \Model as Model;
use \Cache as Cache;
use \Database as Database;

use connectors\NetworkErrorException as NetworkErrorException;
use connectors\ResponseErrorException as ResponseErrorException;

abstract class XML_EndpointConnector extends EndpointConnector
{
	public function __construct($endpoint)
	{
		parent::__construct($endpoint);
	}

	public function performGET($url, $force = false)
	{
		if (empty($url) == false) {
			list($data, $headers) = parent::performGET($url, $force);
			if ( $data != false )
			{
				libxml_use_internal_errors(true);
				$xml = simplexml_load_string($data);
				$xmlErrors = libxml_get_errors();
				libxml_clear_errors();

				if ( is_a($xml, 'SimpleXMLElement') == false )
				{
					if ( $this->isDebuggingResponses() ) {
						$this->debugData( $data, $this->cleanURLForLog($url) . ".data" );
					}

					if (is_array($xmlErrors) && count($xmlErrors) > 0) {
						if (count($xmlErrors) == 1) {
							throw new ResponseErrorException("XML Parsing error: " . $xmlErrors[0]->message . PHP_EOL . $data);
						}
						else {
							Logger::logError( $this->cleanURLForLog($url) . PHP_EOL . "Parsing encountered " . count($xmlErrors) . " unparsable errors",
								$this->endpointDisplayName());
						}
					}
					else {
						Logger::logError( $this->cleanURLForLog($url) . PHP_EOL . "Error parsing XML " . var_export($xml, true),
							$this->endpointDisplayName());
					}
				}

				return array($xml, $headers);
			}
		}
		return array(false, null);
	}
}
