<?php

namespace migration;

use \Migrator as Migrator;
use \MigrationFailedException as MigrationFailedException;
use \Config as Config;
use \Logger as Logger;
use \Model as Model;
use \Database as Database;
use \SQL as SQL;

use \model\user\Users as Users;

use \model\network\Endpoint as Endpoint;

use \model\media\Series as Series;
use \model\media\Publisher as Publisher;
use \model\media\Series_Alias as Series_Alias;
use \model\media\Character as Character;
use \model\media\Character_Alias as Character_Alias;
use \model\media\Series_Character as Series_Character;
use \model\media\Story_Arc as Story_Arc;
use \model\media\Story_Arc_Character as Story_Arc_Character;
use \model\media\Story_Arc_Series as Story_Arc_Series;
use \model\media\Story_Arc_Publication as Story_Arc_Publication;
use \model\media\Publication as Publication;
use \model\media\Publication_Character as Publication_Character;
use \model\media\Media_Type as Media_Type;
use \model\media\Media as Media;

class Migration_9 extends Migrator
{
	public function targetVersion() { return "0.7.2"; }

	public function sqlite_preUpgrade()
	{
		$this->sqlite_backupDatabase();
	}

	public function sqlite_upgrade()
	{
		$table_fields = \SQL::pragma_TableInfo(Endpoint::TABLE);
		if ( isset($table_fields[ Endpoint::error_count ]) == false ) {
			$this->sqlite_execute(
				Endpoint::TABLE ,
				"ALTER TABLE " . Endpoint::TABLE . " ADD COLUMN " . Endpoint::error_count . " INTEGER",
				"Adding the error_count column to Endpoint"
			);
		}

		if ( isset($table_fields[ Endpoint::parameter ]) == false ) {
			$this->sqlite_execute(
				Endpoint::TABLE ,
				"ALTER TABLE " . Endpoint::TABLE . " ADD COLUMN " . Endpoint::parameter . " TEXT",
				"Adding the parameter column to Endpoint"
			);
		}

		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS reading_item_user_idpublication_id on reading_item (user_id,publication_id)';
		$this->sqlite_execute( "reading_item", $sql, "Index on reading_item (user_id,publication_id)" );

		/* create missing FK indexes */
		$sql = 'CREATE INDEX IF NOT EXISTS jobJob_Type_fk on job (type_code)';
		$this->sqlite_execute( "job", $sql, "FK Index on job (type_code)" );
		$sql = 'CREATE INDEX IF NOT EXISTS jobEndpoint_fk on job (endpoint_id)';
		$this->sqlite_execute( "job", $sql, "FK Index on job (endpoint_id)" );

		$sql = 'CREATE INDEX IF NOT EXISTS job_runningJob_fk on job_running (job_id)';
		$this->sqlite_execute( "job_running", $sql, "FK Index on job_running (job_id)" );
		$sql = 'CREATE INDEX IF NOT EXISTS job_runningJob_Type_fk on job_running (type_code)';
		$this->sqlite_execute( "job_running", $sql, "FK Index on job_running (type_code)" );

		$sql = 'CREATE INDEX IF NOT EXISTS logLog_Level_fk on log (level_code)';
		$this->sqlite_execute( "log", $sql, "FK Index on log (level_code)" );

		$sql = 'CREATE INDEX IF NOT EXISTS characterPublisher_fk on character (publisher_id)';
		$this->sqlite_execute( "character", $sql, "FK Index on character (publisher_id)" );

		$sql = 'CREATE INDEX IF NOT EXISTS character_aliasCharacter_fk on character_alias (character_id)';
		$this->sqlite_execute( "character_alias", $sql, "FK Index on character_alias (character_id)" );

		$sql = 'CREATE INDEX IF NOT EXISTS mediaMedia_Type_fk on media (type_code)';
		$this->sqlite_execute( "media", $sql, "FK Index on media (type_code)" );
		$sql = 'CREATE INDEX IF NOT EXISTS mediaPublication_fk on media (publication_id)';
		$this->sqlite_execute( "media", $sql, "FK Index on media (publication_id)" );

		$sql = 'CREATE INDEX IF NOT EXISTS publicationSeries_fk on publication (series_id)';
		$this->sqlite_execute( "publication", $sql, "FK Index on publication (series_id)" );

		$sql = 'CREATE INDEX IF NOT EXISTS publication_characterPublication_fk on publication_character (publication_id)';
		$this->sqlite_execute( "publication_character", $sql, "FK Index on publication_character (publication_id)" );
		$sql = 'CREATE INDEX IF NOT EXISTS publication_characterCharacter_fk on publication_character (character_id)';
		$this->sqlite_execute( "publication_character", $sql, "FK Index on publication_character (character_id)" );

		$sql = 'CREATE INDEX IF NOT EXISTS seriesPublisher_fk on series (publisher_id)';
		$this->sqlite_execute( "series", $sql, "FK Index on series (publisher_id)" );
		$sql = 'CREATE  INDEX IF NOT EXISTS series_search_namepub_wanted on series (search_name,pub_wanted)';
		$this->sqlite_execute( "series", $sql, "Index on series (search_name,pub_wanted)" );

		$sql = 'CREATE INDEX IF NOT EXISTS series_aliasSeries_fk on series_alias (series_id)';
		$this->sqlite_execute( "series_alias", $sql, "FK Index on series_alias (series_id)" );

		$sql = 'CREATE INDEX IF NOT EXISTS series_characterSeries_fk on series_character (series_id)';
		$this->sqlite_execute( "series_character", $sql, "FK Index on series_character (series_id)" );
		$sql = 'CREATE INDEX IF NOT EXISTS series_characterCharacter_fk on series_character (character_id)';
		$this->sqlite_execute( "series_character", $sql, "FK Index on series_character (character_id)" );

		$sql = 'CREATE INDEX IF NOT EXISTS story_arcPublisher_fk on story_arc (publisher_id)';
		$this->sqlite_execute( "story_arc", $sql, "FK Index on story_arc (publisher_id)" );

		$sql = 'CREATE INDEX IF NOT EXISTS story_arc_characterStory_Arc_fk on story_arc_character (story_arc_id)';
		$this->sqlite_execute( "story_arc_character", $sql, "FK Index on story_arc_character (story_arc_id)" );
		$sql = 'CREATE INDEX IF NOT EXISTS story_arc_characterCharacter_fk on story_arc_character (character_id)';
		$this->sqlite_execute( "story_arc_character", $sql, "FK Index on story_arc_character (character_id)" );

		$sql = 'CREATE INDEX IF NOT EXISTS story_arc_publicationStory_Arc_fk on story_arc_publication (story_arc_id)';
		$this->sqlite_execute( "story_arc_publication", $sql, "FK Index on story_arc_publication (story_arc_id)" );
		$sql = 'CREATE INDEX IF NOT EXISTS story_arc_publicationPublication_fk on story_arc_publication (publication_id)';
		$this->sqlite_execute( "story_arc_publication", $sql, "FK Index on story_arc_publication (publication_id)" );

		$sql = 'CREATE INDEX IF NOT EXISTS story_arc_seriesStory_Arc_fk on story_arc_series (story_arc_id)';
		$this->sqlite_execute( "story_arc_series", $sql, "FK Index on story_arc_series (story_arc_id)" );
		$sql = 'CREATE INDEX IF NOT EXISTS story_arc_seriesSeries_fk on story_arc_series (series_id)';
		$this->sqlite_execute( "story_arc_series", $sql, "FK Index on story_arc_series (series_id)" );

		$sql = 'CREATE INDEX IF NOT EXISTS endpointEndpoint_Type_fk on endpoint (type_code)';
		$this->sqlite_execute( "endpoint", $sql, "FK Index on endpoint (type_code)" );

		$sql = 'CREATE INDEX IF NOT EXISTS fluxEndpoint_fk on flux (src_endpoint)';
		$this->sqlite_execute( "flux", $sql, "FK Index on flux (src_endpoint)" );
		$sql = 'CREATE INDEX IF NOT EXISTS fluxEndpoint_fk on flux (dest_endpoint)';
		$this->sqlite_execute( "flux", $sql, "FK Index on flux (dest_endpoint)" );

		$sql = 'CREATE INDEX IF NOT EXISTS rssEndpoint_fk on rss (endpoint_id)';
		$this->sqlite_execute( "rss", $sql, "FK Index on rss (endpoint_id)" );

		$sql = 'CREATE INDEX IF NOT EXISTS user_networkUsers_fk on user_network (user_id)';
		$this->sqlite_execute( "user_network", $sql, "FK Index on user_network (user_id)" );
		$sql = 'CREATE INDEX IF NOT EXISTS user_networkNetwork_fk on user_network (network_id)';
		$this->sqlite_execute( "user_network", $sql, "FK Index on user_network (network_id)" );

		$sql = 'CREATE INDEX IF NOT EXISTS pull_listEndpoint_fk on pull_list (endpoint_id)';
		$this->sqlite_execute( "pull_list", $sql, "FK Index on pull_list (endpoint_id)" );

		$sql = 'CREATE INDEX IF NOT EXISTS pull_list_exclEndpoint_Type_fk on pull_list_excl (endpoint_type_code)';
		$this->sqlite_execute( "pull_list_excl", $sql, "FK Index on pull_list_excl (endpoint_type_code)" );

		$sql = 'CREATE INDEX IF NOT EXISTS pull_list_expansionEndpoint_Type_fk on pull_list_expansion (endpoint_type_code)';
		$this->sqlite_execute( "pull_list_expansion", $sql, "FK Index on pull_list_expansion (endpoint_type_code)" );

		$sql = 'CREATE INDEX IF NOT EXISTS pull_list_itemPull_List_Group_fk on pull_list_item (pull_list_group_id)';
		$this->sqlite_execute( "pull_list_item", $sql, "FK Index on pull_list_item (pull_list_group_id)" );
		$sql = 'CREATE INDEX IF NOT EXISTS pull_list_itemPull_List_fk on pull_list_item (pull_list_id)';
		$this->sqlite_execute( "pull_list_item", $sql, "FK Index on pull_list_item (pull_list_id)" );

		$sql = 'CREATE INDEX IF NOT EXISTS reading_itemUsers_fk on reading_item (user_id)';
		$this->sqlite_execute( "reading_item", $sql, "FK Index on reading_item (user_id)" );
		$sql = 'CREATE INDEX IF NOT EXISTS reading_itemPublication_fk on reading_item (publication_id)';
		$this->sqlite_execute( "reading_item", $sql, "FK Index on reading_item (publication_id)" );

		$sql = 'CREATE INDEX IF NOT EXISTS reading_queueUsers_fk on reading_queue (user_id)';
		$this->sqlite_execute( "reading_queue", $sql, "FK Index on reading_queue (user_id)" );
		$sql = 'CREATE INDEX IF NOT EXISTS reading_queueSeries_fk on reading_queue (series_id)';
		$this->sqlite_execute( "reading_queue", $sql, "FK Index on reading_queue (series_id)" );
		$sql = 'CREATE INDEX IF NOT EXISTS reading_queueStory_Arc_fk on reading_queue (story_arc_id)';
		$this->sqlite_execute( "reading_queue", $sql, "FK Index on reading_queue (story_arc_id)" );

		$sql = 'CREATE INDEX IF NOT EXISTS patchVersion_fk on patch (version_id)';
		$this->sqlite_execute( "patch", $sql, "FK Index on patch (version_id)" );

		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS publisher_xidxsource on publisher (xid,xsource)';
		$this->sqlite_execute( "publisher", $sql, "xidxsource Index on publisher" );
		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS series_xidxsource on series (xid,xsource)';
		$this->sqlite_execute( "series", $sql, "xidxsource Index on series" );
		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS story_arc_xidxsource on story_arc (xid,xsource)';
		$this->sqlite_execute( "story_arc", $sql, "xidxsource Index on story_arc" );
		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS character_xidxsource on character (xid,xsource)';
		$this->sqlite_execute( "character", $sql, "xidxsource Index on character" );
		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS publication_xidxsource on publication (xid,xsource)';
		$this->sqlite_execute( "publication", $sql, "xidxsource Index on publication" );
	}

	public function sqlite_postUpgrade()
	{
	}
}
