<?php

namespace processor;

use \Processor as Processor;
use \Migrator as Migrator;
use \Config as Config;
use \Logger as Logger;
use \Model as Model;
use \Exception as Exception;

use \model\user\Users as Users;
use \model\media\Publisher as Publisher;
use \model\media\Character as Character;
use \model\media\Series as Series;
use \model\media\Publication as Publication;
use \model\network\Endpoint as Endpoint;
use \model\network\Endpoint_Type as Endpoint_Type;
use \model\network\EndpointDBO as EndpointDBO;
use \model\media\Story_Arc as Story_Arc;
use \model\media\Story_Arc_Character as Story_Arc_Character;
use \model\media\Story_Arc_Series as Story_Arc_Series;
use \model\network\Flux as Flux;
use \model\network\FluxDBO as FluxDBO;

class FluxStatusUpdater extends EndpointImporter
{
	function __construct($guid = '')
	{
		if ( empty( $guid ) ) {
			$guid = uuid();
		}
		parent::__construct($guid);
	}

	public function processData()
	{
		$FluxModel = Model::Named('Flux');

		$incomplete = $FluxModel->allDestinationIncomplete(-1);
		if ( is_array($incomplete) && count($incomplete) > 0 ) {
			$sab_connector = $this->endpointConnector();
			$queue = $sab_connector->queueSlots();
			foreach( $queue as $slot ) {
				$sab_id = $slot['nzo_id'];
				$sab_percent = $slot['percentage'];
				$sab_status = $slot['status'];
				$flux = $FluxModel->objectForDest_guid($sab_id);
				if ( $flux != false && $flux->isComplete() == false) {
					$FluxModel->updateObject( $flux, array( Flux::dest_status => $sab_status . ' ' . $sab_percent . '%'	));
				}
			}

			$history = $sab_connector->historySlots();
			foreach( $history as $slot ) {
				$sab_id = $slot['nzo_id'];
				$sab_fail_message = $slot['fail_message'];
				$sab_status = $slot['status'];
				$flux = $FluxModel->objectForDest_guid($sab_id);
				if ( $flux != false ) {
					// delete the history from SABnzbd
					$del_status = $sab_connector->historyDelete($sab_id);
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
