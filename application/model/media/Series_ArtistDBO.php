<?php

namespace model\media;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use \model\media\Series_Artist as Series_Artist;

/* import related objects */
use \model\media\Series as Series;
use \model\media\SeriesDBO as SeriesDBO;
use \model\media\Artist as Artist;
use \model\media\ArtistDBO as ArtistDBO;

use \model\media\Artist_Role as Artist_Role;

class Series_ArtistDBO extends _Series_ArtistDBO
{
	public function roleName()
	{
		$role = $this->artist_role();
		if ( $role != false ) {
			return $role->name();
		}
		return Artist_Role::UNKNOWN_ROLE;
	}
}

?>
