<?php

namespace model\media;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use \model\media\Artist_Role as Artist_Role;

/* import related objects */

class Artist_RoleDBO extends _Artist_RoleDBO
{
	public function isUnknown()
	{
		return $this->code == Artist_Role::UNKNOWN_ROLE;
	}
}

?>
