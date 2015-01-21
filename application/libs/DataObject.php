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
			$className = get_called_class();
			return substr($className, 0, strpos($className, 'DBO'));
		}

		public function model()
		{
			return loadModel($this->modelName());
		}

		public function __toString()
		{
			return $this->modelName() . ' (' . $this->pkValue() . ')';
		}

		public function formattedDate( $key ) {
			if (isset($key, $this->{$key})) {
				$val = $this->{$key};
				if ( is_numeric($val) ) {
					return date('M d, Y', $this->{$key});
				}

				return $val;
			}
			return '';
		}
	}
?>
