<?php

namespace migration;

use \Migrator as Migrator;
use \MigrationFailedException as MigrationFailedException;
use \Config as Config;
use \Logger as Logger;
use \Model as Model;
use \Database as Database;

use model\Users as Users;
use model\Endpoint_Type as Endpoint_Type;
use model\Endpoint as Endpoint;
use model\Network as Network;
use model\User_Network as User_Network;

class Migration_4 extends Migrator
{
	public function sqlite_preUpgrade()
	{
		// backup sqlite database file
		$db_path = Config::GetPath("Database/path", null);
		if ( strlen($db_path) == 0 ) {
			throw new Exception('No path set in configuration for sqlite database');
		}
		$db_file = appendPath($db_path, "contenta.sqlite" );
		$backupDatabase = appendPath($this->scratch, "contenta.Migration_4." . date('Y-m-d.H-i-s') . ".backup");
		file_exists($db_file) == false || copy($db_file, $backupDatabase) || die('Failed to backup ' . $db_file);
	}

	public function sqlite_upgrade()
	{
		$model = Model::Named("Endpoint_Type");

		$table_fields = $model->pragma_TableInfo(Endpoint_Type::TABLE);
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
			$type = $ept_model->endpointTypeForCode($dict[Endpoint_Type::code]);
			if ($type == false) {
				$newObjId = $ept_model->createObj(Endpoint_Type::TABLE, $dict);
			}
			else {
				$ept_model->updateObject($type, array(Endpoint_Type::TABLE => $dict));
			}
		}
		return true;
	}
}
