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
use \model\media\Series as Series;
use \model\media\Publisher as Publisher;
use \model\media\Story_Arc as Story_Arc;
use \model\media\Series_Alias as Series_Alias;
use \model\media\Character as Character;
use \model\media\Character_Alias as Character_Alias;
use \model\media\Series_Character as Series_Character;
use \model\media\Story_Arc_Character as Story_Arc_Character;
use \model\media\Story_Arc_Series as Story_Arc_Series;
use \model\media\Story_Arc_Publication as Story_Arc_Publication;
use \model\media\Publication as Publication;
use \model\media\Publication_Character as Publication_Character;

use \model\reading\Reading_Queue as Reading_Queue;
use \model\reading\Reading_Queue_Item as Reading_Queue_Item;
use \model\reading\Reading_Item as Reading_Item;

class Migration_8 extends Migrator
{
	public function targetVersion() { return "0.7.1"; }

	public function sqlite_preUpgrade()
	{
		$this->sqlite_backupDatabase();
	}

	public function sqlite_upgrade()
	{
		$sql = "DROP TABLE IF EXISTS user_series";
		$this->sqlite_execute( "user_series", $sql, "drop user_series table" );

		/** READING_ITEM */
		$sql = "CREATE TABLE IF NOT EXISTS reading_item ( "
			. Reading_Item::id . " INTEGER PRIMARY KEY, "
			. Reading_Item::user_id . " INTEGER, "
			. Reading_Item::publication_id . " INTEGER, "
			. Reading_Item::created . " INTEGER, "
			. Reading_Item::read_date . " INTEGER, "
			. Reading_Item::mislabeled . " INTEGER, "
			. "FOREIGN KEY (". Reading_Item::user_id .") REFERENCES " . Users::TABLE . "(" . Users::id . "),"
			. "FOREIGN KEY (". Reading_Item::publication_id .") REFERENCES " . Publication::TABLE . "(" . Publication::id . ")"
		. ")";
		$this->sqlite_execute( "reading_item", $sql, "Create table reading_item" );

		$sql = 'CREATE  INDEX IF NOT EXISTS reading_item_read_date on reading_item (read_date)';
		$this->sqlite_execute( "reading_item", $sql, "Index on reading_item (read_date)" );

		/** READING_QUEUE */
		$sql = "CREATE TABLE IF NOT EXISTS reading_queue ( "
			. Reading_Queue::id . " INTEGER PRIMARY KEY, "
			. Reading_Queue::user_id . " INTEGER, "
			. Reading_Queue::series_id . " INTEGER, "
			. Reading_Queue::story_arc_id . " INTEGER, "
			. Reading_Queue::created . " INTEGER, "
			. Reading_Queue::title . " TEXT, "
			. Reading_Queue::favorite . " INTEGER, "
			. Reading_Queue::pub_count . " INTEGER, "
			. Reading_Queue::pub_read . " INTEGER, "
			. Reading_Queue::queue_order . " INTEGER, "
			. "FOREIGN KEY (". Reading_Queue::user_id .") REFERENCES " . Users::TABLE . "(" . Users::id . "),"
			. "FOREIGN KEY (". Reading_Queue::series_id .") REFERENCES " . Series::TABLE . "(" . Series::id . "),"
			. "FOREIGN KEY (". Reading_Queue::story_arc_id .") REFERENCES " . Story_Arc::TABLE . "(" . Story_Arc::id . ")"
		. ")";
		$this->sqlite_execute( "reading_queue", $sql, "Create table reading_queue" );

		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS reading_queue_user_idseries_idstory_arc_id on reading_queue (user_id,series_id,story_arc_id)';
		$this->sqlite_execute( "reading_queue", $sql, "Index on reading_queue (user_id,series_id,story_arc_id)" );

		/** READING_QUEUE_ITEM */
		$sql = "CREATE TABLE IF NOT EXISTS reading_queue_item ( "
			. Reading_Queue_Item::id . " INTEGER PRIMARY KEY, "
			. Reading_Queue_Item::issue_order . " INTEGER, "
			. Reading_Queue_Item::reading_item_id . " INTEGER, "
			. Reading_Queue_Item::reading_queue_id . " INTEGER, "
			. "FOREIGN KEY (". Reading_Queue_Item::reading_queue_id .") REFERENCES " . Reading_Queue::TABLE . "(" . Reading_Queue::id . "),"
			. "FOREIGN KEY (". Reading_Queue_Item::reading_item_id .") REFERENCES " . Reading_Item::TABLE . "(" . Reading_Item::id . ")"
		. ")";
		$this->sqlite_execute( "reading_queue_item", $sql, "Create table reading_queue_item" );

		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS reading_queue_item_reading_item_idreading_queue_id on reading_queue_item (reading_item_id,reading_queue_id)';
		$this->sqlite_execute( "reading_queue_item", $sql, "Index on reading_queue_item (reading_item_id,reading_queue_id)" );
	}

	public function sqlite_postUpgrade()
	{
	}
}
