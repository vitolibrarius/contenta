<?php

namespace endpoints;

use \Config as Config;
use \Logger as Logger;
use \Model as Model;
use \Cache as Cache;
use \Database as Database;

abstract class JSON_EndpointConnector extends EndpointConnector
{
	public function __construct($endpoint)
	{
		parent::__construct($endpoint);
	}

	public function isDebuggingResponses()
	{
		return true;
	}

	public function performRequest($url)
	{
		if (empty($url) == false) {
			$data = parent::performRequest($url);
			if ( $data != false )
			{
				$json = json_decode($data, true);
				if ( json_last_error() != 0 )
				{
					Cache::Clear( $url );
					if ( $this->isDebuggingResponses() ) {
						$this->debugData( $data, $url . ".data" );
					}
					throw new ResponseErrorException(jsonErrorString(json_last_error()));
				}

				if ( $this->isDebuggingResponses() ) {
					$this->debugData(json_encode($json, JSON_PRETTY_PRINT), $url . ".txt" );
				}
				return $json;
			}
		}
		return false;
	}

}
