<?php

namespace db;

use \Database as Database;
use \Localized as Localized;
use \Logger as Logger;
use \Model as Model;
use \Config as Config;
use \Metadata as Metadata;

class ImportData
{
	public function __construct($filename)
	{
		isset($filename) || die('Import filename is missing.');
		$this->metadata = new Metadata( $filename );
	}

	public function importAll()
	{
		$data = $this->metadata->readMetadata();
		foreach ( $data as $modelName => $recordArray ) {
			$this->import( Model::Named($modelName), $recordArray );
		}
	}

	public function importTable($tableName)
	{
		$data = $this->metadata->readMetadata();
		if ( isset( $data, $data[$tableName] ) ) {
			$this->import( Model::Named($tableName), $data[$tableName] );
		}
	}

	public function import( Model $model, array $records = array())
	{
		if ( $model != null ) {
			foreach( $records as $row ) {
				$pkColumn = $model->tablePK();
				$columns = array_keys($row);

				$existing = null;
				if ( in_array($pkColumn, $columns) ) {
					$pkValue = $row[$pkColumn];
					$select = \SQL::Select($model)->where( Qualifier::Equals( $pkColumn, $pkValue ));
					$existing = $select->fetch();

					echo $select->sqlStatement() . " $pkColumn = $pkValue " . PHP_EOL;
				}

				$changes = array();
				if ( is_null($existing) || ($existing instanceof \DataObject ) == false) {
					$statement = \SQL::Insert( $model );
					$statement->addRecord( $row );
					$statement->commitTransaction();
				}
				else {
// 					$statement = \SQL::UpdateObject( $existing );
				}

			}
		}
	}
}
