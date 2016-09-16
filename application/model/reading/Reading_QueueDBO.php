<?php

namespace model\reading;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use \db\Qualifier as Qualifier;

use \model\reading\Reading_Queue as Reading_Queue;

/* import related objects */
use \model\user\Users as Users;
use \model\user\UsersDBO as UsersDBO;
use \model\media\Series as Series;
use \model\media\SeriesDBO as SeriesDBO;
use \model\media\Story_Arc as Story_Arc;
use \model\media\Story_ArcDBO as Story_ArcDBO;
use \model\reading\Reading_Item as Reading_Item;
use \model\reading\Reading_ItemDBO as Reading_ItemDBO;
use \model\reading\Reading_Queue_Item as Reading_Queue_Item;
use \model\reading\Reading_Queue_ItemDBO as Reading_Queue_ItemDBO;

class Reading_QueueDBO extends _Reading_QueueDBO
{
	public function source()
	{
		if ( $this->series() != false ) {
			return $this->series();
		}
		else if ( $this->story_arc() != false ) {
			return $this->story_arc();
		}
		return false;
	}

	public function sourceType()
	{
		if ( $this->series() != false ) {
			return "Series";
		}
		else if ( $this->story_arc() != false ) {
			return "Story Arc";
		}
		return "";
	}

	public function displayName()
	{
		return $this->sourceType() . ": " . $this->title();
	}

	public function joinToReading_Item(Reading_ItemDBO $item)
	{
		$model = Model::Named('Reading_Queue_Item');
		return $model->createJoin($this, $item);
	}

	public function unreadReadingItems($limit = null)
	{
		$select = \SQL::SelectJoin( Model::Named("Reading_Item") );
		$select->joinOn( Model::Named("Reading_Item"), Model::Named("Reading_Queue_Item"), null,
			Qualifier::AndQualifier(
				Qualifier::FK( Reading_Queue_Item::reading_queue_id, $this),
				Qualifier::IsNull( Reading_Item::read_date )
			)
		);
		$select->limit($limit);
		$select->orderBy( Model::Named("Reading_Queue_Item"), Reading_Queue_Item::issue_order);
		return $select->fetchAll();
	}

	public function notify( $type = 'none', $object = null )
	{
		Logger::logInfo( $this . " Notified $type " . $object, "Notification", $type );
		if ( $object instanceof DataObject ) {
			switch( $object->tableName() ) {
				case 'publication':
					if ( $type === Model::NotifyInserted || $type === Model::NotifyUpdated || $type === Model::NotifyDeleted ) {
						$readingItem = Model::Named("Reading_Item")->createReadingItemPublication( $this->user(), $object );
						if ( $readingItem != false ) {
							Model::Named("Reading_Queue_Item")->createJoin( $this, $readingItem );
						}
					}
					break;
				case 'reading_item':
					if ( $type === Model::NotifyInserted || $type === Model::NotifyUpdated || $type === Model::NotifyDeleted ) {
						\SQL::raw(
							"update reading_queue set pub_read = ( "
								. "select count(*) from reading_queue_item rqi  "
								. "join reading_item ri on rqi.reading_item_id = ri.id "
								. "where rqi.reading_queue_id = reading_queue.id and ri.read_date is not null) "
								. "where reading_queue.id = :myid;",
							array( ":myid" => $this->id)
						);
					}
					break;

				case 'reading_queue_item':
					if ( $type === Model::NotifyInserted ) {
						\SQL::raw(
							"update reading_queue set pub_count = ( "
								. "select count(*) from reading_queue_item rqi  "
								. "join reading_item ri on rqi.reading_item_id = ri.id "
								. "where rqi.reading_queue_id = reading_queue.id) "
								. "where reading_queue.id = :myid;",
							array( ":myid" => $this->id)
						);
					}
					break;

				default:
					Logger::logError( $this . " Notified about unknown value " . $object );
					break;
			}
		}
	}
}

?>
