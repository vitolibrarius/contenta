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
	}

	public function sqlite_postUpgrade()
	{
		$ept_model = Model::Named("Endpoint_Type");
		$type = \SQL::Select( $ept_model, array( "code", "favicon_url") )->whereEqual( "code", 'SABnzbd' )->fetch();
		if ($type != false) {
			\SQL::Update(
				$ept_model,
				Qualifier::Equals( "code", 'SABnzbd' ),
				array( "favicon_url" => "https://sabnzbd.org/images/favicon.ico")
			)->commitTransaction();
		}
	}
}
