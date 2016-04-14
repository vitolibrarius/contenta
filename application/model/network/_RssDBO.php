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

class _RssDBO extends DataObject
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


	public function formattedDateTime_created() { return $this->formattedDate( Rss::created, "M d, Y H:i" ); }
	public function formattedDate_created() {return $this->formattedDate( Rss::created, "M d, Y" ); }

	public function formattedDateTime_pub_date() { return $this->formattedDate( Rss::pub_date, "M d, Y H:i" ); }
	public function formattedDate_pub_date() {return $this->formattedDate( Rss::pub_date, "M d, Y" ); }

	public function isEnclosure_password() {
		return (isset($this->enclosure_password) && $this->enclosure_password == 1);
	}


	// to-one relationship
	public function endpoint()
	{
		if ( isset( $this->endpoint_id ) ) {
			$model = Model::Named('Endpoint');
			return $model->objectForId($this->endpoint_id);
		}
		return false;
	}

	// to-one relationship
	public function flux()
	{
		if ( isset( $this->guid ) ) {
			$model = Model::Named('Flux');
			return $model->objectForSrc_guid($this->guid);
		}
		return false;
	}

}

?>
