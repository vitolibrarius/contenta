<?php

namespace model;

use \DataObject as DataObject;
use \Model as Model;
use \Config as Config;
use \Logger as Logger;

class Endpoint_TypeDBO extends DataObject
{
	public $name;
	public $code;
	public $data_type;
	public $site_url;
	public $api_url;
	public $favicon_url;
	public $throttle_hits;
	public $throttle_time;
	public $comments;

	public function displayName()
	{
		return $this->name;
	}

	public function favicon()
	{
		if (isset($this->favicon_url) ) {
			return $this->favicon_url;
		}

		return Config::Web('public/img/Logo_favicon.png');
	}
}
