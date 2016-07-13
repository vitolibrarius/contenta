<?php

namespace migration;

use \Migrator as Migrator;
use \MigrationFailedException as MigrationFailedException;
use \Config as Config;
use \Logger as Logger;
use \Model as Model;
use \SQL as SQL;

use \model\user\Users as Users;
use \model\network\Network as Network;
use \model\network\User_Network as User_Network;
use \model\network\Endpoint_Type as Endpoint_Type;
use \model\network\Endpoint as Endpoint;
use \model\network\Rss as Rss;
use \model\network\Flux as Flux;

class Migration_3 extends Migrator
{
	public function targetVersion() { return "0.3.0"; }

	public function sqlite_preUpgrade()
	{
		$this->sqlite_backupDatabase();
	}

	public function sqlite_upgrade()
	{
		/** ENDPOINT_TYPE */
		$sql = "CREATE TABLE IF NOT EXISTS endpoint_type ( "
			. Endpoint_Type::id . " INTEGER PRIMARY KEY, "
			. Endpoint_Type::code . " TEXT, "
			. Endpoint_Type::name . " TEXT, "
			. Endpoint_Type::comments . " TEXT, "
			. Endpoint_Type::data_type . " TEXT, "
			. Endpoint_Type::site_url . " TEXT, "
			. Endpoint_Type::api_url . " TEXT, "
			. Endpoint_Type::favicon_url . " TEXT, "
			. Endpoint_Type::throttle_hits . " INTEGER, "
			. Endpoint_Type::throttle_time . " INTEGER "
		. ")";
		$this->sqlite_execute( "endpoint_type", $sql, "Create table endpoint_type" );

		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS endpoint_type_code on endpoint_type (code)';
		$this->sqlite_execute( "endpoint_type", $sql, "Index on endpoint_type (code)" );
		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS endpoint_type_name on endpoint_type (name)';
		$this->sqlite_execute( "endpoint_type", $sql, "Index on endpoint_type (name)" );

		/** ENDPOINT */
		$sql = "CREATE TABLE IF NOT EXISTS endpoint ( "
			. Endpoint::id . " INTEGER PRIMARY KEY, "
			. Endpoint::type_id . " INTEGER, "
			. Endpoint::name . " TEXT, "
			. Endpoint::base_url . " TEXT, "
			. Endpoint::api_key . " TEXT, "
			. Endpoint::username . " TEXT, "
			. Endpoint::enabled . " INTEGER, "
			. Endpoint::compressed . " INTEGER, "
			. "FOREIGN KEY (". Endpoint::type_id .") REFERENCES " . Endpoint_Type::TABLE . "(" . Endpoint_Type::id . ")"
		. ")";
		$this->sqlite_execute( "endpoint", $sql, "Create table endpoint" );

		/** NETWORK */
		$sql = "CREATE TABLE IF NOT EXISTS network ( "
			. Network::id . " INTEGER PRIMARY KEY, "
			. Network::ip_address . " TEXT, "
			. Network::ip_hash . " TEXT, "
			. Network::created . " INTEGER, "
			. Network::disable . " INTEGER "
		. ")";
		$this->sqlite_execute( "network", $sql, "Create table network" );

		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS network_ip_address on network (ip_address)';
		$this->sqlite_execute( "network", $sql, "Index on network (ip_address)" );
		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS network_ip_hash on network (ip_hash)';
		$this->sqlite_execute( "network", $sql, "Index on network (ip_hash)" );


		/** USER_NETWORK */
		$sql = "CREATE TABLE IF NOT EXISTS user_network ( "
			. User_Network::id . " INTEGER PRIMARY KEY, "
			. User_Network::user_id . " INTEGER, "
			. User_Network::network_id . " INTEGER, "
			. "FOREIGN KEY (". User_Network::user_id .") REFERENCES " . Users::TABLE . "(" . Users::id . "),"
			. "FOREIGN KEY (". User_Network::network_id .") REFERENCES " . Network::TABLE . "(" . Network::id . ")"
		. ")";
		$this->sqlite_execute( "user_network", $sql, "Create table user_network" );

		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS user_network_user_idnetwork_id on user_network (user_id,network_id)';
		$this->sqlite_execute( "user_network", $sql, "Index on user_network (user_id,network_id)" );

		/** FLUX */
		$sql = "CREATE TABLE IF NOT EXISTS flux ( "
			. Flux::id . " INTEGER PRIMARY KEY, "
			. Flux::created . " INTEGER, "
			. Flux::name . " TEXT, "
			. Flux::flux_hash . " TEXT, "
			. Flux::flux_error . " INTEGER, "
			. Flux::src_endpoint . " INTEGER, "
			. Flux::src_guid . " TEXT, "
			. Flux::src_url . " TEXT, "
			. Flux::src_status . " TEXT, "
			. Flux::src_pub_date . " INTEGER, "
			. Flux::dest_endpoint . " INTEGER, "
			. Flux::dest_guid . " TEXT, "
			. Flux::dest_status . " TEXT, "
			. Flux::dest_submission . " INTEGER, "
			. "FOREIGN KEY (". Flux::dest_endpoint .") REFERENCES " . Endpoint::TABLE . "(" . Endpoint::id . "),"
			. "FOREIGN KEY (". Flux::src_endpoint .") REFERENCES " . Endpoint::TABLE . "(" . Endpoint::id . ")"
		. ")";
		$this->sqlite_execute( "flux", $sql, "Create table flux" );

		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS flux_src_endpointsrc_guid on flux (src_endpoint,src_guid)';
		$this->sqlite_execute( "flux", $sql, "Index on flux (src_endpoint,src_guid)" );
		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS flux_dest_endpointdest_guid on flux (dest_endpoint,dest_guid)';
		$this->sqlite_execute( "flux", $sql, "Index on flux (dest_endpoint,dest_guid)" );
		$sql = 'CREATE  INDEX IF NOT EXISTS flux_flux_hash on flux (flux_hash)';
		$this->sqlite_execute( "flux", $sql, "Index on flux (flux_hash)" );

		/** RSS */
		$sql = "CREATE TABLE IF NOT EXISTS rss ( "
			. Rss::id . " INTEGER PRIMARY KEY, "
			. Rss::endpoint_id . " INTEGER, "
			. Rss::created . " INTEGER, "
			. Rss::title . " TEXT, "
			. Rss::desc . " TEXT, "
			. Rss::pub_date . " INTEGER, "
			. Rss::guid . " TEXT, "
			. Rss::clean_name . " TEXT, "
			. Rss::clean_issue . " TEXT, "
			. Rss::clean_year . " INTEGER, "
			. Rss::enclosure_url . " TEXT, "
			. Rss::enclosure_length . " INTEGER, "
			. Rss::enclosure_mime . " TEXT, "
			. Rss::enclosure_hash . " TEXT, "
			. Rss::enclosure_password . " INTEGER, "
			. "FOREIGN KEY (". Rss::endpoint_id .") REFERENCES " . Endpoint::TABLE . "(" . Endpoint::id . ")"
		. ")";
		$this->sqlite_execute( "rss", $sql, "Create table rss" );

		$sql = 'CREATE  INDEX IF NOT EXISTS rss_clean_nameclean_issueclean_year on rss (clean_name,clean_issue,clean_year)';
		$this->sqlite_execute( "rss", $sql, "Index on rss (clean_name,clean_issue,clean_year)" );
		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS rss_guid on rss (guid)';
		$this->sqlite_execute( "rss", $sql, "Index on rss (guid)" );
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
				Endpoint_Type::throttle_hits => 1,
				Endpoint_Type::throttle_time => 2,
				Endpoint_Type::data_type => "Newznab",
				Endpoint_Type::comments => "Newznab is a usenet indexing application that many sites employ.  Often you will need to register with the individual sites to obtain an API key allowing you access.  Please check the Newznab home site for more information"
			),
			array(
				Endpoint_Type::code => Endpoint_Type::RSS,
				Endpoint_Type::site_url => "http://en.wikipedia.org/wiki/RSS",
				Endpoint_Type::favicon_url => "http://en.wikipedia.org/favicon.ico",
				Endpoint_Type::name => "RSS (NZB posting feed)",
				Endpoint_Type::throttle_hits => 1,
				Endpoint_Type::throttle_time => 2,
				Endpoint_Type::data_type => "RSS",
				Endpoint_Type::comments => "Many sites provide RSS feeds of indexed NZB content.  Please see the included Wikipedia link for more informaiton about RSS."
			),
			array(
				Endpoint_Type::code => Endpoint_Type::ComicVine,
				Endpoint_Type::site_url => "http://comicvine.gamespot.com",
				Endpoint_Type::api_url => "http://comicvine.gamespot.com/api",
				Endpoint_Type::favicon_url => "http://static.comicvine.com/bundles/phoenixsite/images/core/loose/favicon-cv.ico",
				Endpoint_Type::name => "ComicVine (Comic book database)",
				Endpoint_Type::throttle_hits => 1,
				Endpoint_Type::throttle_time => 2,
				Endpoint_Type::data_type => "ComicVine",
				Endpoint_Type::comments => "ComicVine provides a detailed information source about comics and graphic novels that allow Contenta to automatically load and categorize uploaded media content.  Please see the ComicVine home page for information on obtaining an API key."
			),
			array(
				Endpoint_Type::code => Endpoint_Type::SABnzbd,
				Endpoint_Type::site_url => "http://sabnzbd.org",
				Endpoint_Type::favicon_url => "http://sabnzbd.org/favicon.ico",
				Endpoint_Type::name => "SABnzbd (Open Source Binary Newsreader)",
				Endpoint_Type::throttle_hits => 1,
				Endpoint_Type::throttle_time => 2,
				Endpoint_Type::data_type => "SABnzbd",
				Endpoint_Type::comments => "SABnzbd makes Usenet as simple and streamlined as possible to download media content using NZB index files.  You will probably need to install your own copy of Sabnzbd and configure it with your usenet provider.  Please see the Sabnzbd site for more information and installation packages."
			),
			array(
				Endpoint_Type::code => Endpoint_Type::PreviewsWorld,
				Endpoint_Type::site_url => "http://www.previewsworld.com",
				Endpoint_Type::api_url => "http://www.previewsworld.com/shipping/newreleases.txt",
				Endpoint_Type::name => "PreviewsWorld Upcoming Releases",
				Endpoint_Type::throttle_hits => 1,
				Endpoint_Type::throttle_time => 2,
				Endpoint_Type::favicon_url => "http://www.previewsworld.com/favicon.ico",
				Endpoint_Type::data_type => "TXT_",
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
		return true;
	}
}
