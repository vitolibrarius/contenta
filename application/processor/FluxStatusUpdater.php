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
		$statusForLog = array(
			"incomplete" => 0,
			"complete" => 0,
			"failed" => 0
		);
		if ( is_array($incomplete) && count($incomplete) > 0 ) {
			$map_incomplete = array();
			foreach ($incomplete as $flux) {
				$dest_guid = $flux->dest_guid();
				$map_incomplete[$dest_guid] = $flux;
			}

			$sab_connector = $this->endpointConnector();
			$queue = $sab_connector->queueSlots();
			foreach( $queue as $slot ) {
				$sab_id = $slot['nzo_id'];
				$sab_percent = $slot['percentage'];
				$sab_status = $slot['status'];
				$flux = $FluxModel->objectForDest_guid($sab_id);
				if ( $flux != false && $flux->isComplete() == false) {
					$statusForLog["incomplete"]++;
					$FluxModel->updateObject( $flux, array( Flux::dest_status => $sab_status . ' ' . $sab_percent . '%'	));
				}
				if ( isset($map_incomplete[$sab_id])) {
					unset($map_incomplete[$sab_id]);
				}
			}

			$history = $sab_connector->historySlots();
			foreach( $history as $slot ) {
				$sab_id = $slot['nzo_id'];
				$sab_fail_message = $slot['fail_message'];
				$sab_status = $slot['status'];
				$flux = $FluxModel->objectForDest_guid($sab_id);
				if ( $flux != false ) {
					$updates = array();
					$deleteHistory = false;
					switch ( strtolower($sab_status) ) {
						case 'failed':
							$statusForLog["failed"]++;
							$updates[Flux::flux_error] = Model::TERTIARY_TRUE;
							$updates[Flux::dest_status] = $sab_status . " ($sab_fail_message)";
							$deleteHistory = true;
							break;
						case 'completed':
							$statusForLog["complete"]++;
							$updates[Flux::flux_error] = Model::TERTIARY_TRUE;
							$updates[Flux::dest_status] = $sab_status;
							$deleteHistory = true;
							break;
						case 'running':
						case 'queued':
						default:
							$statusForLog["incomplete"]++;
							$updates[Flux::dest_status] = $sab_status . (isset($slot['action_line']) ? " (" . $slot['action_line'] . ")": "");
							break;
					}
					$FluxModel->updateObject( $flux, $updates);

					if ( $deleteHistory === true ) {
						// delete the history from SABnzbd
						$del_status = $sab_connector->historyDelete($sab_id);
					}
				}

				if ( isset($map_incomplete[$sab_id])) {
					unset($map_incomplete[$sab_id]);
				}
			}

			if ( count($map_incomplete) > 0 ) {
				// anything still in the incomplete list has probably been removed from the SAB history by the user
				foreach( $map_incomplete as $sab_id => $flux ) {
					$FluxModel->updateObject( $flux, array( Flux::dest_status => "Failed (Unknown history)" ));
				}
			}

			Logger::logInfo( "Flux status updated " . var_export($statusForLog, true), "SABnzbd" );
		}
		$this->setPurgeOnExit(true);
		return true;
	}
}
