<?php

namespace processor;

use \Processor as Processor;
use \Config as Config;
use \Logger as Logger;
use \Model as Model;
use \SimpleXMLElement as SimpleXMLElement;

use model\Endpoint_Type as Endpoint_Type;
use model\Endpoint as Endpoint;

class PreviewsWorldImporter extends EndpointImporter
{
	function __construct($guid)
	{
		parent::__construct($guid);
	}

	public function setEndpoint($point) {
		if ( is_a($point, '\model\EndpointDBO')) {
			$type = $point->type();
			if ( $type == false || $type->code != Endpoint_Type::PreviewsWorld ) {
				throw new Exception("Endpoint " . $point->displayName() . " is is not for " . Endpoint_Type::PreviewsWorld);
			}
		}
		parent::setEndpoint($point);
	}

	public function processData()
	{
		Logger::logInfo( "processingData start", $this->type, $this->guid);
		$connection = $this->endpointConnector();
		$endpoint = $this->endpoint();
		list( $releaseDate, $dataArray) = $connection->performRequest( $endpoint->base_url );

		echo "+++ " . $releaseDate . PHP_EOL. PHP_EOL;

		foreach( $dataArray as $groupname => $items ) {
			echo "*** " . $groupname . PHP_EOL;
			foreach( $items as $meta ) {
				echo "	" .  array_valueForKeypath( 'name', $meta )
					. "	" .  array_valueForKeypath( 'issue', $meta )
					. "	" .  array_valueForKeypath( 'year', $meta )
					. PHP_EOL;
			}
		}

		Logger::logInfo( "processingData end", $this->type, $this->guid);
	}
}
