<?php

namespace processor;

use \Processor as Processor;
use \Migrator as Migrator;
use \Config as Config;
use \Logger as Logger;
use \Model as Model;
use \Exception as Exception;

use model\Endpoint as Endpoint;
use model\EndpointDBO as EndpointDBO;

abstract class EndpointImporter extends Processor
{
	const META_ENDPOINT_DISPLAY = "endpoint_name";
	const META_ENDPOINT_ID = "endpoint_id";

	function __construct($guid)
	{
		parent::__construct($guid);
	}

	public function endpointConnector()
	{
		$endpoint = $this->endpoint();
		if ( $endpoint == false ) {
			throw new Exception("No Endpoint set for Importer " . get_class() );
		}

		$connectorName = 'endpoints\\' . $endpoint->type()->code . 'Connector';
		$connection = new $connectorName($comicVine);
		return $connection;
	}

	public function endpoint()
	{
		if ( isset($this->endpoint) == false ) {
			if ( $this->isMeta(EndpointImporter::META_ENDPOINT_ID) ) {
				$endid = $this->getMeta(EndpointImporter::META_ENDPOINT_ID);
				$model = Model::Named('Endpoint');
				$this->endpoint = $model->objectForId($endid);
				return $this->endpoint;
			}
		}
		else {
			return $this->endpoint;
		}

		return false;
	}

	public function setEndpoint($point) {
		if ( is_a($point, '\model\EndpointDBO')) {
			if ($point->isEnabled() ) {
				$this->endpoint = $point;
				$this->setMeta(EndpointImporter::META_ENDPOINT_DISPLAY, $point->displayName() );
				$this->setMeta(EndpointImporter::META_ENDPOINT_ID, $point->id);
			}
			else {
				throw new Exception("Endpoint " . $point->displayName() . " is disabled");
			}
		}
		else {
			throw new Exception("Cannot be initialized with "
				. (empty($point) ? '-null-' : get_class($point))
				. ", requires a configuration of type 'endpointDBO'");
		}
	}
}