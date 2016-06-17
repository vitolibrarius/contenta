<?php

namespace processor;

use \Processor as Processor;
use \Migrator as Migrator;
use \Config as Config;
use \Logger as Logger;
use \Model as Model;
use \Exception as Exception;

use \model\network\Endpoint as Endpoint;
use \model\network\Endpoint_Type as Endpoint_Type;
use \model\network\EndpointDBO as EndpointDBO;

abstract class EndpointImporter extends Processor
{
	const META_ENDPOINT_DISPLAY = "endpoint_name";
	const META_ENDPOINT_ID 		= "endpoint_id";
	const META_ENDPOINT_TYPE 	= "endpoint_type_code";

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
		return $endpoint->endpointConnector();
	}

	public function endpointTypeCode() {
		if ( $this->isMeta(EndpointImporter::META_ENDPOINT_TYPE) ) {
			return $this->getMeta(EndpointImporter::META_ENDPOINT_TYPE);
		}

		$endpoint = $this->endpoint();
		if ( $endpoint == false ) {
			throw new Exception("No Endpoint set for Importer " . get_class() );
		}
		$this->setMeta(EndpointImporter::META_ENDPOINT_TYPE, $endpoint->endpointType()->code );
		return $endpoint->endpointType()->code;
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

	public function setEndpointId( $endpoint_id = 0)
	{
		$endp = Model::Named( "Endpoint" )->objectForId( $endpoint_id );
		$this->setEndpoint( $endp );
	}

	public function setEndpoint(EndpointDBO $point = null)
	{
		if ( is_null($point) == false ) {
			$this->setJobDescription( "Refreshing " . $point->displayName());
			if ($point->isEnabled() ) {
				$this->endpoint = $point;
				$this->setMeta(EndpointImporter::META_ENDPOINT_DISPLAY, $point->displayName() );
				$this->setMeta(EndpointImporter::META_ENDPOINT_ID, $point->id);
				$this->setMeta(EndpointImporter::META_ENDPOINT_TYPE, $point->endpointType()->code );
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
