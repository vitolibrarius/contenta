<?php
namespace model;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

class NetworkDBO extends DataObject
{
	public $ip_address;
	public $ip_hash;
	public $disable;
	public $created;
}
