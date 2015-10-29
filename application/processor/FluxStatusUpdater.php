<?php

namespace processor;

use \Processor as Processor;
use \Migrator as Migrator;
use \Config as Config;
use \Logger as Logger;
use \Model as Model;
use \Exception as Exception;

use model\Users as Users;
use model\Publisher as Publisher;
use model\Character as Character;
use model\Series as Series;
use model\Publication as Publication;
use model\Endpoint as Endpoint;
use model\Endpoint_Type as Endpoint_Type;
use model\EndpointDBO as EndpointDBO;
use model\Story_Arc as Story_Arc;
use model\Story_Arc_Character as Story_Arc_Character;
use model\Story_Arc_Series as Story_Arc_Series;
use model\Flux as Flux;
use model\FluxDBO as FluxDBO;

class FluxStatusUpdater extends EndpointImporter
{
	function __construct($guid = '')
	{
		if ( empty( $guid ) ) {
			$guid = uuid();
		}
		parent::__construct($guid);
	}

	public function setEndpoint(EndpointDBO $point = null)
	{
		if ( is_null($point) == false ) {
			$type = $point->type();
			if ( $type == false || $type->code != Endpoint_Type::SABnzbd ) {
				throw new Exception("Endpoint " . $point->displayName() . " is is not for " . Endpoint_Type::SABnzbd);
			}
			$this->setJobDescription( "Refreshing " . $point->displayName());
		}
		parent::setEndpoint($point);
	}

	public function processData()
	{
		$FluxModel = Model::Named('Flux');
		$incomplete = $FluxModel->destinationIncomplete(-1);
		if ( is_array($incomplete) && count($incomplete) > 0 ) {
			$sab_connector = $this->endpointConnector();
			$queue = $sab_connector->queueSlots();
			foreach( $queue as $slot ) {
				$sab_id = $slot['nzo_id'];
				$sab_percent = $slot['percentage'];
				$sab_status = $slot['status'];
				$flux = $FluxModel->objectForDestinationEndpointGUID($sab_connector->endpoint(), $sab_id);
				if ( $flux != false && $flux->isComplete() == false) {
					$FluxModel->updateObject( $flux, array( Flux::dest_status => $sab_status . ' ' . $sab_percent . '%'	));
				}
			}

			$history = $sab_connector->historySlots();
			foreach( $history as $slot ) {
				$sab_id = $slot['nzo_id'];
				$sab_fail_message = $slot['fail_message'];
				$sab_status = $slot['status'];
				$flux = $FluxModel->objectForDestinationEndpointGUID($sab_connector->endpoint(), $sab_id);
				if ( $flux != false && $flux->isComplete() == false) {
					if ( $sab_status == 'Failed' ) {
						$FluxModel->updateObject( $flux, array(
								Flux::flux_error => Model::TERTIARY_TRUE,
								Flux::dest_status => $sab_status . " ($sab_fail_message)",
							)
						);
					}
					else {
						$FluxModel->updateObject( $flux, array(
								Flux::flux_error => Model::TERTIARY_FALSE,
								Flux::dest_status => $sab_status
							)
						);
					}
				}
			}
		}
		$this->setPurgeOnExit(true);
		return true;
	}
}
