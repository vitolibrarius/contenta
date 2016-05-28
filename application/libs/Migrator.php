<?php

class MigrationFailedException extends \Exception {}

use \SQL as SQL;
use model\version\Version as Version;
use model\version\VersionDBO as VersionDBO;
use model\version\Patch as Patch;
use model\version\PatchDBO as PatchDBO;

class Migrator
{
	const IDX_TABLE = 'index_table';
	const IDX_COLS = 'index_columns';
	const IDX_UNIQUE = 'index_unique';

	public static function Upgrade($scratchDirectory)
	{
		$currentVersionNumber = currentVersionNumber();
		$patch_model = Model::Named("Patch");

		Logger::logInfo( "Migrating application to version " . $currentVersionNumber,	"Migrator", "Upgrade" );

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
				die( "database migration error" );
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
							$patch = $patch_model->create($version, $migrationClass);
							if ( ($patch instanceof PatchDBO ) == false) {
								throw new MigrationFailedException("Failed to create migration record " . $migrationClass);
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
				break;
			}
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
			$vers = explode(".", $code );
			$version = $version_model->create(
				$code,
				(isset($vers[0]) ? intval($vers[0]) : 0),
				(isset($vers[1]) ? intval($vers[1]) : 0),
				(isset($vers[2]) ? intval($vers[2]) : 0)
			);
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
