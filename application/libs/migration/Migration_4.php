<?php

namespace migration;

use \Migrator as Migrator;
use \MigrationFailedException as MigrationFailedException;
use \Config as Config;
use \Logger as Logger;
use \Model as Model;
use \SQL as SQL;

use \model\user\Users as Users;
use \model\network\Endpoint_Type as Endpoint_Type;
use \model\network\Endpoint as Endpoint;
use model\Network as Network;
use model\User_Network as User_Network;

class Migration_4 extends Migrator
{
	public function targetVersion() { return "0.2.2"; }

	public function sqlite_preUpgrade()
	{
		$this->sqlite_backupDatabase();
	}

	public function sqlite_upgrade()
	{
		$model = Model::Named("Endpoint_Type");

		$table_fields = \SQL::pragma_TableInfo(Endpoint_Type::TABLE);
		if ( isset($table_fields[ Endpoint_Type::comments ]) == false ) {
			$this->db->exec("ALTER TABLE " . Endpoint_Type::TABLE . " ADD COLUMN " . Endpoint_Type::comments . " TEXT");
			Logger::logInfo( Endpoint_Type::TABLE . " - " . Endpoint_Type::comments . " column added" );
		}

		if ( isset($table_fields[ Endpoint_Type::favicon_url ]) == false ) {
			$this->db->exec("ALTER TABLE " . Endpoint_Type::TABLE . " ADD COLUMN " . Endpoint_Type::favicon_url . " TEXT");
			Logger::logInfo( Endpoint_Type::TABLE . " - " . Endpoint_Type::favicon_url . " column added" );
		}
	}

	public function sqlite_postUpgrade()
	{
		$ept_model = Model::Named("Endpoint_Type");
		$types = array(
			array(
				Endpoint_Type::code => Endpoint_Type::Newznab,
				Endpoint_Type::site_url => "http://www.newznab.com",
				Endpoint_Type::name => "Newsnab (Usenet Indexing Service)",
				Endpoint_Type::favicon_url => "http://www.newznab.com/favicon.ico",
				Endpoint_Type::data_type => "NZB Index",
				Endpoint_Type::comments => "Newznab is a usenet indexing application that many sites employ.  Often you will need to register with the individual sites to obtain an API key allowing you access.  Please check the Newznab home site for more information"
			),
			array(
				Endpoint_Type::code => Endpoint_Type::RSS,
				Endpoint_Type::site_url => "http://en.wikipedia.org/wiki/RSS",
				Endpoint_Type::favicon_url => "http://en.wikipedia.org/favicon.ico",
				Endpoint_Type::name => "RSS (NZB posting feed)",
				Endpoint_Type::data_type => "RSS feed of NZB",
				Endpoint_Type::comments => "Many sites provide RSS feeds of indexed NZB content.  Please see the included Wikipedia link for more informaiton about RSS."
			),
			array(
				Endpoint_Type::code => Endpoint_Type::ComicVine,
				Endpoint_Type::site_url => "http://www.comicvine.com",
				Endpoint_Type::api_url => "http://www.comicvine.com/api",
				Endpoint_Type::favicon_url => "http://static.comicvine.com/bundles/phoenixsite/images/core/loose/favicon-cv.ico",
				Endpoint_Type::throttle_hits => 200, // max hits per 15 minutes
				Endpoint_Type::throttle_time => 900,
				Endpoint_Type::name => "ComicVine (Comic book database)",
				Endpoint_Type::data_type => "Media Metadata",
				Endpoint_Type::comments => "ComicVine provides a detailed information source about comics and graphic novels that allow Contenta to automatically load and categorize uploaded media content.  Please see the ComicVine home page for information on obtaining an API key."
			),
			array(
				Endpoint_Type::code => Endpoint_Type::SABnzbd,
				Endpoint_Type::site_url => "http://sabnzbd.org",
				Endpoint_Type::favicon_url => "http://sabnzbd.org/favicon.ico",
				Endpoint_Type::name => "SABnzbd (Open Source Binary Newsreader)",
				Endpoint_Type::data_type => "Downloader",
				Endpoint_Type::comments => "SABnzbd makes Usenet as simple and streamlined as possible to download media content using NZB index files.  You will probably need to install your own copy of Sabnzbd and configure it with your usenet provider.  Please see the Sabnzbd site for more information and installation packages."
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
		return true;
	}
}
