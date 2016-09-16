<?php

namespace model\reading;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \db\Qualifier as Qualifier;

use \model\reading\Reading_Item as Reading_Item;

/* import related objects */
use \model\user\Users as Users;
use \model\user\UsersDBO as UsersDBO;
use \model\media\Publication as Publication;
use \model\media\PublicationDBO as PublicationDBO;
use \model\reading\Reading_Queue_Item as Reading_Queue_Item;
use \model\reading\Reading_Queue_ItemDBO as Reading_Queue_ItemDBO;

class Reading_ItemDBO extends _Reading_ItemDBO
{
	public function publisher()
	{
		$item = $this->publication();
		if ( $item != false) {
			return $item->publisher();
		}
		return false;
	}

	public function reading_queues($limit = null) {
		$select = \SQL::SelectJoin( Model::Named("Reading_Queue") );
		$select->joinOn( Model::Named("Reading_Queue"), Model::Named("Reading_Queue_Item"), null,
			Qualifier::FK( Reading_Queue_Item::reading_item_id, $this)
		);
		$select->limit = $limit;
		return $select->fetchAll();
	}
}

?>
