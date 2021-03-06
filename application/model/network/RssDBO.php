<?php

namespace model\network;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use \model\network\Rss as Rss;

/* import related objects */
use \model\network\Endpoint as Endpoint;
use \model\network\EndpointDBO as EndpointDBO;
use \model\network\Flux as Flux;
use \model\network\FluxDBO as FluxDBO;

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
		return sanitize_html_id($this->guid);
	}

	public function publishedMonthYear() {
		return $this->formattedDate( Rss::pub_date, "M Y" );
	}

	public function flux() {
		if ( isset($this->guid) ) {
			$model = Model::Named('Flux');
			return $model->objectForSrc_guid($this->guid);
		}
		return false;
	}

	public function publicationMatches()
	{
		$model = Model::Named('Publication');
		return $model->publicationsLike(
			$this->clean_name,
			$this->clean_issue,
			(intval($this->clean_year) > 1900 ? $this->clean_year : null)
		);
	}
}

?>
