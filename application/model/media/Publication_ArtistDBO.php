<?php

namespace model\media;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use \model\media\Publication_Artist as Publication_Artist;

/* import related objects */
use \model\media\Publication as Publication;
use \model\media\PublicationDBO as PublicationDBO;
use \model\media\Artist as Artist;
use \model\media\ArtistDBO as ArtistDBO;

class Publication_ArtistDBO extends _Publication_ArtistDBO
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
