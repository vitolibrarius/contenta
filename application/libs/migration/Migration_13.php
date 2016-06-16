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
use model\Rss as Rss;

use \model\jobs\Job_Type as Job_Type;
use \model\jobs\Job_Running as Job_Running;
use \model\jobs\Job as Job;


class Migration_13 extends Migrator
{
	public function targetVersion() { return "0.4.2"; }

	public function sqlite_preUpgrade()
	{
		$this->sqlite_backupDatabase();
	}

	public function sqlite_upgrade()
	{
		/** JOB_TYPE */
		$table_fields = \SQL::pragma_TableInfo(Job_Type::TABLE);
		if ( isset($table_fields[ Job_Type::requires_endpoint ]) == false ) {
			$this->sqlite_execute(
				Job_Type::TABLE ,
				"ALTER TABLE " . Job_Type::TABLE . " ADD COLUMN " . Job_Type::requires_endpoint . " INTEGER",
				"Adding the requires_endpoint column to Job_Type"
			);
		}

		/** PUBLICATION */
		$table_fields = \SQL::pragma_TableInfo(Publication::TABLE);
		if ( isset($table_fields[ Publication::media_count ]) == false ) {
			$this->sqlite_execute(
				Publication::TABLE ,
				"ALTER TABLE " . Publication::TABLE . " ADD COLUMN " . Publication::media_count . " INTEGER",
				"Adding the media_count column to Publication"
			);
		}

		/** SERIES */
		$table_fields = \SQL::pragma_TableInfo(Series::TABLE);
		if ( isset($table_fields[ Series::search_name ]) == false ) {
			$this->sqlite_execute(
				Series::TABLE ,
				"ALTER TABLE " . Series::TABLE . " ADD COLUMN " . Series::search_name . " TEXT COLLATE NOCASE",
				"Adding the search_name column to Series"
			);
		}

		$intColumns = array(Series::pub_active, Series::pub_cycle, Series::pub_count, Series::pub_available, Series::pub_wanted);
		foreach( $intColumns as $column ) {
			if ( isset($table_fields[ $column ]) == false ) {
				$this->sqlite_execute(
					Series::TABLE ,
					"ALTER TABLE " . Series::TABLE . " ADD COLUMN " . $column . " INTEGER",
					"Adding the " . $column . " column to Series"
				);
			}
		}

		/** STORY_ARC */
		$table_fields = \SQL::pragma_TableInfo(Story_Arc::TABLE);
		$intColumns = array(Story_Arc::pub_active, Story_Arc::pub_cycle, Story_Arc::pub_count, Story_Arc::pub_available, Story_Arc::pub_wanted);
		foreach( $intColumns as $column ) {
			if ( isset($table_fields[ $column ]) == false ) {
				$this->sqlite_execute(
					Story_Arc::TABLE ,
					"ALTER TABLE " . Story_Arc::TABLE . " ADD COLUMN " . $column . " INTEGER",
					"Adding the " . $column . " column to Story_Arc"
				);
			}
		}

		/** NEW INDEXES */
		$indexStatements = array(
			array( Migrator::IDX_TABLE => Series::TABLE, Migrator::IDX_COLS => array( Series::search_name ) ),
		);
		foreach( $indexStatements as $config ) {
			$table = $config[Migrator::IDX_TABLE];
			$columns = $config[Migrator::IDX_COLS];
			$indexName = $table . '_' . implode("", $columns);
			$unique = (isset($config[Migrator::IDX_UNIQUE]) ? boolval($config[Migrator::IDX_UNIQUE]) : false);

			$statement = 'CREATE ' . ($unique ? 'UNIQUE' : '') . ' INDEX IF NOT EXISTS ' . $indexName . ' on ' . $table . '(' . implode(",", $columns) . ')';
			$this->sqlite_execute( $table, $statement, "Index on " . $table );
		}
	}

	public function sqlite_postUpgrade()
	{
		$jobTypes = array("character", "publisher", "series", "story_arc", "rss");
		$jtModel = Model::Named('Job_Type');
		foreach( $jobTypes as $t ) {
			\SQL::Update(
				$jtModel,
				Qualifier::Equals( Job_Type::code, $t ),
				array(Job_Type::requires_endpoint => Model::TERTIARY_TRUE)
			)->commitTransaction() || die("failed to update JobType " . $t);
		}

		$ept_model = Model::Named("Endpoint_Type");
		$types = array(
			array(
				Endpoint_Type::code => Endpoint_Type::PreviewsWorld,
				Endpoint_Type::site_url => "http://www.previewsworld.com",
				Endpoint_Type::api_url => "http://www.previewsworld.com/shipping/newreleases.txt",
				Endpoint_Type::name => "PreviewsWorld Upcoming Releases",
				Endpoint_Type::favicon_url => "http://www.previewsworld.com/favicon.ico",
				Endpoint_Type::data_type => "PreviewsWorld",
				Endpoint_Type::comments => "Every week, PREVIEWSworld announces which comics, graphic novels, toys and other pop-culture merchandise will arrive at your local comic shop"
			)
		);
		foreach ($types as $dict) {
			$type = \SQL::Select( $ept_model, array( "code", "name") )->whereEqual( "code", $dict["code"] )->fetch();
			if ($type == false) {
				$newObjId = \SQL::Insert($ept_model)->addRecord($dict)->commitTransaction();
			}
			else {
				\SQL::Update($ept_model, $dict)->commitTransaction();
			}
		}

		$update = \SQL::Update( Model::Named('Series'),
			null,
			array(
				Series::pub_active => 0,
				Series::pub_cycle => 0,
				Series::pub_count => 0,
				Series::pub_available => 0,
				Series::pub_wanted => 0
			)
		);
		$update->allowFullTableUpdate = true;
		$success = $update->commitTransaction();
		if ( $success != true ) {
			throw new \Exception( "Failed to update series pub_* values with defaults");
		}

		$update = \SQL::Update( Model::Named('Story_Arc'),
			null,
			array(
				Story_Arc::pub_active => 0,
				Story_Arc::pub_cycle => 0,
				Story_Arc::pub_count => 0,
				Story_Arc::pub_available => 0,
				Story_Arc::pub_wanted => 0
			)
		);
		$update->allowFullTableUpdate = true;
		$success = $update->commitTransaction();
		if ( $success != true ) {
			throw new \Exception( "Failed to update series pub_* values with defaults");
		}

		$series_model = Model::Named("Series");
		$select = \SQL::Select( $series_model, array(Series::id, Series::name, Series::search_name))
			->where( Qualifier::IsNull(Series::search_name))
			->orderBy( array( "name" ))
			->limit( 0 );
		$records = $select->fetchAll();
		foreach( $records as $row ) {
			$update = \SQL::Update( $series_model,
				Qualifier::Equals( Series::id, $row->id ),
				array( Series::search_name => normalizeSearchString($row->name))
			);
			$success = $update->commitTransaction();
			if ( $success != true ) {
				throw new \Exception( "Failed to update " . var_export($row, true) . " with new search_name");
			}
		}

		\SQL::raw( "update publication set media_count = (select count(*) from media where media.publication_id = publication.id)" );
		\SQL::raw( "update series set pub_count = (select count(*) from publication where publication.series_id = series.id)" );
		\SQL::raw( "update series set pub_available = (select count(*)
			from publication where publication.series_id = series.id AND publication.media_count > 0)" );
		\SQL::raw( "update story_arc set pub_count = "
			. "(select count(*) from story_arc_publication join publication on story_arc_publication.publication_id = publication.id"
			. " where story_arc_publication.story_arc_id = story_arc.id)" );
		\SQL::raw( "update story_arc set pub_available = "
			. "(select count(*) from story_arc_publication join publication on story_arc_publication.publication_id = publication.id"
			. " where story_arc_publication.story_arc_id = story_arc.id AND publication.media_count > 0)" );

		\SQL::raw( "update series set pub_cycle = (
			select (julianday(max(pub_date), 'unixepoch') - julianday(min(pub_date), 'unixepoch')) / count(*)
			from publication where publication.series_id = series.id)" );

		\SQL::raw( "update story_arc set pub_cycle = (
			select (julianday(max(publication.pub_date), 'unixepoch') - julianday(min(publication.pub_date), 'unixepoch')) / count(*)
			from story_arc_publication join publication on story_arc_publication.publication_id = publication.id
			where story_arc_publication.story_arc_id = story_arc.id)" );

		\SQL::raw( "update series set pub_active = (
			select (((julianday('now') - julianday(max(pub_date), 'unixepoch'))/365) < 1)
			from publication where publication.series_id = series.id)" );

		\SQL::raw( "update story_arc set pub_active = (
			select (((julianday('now') - julianday(max(pub_date), 'unixepoch'))/365) < 1)
			from story_arc_publication join publication on story_arc_publication.publication_id = publication.id
			where story_arc_publication.story_arc_id = story_arc.id)" );

		return true;
	}
}
