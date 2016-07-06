<?php

namespace model\network;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use \model\network\Flux as Flux;

/* import related objects */
use \model\network\Endpoint as Endpoint;
use \model\network\EndpointDBO as EndpointDBO;

class FluxDBO extends _FluxDBO
{
	public function isSourceComplete() {
		return (isset($this->src_status) && $this->src_status == 'Downloaded');
	}

	public function isComplete() {
		return (isset($this->dest_guid, $this->dest_status) &&
			($this->dest_status == 'Completed' || startsWith('Failed', $this->dest_status)));
	}
}

?>
