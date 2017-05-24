<?php

use \Database as Database;
use \Localized as Localized;
use \Logger as Logger;
use \Model as Model;
use \Config as Config;

abstract class DataObject implements JsonSerializable
{
	public $id;
	public $unsavedUpdates;

	function __construct()
	{
	}

	public function __call($method, $args)
	{
		if ( is_array($args) && count($args) == 1) {
			return dbo_setValueForKeypath( $method, $args[0], $this );
		}
		return dbo_valueForKeypath( $method, $this );
	}

	public abstract function pkValue();

	public function cacheKey() {
		return $this->modelName() . '-' . $this->pkValue();
	}

	public function displayName() {
		return $this->modelName() . ' (' . $this->pkValue() . ')';
	}

	public function displayDescription() {
		if ( isset($this->desc)) {
			return $this->desc;
		}
		return '';
	}

	public function shortDescription($maxLength = 100) {
		$full = $this->displayDescription();
		if ( is_string($full) && strlen($full) > $maxLength ) {
			return substr($full, 0 , $maxLength) . '...';
		}
		return $full;
	}

	public function publisher() {
		return false;
	}

	public function modelName() {
		return substr(get_short_class($this), 0, strpos(get_short_class($this), 'DBO'));
	}

	public function tableName()
	{
		return $this->model()->tableName();
	}

	public function model()
	{
		return Model::Named($this->modelName());
	}

	public function __toString()
	{
		$str = null;
		try {
			$str = '[' . get_called_class() . '(' . $this->pkValue() . ')] ' . $this->displayName();
		}
		catch ( \Exception $e ) {
			$str = get_class($this) . '__toString() : ' . $e;
		}
		return $str;
	}

	public function formattedDate( $key, $format = 'M d, Y' ) {
		if (isset($key)) {
			$val = dbo_valueForKeypath( $key, $this );
			if ( is_numeric($val) ) {
				return date($format, $val);
			}

			return $val;
		}
		return '';
	}

	public function lastXupdated() {
		return $this->formattedDate( "xupdated" );
	}

	public function hasAdditionalMedia()
	{
		$tbl = $this->tableName();
		return in_array($tbl, array('series', 'publication', 'media', 'story_arc', 'character', 'publisher', 'artist'));
	}

	public function hasIcons()
	{
		if ( $this->hasAdditionalMedia() == true ) {
			$smIcon = $this->imagePath(Model::IconName);
			$lgIcon = $this->imagePath(Model::ThumbnailName);
			return (is_file($smIcon) && is_file($lgIcon));
		}
		return false;
	}

	public function imagePath($filename = null)
	{
		if ( $this->hasAdditionalMedia() == true ) {
			return hashedImagePath($this->tableName(), $this->pkValue(), $filename);
		}
		return null;
	}

	public function mediaPath($filename = null)
	{
		if ( $this->hasAdditionalMedia() == true ) {
			return hashedPath($this->tableName(), $this->pkValue(), $filename);
		}
		return null;
	}

	public function hasExternalEndpoint()
	{
		$endpoint = $this->externalEndpoint();
		return (is_null($endpoint) ? false : true);
	}

	public function externalEndpoint()
	{
		if ( isset( $this->xsource) ) {
			$ep_model = Model::Named('Endpoint');
			$points = $ep_model->allForType_code($this->xsource);
			if ( is_array($points) && count($points) > 0) {
				return $points[0];
			}
		}
		return null;
	}

	public function neverEndpointUpdated()
	{
		if ( isset( $this->xsource) ) {
			return (isset($this->xupdated) == false);
		}
		return false;
	}

	public function needsEndpointUpdate()
	{
		if ( isset( $this->xsource) ) {
			return (isset($this->xupdated) == false || $this->xupdated < (time() - (3600 * 24 * 7)) );
		}
		return false;
	}

	public function needsEndpointUpdateString()
	{
		return ($this->needsEndpointUpdate() ? "Update yes" : "No Update needed");
	}

	public function isWanted() { return false; }

	/* notifications of changes in the object graph
	 */
	public function notify( $type = 'none', $object = null )
	{
		//echo PHP_EOL . PHP_EOL . "------------------".PHP_EOL."$this ->notify( $type,  $object)". PHP_EOL.PHP_EOL;
	}

	public function changedValue( $attr = null, $existing = null )
	{
		if ( is_null($attr) === false && is_array($this->unsavedUpdates) && array_key_exists($attr, $this->unsavedUpdates)) {
			return $this->unsavedUpdates[$attr];
		}
		return $existing;
	}

	public function storeChange( $attr = null, $value = null )
	{
		if ( is_null($attr) === false && $this->$attr != $value) {
			$this->unsavedUpdates[$attr] = $value;
		}
	}

	public function saveChanges()
	{
		if ( is_array($this->unsavedUpdates)) {
			list($obj, $errors) = $this->model()->updateObject( $this, $this->unsavedUpdates );
			if( is_array($errors) && count($errors) > 0) {
				$logMsg = "Validation errors update " . $this;
				foreach ($errors as $attr => $errMsg ) {
					$logMsg .= "\n\t" . $attr . " => " . $errMsg;
				}
				Logger::LogWarning( $logMsg, __METHOD__, $this->tableName() );
			}
			return ( $obj != false );
		}
		return true;
	}

	public function discardChanges()
	{
		unset( $this->unsavedUpdates );
	}

	/* self test for consistency */
	public function consistencyTest()
	{
		return "ok";
	}

	public function jsonSerialize ()
	{
		$data = array(
		  "_id" => $this->pkValue(),
		  "_type" => $this->modelName(),
		  "_uri" => Config::Web("Api", $this->modelName(), $this->pkValue() )
		);

		$attributes = $this->model()->attributes();
		foreach ($attributes as $name => $details ) {
			$type = $details['type'];
			$value = $this->$name();
			switch( $type ) {
				case 'DATE':
					$value = date("M d, Y H:i", $value);
					break;
				case 'BOOLEAN':
					$value = (boolValue($value) ? true : false);
					break;
			}

			$data[$name] = $value;
		}

		$relations = $this->model()->relationships();
		foreach ($relations as $name => $details ) {
			$data[$name] = Config::Web("Api", $this->modelName(), $this->pkValue(), $name );
// 			$destName = $details['destination'];
// 			$joins = $details['joins'];
// 			$joinValue = array();
// 			foreach( $joins as $src => $dest ) {
// 				$foreignValue = $this->$src();
// 				$joinValue[] = $dest;
// 				$joinValue[] = $foreignValue;
// 			}
// 			$data[$name] = Config::Web("Api", $destName, $joinValue );
		}

		return $data;
	}
}
?>
