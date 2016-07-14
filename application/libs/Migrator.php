<?php

class MigrationFailedException extends \Exception {}

use \SQL as SQL;
use \model\version\Version as Version;
use \model\version\VersionDBO as VersionDBO;
use \model\version\Patch as Patch;
use \model\version\PatchDBO as PatchDBO;

class Migrator
{
	const IDX_TABLE = 'index_table';
	const IDX_COLS = 'index_columns';
	const IDX_UNIQUE = 'index_unique';

	public static function Upgrade($scratchDirectory)
	{
		// bump our execution time
		ini_set('max_execution_time', 300);

		// evaluate the database version first
		$dbversion = Database::DBVersion();
		$dbpatch = Database::DBPatchLevel();
		$currentVersionNumber = currentVersionNumber();

		if ( $dbversion == 0 ) {
			$dbConnection = new Database();
			$specialMap = array(
				"log" => array(
					"level" => "level_code"
				),
				"users" => array(
					"creation_timestamp" => "created"
				)
			);
			// move - rename existing data tables
			$oldTableNames = $dbConnection->dbTableNames();
			$oldTableMap = array();
			foreach( $oldTableNames as $oldTable ) {
				if ( endsWith( '_old', $oldTable ) == false ) {
					$oldTableMap[$oldTable] = $dbConnection->dbPKForTable($oldTable);
					$dbConnection->dbTableRename( $oldTable, $oldTable . '_old' );

					Migrator::FixOldDataIssues( $dbConnection,  $oldTable . '_old' );
				}
			}
			unset($dbConnection);
			Database::ResetConnection();

			// re-create data tables
			Migrator::ApplyNeededMigrations($scratchDirectory);

			// reload old data
			$skipTables = array( "version", "patch", "log_level", "endpoint_type", "job_type", "media_type", "pull_list_excl", "pull_list_expansion");
			foreach( $oldTableMap as $tblName => $pkArray ) {
				if ( in_array( $tblName, $skipTables ) == false ) {
					Migrator::LoadDataFromTo(
						$tblName . "_old",
						$tblName,
						(isset($specialMap[$tblName]) ? $specialMap[$tblName] : null)
					);
				}
			}
			Database::ResetConnection();

			// drop old data
// 			$dbConnection = new Database();
// 			$oldTableNames = $dbConnection->dbTableNames();
// 			foreach( $oldTableNames as $oldTable ) {
// 				if ( endsWith( '_old', $oldTable ) == true ) {
// 					Logger::logInfo( "drop table " . $oldTable, "Migrator", "Upgrade" );
// 					$dbConnection->execute_sql( "drop table " . $oldTable );
// 				}
// 			}
// 			$dbConnection->dbOptimize();
// 			unset($dbConnection);


			// update database version
			$dbversion = Database::DBVersion(Database::CONTENTA_DB_VERSION);
		}
		else if ($dbversion == Database::CONTENTA_DB_VERSION ) {
			Migrator::ApplyNeededMigrations($scratchDirectory);
		}
		else {
			throw new MigrationFailedException("Unknown database version '$dbversion'");
		}

		// ensure application patch version is up to date
		$version_model = Model::Named("Version");
		$version = $version_model->objectForCode( $currentVersionNumber );
		if ( $version == false ) {
			list($version, $errors) = $version_model->createObject( array( Version::code => $currentVersionNumber ));
		}
		Logger::logInfo( "Migration complete for version $currentVersionNumber", "Migrator", "Upgrade" );
	}

	private static function FixOldDataIssues( $dbConnection, $oldTable )
	{
		$allColumns = $dbConnection->dbTableInfo( $oldTable );
		if ( is_array( $allColumns ) ) {
			switch ( $oldTable ) {
				case 'job_running_old':
					$dbConnection->execute_sql("alter table ".$oldTable." add column type_code TEXT");
					$dbConnection->execute_sql("update ".$oldTable." set type_code = (select code from job_type_old where job_type_old.id = ".$oldTable.".job_type_id)");
					break;
				case 'job_old':
					$dbConnection->execute_sql("alter table ".$oldTable." add column type_code TEXT");
					$dbConnection->execute_sql("update ".$oldTable." set type_code = (select code from job_type_old where job_type_old.id = ".$oldTable.".type_id)");
					break;
				case 'endpoint_old':
					$dbConnection->execute_sql("alter table ".$oldTable." add column type_code TEXT");
					$dbConnection->execute_sql("update ".$oldTable." set type_code = (select code from endpoint_type_old where endpoint_type_old.id = ".$oldTable.".type_id)");
					break;
				case 'pull_list_excl_old':
				case 'pull_list_expansion_old':
					$dbConnection->execute_sql("alter table ".$oldTable." add column endpoint_type_code TEXT");
					$dbConnection->execute_sql("update ".$oldTable." set endpoint_type_code = (select code from endpoint_type_old where endpoint_type_old.id = ".$oldTable.".endpoint_type_id)");
					break;
				case 'media_old':
					$dbConnection->execute_sql("alter table ".$oldTable." add column type_code TEXT");
					$dbConnection->execute_sql("update ".$oldTable." set type_code = (select code from media_type_old where media_type_old.id = ".$oldTable.".type_id)");
					break;
				case 'series_old':
					$dbConnection->execute_sql("update series_old set search_name = lower(name) where search_name is null or length(search_name) < 2");
					break;
				case 'rss_old':
					$dbConnection->execute_sql("delete from rss_old where guid in "
						. "(select guid from rss_old group by guid having count(*) > 1)"
					);
					break;
				case 'series_character_old':
					$count_sql = "select count(*) as COUNT from series_character_old as s "
						. " inner join (select d.series_id, d.character_id from series_character_old d group by d.series_id, d.character_id having count(*) > 1) "
						. " as dup on s.series_id = dup.series_id and s.character_id = dup.character_id";
					$count = $dbConnection->dbFetchRawCountForSQL($count_sql);
					while ( is_int($count) && intval($count) > 0 ) {
						$dbConnection->execute_sql("delete from series_character_old where id in "
							. "(select MIN(id) from series_character_old group by series_id, character_id having count(*) > 1)"
						);
						$count = $dbConnection->dbFetchRawCountForSQL($count_sql);
					}
					break;
				default:
					break;
			}
		}
	}

	private static function LoadDataFromTo( $srcTable, $destTable, $specialMappings = array())
	{
		$dbConnection = new Database();
		$srcCount = $dbConnection->dbFetchRawCount( $srcTable );
		if ( $srcCount == 0 ) {
			return true;
		}

		$srcColumns = $dbConnection->dbTableInfo( $srcTable );
		$destColumns = $dbConnection->dbTableInfo( $destTable );
		if ( $srcColumns == false ) {
			Logger::logWarning( "Source $srcTable does not exist", "Migrator", "LoadDataFromTo" );
			return false;
		}
		else if ( $destColumns == false ) {
			Logger::logWarning( "Destination $destTable does not exist", "Migrator", "LoadDataFromTo" );
			return false;
		}

		$copySrcToDestColumn = array();
		foreach( $srcColumns as $sCol => $sDetails ) {
			$dCol = $sCol;
			if ( isset( $specialMappings[$sCol] ) ) {
				$dCol = $specialMappings[$sCol];
			}

			if ( isset($destColumns[$dCol]) ) {
				$copySrcToDestColumn[$sCol] = $dCol;
			}
			else {
				Logger::logWarning( "$srcTable column '$sCol' will be dropped from $destTable", "Migrator", "LoadDataFromTo" );
			}
		}

		/* this could be faster using a select .. into but this way makes it easier to identify specific data issues */
		$batch = 0;
		$count = 0;
		while ( true ) {
			$data = $dbConnection->dbFetchRawBatch( $srcTable, $batch );
			if ( $data == false )  {
				break;
			}

			foreach( $data as $row ) {
				$insertKeys = array();
				$insertParams = array();
				$insertValues = array();
				foreach( $row as $column => $value ) {
					if ( isset($copySrcToDestColumn[$column]) ) {
						$insertKeys[] = $copySrcToDestColumn[$column];
						$insertParams[] = ":".$column;
						$insertValues[":".$column] = $value;
					}
				}
				$sql = "INSERT INTO " . $destTable . " (" . implode(",", $insertKeys) . ") values (" . implode(",", $insertParams) . ")";
				$dbConnection->execute_sql($sql, $insertValues);
			}
			$batch ++;
			$count += count($data);
		}

		$destCount = $dbConnection->dbFetchRawCount( $destTable );
		if ( $destCount != $srcCount ) {
			throw new \Exception( "Counts mismatch $srcTable $srcCount != $destCount" );
		}
	}

	private static function ApplyNeededMigrations($scratchDirectory)
	{
		$currentVersionNumber = currentVersionNumber();
		Logger::logInfo( "Migrating application to version " . $currentVersionNumber,	"Migrator", "Upgrade" );
		$patch_model = Model::Named("Patch");
		$patches = array();
		try {
			$fetch = \SQL::Select( $patch_model, array("name"))->fetchAll();
			if ( is_array($fetch) ) {
				foreach( $fetch as $row ) {
					$patches[] = $row->name;
				}
			}
		}
		catch ( \PDOException $pdox ) {
			if ( $pdox->getCode() != 'HY000' ) {
				Logger::logException( $pdox );
				die( "database migration error selecting patches" );
			}
		}

		$number = 0;
		$migrationClass = 'migration\\Migration_' . $number;
		try {
			while ( class_exists( $migrationClass ) ) {
				if ( in_array($migrationClass, $patches) == false ) {
					$worker = new $migrationClass($scratchDirectory);
					$workerTargetVersion = $worker->targetVersion();

					Logger::logInfo("Starting $migrationClass for " . $workerTargetVersion);

					if ( $worker->canMigrateToVersion( $currentVersionNumber ) ) {
						$success = $worker->performMigration();
						if ( $success ) {
							$version = $worker->targetVersionDBO();
							list($patch, $errors) = $patch_model->createObject( array( "version" => $version, "name" => $migrationClass));
							if ( ($patch instanceof PatchDBO ) == false) {
								throw new MigrationFailedException("Failed to create migration 'patch' record " . $migrationClass);
							}
						}
						else {
							break;
						}
					}
				}
				$number++;
				$migrationClass = 'migration\\Migration_' . $number;
			}
		}
		catch (\Exception $e) {
			if ( is_a( $e, "ClassNotFoundException" ) && $e->getMessage() === $migrationClass ) {
				// this marks the end of the Migration
			}
			else {
				// something else?
				Logger::logException( $e );
				die( "database migration error " . $e );
			}
		}
		finally {
			Database::ResetConnection();
		}
	}

	public function __construct($scratchDirectory)
	{
		is_dir($scratchDirectory) || die("Scratch direcotry is not valid '" . $scratchDirectory . "'");
		$this->scratch = $scratchDirectory;
	}

	private function performMigration()
	{
		try {
			$db_type = Config::Get("Database/type");
			if ( is_string($db_type) ) {
				$preFunction = $db_type . '_preUpgrade';
				$upgradeFunction = $db_type . '_upgrade';
				$postFunction = $db_type . '_postUpgrade';
				$missingFunctions = false;

				if (method_exists($this, $preFunction) == false) {
					$missingFunctions = true;
					Logger::logError( get_class($this) . ": failed to find pre-upgrade function " . $preFunction,
						"Migration", "preUpgrade" );
				}

				if (method_exists($this, $upgradeFunction) == false) {
					$missingFunctions = true;
					Logger::logError( get_class($this) . ": failed to find upgrade function " . $upgradeFunction,
						"Migration", "upgrade" );
				}

				if (method_exists($this, $postFunction) == false) {
					$missingFunctions = true;
					Logger::logError( get_class($this) . ": failed to find post-upgrade function " . $postFunction,
						"Migration", "postUpgrade" );
				}

				if ($missingFunctions == false) {
					Logger::logInfo( get_class($this) . ": Starting pre-upgrade", "Migration", "preUpgrade");
					$this->$preFunction();
					Logger::logInfo( get_class($this) . ": Ending pre-upgrade", "Migration", "preUpgrade");

					Logger::logInfo( get_class($this) . ": Starting upgrade", "Migration", "upgrade");
					$this->$upgradeFunction();
					Logger::logInfo( get_class($this) . ": Ending upgrade", "Migration", "upgrade");

					Logger::logInfo( get_class($this) . ": Starting post-upgrade", "Migration", "postUpgrade");
					$this->$postFunction();
					Logger::logInfo( get_class($this) . ": Ending post-upgrade", "Migration", "postUpgrade");

					return true;
				}
			}
		}
		catch ( MigrationFailedException $mfe ) {
			Logger::logException( $mfe );
		}
		catch ( \Exception $exception ) {
			Logger::logException( $exception );
		}
		return false;
	}

	public function sqlite_backupDatabase()
	{
		// backup sqlite database file
		$db_path = Config::GetPath("Database/path", null);
		if ( strlen($db_path) == 0 ) {
			throw new \Exception('No path set in configuration for sqlite database');
		}
		$db_file = appendPath($db_path, "contenta.sqlite" );
		$backupDatabase = appendPath($this->scratch, "contenta-" . get_short_class($this) . "-" . date('Y-m-d.H-i-s') . ".backup");
		file_exists($db_file) == false || copy($db_file, $backupDatabase) || die('Failed to backup ' . $db_file);
	}

	public function targetVersion() { return "0.0.0"; }
	public function targetVersionDBO()
	{
		return $this->versionDBO( $this->targetVersion() );
	}

	public function versionDBO( $code )
	{
		$version_model = Model::Named("Version");
		$version = $version_model->objectForCode( $code );
		if ( $version == false ) {
			list($version, $errors) = $version_model->createObject( array( Version::code => $code ));
		}
		return $version;
	}

	/** $other represents the currently installed version, the migration target should be less than or equal. So if the system is 1.3.2
		and the migration targets 1.3.1 then installation is OK, but if the target is 1.4.0 then this migration should not install yet
	*/
	public function canMigrateToVersion( $other )
	{
		$otherVers = explode(".", $other );
		$otherMajorVersion = (isset($otherVers[0]) ? intval($otherVers[0]) : 0);
		$otherMinorVersion = (isset($otherVers[1]) ? intval($otherVers[1]) : 0);
		$otherPatchVersion = (isset($otherVers[2]) ? intval($otherVers[2]) : 0);
		$otherVers = ($otherMajorVersion * 10000) + ($otherMinorVersion * 100) +  $otherPatchVersion;

		$targetVers = explode(".", $this->targetVersion() );
		$targetMajorVersion = (isset($targetVers[0]) ? intval($targetVers[0]) : 0);
		$targetMinorVersion = (isset($targetVers[1]) ? intval($targetVers[1]) : 0);
		$targetPatchVersion = (isset($targetVers[2]) ? intval($targetVers[2]) : 0);
		$targetVers = ($targetMajorVersion * 10000) + ($targetMinorVersion * 100) +  $targetPatchVersion;

		return $targetVers <= $otherVers;
	}

	public function sqlite_execute( $table = null, $sql = null, $comment = null )
	{
		if ( is_null($sql) || is_null($table) ) {
			throw new MigrationFailedException("Unable to execute SQL for -null- table or statement");
		}
		else {
			$statement = false;
			try {
				$statement = Database::instance()->prepare($sql);
				if ($statement == false || $statement->execute() == false) {
					$errPoint = ($statement ? $statement : Database::instance());
					$pdoError = $errPoint->errorInfo()[1] . ':' . $errPoint->errorInfo()[2];
					Logger::logSQLError($table, 'sqlite_execute', $errPoint->errorCode(), $pdoError, $sql, null);
					throw new MigrationFailedException("Error executing change to " . $table . " table");
				}
			}
			catch ( \PDOException $e ) {
				$errPoint = ($statement ? $statement : Database::instance());
				$pdoError = $errPoint->errorInfo()[1] . ':' . $errPoint->errorInfo()[2];
				Logger::logSQLError($table, 'sqlite_execute', $errPoint->errorCode(), $pdoError, $sql, null);
				throw new MigrationFailedException("Error executing change to " . $table . " table");
			}
		}

		if ( is_null($comment) == false) {
			Logger::logInfo( $comment, get_class(), $table);
		}
	}
}
