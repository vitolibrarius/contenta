<?php

namespace model;

use \DataObject as DataObject;
use \Model as Model;
use \Config as Config;

use model\Publisher as Publisher;
use model\Character as Character;
use model\Series as Series;
use model\Endpoint as Endpoint;

use db\Qualifier as Qualifier;

class RSSDBO extends DataObject
{
	public $endpoint_id;
	public $created;

	public $title;
	public $desc;
	public $pub_date;
	public $guid;

	public $clean_name;
	public $clean_issue;
	public $clean_year;

	public $enclosure_url;
	public $enclosure_length;
	public $enclosure_mime;
	public $enclosure_hash;
	public $enclosure_password;

	public function displayName() {
		return $this->title;
	}

	public function displayDescription() {
		return $this->shortDescription();
	}

	public function endpoint() {
		if ( isset($this->endpoint_id) ) {
			$model = Model::Named('Endpoint');
			return $model->objectForId($this->endpoint_id);
		}
		return false;
	}
}

