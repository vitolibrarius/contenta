<?php

namespace model\network;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use \model\network\Rss as Rss;

/* import related objects */
use \model\Endpoint as Endpoint;
use \model\EndpointDBO as EndpointDBO;

abstract class _RssDBO extends DataObject
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
		return (isset($this->enclosure_password) && $this->enclosure_password == Model::TERTIARY_TRUE);
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


	/** Attributes */
	public function endpoint_id()
	{
		return parent::changedValue( Rss::endpoint_id, $this->endpoint_id );
	}

	public function setEndpoint_id( $value = null)
	{
		parent::storeChange( Rss::endpoint_id, $value );
	}

	public function title()
	{
		return parent::changedValue( Rss::title, $this->title );
	}

	public function setTitle( $value = null)
	{
		parent::storeChange( Rss::title, $value );
	}

	public function desc()
	{
		return parent::changedValue( Rss::desc, $this->desc );
	}

	public function setDesc( $value = null)
	{
		parent::storeChange( Rss::desc, $value );
	}

	public function pub_date()
	{
		return parent::changedValue( Rss::pub_date, $this->pub_date );
	}

	public function setPub_date( $value = null)
	{
		parent::storeChange( Rss::pub_date, $value );
	}

	public function guid()
	{
		return parent::changedValue( Rss::guid, $this->guid );
	}

	public function setGuid( $value = null)
	{
		parent::storeChange( Rss::guid, $value );
	}

	public function clean_name()
	{
		return parent::changedValue( Rss::clean_name, $this->clean_name );
	}

	public function setClean_name( $value = null)
	{
		parent::storeChange( Rss::clean_name, $value );
	}

	public function clean_issue()
	{
		return parent::changedValue( Rss::clean_issue, $this->clean_issue );
	}

	public function setClean_issue( $value = null)
	{
		parent::storeChange( Rss::clean_issue, $value );
	}

	public function clean_year()
	{
		return parent::changedValue( Rss::clean_year, $this->clean_year );
	}

	public function setClean_year( $value = null)
	{
		parent::storeChange( Rss::clean_year, $value );
	}

	public function enclosure_url()
	{
		return parent::changedValue( Rss::enclosure_url, $this->enclosure_url );
	}

	public function setEnclosure_url( $value = null)
	{
		parent::storeChange( Rss::enclosure_url, $value );
	}

	public function enclosure_length()
	{
		return parent::changedValue( Rss::enclosure_length, $this->enclosure_length );
	}

	public function setEnclosure_length( $value = null)
	{
		parent::storeChange( Rss::enclosure_length, $value );
	}

	public function enclosure_mime()
	{
		return parent::changedValue( Rss::enclosure_mime, $this->enclosure_mime );
	}

	public function setEnclosure_mime( $value = null)
	{
		parent::storeChange( Rss::enclosure_mime, $value );
	}

	public function enclosure_hash()
	{
		return parent::changedValue( Rss::enclosure_hash, $this->enclosure_hash );
	}

	public function setEnclosure_hash( $value = null)
	{
		parent::storeChange( Rss::enclosure_hash, $value );
	}

	public function enclosure_password()
	{
		return parent::changedValue( Rss::enclosure_password, $this->enclosure_password );
	}

	public function setEnclosure_password( $value = null)
	{
		parent::storeChange( Rss::enclosure_password, $value );
	}


}

?>
