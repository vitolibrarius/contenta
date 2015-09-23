<?php

namespace connectors;

use \Config as Config;
use \Logger as Logger;
use \Model as Model;
use \Cache as Cache;
use \Database as Database;

use connectors\NetworkErrorException as NetworkErrorException;
use connectors\ResponseErrorException as ResponseErrorException;

abstract class JSON_EndpointConnector extends EndpointConnector
{
	public function __construct($endpoint)
	{
		parent::__construct($endpoint);
	}

	public function isDebuggingResponses()
	{
		return false;
	}

	public function performPOST( $url, array $postfields = null, array $headers = null)
	{
		if (empty($url) == false) {
			list($data, $headers) = parent::performPOST( $url, $postfields, $headers);
			if ( $data != false )
			{
				$json = json_decode($data, true);
				if ( json_last_error() != 0 )
				{
					if ( $this->isDebuggingResponses() ) {
						$this->debugData( $data, $this->cleanURLForLog($url) . ".data" );
					}
					throw new ResponseErrorException(jsonErrorString(json_last_error()));
				}

				if ( $this->isDebuggingResponses() ) {
					$this->debugData(json_encode($json, JSON_PRETTY_PRINT), $this->cleanURLForLog($url) . ".txt" );
				}
				return array($json, $headers);
			}
		}
		return false;
	}

	public function performGET($url, $force = false)
	{
		if (empty($url) == false) {
			list($data, $headers) = parent::performGET($url, $force);
			if ( $data != false )
			{
				$json = json_decode($data, true);
				if ( json_last_error() != 0 )
				{
					Cache::Clear( $url );
					if ( $this->isDebuggingResponses() ) {
						$this->debugData( $data, $this->cleanURLForLog($url) . ".data" );
					}
					throw new ResponseErrorException(jsonErrorString(json_last_error()));
				}

				if ( $this->isDebuggingResponses() ) {
					$this->debugData(json_encode($json, JSON_PRETTY_PRINT), $this->cleanURLForLog($url) . ".txt" );
				}
				return array($json, $headers);
			}
			else {
				Logger::logError( 'Error (?) with url: ' . var_export($data, true),
						get_class($this), $this->endpointDisplayName());
			}
		}
		return false;
	}
}
