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

	public function setEndpoint(EndpointDBO $point = null)
	{
		if ( is_null($point) == false ) {
			$type = $point->type();
			if ( $type == false || $type->code != Endpoint_Type::PreviewsWorld ) {
				throw new Exception("Endpoint " . $point->displayName() . " is is not for " . Endpoint_Type::PreviewsWorld);
			}
			$this->setJobDescription( "Refreshing " . $point->displayName());
		}
		parent::setEndpoint($point);
	}

	public function processData()
	{
		$connection = $this->endpointConnector();
		$endpoint = $this->endpoint();
		list( $dataArray, $headers ) = $connection->performGET( $endpoint->base_url );
		$releaseDate = $connection->releaseDate();

		echo "+++ " . date("M d, Y", $releaseDate) . " " . $releaseDate . PHP_EOL;
		echo "+++ ETag:" . $headers['ETag'] . PHP_EOL. PHP_EOL;

		foreach( $dataArray as $groupname => $items ) {
			echo "*** " . $groupname . PHP_EOL;
			foreach( $items as $meta ) {
				echo "	" .  array_valueForKeypath( 'name', $meta )
					. "	" .  array_valueForKeypath( 'issue', $meta )
					. "	" .  array_valueForKeypath( 'year', $meta )
					. PHP_EOL;
			}
		}
	}
}
