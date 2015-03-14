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
		return (isset($this->enabled) && $this->enabled == 1);
	}

	public function requiresCompression() {
		return (isset($this->compressed) && $this->compressed == 1);
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

	public function endpointConnector()
	{
		$type = $this->type();
		$className = "connectors\\" . $type->code;
		return new $className($this);
	}

	public function __toString()
	{
		return $this->displayName() . ' (' . $this->pkValue() . ') ' . $this->base_url;
	}
}
