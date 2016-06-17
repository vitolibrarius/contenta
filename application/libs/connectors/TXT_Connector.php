<?php

namespace connectors;

use \Config as Config;
use \Logger as Logger;
use \Model as Model;
use \Cache as Cache;
use \Database as Database;

use utilities\MediaFilename as MediaFilename;

use \model\network\Endpoint as Endpoint;
use \model\network\EndpointDBO as EndpointDBO;

class TXT_ConnectorException extends \Exception {}

class TXT_Connector extends EndpointConnector
{
	private $document;

	public function __construct($endpoint)
	{
		parent::__construct($endpoint);
	}

	public function testConnnector()
	{
		return array(true, "");
	}

	public function performGET($url, $force = false)
	{
		$this->document = null;

		list($data, $headers) = parent::performGET($url, $force);
		if ( empty($data) == true ) {
			throw new \Exception( "No TXT_Connector data" );
		}

		return array( $data, $headers );
	}
}
