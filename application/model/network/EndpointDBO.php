<?php

namespace model\network;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use \model\network\Endpoint as Endpoint;

/* import related objects */
use \model\network\Endpoint_Type as Endpoint_Type;
use \model\network\Endpoint_TypeDBO as Endpoint_TypeDBO;
use \model\pull_list\Pull_List as Pull_List;
use \model\pull_list\Pull_ListDBO as Pull_ListDBO;
use \model\network\Rss as Rss;
use \model\network\RssDBO as RssDBO;
use \model\network\Flux as Flux;
use \model\network\FluxDBO as FluxDBO;
use \model\jobs\Job as Job;
use \model\jobs\JobDBO as JobDBO;

class EndpointDBO extends _EndpointDBO
{
	public function displayName() {
		$type = $this->endpointType();
		return $this->name . ' (' . (empty($type) ? 'Unknown' : $type->code) . ')';
	}

	public function endpointConnector()
	{
		$connectorName = 'connectors\\' . $this->endpointType()->data_type . 'Connector';
		$connection = new $connectorName($this);
		return $connection;
	}

	public function __toString()
	{
		return $this->displayName() . ' (' . $this->pkValue() . ') ' . $this->base_url;
	}
}

?>
