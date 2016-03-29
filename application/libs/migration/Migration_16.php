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
use model\Users as Users;
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
use model\Rss as Rss;
use model\Job as Job;
use model\Job_Type as Job_Type;
use model\Job_Running as Job_Running;
use model\Flux as Flux;
use model\Log as Log;


class Migration_16 extends Migrator
{
	public function sqlite_preUpgrade()
	{
		// backup sqlite database file
		$db_path = Config::GetPath("Database/path", null);
		if ( strlen($db_path) == 0 ) {
			throw new \Exception('No path set in configuration for sqlite database');
		}
		$db_file = appendPath($db_path, "contenta.sqlite" );
		$backupDatabase = appendPath($this->scratch, "contenta.Migration_16." . date('Y-m-d.H-i-s') . ".backup");
		file_exists($db_file) == false || copy($db_file, $backupDatabase) || die('Failed to backup ' . $db_file);
	}

	public function sqlite_upgrade()
	{
	}

	public function sqlite_postUpgrade()
	{
		$job_type_model = Model::Named("Job_Type");
		$types = array(
			array(
				Job_Type::name => "Newznab Search Processing",
				Job_Type::code => "newznab_search",
				Job_Type::desc => "Automated Search for Wanted publications against all Newznab endpoints and submission to a Sabnzbd serice.  Requires at least one Newznab endpoint and one Sabnzbd endpoint",
				Job_Type::processor => "NewznabSearchProcessor",
				Job_Type::parameter => null,
				Job_Type::scheduled => Model::TERTIARY_TRUE,
				Job_Type::requires_endpoint => Model::TERTIARY_FALSE
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

		$job_model = Model::Named("Job");
		$existing = \SQL::raw( "select id, code FROM " . Job_Type::TABLE );
		$map = array();
		foreach ($existing as $row) {
			$map[$row->code] = $row->id;
		}

		$job_data = array(
			array(
				"type_id" => $map["newznab_search"],
				"minute" => "10",
				"hour" => "0-8",
				"dayOfWeek" => "*",
				"one_shot" => Model::TERTIARY_FALSE,
				"enabled" => Model::TERTIARY_TRUE
			)
		);

		foreach ($job_data as $dict) {
			$existing = \SQL::raw( "select id FROM " . Job::TABLE . " where type_id = :type_id",
				array(":type_id" => $dict["type_id"]));
			if ( is_array($existing) == false || count($existing) == 0) {
				$cronEval = new CronEvaluator( $dict["minute"], $dict["hour"], $dict["dayOfWeek"]);
				$dict["next"] = $cronEval->nextDate()->getTimestamp();

				$inserts = \SQL::Insert( $job_model, array( "type_id", "minute", "hour", "dayOfWeek", "one_shot", "enabled", "next") );
				$inserts->addRecord( $dict );
				$inserts->commitTransaction(true);
			}
		}

		return true;
	}
}
