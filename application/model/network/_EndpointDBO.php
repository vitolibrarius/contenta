<?php

namespace model\network;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

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

abstract class _EndpointDBO extends DataObject
{
	public $type_code;
	public $name;
	public $base_url;
	public $api_key;
	public $username;
	public $daily_max;
	public $daily_dnld_max;
	public $error_count;
	public $parameter;
	public $enabled;
	public $compressed;

	public function displayName()
	{
		return $this->name;
	}

	public function pkValue()
	{
		return $this->{Endpoint::id};
	}

	public function modelName()
	{
		return "Endpoint";
	}

	public function dboName()
	{
		return "\model\network\EndpointDBO";
	}

	public function isEnabled() {
		return (isset($this->enabled) && $this->enabled == Model::TERTIARY_TRUE);
	}

	public function isCompressed() {
		return (isset($this->compressed) && $this->compressed == Model::TERTIARY_TRUE);
	}


	// to-one relationship
	public function endpointType()
	{
		if ( isset( $this->type_code ) ) {
			$model = Model::Named('Endpoint_Type');
			return $model->objectForCode($this->type_code);
		}
		return false;
	}

	public function setEndpointType(Endpoint_TypeDBO $obj = null)
	{
		if ( isset($obj, $obj->code) && (isset($this->type_code) == false || $obj->code != $this->type_code) ) {
			parent::storeChange( Endpoint::type_code, $obj->code );
			$this->saveChanges();
		}
	}

	// to-many relationship
	public function pull_lists($limit = SQL::SQL_DEFAULT_LIMIT)
	{
		if ( isset( $this->id ) ) {
			$model = Model::Named('Pull_List');
			return $model->allObjectsForKeyValue(
				Pull_List::endpoint_id,
				$this->id,
				null,
				$limit
			);
		}

		return false;
	}

	// to-many relationship
	public function rss($limit = SQL::SQL_DEFAULT_LIMIT)
	{
		if ( isset( $this->id ) ) {
			$model = Model::Named('Rss');
			return $model->allObjectsForKeyValue(
				Rss::endpoint_id,
				$this->id,
				null,
				$limit
			);
		}

		return false;
	}

	// to-many relationship
	public function flux_sources($limit = SQL::SQL_DEFAULT_LIMIT)
	{
		if ( isset( $this->id ) ) {
			$model = Model::Named('Flux');
			return $model->allObjectsForKeyValue(
				Flux::src_endpoint,
				$this->id,
				null,
				$limit
			);
		}

		return false;
	}

	// to-many relationship
	public function flux_destinations($limit = SQL::SQL_DEFAULT_LIMIT)
	{
		if ( isset( $this->id ) ) {
			$model = Model::Named('Flux');
			return $model->allObjectsForKeyValue(
				Flux::dest_endpoint,
				$this->id,
				null,
				$limit
			);
		}

		return false;
	}

	// to-many relationship
	public function jobs($limit = SQL::SQL_DEFAULT_LIMIT)
	{
		if ( isset( $this->id ) ) {
			$model = Model::Named('Job');
			return $model->allObjectsForKeyValue(
				Job::endpoint_id,
				$this->id,
				null,
				$limit
			);
		}

		return false;
	}


	/** Attributes */
	public function name()
	{
		return parent::changedValue( Endpoint::name, $this->name );
	}

	public function setName( $value = null)
	{
		parent::storeChange( Endpoint::name, $value );
	}

	public function base_url()
	{
		return parent::changedValue( Endpoint::base_url, $this->base_url );
	}

	public function setBase_url( $value = null)
	{
		parent::storeChange( Endpoint::base_url, $value );
	}

	public function api_key()
	{
		return parent::changedValue( Endpoint::api_key, $this->api_key );
	}

	public function setApi_key( $value = null)
	{
		parent::storeChange( Endpoint::api_key, $value );
	}

	public function username()
	{
		return parent::changedValue( Endpoint::username, $this->username );
	}

	public function setUsername( $value = null)
	{
		parent::storeChange( Endpoint::username, $value );
	}

	public function daily_max()
	{
		return parent::changedValue( Endpoint::daily_max, $this->daily_max );
	}

	public function setDaily_max( $value = null)
	{
		parent::storeChange( Endpoint::daily_max, $value );
	}

	public function daily_dnld_max()
	{
		return parent::changedValue( Endpoint::daily_dnld_max, $this->daily_dnld_max );
	}

	public function setDaily_dnld_max( $value = null)
	{
		parent::storeChange( Endpoint::daily_dnld_max, $value );
	}

	public function error_count()
	{
		return parent::changedValue( Endpoint::error_count, $this->error_count );
	}

	public function setError_count( $value = null)
	{
		parent::storeChange( Endpoint::error_count, $value );
	}

	public function parameter()
	{
		return parent::changedValue( Endpoint::parameter, $this->parameter );
	}

	public function setParameter( $value = null)
	{
		parent::storeChange( Endpoint::parameter, $value );
	}

	public function enabled()
	{
		return parent::changedValue( Endpoint::enabled, $this->enabled );
	}

	public function setEnabled( $value = null)
	{
		parent::storeChange( Endpoint::enabled, $value );
	}

	public function compressed()
	{
		return parent::changedValue( Endpoint::compressed, $this->compressed );
	}

	public function setCompressed( $value = null)
	{
		parent::storeChange( Endpoint::compressed, $value );
	}


}

?>
