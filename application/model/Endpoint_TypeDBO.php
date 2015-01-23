<?php

namespace model;

use \DataObject as DataObject;
use \Model as Model;

class Endpoint_TypeDBO extends DataObject
{
	public $name;
	public $code;
	public $data_type;
	public $site_url;
	public $api_url;
	public $throttle_hits;
	public $throttle_time;
}
