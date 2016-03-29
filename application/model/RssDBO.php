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

class RssDBO extends DataObject
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
		return $this->clean_name
			. " " . $this->clean_issue
			. (intval($this->clean_year) > 1900 ? " " . $this->clean_year : '');
	}

	public function displayDescription() {
		return $this->shortDescription();
	}

	public function safe_guid()
	{
		return sanitize($this->guid, true, true);
	}

	public function publishedMonthYear() {
		return $this->formattedDate( Rss::pub_date, "M Y" );
	}

	public function endpoint() {
		if ( isset($this->endpoint_id) ) {
			$model = Model::Named('Endpoint');
			return $model->objectForId($this->endpoint_id);
		}
		return false;
	}

	public function flux() {
		if ( isset($this->guid) ) {
			$model = Model::Named('Flux');
			return $model->objectForSourceGUID($this->guid);
		}
		return false;
	}
}

