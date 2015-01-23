<?php

namespace model;

use \DataObject as DataObject;
use \Model as Model;
use model\Endpoint_Type as Endpoint_Type;

class EndpointDBO extends DataObject
{
	public $type_id;
	public $name;
	public $base_url;
	public $api_key;
	public $username;
	public $enabled;
	public $compressed;

	public function isEnabled() {
		return ( (empty($this->enabled) == false) && ($this->enabled == Model::TERTIARY_TRUE) );
	}

	public function requiresCompression() {
		return ( (empty($this->compressed) == false) && ($this->compressed == Model::TERTIARY_TRUE) );
	}

	public function displayName() {
		$type = $this->type();
		return (empty($type) ? 'Unknown' : $type->name) . " " . $this->name;
	}

	public function type() {
		$type_model = Model::Named("Endpoint_Type");
		$type = $type_model->objectForId($this->type_id);
		return $type;
	}

	public function __toString()
	{
		return $this->displayName() . ' (' . $this->pkValue() . ') ' . $this->base_url;
	}
}
