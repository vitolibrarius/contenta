<?php

class MigrationFailedException extends Exception {}

use model\Version as Version;
use model\VersionDBO as VersionDBO;
use model\Patch as Patch;
use model\PatchDBO as PatchDBO;

class Migrator
{
	public static function Upgrade($scratchDirectory)
	{
		$versionNum = currentVersionNumber();
		$versionHash = currentVersionHash();
		$version_model = new Version(Database::instance());
		$patch_model = new Patch(Database::instance());

		Logger::logInfo( "Migrating application to version " . currentVersionNumber(),	"Migrator", "Upgrade" );

		$patches = array();
		$sql = "SELECT * FROM patch";
		$statement = Database::instance()->prepare($sql);
		if ($statement && $statement->execute()) {
			$fetch = $statement->fetchAll();
			if ( is_array($fetch) ) {
				foreach( $fetch as $row ) {
					$patches[] = $row->name;
				}
			}
		}

		$number = 0;
		$continue = true;
		while ( $continue == true ) {
			try {
				$migrationClass = 'migration\\Migration_' . $number;
				if ( in_array($migrationClass, $patches) == false ) {
					Logger::logInfo( "Trying " . $migrationClass ,	"Migrator", "Upgrade" );

					$worker = new $migrationClass(Database::instance(), $scratchDirectory);
					Logger::logInfo( "$migrationClass", "Migration", $migrationClass);

					$continue = $worker->performMigration();
					if ( $continue == true ) {
						$version = $version_model->create($versionNum, $versionHash);
						if ( ($version instanceof VersionDBO ) == false) {
							throw new MigrationFailedException("Failed to create version record " . var_export($version, true));
						}

						$patch = $patch_model->create($version, $migrationClass);
						if ( ($patch instanceof PatchDBO ) == false) {
							throw new MigrationFailedException("Failed to create migration record " . $migrationClass);
						}
					}
				}
				else {
					Logger::logInfo( "Already completed " . $migrationClass ,	"Migrator", "Upgrade" );
				}
				$number++;
			}
			catch (Exception $e) {
				if ( is_a( $e, "ClassNotFoundException" ) && $e->getMessage() === $migrationClass ) {
					// this marks the end of the Migration
					$continue = false;

					// ensure the version is created, even if the patches are already completed in previous versions
					$version = $version_model->create($versionNum, $versionHash);
				}
				else {
					// something else?
					Logger::logException( $e );
				}
				break;
			}
		}
	}

	public function __construct(Database $db, $scratchDirectory)
	{
		isset($db) || die("No database object");
		is_dir($scratchDirectory) || die("Scratch direcotry is not valid '" . $scratchDirectory . "'");

		$this->db = $db;
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
		catch ( Exception $exception ) {
			Logger::logException( $exception );
		}
		return false;
	}

	public function sqlite_execute( $table = null, $sql = null, $comment = null )
	{
		if ( is_null($sql) || is_null($table) ) {
			throw new MigrationFailedException("Unable to execute SQL for -null- table or statement");
		}
		else {
			$statement = $this->db->prepare($sql);
			if ($statement == false || $statement->execute() == false) {
				$errPoint = ($statement ? $statement : $this->db);
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
