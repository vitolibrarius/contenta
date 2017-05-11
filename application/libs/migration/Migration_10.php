<?php

namespace migration;

use \Migrator as Migrator;
use \MigrationFailedException as MigrationFailedException;
use \Config as Config;
use \Logger as Logger;
use \Model as Model;
use \Database as Database;

use \SQL as SQL;
use \db\Qualifier as Qualifier;

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

class Migration_10 extends Migrator
{
	public function targetVersion() { return "0.7.5"; }

	public function sqlite_preUpgrade()
	{
		$this->sqlite_backupDatabase();
	}

	public function sqlite_upgrade()
	{
		$table_fields = \SQL::pragma_TableInfo(Publication::TABLE);
		if ( isset($table_fields[ Publication::search_date ]) == false ) {
			$this->sqlite_execute(
				Publication::TABLE ,
				"ALTER TABLE " . Publication::TABLE . " ADD COLUMN " . Publication::search_date . " INTEGER",
				"Adding the Publication::search_date column"
			);
		}
	}

	public function sqlite_postUpgrade()
	{
	}
}
