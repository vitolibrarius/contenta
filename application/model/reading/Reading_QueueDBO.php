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

	public function notify( $type = 'none', $object = null )
	{
// 		Logger::logInfo( $this . " Notified $type " . $object, "Notification", $type );
		if ( $object instanceof DataObject ) {
			switch( $object->tableName() ) {
				case 'series':
					if ( $object->pub_available != $this->pub_count ) {
						$this->setPub_count($object->pub_available);
						$this->saveChanges();
					}
					break;
				case 'story_arc':
					if ( $object->pub_available != $this->pub_count ) {
						$this->setPub_count($object->pub_available);
						$this->saveChanges();
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
