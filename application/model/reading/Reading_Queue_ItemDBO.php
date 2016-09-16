<?php

namespace model\reading;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use \model\reading\Reading_Queue_Item as Reading_Queue_Item;

/* import related objects */
use \model\reading\Reading_Queue as Reading_Queue;
use \model\reading\Reading_QueueDBO as Reading_QueueDBO;
use \model\reading\Reading_Item as Reading_Item;
use \model\reading\Reading_ItemDBO as Reading_ItemDBO;

class Reading_Queue_ItemDBO extends _Reading_Queue_ItemDBO
{
	public function publisher()
	{
		$item = $this->reading_item();
		if ( $item != false) {
			return $item->publisher();
		}
		return false;
	}

	public function read_date()
	{
		$item = $this->reading_item();
		if ( $item != false) {
			return $item->read_date();
		}
		return null;
	}
}

?>
