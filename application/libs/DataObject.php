<?php

	class DataObject
	{
		public $id;

		function __construct()
		{
		}

		public function pkValue() {
			return $this->id;
		}

		public function displayName() {
			return $this->id;
		}

		public function modelName() {
			$reflect = new ReflectionClass($this);
			return substr($reflect->getShortName(), 0, strpos($reflect->getShortName(), 'DBO'));
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
			return $this->modelName() . ' (' . $this->pkValue() . ')';
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
	}
?>
