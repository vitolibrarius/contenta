<?php

namespace model;

use \DataObject as DataObject;
use \Model as Model;
use \Config as Config;
use \Logger as Logger;

use model\Publisher as Publisher;
use model\Character as Character;
use model\Series as Series;
use model\Endpoint as Endpoint;

use db\Qualifier as Qualifier;

class FluxDBO extends DataObject
{
	public $publication_id;
	public $created;

	public $name;
	public $flux_hash;
	public $flux_error;

	public $src_endpoint;
	public $src_guid;
	public $src_status;
	public $src_pub_date;

	public $dest_endpoint;
	public $dest_guid;
	public $dest_submission;
	public $dest_status;

	public function isError() {
		return (isset($this->flux_error) && $this->flux_error == Model::TERTIARY_TRUE);
	}

	public function isSourceComplete() {
		return (isset($this->src_status) && $this->src_status == 'Downloaded');
	}

	public function isComplete() {
		return (isset($this->dest_guid, $this->dest_status) &&
			($this->dest_status == 'Completed' || startsWith('Failed', $this->dest_status)));
	}

	public function errorReason() {
		if ( $this->isError() ) {
			if ( isset($this->src_status) && $this->src_status !== 'ok' ) {
				return $this->src_status;
			}
			return $this->dest_status;
		}
		return null;
	}

	public function displayName() {
		return $this->name;
	}

	public function sourceEndpoint() {
		if ( isset($this->src_endpoint) ) {
			$model = Model::Named('Endpoint');
			return $model->objectForId($this->src_endpoint);
		}
		return false;
	}

	public function destinationEndpoint() {
		if ( isset($this->dest_endpoint) ) {
			$model = Model::Named('Endpoint');
			return $model->objectForId($this->dest_endpoint);
		}
		return false;
	}

	public function sourcePostedDate() {
		return $this->formattedDate( Flux::src_pub_date );
	}

	public function destinationPostedDate() {
		return $this->formattedDate( Flux::dest_submission );
	}
}
