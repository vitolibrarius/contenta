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
}

?>
