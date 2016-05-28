<?php

namespace model\network;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use \model\network\Rss as Rss;

/* import related objects */
use \model\Endpoint as Endpoint;
use \model\EndpointDBO as EndpointDBO;
use \model\Flux as Flux;
use \model\FluxDBO as FluxDBO;

class RssDBO extends _RssDBO
{
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
}

?>
