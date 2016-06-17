<?php

namespace migration;

use \Migrator as Migrator;
use \MigrationFailedException as MigrationFailedException;
use \Config as Config;
use \Logger as Logger;
use \Model as Model;
use \SQL as SQL;

use utilities\CronEvaluator as CronEvaluator;
use db\Qualifier as Qualifier;

use model\Series as Series;
use model\Publisher as Publisher;
use model\Series_Alias as Series_Alias;
use model\Character as Character;
use model\Character_Alias as Character_Alias;
use model\Series_Character as Series_Character;
use \model\user\Users as Users;
use model\User_Series as User_Series;
use model\Story_Arc as Story_Arc;
use model\Story_Arc_Character as Story_Arc_Character;
use model\Story_Arc_Series as Story_Arc_Series;
use model\Story_Arc_Publication as Story_Arc_Publication;
use model\Publication as Publication;
use model\Publication_Character as Publication_Character;
use model\Media_Type as Media_Type;
use model\Media as Media;
use model\Endpoint_Type as Endpoint_Type;
use model\Endpoint as Endpoint;

use \model\network\Flux as Flux;
use \model\jobs\Job_Type as Job_Type;
use \model\jobs\Job_Running as Job_Running;
use \model\jobs\Job as Job;

class Migration_14 extends Migrator
{
	public function targetVersion() { return "0.4.3"; }

	public function sqlite_preUpgrade()
	{
		$this->sqlite_backupDatabase();
	}

	public function sqlite_upgrade()
	{
		/** FLUX */
		$sql = 'CREATE TABLE IF NOT EXISTS ' . Flux::TABLE . " ( "
			. Flux::id . " INTEGER PRIMARY KEY, "
			. Flux::created . " INTEGER, "
			. Flux::name . " TEXT, "
			. Flux::flux_hash . " TEXT, "
			. Flux::flux_error . " INTEGER, "
			. Flux::src_endpoint . " INTEGER, "
			. Flux::src_guid . " TEXT, "
			. Flux::src_status . " TEXT, "
			. Flux::src_pub_date . " INTEGER, "
			. Flux::src_url . " TEXT, "
			. Flux::dest_endpoint . " INTEGER, "
			. Flux::dest_guid . " TEXT, "
			. Flux::dest_status . " TEXT, "
			. Flux::dest_submission . " INTEGER, "
			. "FOREIGN KEY (". Flux::src_endpoint .") REFERENCES " . Endpoint::TABLE . "(" . Endpoint::id . "),"
			. "FOREIGN KEY (". Flux::dest_endpoint .") REFERENCES " . Endpoint::TABLE . "(" . Endpoint::id . ")"
			. ")";
		$this->sqlite_execute( Flux::TABLE, $sql, "Create table " . Flux::TABLE );

		/** NEW INDEXES */
		$indexStatements = array(
			array(
				Migrator::IDX_TABLE => Flux::TABLE,
				Migrator::IDX_COLS => array( Flux::src_endpoint, Flux::src_guid ),
				Migrator::IDX_UNIQUE => true
			),
			array(
				Migrator::IDX_TABLE => Flux::TABLE,
				Migrator::IDX_COLS => array( Flux::dest_endpoint, Flux::dest_guid ),
				Migrator::IDX_UNIQUE => true
			),
			array(
				Migrator::IDX_TABLE => Flux::TABLE,
				Migrator::IDX_COLS => array( Flux::flux_hash )
			),
		);
		foreach( $indexStatements as $config ) {
			$table = $config[Migrator::IDX_TABLE];
			$columns = $config[Migrator::IDX_COLS];
			$indexName = $table . '_' . implode("", $columns);
			$unique = (isset($config[Migrator::IDX_UNIQUE]) ? boolval($config[Migrator::IDX_UNIQUE]) : false);

			$statement = 'CREATE ' . ($unique ? 'UNIQUE' : '') . ' INDEX IF NOT EXISTS ' . $indexName
				. ' on ' . $table . '(' . implode(",", $columns) . ')';
			$this->sqlite_execute( $table, $statement, "Index on " . $table );
		}
	}

	public function sqlite_postUpgrade()
	{
		$job_type_model = Model::Named("Job_Type");
		$types = array(
			array(
				Job_Type::name => "Publication Updates",
				Job_Type::code => "publication",
				Job_Type::desc => "Load publication data from metadata endpoints (like ComicVine) in batches of 20",
				Job_Type::processor => "ComicVineImporter",
				Job_Type::parameter => json_encode(array( "enqueueBatch" => array("publication", 20)), JSON_PRETTY_PRINT),
				Job_Type::scheduled => Model::TERTIARY_TRUE,
				Job_Type::requires_endpoint => Model::TERTIARY_TRUE
			),
			array(
				Job_Type::name => "SABnzbd status",
				Job_Type::code => "sabnzbd",
				Job_Type::desc => "Update the download status from SABnzbd",
				Job_Type::processor => "FluxStatusUpdater",
				Job_Type::parameter => null,
				Job_Type::scheduled => Model::TERTIARY_TRUE,
				Job_Type::requires_endpoint => Model::TERTIARY_TRUE
			)
		);

		foreach ($types as $dict) {
			$existing = \SQL::raw( "select id FROM " . Job_Type::TABLE . " where code = :code", array( ":code" => $dict[Job_Type::code]));
			if ( is_array($existing) && count($existing) > 0) {
				$update = \SQL::Update($job_type_model, Qualifier::Equals( "code", $dict[Job_Type::code]), $dict);
				$update->commitTransaction();
			}
			else {
				$inserts = \SQL::Insert( $job_type_model, array(
						"name",
						"code",
						"desc",
						"processor",
						"scheduled",
						"parameter",
						"requires_endpoint"
					)
				);
				$inserts->addRecord( $dict );
				$inserts->commitTransaction(true);
			}
		}

		return true;
	}
}
