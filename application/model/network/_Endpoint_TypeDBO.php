<?php

namespace model\network;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use \model\network\Endpoint_Type as Endpoint_Type;

/* import related objects */
use \model\network\Endpoint as Endpoint;
use \model\network\EndpointDBO as EndpointDBO;
use \model\pull_list\Pull_List_Exclusion as Pull_List_Exclusion;
use \model\pull_list\Pull_List_ExclusionDBO as Pull_List_ExclusionDBO;
use \model\pull_list\Pull_List_Expansion as Pull_List_Expansion;
use \model\pull_list\Pull_List_ExpansionDBO as Pull_List_ExpansionDBO;

abstract class _Endpoint_TypeDBO extends DataObject
{
	public $code;
	public $name;
	public $comments;
	public $data_type;
	public $site_url;
	public $api_url;
	public $favicon_url;
	public $throttle_hits;
	public $throttle_time;

	public function displayName()
	{
		return $this->name;
	}

	public function pkValue()
	{
		return $this->{Endpoint_Type::code};
	}

	public function modelName()
	{
		return "Endpoint_Type";
	}

	public function dboName()
	{
		return "\model\network\Endpoint_TypeDBO";
	}


	// to-many relationship
	public function endpoints()
	{
		if ( isset( $this->code ) ) {
			$model = Model::Named('Endpoint');
			return $model->allObjectsForKeyValue( Endpoint::type_code, $this->code);
		}

		return false;
	}

	// to-many relationship
	public function pull_list_exclusions()
	{
		if ( isset( $this->code ) ) {
			$model = Model::Named('Pull_List_Exclusion');
			return $model->allObjectsForKeyValue( Pull_List_Exclusion::endpoint_type_code, $this->code);
		}

		return false;
	}

	// to-many relationship
	public function pull_list_expansions()
	{
		if ( isset( $this->code ) ) {
			$model = Model::Named('Pull_List_Expansion');
			return $model->allObjectsForKeyValue( Pull_List_Expansion::endpoint_type_code, $this->code);
		}

		return false;
	}


	/** Attributes */
	public function name()
	{
		return parent::changedValue( Endpoint_Type::name, $this->name );
	}

	public function setName( $value = null)
	{
		parent::storeChange( Endpoint_Type::name, $value );
	}

	public function comments()
	{
		return parent::changedValue( Endpoint_Type::comments, $this->comments );
	}

	public function setComments( $value = null)
	{
		parent::storeChange( Endpoint_Type::comments, $value );
	}

	public function data_type()
	{
		return parent::changedValue( Endpoint_Type::data_type, $this->data_type );
	}

	public function setData_type( $value = null)
	{
		parent::storeChange( Endpoint_Type::data_type, $value );
	}

	public function site_url()
	{
		return parent::changedValue( Endpoint_Type::site_url, $this->site_url );
	}

	public function setSite_url( $value = null)
	{
		parent::storeChange( Endpoint_Type::site_url, $value );
	}

	public function api_url()
	{
		return parent::changedValue( Endpoint_Type::api_url, $this->api_url );
	}

	public function setApi_url( $value = null)
	{
		parent::storeChange( Endpoint_Type::api_url, $value );
	}

	public function favicon_url()
	{
		return parent::changedValue( Endpoint_Type::favicon_url, $this->favicon_url );
	}

	public function setFavicon_url( $value = null)
	{
		parent::storeChange( Endpoint_Type::favicon_url, $value );
	}

	public function throttle_hits()
	{
		return parent::changedValue( Endpoint_Type::throttle_hits, $this->throttle_hits );
	}

	public function setThrottle_hits( $value = null)
	{
		parent::storeChange( Endpoint_Type::throttle_hits, $value );
	}

	public function throttle_time()
	{
		return parent::changedValue( Endpoint_Type::throttle_time, $this->throttle_time );
	}

	public function setThrottle_time( $value = null)
	{
		parent::storeChange( Endpoint_Type::throttle_time, $value );
	}


}

?>
