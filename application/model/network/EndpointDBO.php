<?php

namespace model\network;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use \SQL as SQL;
use db\Qualifier as Qualifier;

use \utilities\EndpointRequestCounter as EndpointRequestCounter;

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

	public function dailyMaximumStatus() {
		if ( intval($this->daily_max()) > 0 ) {
			$parse = parse_url($this->base_url);
			$maxCounter = new EndpointRequestCounter( $this );
			list($daily_max_count, $minapidate) = $maxCounter->count('daily_max');
			list($daily_dnld_count, $mindlnddate) = $maxCounter->count('daily_dnld_max');
			$overAPI = ($daily_max_count >= $this->daily_max());
			$overDNLD = ($daily_dnld_count >= $this->daily_dnld_max());

			return "API " . ($overAPI ?
					"<em>" . $daily_max_count ."/". $this->daily_max() . " next in " . formattedTimeElapsed(time() - $minapidate) . "</em>"
					: $daily_max_count ."/". $this->daily_max()
				) . "<hr>"
				. "Downloads " . ($overDNLD ?
					"<em>" . $daily_dnld_count ."/". $this->daily_dnld_max() . " next in " . formattedTimeElapsed(time() - $mindlnddate) . "</em>"
					: $daily_dnld_count ."/". $this->daily_dnld_max()
				);
		}
		return "";
	}

	public function markOverMaximum($type = 'daily_max')
	{
		if ( intval($this->daily_max()) > 0 ) {
			$maxCounter = new EndpointRequestCounter( $this );
			return $maxCounter->markOverMaximum($type);
		}
		return false;
	}

	public function isOverMaximum($type = 'daily_max')
	{
		if ( intval($this->daily_max()) > 0 ) {
			$maxCounter = new EndpointRequestCounter( $this );
			return $maxCounter->isOverMaximum($type);
		}
		return false;
	}

	public function rssCount()
	{
		$model = Model::Named('Rss');
		$ep_qualifier = Qualifier::Equals( Rss::endpoint_id, $this->pkValue() );
		$results = array();

		foreach( [1, 7, 14] as $age ) {
			$ageTime = 86400 * $age;
			$age_qualifier = Qualifier::GreaterThan( Rss::pub_date, (time() - $ageTime) );
			$count = SQL::Count( $model, null, Qualifier::AndQualifier( [$ep_qualifier, $age_qualifier] ) )->fetch();
			$results[$ageTime] = $count->count;
		}
		return $results;
	}

	public function fluxSrcCount()
	{
		$model = Model::Named('Flux');
		$ep_qualifier = Qualifier::Equals( Flux::src_endpoint, $this->pkValue() );
		$results = array();

		foreach( [1, 7, 14, 30] as $age ) {
			$ageTime = 86400 * $age;
			$age_qualifier = Qualifier::GreaterThan( Flux::created, (time() - $ageTime) );
			$count = SQL::Count( $model, [Flux::src_status], Qualifier::AndQualifier( [$ep_qualifier, $age_qualifier] ) )->fetchAll();
			$results[$ageTime] = $count;
		}
		return $results;
	}

	public function fluxDestCount()
	{
		$model = Model::Named('Flux');
		$ep_qualifier = Qualifier::Equals( Flux::dest_endpoint, $this->pkValue() );
		$results = array();

		foreach( [1, 7, 14, 30] as $age ) {
			$ageTime = 86400 * $age;
			$age_qualifier = Qualifier::GreaterThan( Flux::dest_submission, (time() - $ageTime) );
			$count = SQL::Count( $model, [Flux::dest_status], Qualifier::AndQualifier( [$ep_qualifier, $age_qualifier] ) )->fetchAll();
			$results[$ageTime] = $count;
		}
		return $results;
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
