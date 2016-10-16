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

	/** fail tracking */
	function increaseErrorCount()
	{
		$count = $this->error_count();
		if ( is_null($count) || intval($count) == 0) {
			$count = 1;
		}
		else {
			$count ++;
		}
		$this->setError_count($count);
		if ( $count > 5 ) {
			$this->setEnabled(false);
			Logger::logWarning("Endpoint automatically disabled.  Too many errors", $this->displayName(), $this->pkValue());
		}
		return $this->saveChanges();
	}

	public function clearErrorCount()
	{
		if ($this->error_count() > 0 ) {
			$this->setError_count(0);
			$this->setEnabled(true);
			return $this->saveChanges();
		}
		return true;
	}

	public function setJsonParameters(array $values = array())
	{
		if (is_array($values) && count($values) > 0 ) {
			$jsonData = json_encode($values);
			if ( json_last_error() != 0 ) {
				$jsonData = jsonErrorString(json_last_error()) . " " . var_export($values, true);
			}

			$this->setParameter($jsonData);
		}
		else {
			$this->setParameter(null);
		}
	}

	public function jsonParameters()
	{
		$jsonData = array();
		$raw = $this->parameter();

		if ( is_null($raw) == false ) {
			$override = json_decode($raw, true);
			if ( json_last_error() != 0 ) {
				return jsonErrorString(json_last_error()) . "'" . $raw . "'";
			}

			if (is_array($override) ) {
				foreach( $override as $key=>$value ) {
					$jsonData[$key] = $value;
				}
			}
		}
		return (isset($jsonData) ? $jsonData : array());
	}
}

?>
