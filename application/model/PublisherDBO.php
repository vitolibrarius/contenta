<?php

namespace model;

use \DataObject as DataObject;
use \Model as Model;
use \Config as Config;

use model\Endpoint as Endpoint;
use model\Publisher as Publisher;

class PublisherDBO extends DataObject
{
	public $name;
	public $created;
	public $updated;
	public $xurl;
	public $xsource;
	public $xid;
	public $xupdated;

	public function displayName() {
		return $this->name;
	}

	public function publisher() {
		return $this;
	}

	public function externalEndpoint()
	{
		if ( isset( $this->xsource) ) {
			$ep_model = Model::Named('Endpoint');
			$points = $ep_model->allForTypeCode($this->xsource);
			if ( is_array($points) && count($points) > 0) {
				return $points[0];
			}
		}
		return null;
	}
}
