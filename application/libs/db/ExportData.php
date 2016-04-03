<?php

namespace db;

use \Exception as Exception;
use \Database as Database;
use \Localized as Localized;
use \Logger as Logger;
use \Model as Model;
use \Config as Config;
use \Metadata as Metadata;

abstract class ExportData
{
	public $db;
	public $exportDir;
	public $primaryKeyMap;

	public function __construct($exportDir, Database $sourceDatabase)
	{
		isset($exportDir) || die('Export directory is required.');
		is_dir($exportDir) == false || die('Export directory already exists.');
		mkdir($exportDir, DIR_PERMS, true) || die('Failed to create Export directory. ' . $exportDir);
		isset($sourceDatabase) || die('Source database is required.');

		$this->db = $sourceDatabase;
		$this->exportDir = $exportDir;
	}

	public abstract function execute_sql( $sql = null, $params = array(), $comment = null );
	public abstract function tableNames();
	public abstract function primaryKeysForTable($tablename);
	public abstract function batch($table, $page = 0, $page_size = 500 );

	public function primaryKeys( $tablename )
	{
		isset($tablename) || die('primaryKeys for tablename requires a table name.');
		if ( isset( $this->primaryKeyMap[$tablename] ) == false ) {
			$keys = $this->primaryKeysForTable($tablename);
			sort($keys);
			$this->primaryKeyMap[$tablename] = $keys;
		}
		return $this->primaryKeyMap[$tablename];
	}

	public function directoryFor( $tablename, array $ids = null )
	{
		isset($tablename) || die('directory for tablename requires a table name.');
		$retval = appendPath( $this->exportDir, $tablename );
		if ( is_dir($retval) == false ) {
			mkdir($retval, DIR_PERMS, true) || die('Failed to create Export sub-directory ' . $retval . '.');
		}

		if ( is_null($ids) == false && count($ids) > 0 ) {
			$idDir = implode('.', array_map(
				function ($v, $k) { return $k . '_' . $v; }, $ids, array_keys($ids)
				)
			);
			$retval = appendPath( $this->exportDir, $tablename, $idDir );
			if ( is_dir($retval) == false ) {
				mkdir($retval, DIR_PERMS, true) || die('Failed to create Export sub-directory ' . $retval . '.');
			}
		}
		return $retval;
	}

	public function exportAll()
	{
		$tables = $this->tableNames();
		foreach( $tables as $aTable ) {
			$this->exportTable( $aTable );
		}
		print PHP_EOL;
	}

	public function exportTable( $table = null )
	{
		if ( is_null($table) ) {
			throw new Exception("Unable to export for -null- table");
		}

		$table_method = 'exportRow_' . $table;
		if (method_exists($this, $table_method) == false) {
			$table_method = 'exportRow';
		}

		print PHP_EOL . "Exporting " . $table;
		$batch = 0;
		$count = 0;
		while ( true ) {
			$data = $this->batch( $table, $batch );
			if ( $data == false )  {
				break;
			}

			foreach( $data as $row ) {
				$success_return = $this->$table_method($table, $row);
				if ( is_null($success_return) || $success_return == false ) {
					throw new Exception("export error " . $table_method );
				}
			}
			$batch ++;
			$count += count($data);
			print " " . $count;
		}
	}

	public function primaryKeyValues( $table, $row )
	{
		$pks = $this->primaryKeys($table);
		foreach( $pks as $key ) {
			$pkValues[$key] = $row->{$key};
		}
		return $pkValues;
	}

	public function exportRow( $table, $row )
	{
		$pkValues = $this->primaryKeyValues( $table, $row );
		$directory = $this->directoryFor( $table, $pkValues );
		$metadatafile = appendPath($directory, "data.json" );
		$returnValue = file_put_contents( $metadatafile, json_encode($row, JSON_PRETTY_PRINT));
		if ( json_last_error() != 0 ) {
			throw new \Exception( jsonErrorString(json_last_error()) );
		}
		return $returnValue;
	}

	public function exportRowIcons( $table, $row )
	{
		$pkValues = $this->primaryKeyValues( $table, $row );
		$directory = $this->directoryFor( $table, $pkValues );

		$smIcon = hashedImagePath($table, $row->id, Model::IconName);
		$lgIcon = hashedImagePath($table, $row->id, Model::ThumbnailName);

		if ( is_file( $smIcon ) ) {
			$dest = appendPath( $directory, Model::IconName ) . "." . file_ext($smIcon);
			if (copy($smIcon, $dest ) == false) {
				throw new \Exception( "Failed to copy $smIcon to $dest" );
			}
		}
		if ( is_file( $lgIcon ) ) {
			$dest = appendPath( $directory, Model::ThumbnailName ) . "." . file_ext($lgIcon);
			if (copy($lgIcon, $dest ) == false) {
				throw new \Exception( "Failed to copy $lgIcon to $dest" );
			}
		}
		return true;
	}

	public function exportRow_character( $table, $row )
	{
		$returnValue = $this->exportRow( $table, $row );
		if ( $returnValue ) {
			$returnValue = $this->exportRowIcons( $table, $row );
		}
		return $returnValue;
	}

	public function exportRow_story_arc( $table, $row )
	{
		$returnValue = $this->exportRow( $table, $row );
		if ( $returnValue ) {
			$returnValue = $this->exportRowIcons( $table, $row );
		}
		return $returnValue;
	}

	public function exportRow_series( $table, $row )
	{
		$returnValue = $this->exportRow( $table, $row );
		if ( $returnValue ) {
			$returnValue = $this->exportRowIcons( $table, $row );
		}
		return $returnValue;
	}

	public function exportRow_publisher( $table, $row )
	{
		$returnValue = $this->exportRow( $table, $row );
		if ( $returnValue ) {
			$returnValue = $this->exportRowIcons( $table, $row );
		}
		return $returnValue;
	}

	public function exportRow_publication( $table, $row )
	{
		$returnValue = $this->exportRow( $table, $row );
		if ( $returnValue ) {
			$returnValue = $this->exportRowIcons( $table, $row );
		}
		return $returnValue;
	}

	public function exportRow_media( $table, $row )
	{
		$returnValue = $this->exportRow( $table, $row );
		if ( $returnValue && isset($row->filename) ) {
			$pkValues = $this->primaryKeyValues( $table, $row );
			$directory = $this->directoryFor( $table, $pkValues );
			$media = hashedPath($table, $row->id, $row->filename);

			if ( is_file( $media ) ) {
				$dest = appendPath( $directory, $row->filename );
				if (copy($media, $dest ) == false) {
					throw new \Exception( "Failed to copy $media to $dest" );
				}
			}
			else {
				throw new \Exception( "Failed to find $media" );
			}
		}
		return $returnValue;
	}

	public function exportRow_log( $table, $row )
	{
		return true;
	}

	public function exportRow_job_running( $table, $row )
	{
		return true;
	}
}

class ExportData_sqlite extends ExportData
{
	public function tableNames()
	{
		$sql = "SELECT name FROM sqlite_master WHERE type='table'";
		$rows = $this->execute_sql($sql);
		$results = array();
		foreach( $rows as $row ) {
			$results[] = (isset($row->name) ? $row->name : 'error');
		}
		return $results;
	}

	public function primaryKeysForTable($tablename)
	{
		$sql = "PRAGMA table_info(" . $tablename . ")";
		$rows = $this->execute_sql($sql);
		$results = array();
		foreach( $rows as $row ) {
			if ( isset($row->pk) && $row->pk != 0 ) {
				$results[] = (isset($row->name) ? $row->name : 'error');
			}
		}
		return $results;

	}

	public function batch($table, $page = 0, $page_size = 500 )
	{
		$pk = $this->primaryKeys( $table );
		$sql = "select * from " . $table
			. " order by " . implode(",", $pk)
			. " limit " . $page_size
			. " offset " . ($page * $page_size);
		return $this->execute_sql($sql);;
	}

	public function execute_sql( $sql = null, $params = array(), $comment = null )
	{
		if ( is_null($sql) ) {
			throw new Exception("Unable to execute SQL for -null- statement");
		}
		else {
			$statement = $this->db->prepare($sql);
			if ($statement == false || $statement->execute($params) == false) {
				$errPoint = ($statement ? $statement : Database::instance());
				$pdoError = $errPoint->errorInfo()[1] . ':' . $errPoint->errorInfo()[2];
				throw new Exception("Error executing " . $sql . " " . var_export($params, true));
			}
		}

		if ( is_null($comment) == false) {
			print $comment . PHP_EOL;
		}

		return $statement->fetchAll();
	}

}
