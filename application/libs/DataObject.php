<?php

use \Database as Database;
use \Localized as Localized;
use \Logger as Logger;
use \Model as Model;
use \Config as Config;

class DataObject
{
	public $id;

	public static function NameForTable( $table_name = null)
	{
		if ( is_null($table_name) == false) {
			$parts = explode("_", $table_name);
			$parts = array_map('ucfirst', $parts);
			return 'model\\' . implode("_", $parts) . 'DBO';
		}
		return null;
	}

	public static function NameForModel(Model $model = null)
	{
		if ( is_null($model) == false) {
			return DataObject::NameForTable($model->tableName());
		}
		return null;
	}

	function __construct()
	{
	}

	public function pkValue() {
		return $this->id;
	}

	public function displayName() {
		return $this->modelName() . ' (' . $this->id . ')';
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
		return '[' . $this->modelName() . '(' . $this->pkValue() . ')] ' . $this->displayName();
	}

	public function formattedDate( $key, $format = 'M d, Y' ) {
		if (isset($key, $this->{$key})) {
			$val = $this->{$key};
			if ( is_numeric($val) ) {
				return date($format, $this->{$key});
			}

			return $val;
		}
		return '';
	}

	public function hasIcons()
	{
		$smIcon = $this->imagePath(Model::IconName);
		$lgIcon = $this->imagePath(Model::ThumbnailName);
		return (is_file($smIcon) && is_file($lgIcon));
	}

	public function imagePath($filename = null)
	{
		return hashedImagePath($this->tableName(), $this->id, $filename);
	}

	public function mediaPath($filename = null)
	{
		return hashedPath($this->tableName(), $this->id, $filename);
	}

	public function externalEndpoint()
	{
		if ( isset( $this->xsource) ) {
			$ep_model = Model::Named('Endpoint');
			$points = $ep_model->allForTypeCode($this->xsource);
			if ( is_array($points) && count($points) > 0) {
				return $points[0];
			}
		}
		return null;
	}
}
?>
