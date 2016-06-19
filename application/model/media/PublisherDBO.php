<?php

namespace model\media;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use \model\media\Publisher as Publisher;

/* import related objects */
use \model\Series as Series;
use \model\SeriesDBO as SeriesDBO;
use \model\Character as Character;
use \model\CharacterDBO as CharacterDBO;
use \model\Story_Arc as Story_Arc;
use \model\Story_ArcDBO as Story_ArcDBO;

class PublisherDBO extends _PublisherDBO
{
	public function publisher() {
		return $this;
	}

}

?>
